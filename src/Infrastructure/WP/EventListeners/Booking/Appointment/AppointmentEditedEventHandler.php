<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Notification\NotificationService;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Factory\Booking\Appointment\AppointmentFactory;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Services\Google\GoogleCalendarService;

/**
 * Class AppointmentEditedEventHandler
 *
 * @package AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment
 */
class AppointmentEditedEventHandler
{
    /** @var string */
    const APPOINTMENT_EDITED = 'appointmentEdited';

    /**
     * @param CommandResult $commandResult
     * @param Container     $container
     *
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function handle($commandResult, $container)
    {
        /** @var GoogleCalendarService $googleCalendarService */
        $googleCalendarService = $container->get('infrastructure.google.calendar.service');
        /** @var NotificationService $notificationService */
        $notificationService = $container->get('application.notification.service');

        $appointment = $commandResult->getData()[Entities::APPOINTMENT];
        $bookings = $commandResult->getData()['bookingsWithChangedStatus'];
        $appointmentStatusChanged = $commandResult->getData()['appointmentStatusChanged'];
        $appointmentRescheduled = $commandResult->getData()['appointmentRescheduled'];

        if ($googleCalendarService) {
            try {
                $googleCalendarService->handleEvent(AppointmentFactory::create($appointment), self::APPOINTMENT_EDITED);
            } catch (\Exception $e) {
            }
        }

        if ($appointmentStatusChanged === true) {
            $notificationService->sendAppointmentStatusNotifications($appointment, true, true);
        }

        if ($appointmentRescheduled === true) {
            $notificationService->sendAppointmentRescheduleNotifications($appointment);
        }

        $notificationService->sendAppointmentEditedNotifications($appointment, $bookings, $appointmentStatusChanged);
    }
}
