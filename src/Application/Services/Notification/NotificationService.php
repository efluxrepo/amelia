<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Services\Notification;

use AmeliaBooking\Application\Services\Booking\BookingApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Entity\Booking\Appointment\Appointment;
use AmeliaBooking\Domain\Entity\Notification\Notification;
use AmeliaBooking\Domain\Factory\Booking\Appointment\AppointmentFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\String\NotificationStatus;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\CustomerBookingRepository;
use AmeliaBooking\Infrastructure\Repository\Notification\NotificationLogRepository;
use AmeliaBooking\Infrastructure\Repository\Notification\NotificationRepository;
use AmeliaBooking\Infrastructure\Repository\User\UserRepository;
use AmeliaBooking\Infrastructure\Services\Notification\MailgunService;
use AmeliaBooking\Infrastructure\Services\Notification\PHPMailService;
use AmeliaBooking\Infrastructure\Services\Notification\SMTPService;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

/**
 * Class NotificationService
 *
 * @package AmeliaBooking\Application\Services\Notification
 */
class NotificationService
{
    /** @var Container */
    private $container;

    /**
     * ProviderApplicationService constructor.
     *
     * @param Container $container
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getByName($name)
    {
        /** @var NotificationRepository $notificationRepo */
        $notificationRepo = $this->container->get('domain.notification.repository');

        return $notificationRepo->getByName($name);
    }

    /**
     * @param array $appointmentArray
     * @param bool  $forcedStatusChange - True when appointment status is changed to 'pending' because minimum capacity
     * condition is not satisfied
     * @param bool  $logNotification
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendAppointmentStatusNotifications($appointmentArray, $forcedStatusChange, $logNotification)
    {
        /** @var BookingApplicationService $bookingAS */
        $bookingAS = $this->container->get('application.booking.booking.service');

        /** @var Appointment $appointment */
        $appointment = AppointmentFactory::create($appointmentArray);

        // Notify customers
        if ($appointmentArray['notifyParticipants']) {

            /** @var Notification $customerNotification */
            $customerNotification = $this->getByName("customer_appointment_{$appointmentArray['status']}");

            if ($customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
                // If appointment status is changed to 'pending' because minimum capacity condition is not satisfied,
                // return all 'approved' bookings and send them notification that appointment is now 'pending'.
                if ($forcedStatusChange === true) {
                    $appointmentArray['bookings'] = $bookingAS->filterApprovedBookings($appointmentArray['bookings']);
                }

                // Notify each customer from customer bookings
                foreach (array_keys($appointmentArray['bookings']) as $bookingKey) {
                    $this->sendNotification(
                        $appointmentArray,
                        $appointment,
                        $customerNotification,
                        'customer',
                        $logNotification,
                        $bookingKey
                    );
                }
            }
        }

        // Notify provider
        /** @var Notification $providerNotification */
        $providerNotification = $this->getByName("provider_appointment_{$appointmentArray['status']}");

        if ($providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
            $this->sendNotification(
                $appointmentArray,
                $appointment,
                $providerNotification,
                'employee',
                $logNotification,
                null
            );
        }
    }

    /**
     * @param array $appointmentArray
     * @param array $bookingsArray
     * @param bool  $forcedStatusChange
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendAppointmentEditedNotifications($appointmentArray, $bookingsArray, $forcedStatusChange)
    {
        /** @var BookingApplicationService $bookingAS */
        $bookingAS = $this->container->get('application.booking.booking.service');

        /** @var Appointment $appointment */
        $appointment = AppointmentFactory::create($appointmentArray);

        // Notify customers
        if ($appointmentArray['notifyParticipants']) {
            // If appointment status is 'pending', remove all 'approved' bookings because they can't receive
            // notification that booking is 'approved' until appointment status is changed to 'approved'
            if ($appointmentArray['status'] === 'pending') {
                $bookingsArray = $bookingAS->removeBookingsByStatuses($bookingsArray, ['approved']);
            }

            // If appointment status is changed, because minimum capacity condition is satisfied or not,
            // remove all 'approved' bookings because notification is already sent to them.
            if ($forcedStatusChange === true) {
                $bookingsArray = $bookingAS->removeBookingsByStatuses($bookingsArray, ['approved']);
            }

            $appointmentArray['bookings'] = $bookingsArray;

            foreach (array_keys($appointmentArray['bookings']) as $bookingKey) {
                /** @var Notification $customerNotification */
                $customerNotification =
                    $this->getByName("customer_appointment_{$appointmentArray['bookings'][$bookingKey]['status']}");

                if ($customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
                    $this->sendNotification(
                        $appointmentArray,
                        $appointment,
                        $customerNotification,
                        'customer',
                        true,
                        $bookingKey
                    );
                }
            }
        }
    }

    /**
     * @param $appointmentArray
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendAppointmentRescheduleNotifications($appointmentArray)
    {
        /** @var Appointment $appointment */
        $appointment = AppointmentFactory::create($appointmentArray);

        // Notify customers
        if ($appointmentArray['notifyParticipants']) {

            /** @var Notification $customerNotification */
            $customerNotification = $this->getByName('customer_appointment_rescheduled');

            if ($customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
                // Notify each customer from customer bookings
                foreach (array_keys($appointmentArray['bookings']) as $bookingKey) {
                    $this->sendNotification(
                        $appointmentArray,
                        $appointment,
                        $customerNotification,
                        'customer',
                        true,
                        $bookingKey
                    );
                }
            }
        }

        // Notify provider
        /** @var Notification $providerNotification */
        $providerNotification = $this->getByName('provider_appointment_rescheduled');

        if ($providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
            $this->sendNotification($appointmentArray, $appointment, $providerNotification, 'employee', true, null);
        }
    }

    /**
     * @param array $appointmentArray
     * @param array $bookingArray
     * @param bool  $logNotification
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendBookingAddedNotifications($appointmentArray, $bookingArray, $logNotification)
    {
        /** @var Appointment $appointment */
        $appointment = AppointmentFactory::create($appointmentArray);

        /** @var Notification $customerNotification */
        $customerNotification = $this->getByName("customer_appointment_{$appointmentArray['status']}");

        if ($customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
            // Notify customer that scheduled the appointment
            $this->sendNotification(
                $appointmentArray,
                $appointment,
                $customerNotification,
                'customer',
                $logNotification,
                array_search($bookingArray['id'], array_column($appointmentArray['bookings'], 'id'), true)
            );
        }

        // Notify provider
        /** @var Notification $providerNotification */
        $providerNotification = $this->getByName("provider_appointment_{$appointmentArray['status']}");

        if ($providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
            $this->sendNotification(
                $appointmentArray,
                $appointment,
                $providerNotification,
                'employee',
                $logNotification,
                null
            );
        }
    }

    /**
     * Notify the customer when he change his booking status.
     *
     * @param $appointmentArray
     * @param $bookingArray
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendCancelBookingNotification($appointmentArray, $bookingArray)
    {
        /** @var Appointment $appointment */
        $appointment = AppointmentFactory::create($appointmentArray);

        // Notify customers
        if ($appointmentArray['notifyParticipants']) {

            /** @var Notification $customerNotification */
            $customerNotification = $this->getByName("customer_appointment_{$bookingArray['status']}");

            if ($customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED) {
                // Notify customer
                $bookingKey = array_search(
                    $bookingArray['id'],
                    array_column($appointmentArray['bookings'], 'id'),
                    true
                );

                $this->sendNotification(
                    $appointmentArray,
                    $appointment,
                    $customerNotification,
                    'customer',
                    true,
                    $bookingKey
                );
            }
        }
    }

    /**
     * Returns an array of next day reminder notifications that have to be sent to customers with cron
     *
     * @return void
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendAppointmentNextDayReminderNotifications()
    {
        /** @var NotificationLogRepository $notificationLogRepo */
        $notificationLogRepo = $this->container->get('domain.notificationLog.repository');

        /** @var Notification $customerNotification */
        $customerNotification = $this->getByName('customer_appointment_next_day_reminder');

        // Check if notification is enabled and it is time to send notification
        if ($customerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
            DateTimeService::getNowDateTimeObject() >=
            DateTimeService::getCustomDateTimeObject($customerNotification->getTime()->getValue())
        ) {
            $appointments = $notificationLogRepo->getCustomersNextDayAppointments();

            $this->sendBookingsNotifications($customerNotification, $appointments);
        }

        /** @var Notification $providerNotification */
        $providerNotification = $this->getByName('provider_appointment_next_day_reminder');

        // Check if notification is enabled and it is time to send notification
        if ($providerNotification->getStatus()->getValue() === NotificationStatus::ENABLED &&
            DateTimeService::getNowDateTimeObject() >=
            DateTimeService::getCustomDateTimeObject($providerNotification->getTime()->getValue())
        ) {
            $appointments = $notificationLogRepo->getProvidersNextDayAppointments();

            foreach ((array)$appointments->toArray() as $appointmentArray) {
                $this->sendNotification(
                    $appointmentArray,
                    $appointments->getItem($appointmentArray['id']),
                    $providerNotification,
                    'employee',
                    true
                );
            }
        }
    }

    /**
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendAppointmentFollowUpNotifications()
    {
        /** @var Notification $notification */
        $notification = $this->getByName('customer_appointment_follow_up');

        if ($notification->getStatus()->getValue() === NotificationStatus::ENABLED) {
            /** @var NotificationLogRepository $notificationLogRepo */
            $notificationLogRepo = $this->container->get('domain.notificationLog.repository');

            $appointment = $notificationLogRepo->getFollowUpAppointments($notification);

            $this->sendBookingsNotifications($notification, $appointment);
        }
    }

    /**
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendBirthdayGreetingNotifications()
    {
        /** @var Notification $notification */
        $notification = $this->getByName('customer_birthday_greeting');

        // Check if notification is enabled and it is time to send notification
        if ($notification->getStatus()->getValue() === NotificationStatus::ENABLED &&
            DateTimeService::getNowDateTimeObject() >=
            DateTimeService::getCustomDateTimeObject($notification->getTime()->getValue())
        ) {
            /** @var NotificationLogRepository $notificationLogRepo */
            $notificationLogRepo = $this->container->get('domain.notificationLog.repository');

            /** @var PHPMailService|SMTPService|MailgunService $mailService */
            $mailService = $this->container->get('infrastructure.mail.service');
            /** @var NotificationDataService $notificationDataS */
            $notificationDataS = $this->container->get('application.notification.data.service');

            $customers = $notificationLogRepo->getBirthdayCustomers();
            $companyData = $notificationDataS->getCompanyData();
            $customersArray = $customers->toArray();

            foreach ($customersArray as $bookingKey => $customerArray) {
                $data = [
                    'customer_email'      => $customerArray['email'],
                    'customer_first_name' => $customerArray['firstName'],
                    'customer_last_name'  => $customerArray['lastName'],
                    'customer_full_name'  => $customerArray['firstName'] . ' ' . $customerArray['lastName'],
                    'customer_phone'      => $customerArray['phone']
                ];

                /** @noinspection AdditionOperationOnArraysInspection */
                $data += $companyData;

                $subject = $notificationDataS->applyPlaceholders(
                    $notification->getSubject()->getValue(),
                    $data
                );
                $body = $notificationDataS->applyPlaceholders(
                    $notification->getContent()->getValue(),
                    $data
                );

                $mailService->send($data['customer_email'], $subject, $body);
                $notificationLogRepo->add(
                    $notification,
                    $customers->getItem($bookingKey)
                );
            }
        }
    }

    /**
     * @param $appointmentArray
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function sendLimitReachedNotification($appointmentArray)
    {
        /** @var PHPMailService|SMTPService|MailgunService $mailService */
        $mailService = $this->container->get('infrastructure.mail.service');

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('domain.users.repository');

        /** @var Appointment $appointment */
        $appointment = AppointmentFactory::create($appointmentArray);

        $mailService->send(
            $userRepository->getById($appointment->getProviderId()->getValue())->getEmail()->getValue(),
            BackendStrings::getAppointmentStrings()['notify_free_subject'],
            BackendStrings::getAppointmentStrings()['notify_free_body']
        );
    }

    /**
     * Send passed notification for all passed bookings and save log in the database
     *
     * @param Notification $notification
     * @param Collection   $appointments
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendBookingsNotifications($notification, $appointments)
    {
        /** @var array $appointmentArray */
        foreach ((array)$appointments->toArray() as $appointmentArray) {
            // Notify each customer from customer bookings
            foreach (array_keys($appointmentArray['bookings']) as $bookingKey) {
                $appointment = $appointments->getItem($appointmentArray['id']);

                $this->sendNotification(
                    $appointmentArray,
                    $appointment,
                    $notification,
                    'customer',
                    true,
                    $bookingKey
                );
            }
        }
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param array        $appointmentArray
     * @param Appointment  $appointment
     * @param Notification $notification
     * @param string       $userType
     * @param int|null     $bookingKey
     * @param bool         $logNotification
     *
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendNotification(
        $appointmentArray,
        $appointment,
        $notification,
        $userType,
        $logNotification,
        $bookingKey = null
    ) {
        /** @var NotificationLogRepository $notificationLogRepo */
        $notificationLogRepo = $this->container->get('domain.notificationLog.repository');
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('domain.users.repository');
        /** @var CustomerBookingRepository $bookingRepository */
        $bookingRepository = $this->container->get('domain.booking.customerBooking.repository');

        $token = isset($appointmentArray['bookings'][$bookingKey]) ?
            $bookingRepository->getToken($appointmentArray['bookings'][$bookingKey]['id']) : null;

        /** @var PHPMailService|SMTPService|MailgunService $mailService */
        $mailService = $this->container->get('infrastructure.mail.service');
        /** @var NotificationDataService $notificationDataS */
        $notificationDataS = $this->container->get('application.notification.data.service');

        $data = $notificationDataS->getPlaceholdersData(
            $appointmentArray,
            $bookingKey,
            isset($token['token']) ? $token['token'] : null
        );

        $subject = $notificationDataS->applyPlaceholders($notification->getSubject()->getValue(), $data);

        $body = $notificationDataS->applyPlaceholders($notification->getContent()->getValue(), $data);

        $mailService->send($data[$userType . '_email'], $subject, $body);

        $userId = $userType === 'customer' ?
            $appointmentArray['bookings'][$bookingKey]['customerId'] : $appointmentArray['providerId'];

        if ($logNotification) {
            $notificationLogRepo->add(
                $notification,
                $userRepository->getById($userId),
                $appointment
            );
        }
    }
}
