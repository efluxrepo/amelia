<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\ValueObjects\String\Name;
use AmeliaBooking\Domain\ValueObjects\String\NotificationSendTo;
use AmeliaBooking\Domain\ValueObjects\String\NotificationType;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;

/**
 * Class NotificationsTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification
 */
class NotificationsTable extends AbstractDatabaseTable
{

    const TABLE = 'notifications';

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();

        $name = Name::MAX_LENGTH;
        $typeEmail = NotificationType::EMAIL;
        $typeSms = NotificationType::SMS;
        $sendToCustomer = NotificationSendTo::CUSTOMER;
        $sendToProvider = NotificationSendTo::PROVIDER;

        return "CREATE TABLE {$table} (
                   `id` INT(11) NOT NULL AUTO_INCREMENT,
                   `name` VARCHAR({$name}) NOT NULL DEFAULT '',
                   `niceName` VARCHAR({$name}) NOT NULL DEFAULT '',
                   `status` ENUM('enabled', 'disabled') NOT NULL DEFAULT 'enabled',
                   `type` ENUM('{$typeEmail}', '{$typeSms}') NOT NULL,
                   `time` TIME NULL DEFAULT NULL,
                   `timeBefore` INT(11) NULL DEFAULT NULL,
                   `timeAfter` INT(11) NULL DEFAULT NULL,
                   `sendTo` ENUM('{$sendToCustomer}', '{$sendToProvider}') NOT NULL,
                   `subject` VARCHAR(255) NOT NULL DEFAULT '',
                   `content` TEXT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
    }
}
