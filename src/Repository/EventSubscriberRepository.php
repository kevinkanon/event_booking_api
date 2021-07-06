<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventSubscriber;
use App\Service\PDO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventSubscriberRepository extends ServiceEntityRepository
{
    private $pdo;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventSubscriber::class);
        $this->pdo = new PDO(
            $_ENV['PDO_DRIVER'] . ':host=' . $_ENV['PDO_HOST'] .';port=' . $_ENV['PDO_PORT'] . ';dbname=' . $_ENV['PDO_DATABASE'],
            $_ENV['PDO_USERNAME'],
            $_ENV['PDO_PASSWORD']
        );

        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function addEventSubscriber(string $name, string $firstName, string $phoneNumber, string $email, int $eventId)
    {
        // Creation new subscriber
        $subscriberSql = 'INSERT INTO subscriber (id, name, first_name, phone_number, email) VALUES (nextval(\'subscriber_id_seq\'), :name, :first_name, :phone_number, :email)';
        $stmt1 = $this->pdo->prepare($subscriberSql);
        $stmt1->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt1->bindValue(':first_name', $firstName, PDO::PARAM_STR);
        $stmt1->bindValue(':phone_number', $phoneNumber, PDO::PARAM_STR);
        $stmt1->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt1->execute();

        // get new subscriber
        $stmt2 = $this->pdo->prepare('SELECT * FROM subscriber WHERE email =:email');
        $stmt2->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt2->execute();
        $stmt2->setFetchMode(PDO::FETCH_OBJ);
        $newSubscriber = $stmt2->fetch();
        $newSubscriber_id = $newSubscriber->id;

        // create new event subscriber linked by event and subscriber
        $eventSubscriberSql = 'INSERT INTO event_subscriber (id, event_id, subscriber_id) VALUES (nextval(\'event_subscriber_id_seq\'), :event_id, :subscriber_id)';
        $stmt3 = $this->pdo->prepare($eventSubscriberSql);
        $stmt3->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $stmt3->bindValue(':subscriber_id', $newSubscriber_id, PDO::PARAM_INT);
        $stmt3->execute();
    }

    /**
     *  Check if the event is full with max subscriber
     *
     * @param int $eventId
     * @return bool
     */
    public function isMaxSubscriber(int $eventId)
    {
        $sql = 'SELECT count(*)
           FROM  event_subscriber  
           JOIN  subscriber ON event_subscriber.subscriber_id = subscriber.id
           JOIN  event  ON event_subscriber.event_id = event.id 
           WHERE event.id =:eventId';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['count'] >= Event::MAX_EVENT_SUBSCRIBER;
    }

    public function deleteEventSubscriber(int $eventId)
    {
        $deleteEventSql = 'DELETE FROM event WHERE id=:eventId';
        $deleteSubscriberSql = 'DELETE FROM subscriber WHERE subscriber.id IN (SELECT event_subscriber.subscriber_id FROM event_subscriber WHERE event_id=:eventId)';

        $stmtSubscriber = $this->pdo->prepare($deleteSubscriberSql);
        $stmtSubscriber->bindValue(':eventId', $eventId, PDO::PARAM_INT);
        $stmtSubscriber->execute();

        $stmtRelationSql = $this->pdo->prepare($deleteEventSql);
        $stmtRelationSql->bindValue(':eventId', $eventId, PDO::PARAM_INT);
        return $stmtRelationSql->execute();

    }
}
