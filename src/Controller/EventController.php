<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\EventSubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    private $eventRepository;
    private $eventSubscriberRepository;

    public function __construct(EventRepository $eventRepository, EventSubscriberRepository $eventSubscriberRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->eventSubscriberRepository = $eventSubscriberRepository;
    }

    /**
     * @Route("/event/add", name="add_customer", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $startDate = $this->validate_input($data['startDate']);
        $endDate = $this->validate_input($data['endDate']);
        if (empty($startDate)) {
            return new JsonResponse(['error' => 'Expecting startDate parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        } elseif (empty($endDate)) {
            return new JsonResponse(['error' => 'Expecting endDate parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $startDateToSave = date('Y-m-d H:i:s', strtotime($startDate));
        $endDateToSave = date('Y-m-d H:i:s', strtotime($endDate));
        if ($endDateToSave <= $startDateToSave) {
            return new JsonResponse(['error' => 'startDate must be less than endDate!'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $isCreated = $this->eventRepository->saveEvent($startDateToSave, $endDateToSave);
        $responseType = $isCreated ? Response::HTTP_CREATED : Response::HTTP_CONFLICT;
        $status = $isCreated ? 'Event created!' : 'Error on creation!';

        return new JsonResponse(['status' => $status], $responseType);
    }

    /**
     * @Route("/event/{id}", name="get_one_event", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function get($id): JsonResponse
    {
        $event = $this->eventRepository->getEvent($id);
        if (!$event) {
            throw new NotFoundHttpException('No event found for '.$id);
        }
        $data = [
            'id' => $event->id,
            'startDate' => $event->start_date,
            'endDate' => $event->end_date,
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/events", name="get_all_events", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $events = $this->eventRepository->getAllEvents();
        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->id,
                'startDate' => $event->start_date,
                'endDate' => $event->end_date,
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/event/update/{id}", name="update_event", methods={"PUT"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        $event = $this->eventRepository->getEvent($id);
        $data = json_decode($request->getContent(), true);
        $updatedStartDate = $this->validate_input($data['startDate']);
        $updatedEndDate = $this->validate_input($data['endDate']);
        if (empty($updatedStartDate)) {
            return new JsonResponse(['error' => 'Expecting start date parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        } elseif (empty($updatedEndDate)) {
            return new JsonResponse(['error' => 'Expecting end date parameter!'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $startDateToSave = date('Y-m-d H:i:s', strtotime($updatedStartDate));
        $endDateToSave = date('Y-m-d H:i:s', strtotime($updatedEndDate));
        if ($endDateToSave <= $startDateToSave) {
            return new JsonResponse(['error' => 'startDate must be less than endDate!'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $isUpdated = $this->eventRepository->updateEvent($event, $startDateToSave, $endDateToSave);
        $responseType = $isUpdated ? Response::HTTP_OK : Response::HTTP_EXPECTATION_FAILED;
        $status = $isUpdated ? 'event update!' : 'Error on update!';

        return new JsonResponse(['status' => $status], $responseType);
    }

    /**
     * @Route("/event/delete/{id}", name="delete_event", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $isDeleted = $this->eventSubscriberRepository->deleteEventSubscriber($id);
        $responseType = $isDeleted ? Response::HTTP_OK : Response::HTTP_EXPECTATION_FAILED;
        $status = $isDeleted ? 'event deleted!' : 'Error on delete!';

        return new JsonResponse(['status' =>$status], $responseType);
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
}
