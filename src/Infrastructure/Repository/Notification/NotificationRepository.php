<?php

namespace AmeliaBooking\Infrastructure\Repository\Notification;

use AmeliaBooking\Domain\Entity\Notification\Notification;
use AmeliaBooking\Domain\Factory\Notification\NotificationFactory;
use AmeliaBooking\Domain\Repository\Notification\NotificationRepositoryInterface;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\AbstractRepository;

/**
 * Class NotificationRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository\Notification
 */
class NotificationRepository extends AbstractRepository implements NotificationRepositoryInterface
{

    const FACTORY = NotificationFactory::class;

    /**
     * @param Notification $entity
     *
     * @return bool|mixed
     * @throws QueryExecutionException
     */
    public function add($entity)
    {
        $data = $entity->toArray();

        $params = [
            ':name'       => $data['name'],
            ':sendToType' => $data['sendToType'],
            ':subject'    => $data['subject'],
            ':content'    => $data['content'],
        ];

        try {
            $statement = $this->connection->prepare(
                "INSERT INTO {$this->table} 
                (`name`, `sendToType`, `subject`, `content`)
                VALUES (:name, :sendToType, :subject, :content)"
            );

            $res = $statement->execute($params);
            if (!$res) {
                throw new QueryExecutionException('Unable to add data in ' . __CLASS__);
            }

            return $res;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__);
        }
    }

    /**
     * @param int          $id
     * @param Notification $entity
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function update($id, $entity)
    {
        $data = $entity->toArray();

        $params = [
            ':status'     => $data['status'],
            ':time'       => $data['time'],
            ':timeBefore' => $data['timeBefore'],
            ':timeAfter'  => $data['timeAfter'],
            ':subject'    => $data['subject'],
            ':content'    => $data['content'],
            ':id'         => $id,
        ];

        try {
            $statement = $this->connection->prepare(
                "UPDATE {$this->table} SET 
                `status` = :status,
                `time` = :time,
                `timeBefore` = :timeBefore,
                `timeAfter` = :timeAfter,
                `subject` = :subject,
                `content` = :content
                WHERE id = :id"
            );

            $res = $statement->execute($params);

            if (!$res) {
                throw new QueryExecutionException('Unable to save data in ' . __CLASS__);
            }

            return $res;
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to save data in ' . __CLASS__);
        }
    }

    /**
     * @param $name
     *
     * @return Notification
     * @throws QueryExecutionException
     * @throws NotFoundException
     */
    public function getByName($name)
    {
        try {
            $statement = $this->connection->prepare($this->selectQuery() . " WHERE {$this->table}.name = :name");
            $statement->bindParam(':name', $name);
            $statement->execute();
            $row = $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        if (!$row) {
            throw new NotFoundException('Data not found in ' . __CLASS__);
        }

        return call_user_func([static::FACTORY, 'create'], $row);
    }
}
