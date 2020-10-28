<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Car;
use App\Entity\Option;
use App\Entity\Rental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Datetime;

class TestApiController extends AbstractController
{
    private $serializer;
    private $em;
    private $validator;
    private $cars = [];
    private $rentals = [];
    private $options = [];
    private $actors;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {

        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
        $this->actors = array(
            'driver',
            'owner',
            'insurance',
            'assistance',
            'our_company',
        );
    }

    /**
     * @Route("/api/level1", name="level1", methods={"POST"})
     */
    public function level1(Request $request)
    {
        // Normalization of the request
        $data = json_decode($request->getContent(), true);
        
        try {

            // Validation of data
            $this->CheckData($data);

            $response = [];

            foreach ($this->rentals as $rentalKey => $rental) {

                $startDate = new DateTime($rental->getStartDate());
                $endDate = new DateTime($rental->getEndDate());

                // Number of the rental days with the last day inclued
                $rentalDays = $endDate->diff($startDate)->format('%a') + 1;

                $price = $rentalDays * $rental->getCar()->getPricePerDay() + $rental->getDistance() * $rental->getCar()->getPricePerKm();

                $rentalResponse = array(
                    'id' => $rental->getId(),
                    'price' => $price,
                );

                array_push($response, $rentalResponse);
            }

            return $this->json([
                'rentals' => $response,
            ]);
        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @Route("/api/level2", name="level2", methods={"POST"})
     */
    public function level2(Request $request)
    {
        // Normalization of the request
        $data = json_decode($request->getContent(), true);

        try {

            // Validation of data
            $this->CheckData($data);

            $response = [];
            $discount = 0;

            foreach ($this->rentals as $rentalKey => $rental) {

                $startDate = new DateTime($rental->getStartDate());
                $endDate = new DateTime($rental->getEndDate());

                // Rental days with the last day inclued
                $rentalDays = $endDate->diff($startDate)->format('%a') + 1;

                // Computation of the total rental price

                // Depending on the rental days there is a promotion
                // 1 day => 10%
                // 4 days => 30%
                // 10 days => 50%
                if ($rentalDays > 1) {
                    $discount = 0.1;
                }

                if ($rentalDays > 4) {
                    $discount = 0.3;
                }

                if ($rentalDays > 10) {
                    $discount = 0.5;
                }

                $pricePerDay = $rentalDays * $rental->getCar()->getPricePerDay() * (1 - $discount);

                $pricePerKm = $rental->getDistance() * $rental->getCar()->getPricePerKm();

                $price = $pricePerDay + $pricePerKm;

                $rentalResponse = array(
                    'id' => $rental->getId(),
                    'price' => $price,
                );

                array_push($response, $rentalResponse);
            }

            return $this->json([
                'rentals' => $response,
            ]);
        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @Route("/api/level3", name="level3", methods={"POST"})
     */
    public function level3(Request $request)
    {
        // Normalization of the request
        $data = json_decode($request->getContent(), true);

        try {

            // Validation of data
            $this->CheckData($data);

            $response = [];
            $discount = 0;

            foreach ($this->rentals as $rentalKey => $rental) {

                $startDate = new DateTime($rental->getStartDate());
                $endDate = new DateTime($rental->getEndDate());

                // Rental days with the last day inclued
                $rentalDays = $endDate->diff($startDate)->format('%a') + 1;

                // Computation of the total rental price

                // Depending on the rental days there is a promotion
                // 1 day => 10%
                // 4 days => 30%
                // 10 days => 50%
                if ($rentalDays > 1) {
                    $discount = 0.1;
                }

                if ($rentalDays > 4) {
                    $discount = 0.3;
                }

                if ($rentalDays > 10) {
                    $discount = 0.5;
                }

                $pricePerDay = $rentalDays * $rental->getCar()->getPricePerDay() * (1 - $discount);

                $pricePerKm = $rental->getDistance() * $rental->getCar()->getPricePerKm();

                $price = $pricePerDay + $pricePerKm;

                // Compute commission
                // 30% of the price
                $commission = $price * 0.3;

                // Half goes to the insurance
                $insuranceCommission = $commission * 0.5;

                // 1€/day goes to the roadside assistance
                $raCommission = (100 * $rentalDays);

                // The rest goes to us
                $ourCommission = $commission - ($insuranceCommission + $raCommission);

                $commissionArray = array(
                    'insurance_fee' => $insuranceCommission,
                    'assistance_fee' => $raCommission,
                    'our_fee' => $ourCommission,
                );

                $rentalResponse = array(
                    'id' => $rental->getId(),
                    'price' => $price,
                    'commision' => $commissionArray,
                );

                array_push($response, $rentalResponse);
            }

            return $this->json([
                'rentals' => $response,
            ]);
        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @Route("/api/level4", name="level4", methods={"POST"})
     */
    public function level4(Request $request)
    {
        // Normalization of the request
        $data = json_decode($request->getContent(), true);

        try {

            // Validation of data
            $this->CheckData($data);

            $response = [];
            $discount = 0;

            foreach ($this->rentals as $rentalKey => $rental) {

                $startDate = new DateTime($rental->getStartDate());
                $endDate = new DateTime($rental->getEndDate());

                // Rental days with the last day inclued
                $rentalDays = $endDate->diff($startDate)->format('%a') + 1;

                // Computation of the total rental price

                // Depending on the rental days there is a promotion
                // 1 day => 10%
                // 4 days => 30%
                // 10 days => 50%
                if ($rentalDays > 1) {
                    $discount = 0.1;
                }

                if ($rentalDays > 4) {
                    $discount = 0.3;
                }

                if ($rentalDays > 10) {
                    $discount = 0.5;
                }

                $pricePerDay = $rentalDays * $rental->getCar()->getPricePerDay() * (1 - $discount);

                $pricePerKm = $rental->getDistance() * $rental->getCar()->getPricePerKm();

                $price = $pricePerDay + $pricePerKm;

                // Compute commission
                // 30% of the price
                $commission = $price * 0.3;

                // Half goes to the insurance
                $insuranceCommission = $commission * 0.5;

                // 1€/day goes to the roadside assistance
                $raCommission = (100 * $rentalDays);

                // The rest goes to us
                $ourCommission = $commission - ($insuranceCommission + $raCommission);

                // Creation of the actions
                $actions = [];
                foreach ($this->actors as $actorKey => $actor) {
                    switch ($actor) {
                        case 'driver':
                            $newAction = new Action($actor, 'debit', $price, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'owner':
                            $newAction = new Action($actor, 'credit', $price - $commission, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'insurance':
                            $newAction = new Action($actor, 'credit', $insuranceCommission, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'assistance':
                            $newAction = new Action($actor, 'credit', $raCommission, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'our_company':
                            $newAction = new Action($actor, 'credit', $ourCommission, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                    }
                }

                $rentalResponse = array(
                    'id' => $rental->getId(),
                    'actions' => $actions,
                );

                array_push($response, $rentalResponse);
            }

            return $this->json([
                'rentals' => $response,
            ]);
        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @Route("/api/level5", name="level5", methods={"POST"})
     */
    public function level5(Request $request)
    {
        // Normalization of the request
        $data = json_decode($request->getContent(), true);

        try {

            // Validation of data
            $this->CheckData($data);

            $response = [];
            $discount = 0;

            foreach ($this->rentals as $rentalKey => $rental) {

                $startDate = new DateTime($rental->getStartDate());
                $endDate = new DateTime($rental->getEndDate());

                // Rental days with the last day inclued
                $rentalDays = $endDate->diff($startDate)->format('%a') + 1;

                // Computation of the total rental price
                // Depending on the rental days there is a promotion
                // 1 day => 10%
                // 4 days => 30%
                // 10 days => 50%
                if ($rentalDays > 1) {
                    $discount = 0.1;
                }

                if ($rentalDays > 4) {
                    $discount = 0.3;
                }

                if ($rentalDays > 10) {
                    $discount = 0.5;
                }

                $pricePerDay = $rentalDays * $rental->getCar()->getPricePerDay() * (1 - $discount);

                $pricePerKm = $rental->getDistance() * $rental->getCar()->getPricePerKm();

                $price = $pricePerDay + $pricePerKm;

                // Compute commission
                // 30% of the price
                $commission = $price * 0.3;

                // Half goes to the insurance
                $insuranceCommission = $commission * 0.5;

                // 1€/day goes to the roadside assistance
                $raCommission = (100 * $rentalDays);

                // The rest goes to us
                $ourCommission = $commission - ($insuranceCommission + $raCommission);

                // Check if there are options
                $ownerOptions = 0;
                $ourCompanyOptions = 0;
                $optionsResponse = [];
                if (sizeof($rental->getOptions()) > 0) {
                    foreach ($rental->getOptions() as $optionKey => $option) {
                        switch ($option->gettype()) {
                            case 'gps':
                                // 5€/day, all the money goes to the owner
                                $ownerOptions += 500 * $rentalDays;
                                break;
                            case 'baby_seat':
                                // 2€/day, all the money goes to the owner
                                $ownerOptions += 200 * $rentalDays;
                                break;
                            case 'additional_insurance':
                                // 10€/day, all the money goes to our company
                                $ourCompanyOptions += 1000 * $rentalDays;

                                break;
                        }

                        array_push($optionsResponse, $option->gettype());
                    }
                }

                $optionPrice = $ownerOptions + $ourCompanyOptions;

                // Creation of the actions
                $actions = [];
                foreach ($this->actors as $actorKey => $actor) {
                    switch ($actor) {
                        case 'driver':
                            $newAction = new Action($actor, 'debit', $price + $optionPrice, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'owner':
                            $newAction = new Action($actor, 'credit', ($price - $commission) + $ownerOptions, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'insurance':
                            $newAction = new Action($actor, 'credit', $insuranceCommission, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'assistance':
                            $newAction = new Action($actor, 'credit', $raCommission, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                        case 'our_company':
                            $newAction = new Action($actor, 'credit', $ourCommission + $ourCompanyOptions, $rental);
                            array_push($actions, $this->createAction($newAction, $rental));
                            break;
                    }
                }

                $rentalResponse = array(
                    'id' => $rental->getId(),
                    'options' => $optionsResponse,
                    'actions' => $actions,
                );

                array_push($response, $rentalResponse);
            }

            return $this->json([
                'rentals' => $response,
            ]);
        } catch (\Exception $e) {

            return $this->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    private function CheckData(array $data = null)
    {
        // If the data given are invalid
        if (!$data) {
            throw new \Exception('The request data are invalid');
        }

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

            // The full Car object is needed for the Rental Object, not only the car_id
            foreach ($this->cars as $car) {
                // Get the Car with the correct car_id
                if ($car->getId() === $jsonRental['car_id']) {
                    $this->rentals[$jsonRentalKey]->setCar($car);
                }
            }

            $this->checkFormatErrors($this->rentals[$jsonRentalKey]);
        }

        if (isset($data['options'])) {
            // Creation of an array containing Option objects
            foreach ($data['options'] as $jsonOptionKey => $jsonOption) {

                $this->options[$jsonOptionKey] = $this->serializer->deserialize(json_encode($jsonOption), Option::class, 'json');

                // The full Car object is needed for the Rental Object, not only the car_id
                foreach ($this->rentals as $rental) {
                    // Get the Car with the correct car_id
                    if ($rental->getId() === $jsonOption['rental_id']) {

                        $this->options[$jsonOptionKey]->addRental($rental);
                        $rental->addOption($this->options[$jsonOptionKey]);
                    }
                }

                $this->checkFormatErrors($this->options[$jsonOptionKey]);
            }
        }
    }

    private function checkFormatErrors(Object $object)
    {
        $errors = $this->validator->validate($object);
        if (count($errors) > 0) {
            throw new \Exception($errors);
        }
    }

    private function checkRequestBody(array $body)
    {
        if (!isset($body['cars'])) {
            throw new \Exception('Invalid request body: The fiel cars is missing');
        }

        if (!isset($body['rentals'])) {
            throw new \Exception('Invalid request body: The field rentals is missing');
        }
    }

    private function createAction(Action $newAction, Rental $rental)
    {
        $this->checkFormatErrors($newAction);

        $action = array(
            'who' => $newAction->getActor(),
            'type' => $newAction->getType(),
            'amount' => $newAction->getAmount(),
        );

        $rental->addAction($newAction);

        return $action;
    }
}
