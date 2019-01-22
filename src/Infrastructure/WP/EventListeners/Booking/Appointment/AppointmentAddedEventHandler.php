<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Notification\NotificationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Factory\Booking\Appointment\AppointmentFactory;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Services\Google\GoogleCalendarService;

/**
 * Class AppointmentAddedEventHandler
 *
 * @package AmeliaBooking\Infrastructure\WP\EventListeners\Booking\Appointment
 */
class AppointmentAddedEventHandler
{
    /** @var string */
    const APPOINTMENT_ADDED = 'appointmentAdded';

    /**
     * @param CommandResult $commandResult
     * @param Container     $container
     *
     * @throws InvalidArgumentException
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

        if ($googleCalendarService) {
            try {
                $googleCalendarService->handleEvent(AppointmentFactory::create($appointment), self::APPOINTMENT_ADDED);
            } catch (\Exception $e) {
            }
        }

        $notificationService->sendAppointmentStatusNotifications($appointment, false, true);

        $container->get('application.booking.appointment.service')->notifyCanBeBooked($appointment);
    }
}
