<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Car;
use App\Entity\Rental;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use \Datetime;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestApiController extends AbstractController
{
    const PDO_EXCEPTION_CODE = '23000';

    private $validator;
    private $serializer;
    private $em;
   
    /**
     * @Route("/api/level1", name="level1", methods={"POST"})
     */
    public function level1(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,  ValidatorInterface $validator): Response
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->cars = [];
        $this->rentals = [];
        // Normalization of the request
        $data = json_decode($request->getContent(), true);

        try {

            $this->CheckAndPersistData($data);

            $response = [];

            foreach ($this->rentals as $rentalKey => $rental) {
                $startDate = new DateTime($rental->getStartDate());
                $endDate = new DateTime($rental->getEndDate());
                
                $rentalDays = $endDate->diff($startDate)->format("%a");

                $price = $rentalDays * $rental->getCar()->getPricePerDay() + $rental->getDistance() * $rental->getCar()->getPricePerKm();
                
                $rentalResponse = array(
                    "id" => $rental->getId(),
                    "price" => $price
                );

                array_push($response, $rentalResponse);
            }

            return $this->json([
                'rentals' => $response
            ]);

        } catch (UniqueConstraintViolationException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
        catch (\Exception $e) {
            
            switch ($e->getCode()) {
                case $this::PDO_EXCEPTION_CODE:
                    
                    return $this->json([
                        'error' => $e->getMessage()
                    ], 500);

                    break;
                
                default:
                    return $this->json([
                        'error' => $e->getMessage()
                    ], 400);
                    break;
            }

        }
    }

    private function CheckAndPersistData(Array $data)
    {
        // Check the request
        $this->checkRequestBody($data);

        // Creation of an array containing Car objects
        foreach ($data['cars'] as $jsonCarkey => $jsonCar) {

            // Json to Car objects
            $this->cars[$jsonCarkey] = $this->serializer->deserialize(json_encode($jsonCar), Car::class, 'json');

            $this->checkFormatErrors($this->cars[$jsonCarkey]);
            
            $this->em->persist($this->cars[$jsonCarkey]);
        }
        
        // Creation of an array containing Rental objects
        foreach ($data['rentals'] as $jsonRentalKey => $jsonRental) {

            $this->rentals[$jsonRentalKey] = $this->serializer->deserialize(json_encode($jsonRental), Rental::class, 'json');
            $rental_car = null;
            // The full Car object is needed for the Rental Object, not only the car_id
            foreach ($this->cars as $car) {
                // Get the Car with the correct car_id
                if($car->getId() === $jsonRental['car_id']) $rental_car = $car;
            }
            $this->rentals[$jsonRentalKey]->setCar($rental_car);

            $this->checkFormatErrors($this->rentals[$jsonRentalKey]);
            
            $this->em->persist($this->rentals[$jsonRentalKey]);
        }

        // Execution of the queries to persist data if everything is ok
        $result = $this->em->flush();

    }

    private function checkFormatErrors(Object $object)
    {
        $errors = $this->validator->validate($object);
        if(count($errors) > 0) throw new \Exception($errors);
    }

    private function checkRequestBody(Array $body)
    {
        if(!isset($body['cars']) || !isset($body['rentals'])) throw new \Exception('Invalid request body');
    }

}
