<?php

namespace App\Repository;

use App\Entity\Event;
use App\Service\PDO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    private $pdo;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
        $this->pdo = new PDO(
            $_ENV['PDO_DRIVER'] . ':host=' . $_ENV['PDO_HOST'] .';port=' . $_ENV['PDO_PORT'] . ';dbname=' . $_ENV['PDO_DATABASE'],
            $_ENV['PDO_USERNAME'],
            $_ENV['PDO_PASSWORD']
        );

        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function saveEvent(string $startDate, string $endDate)
    {
        $sql = 'INSERT INTO event (id, start_date, end_date) VALUES (nextval(\'event_id_seq\'), :start_date, :end_date)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $endDate, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getEvent(int $id)
    {
        $sql = 'SELECT * FROM event WHERE id = :eventId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':eventId', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_OBJ);

        return $stmt->fetch();
    }

    public function getAllEvents()
    {
        $sql = 'SELECT * FROM event';
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateEvent($event, string $updatedStartDate, string $updatedEndDate)
    {
        $sql = 'UPDATE event SET start_date=:start_date, end_date=:end_date WHERE id =:eventId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':start_date', $updatedStartDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $updatedEndDate, PDO::PARAM_STR);
        $stmt->bindValue(':eventId', $event->id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->execute();
    }

    public function removeEvent($event)
    {
        $eventId = $event->id;
        $sql = 'DELETE FROM event WHERE id = :eventId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':eventId', $eventId, \App\Service\PDO::PARAM_INT);

        return $stmt->execute();
    }
}
