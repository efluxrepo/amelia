<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;
use AmeliaBooking\Infrastructure\WP\Translations\NotificationsStrings;

/**
 * Class NotificationsTableInsertRows
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification
 */
class NotificationsTableInsertRows extends AbstractDatabaseTable
{

    const TABLE = 'notifications';

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();
        $rows = [];
        $rows = array_merge($rows, NotificationsStrings::getCustomerNonTimeBasedNotifications());
        $rows = array_merge($rows, NotificationsStrings::getCustomerTimeBasedNotifications());
        $rows = array_merge($rows, NotificationsStrings::getProviderNonTimeBasedNotifications());
        $rows = array_merge($rows, NotificationsStrings::getProviderTimeBasedNotifications());

        $result = [];
        foreach ($rows as $row) {
            $result[] = "INSERT INTO {$table} 
                        (
                            `name`,
                            `niceName`,
                            `type`,
                            `time`,
                            `timeBefore`,
                            `timeAfter`,
                            `sendTo`,
                            `subject`,
                            `content`
                        ) 
                        VALUES
                        (
                            '{$row['name']}',
                            '{$row['niceName']}',
                            '{$row['type']}',
                             {$row['time']},
                             {$row['timeBefore']},
                             {$row['timeAfter']},
                            '{$row['sendTo']}',
                            '{$row['subject']}',
                            '{$row['content']}'
                        )";
        }

        return $result;
    }
}
