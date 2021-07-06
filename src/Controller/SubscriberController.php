<?php

namespace App\Controller;

use App\Repository\EventSubscriberRepository;
use App\Repository\SubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriberController extends AbstractController
{
    private $subscriberRepository;
    private $eventSubscriberRepository;

    public function __construct(SubscriberRepository $subscriberRepository, EventSubscriberRepository $eventSubscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
        $this->eventSubscriberRepository = $eventSubscriberRepository;
    }

    /**
     * @Route("/subscriber/add", name="add_subscriber", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = $this->validate_input($data['name']);
        $firstName = $this->validate_input($data['lastName']);
        $phoneNumber = $this->validate_input($data['phoneNumber']);
        $email = $this->validate_input($data['email']);
        $eventId = $this->validate_id_input($data['eventId']);
        if (empty($name)) {
            return new JsonResponse(['error' => 'Expecting name parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        } elseif (empty($firstName)) {
            return new JsonResponse(['error' => 'Expecting first name parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        } elseif (empty($phoneNumber)) {
            return new JsonResponse(['error' => 'Expecting phone number parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        } elseif (empty($email)) {
            return new JsonResponse(['error' => 'Expecting email parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        } elseif (empty($eventId)) {
            return new JsonResponse(['error' => 'event field is mandatory !'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Check if the event is full with max subscriber
        $isMaxSubscriber = $this->eventSubscriberRepository->isMaxSubscriber($eventId);
        if ($isMaxSubscriber) {
            return new JsonResponse(['error' => 'This event is already full, try to choose another !'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $this->eventSubscriberRepository->addEventSubscriber($name, $firstName, $phoneNumber, $email, $eventId);

        return new JsonResponse(['status' => 'subscriber created!'], Response::HTTP_CREATED);
    }

    /**
     * Clean data comming from front before manage database
     *
     * @param string $data
     * @return string
     */
    public function validate_input(string $data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = strip_tags($data);

        return $data;
    }

    /**
     * Clean data comming from front before manage database
     *
     * @param string $data
     * @return int
     */
    public function validate_id_input(string $data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = strip_tags($data);

        return (int) $data;
    }
}
