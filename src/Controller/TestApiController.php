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
    private $serializer;
    private $em;
    private $validator;
    private $cars = [];
    private $rentals = []; 

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em,  ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
    }
   
    /**
     * @Route("/api/level1", name="level1", methods={"POST"})
     */
    public function level1(Request $request): Response
    {
        // Normalization of the request
        $data = json_decode($request->getContent(), true);

        try {

            $this->CheckData($data);

            $response = [];

            foreach ($this->rentals as $rentalKey => $rental) {
                $startDate = new DateTime($rental->getStartDate());
                $endDate = new DateTime($rental->getEndDate());
                
                // Number of the rental days with the last day inclued
                $rentalDays = $endDate->diff($startDate)->format("%a") + 1;

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

        } catch (\Exception $e) {
            
            switch ($e->getCode()) {
                
                default:
                    return $this->json([
                        'error' => $e->getMessage()
                    ], 400);
                    break;
            }

        }
    }

    /**
     * @Route("/api/level2", name="level2", methods={"POST"})
     */
    public function level2(Request $request)
    {

    }

    private function CheckData(Array $data)
    {
        // Check the request
        $this->checkRequestBody($data);

        // Creation of an array containing Car objects
        foreach ($data['cars'] as $jsonCarkey => $jsonCar) {

            // Json to Car objects
            $this->cars[$jsonCarkey] = $this->serializer->deserialize(json_encode($jsonCar), Car::class, 'json');

            $this->checkFormatErrors($this->cars[$jsonCarkey]);

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
            
        }
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
