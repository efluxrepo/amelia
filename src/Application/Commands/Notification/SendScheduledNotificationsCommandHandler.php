<?php

namespace AmeliaBooking\Application\Commands\Notification;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Notification\NotificationService;

/**
 * Class SendScheduledNotificationsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Notification
 */
class SendScheduledNotificationsCommandHandler extends CommandHandler
{
    /**
     * @return CommandResult
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function handle()
    {
        $result = new CommandResult();

        /** @var NotificationService $notificationService */
        $notificationService = $this->getContainer()->get('application.notification.service');

        $notificationService->sendAppointmentNextDayReminderNotifications();

        $notificationService->sendAppointmentFollowUpNotifications();

        $notificationService->sendBirthdayGreetingNotifications();

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Scheduled email notifications successfully sent');

        return $result;
    }
}
