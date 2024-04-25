<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/car')]
class CarController extends AbstractController
{
    #[Route('s/', name: 'app_car_index', methods: ['GET'])]
    public function index(CarRepository $carRepository): Response
    {
        return $this->json([
            'cars' => $carRepository->findAll()
        ]);
    }

    #[Route('/', name: 'app_car_new', methods: [ 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        // Créer une nouvelle instance de Car
        $car = new Car();
        $car->setModel($data['model'] ?? null);
        $car->setRegistrationNum($data['registration_num'] ?? null);
        $car->setDispo($data['isDispo'] ?? 0);
        return $this->json([
            'car' => $car
        ]);
    }

    #[Route('/{id}', name: 'app_car_show', methods: ['GET'])]
    public function show(Car $car): Response
    {
        return $this->json([
            'car' => $car
        ]);
    }

    #[Route('/{id}', name: 'app_car_edit', methods: ['PATCH', 'PUT'])]
    public function edit(Request $request, Car $car, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        // Désérialisation des données dans l'entité Car existante
        $serializer->deserialize($request->getContent(), Car::class, 'json', ['object_to_populate' => $car]);

        // Si nécessaire, validez les données ici

        $entityManager->flush();

        return $this->json([
            'message' => 'Car updated successfully',
            'car' => $car
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_car_delete', methods: ['DELETE'])]
    public function delete(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($car);
        $entityManager->flush();

        return $this->json(['message' => 'Car deleted successfully'], Response::HTTP_OK);
    }
}
