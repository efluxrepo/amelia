<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Application\Services\Notification;

use AmeliaBooking\Application\Services\Helper\HelperService;
use AmeliaBooking\Domain\Entity\Bookable\Service\Category;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Location\Location;
use AmeliaBooking\Domain\Entity\User\Customer;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\CategoryRepository;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ServiceRepository;
use AmeliaBooking\Infrastructure\Repository\Location\LocationRepository;
use AmeliaBooking\Infrastructure\Repository\Location\ProviderLocationRepository;
use AmeliaBooking\Infrastructure\Repository\User\UserRepository;
use DateTime;

/**
 * Class NotificationDataService
 *
 * @package AmeliaBooking\Application\Services\Notification
 */
class NotificationDataService
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
     * @param string $text
     * @param array  $data
     *
     * @return mixed
     */
    public function applyPlaceholders($text, $data)
    {
        $placeholders = array_map(
            function ($placeholder) {
                return "%{$placeholder}%";
            },
            array_keys($data)
        );

        return str_replace($placeholders, array_values($data), $text);
    }

    /**
     * @return array
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getPlaceholdersDummyData()
    {
        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');
        /** @var HelperService $helperService */
        $helperService = $this->container->get('application.helper.service');

        $dateFormat = $settingsService->getSetting('wordpress', 'dateFormat');
        $timeFormat = $settingsService->getSetting('wordpress', 'timeFormat');

        $companySettings = $settingsService->getCategorySettings('company');

        return [
            'appointment_date'       => date_create()->format($dateFormat),
            'appointment_date_time'  => date_create()->format($dateFormat . ' ' . $timeFormat),
            'appointment_start_time' => date_create()->format($timeFormat),
            'appointment_end_time'   => date_create('1 hour')->format($timeFormat),
            'appointment_notes'      => 'Appointment note',
            'appointment_price'      => $helperService->getFormattedPrice(100),
            'company_address'        => $companySettings['address'],
            'company_name'           => $companySettings['name'],
            'company_phone'          => $companySettings['phone'],
            'company_website'        => $companySettings['website'],
            'customer_email'         => 'customer@domain.com',
            'customer_first_name'    => 'John',
            'customer_last_name'     => 'Doe',
            'customer_full_name'     => 'John Doe',
            'customer_phone'         => '193-951-2600',
            'employee_email'         => 'employee@domain.com',
            'employee_first_name'    => 'Richard',
            'employee_last_name'     => 'Roe',
            'employee_full_name'     => 'Richard Roe',
            'employee_phone'         => '150-698-1858',
            'location_address'       => $companySettings['address'],
            'location_name'          => 'Location Name',
            'category_name'          => 'Category Name',
            'service_duration'       => $helperService->secondsToNiceDuration(5400),
            'service_name'           => 'Service Name',
            'service_price'          => $helperService->getFormattedPrice(100)
        ];
    }

    /**
     * @param array  $appointment
     * @param int    $bookingKey
     * @param string $token
     *
     * @return array
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getPlaceholdersData($appointment, $bookingKey = null, $token = null)
    {
        $data = [];
        $data = array_merge($data, $this->getAppointmentData($appointment, $bookingKey, $token));
        $data = array_merge($data, $this->getCompanyData());
        $data = array_merge($data, $this->getCustomersData($appointment, $bookingKey));
        $data = array_merge($data, $this->getEmployeeData($appointment));
        $data = array_merge($data, $this->getServiceData($appointment));
        $data = array_merge($data, $this->getCustomFieldsData($appointment, $bookingKey));

        return $data;
    }

    /**
     * @return array
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getCompanyData()
    {
        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        $companySettings = $settingsService->getCategorySettings('company');

        return [
            'company_address' => $companySettings['address'],
            'company_name'    => $companySettings['name'],
            'company_phone'   => $companySettings['phone'],
            'company_website' => $companySettings['website']
        ];
    }

    /**
     * @param $appointmentArray
     *
     * @return array
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function getServiceData($appointmentArray)
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->container->get('domain.bookable.category.repository');
        /** @var ServiceRepository $serviceRepository */
        $serviceRepository = $this->container->get('domain.bookable.service.repository');

        /** @var HelperService $helperService */
        $helperService = $this->container->get('application.helper.service');

        /** @var Service $service */
        $service = $serviceRepository->getById($appointmentArray['serviceId']);
        /** @var Category $category */
        $category = $categoryRepository->getById($service->getCategoryId()->getValue());

        return [
            'category_name'    => $category->getName()->getValue(),
            'service_duration' => $helperService->secondsToNiceDuration($service->getDuration()->getValue()),
            'service_name'     => $service->getName()->getValue(),
            'service_price'    => $helperService->getFormattedPrice($service->getPrice()->getValue())
        ];
    }

    /**
     * @param      $appointment
     * @param null $bookingKey
     * @param null $token
     *
     * @return array
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function getAppointmentData($appointment, $bookingKey = null, $token = null)
    {
        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');
        /** @var HelperService $helperService */
        $helperService = $this->container->get('application.helper.service');

        $dateFormat = $settingsService->getSetting('wordpress', 'dateFormat');
        $timeFormat = $settingsService->getSetting('wordpress', 'timeFormat');

        $appointmentPrice = 0;
        // If notification is for provider: Appointment price will be sum of all bookings prices
        // If notification is for customer: Appointment price will be price of his booking
        if ($bookingKey === null) {
            foreach ((array)$appointment['bookings'] as $customerBooking) {
                $appointmentPrice += (int)$customerBooking['price'] * $customerBooking['persons'];

                foreach ($customerBooking['extras'] as $extra) {
                    $appointmentPrice += $extra['price'] * $extra['quantity'] * $customerBooking['persons'];
                }

                if (!empty($customerBooking['coupon']['discount'])) {
                    $appointmentPrice =
                        (1 - $customerBooking['coupon']['discount'] / 100) * $appointmentPrice;
                }

                if (!empty($customerBooking['coupon']['deduction'])) {
                    $appointmentPrice -= $customerBooking['coupon']['deduction'];
                }
            }
        } else {
            $appointmentPrice =
                $appointment['bookings'][$bookingKey]['price'] * $appointment['bookings'][$bookingKey]['persons'];

            foreach ($appointment['bookings'][$bookingKey]['extras'] as $extra) {
                $appointmentPrice +=
                    $extra['price'] * $extra['quantity'] * $appointment['bookings'][$bookingKey]['persons'];
            }

            if (!empty($appointment['bookings'][$bookingKey]['coupon']['discount'])) {
                $appointmentPrice =
                    (1 - $appointment['bookings'][$bookingKey]['coupon']['discount'] / 100) * $appointmentPrice;
            }

            if (!empty($appointment['bookings'][$bookingKey]['coupon']['deduction'])) {
                $appointmentPrice -= $appointment['bookings'][$bookingKey]['coupon']['deduction'];
            }
        }

        return [
            'appointment_cancel_url' => $bookingKey !== null ?
                AMELIA_ACTION_URL . '/bookings/cancel/' . $appointment['bookings'][$bookingKey]['id'] .
                ($token ? '&token=' . $token : '') : '',
            'appointment_date'       => DateTime::createFromFormat('Y-m-d H:i:s', $appointment['bookingStart'])
                ->format($dateFormat),
            'appointment_date_time'  => DateTime::createFromFormat('Y-m-d H:i:s', $appointment['bookingStart'])
                ->format($dateFormat . ' ' . $timeFormat),
            'appointment_start_time' => DateTime::createFromFormat('Y-m-d H:i:s', $appointment['bookingStart'])
                ->format($timeFormat),
            'appointment_end_time'   => DateTime::createFromFormat('Y-m-d H:i:s', $appointment['bookingEnd'])
                ->format($timeFormat),
            'appointment_notes'      => $appointment['internalNotes'],
            'appointment_price'      => $helperService->getFormattedPrice($appointmentPrice),
        ];
    }

    /**
     * @param array $appointment
     * @param null  $bookingKey
     *
     * @return array
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function getCustomersData($appointment, $bookingKey = null)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('domain.users.repository');

        if ($bookingKey === null) {
            $customers = [];
            foreach ((array)$appointment['bookings'] as $customerBooking) {
                $customers[] = $userRepository->getById($customerBooking['customerId']);
            }
        } else {
            $customers[] = $userRepository->getById($appointment['bookings'][$bookingKey]['customerId']);
        }

        return [
            'customer_email'      => implode(', ', array_map(function ($customer) {
                /** @var Customer $customer */
                return $customer->getEmail()->getValue();
            }, $customers)),
            'customer_first_name' => implode(', ', array_map(function ($customer) {
                /** @var Customer $customer */
                return $customer->getFirstName()->getValue();
            }, $customers)),
            'customer_last_name'  => implode(', ', array_map(function ($customer) {
                /** @var Customer $customer */
                return $customer->getLastName()->getValue();
            }, $customers)),
            'customer_full_name'  => implode(', ', array_map(function ($customer) {
                /** @var Customer $customer */
                return $customer->getFirstName()->getValue() . ' ' . $customer->getLastName()->getValue();
            }, $customers)),
            'customer_phone'      => implode(', ', array_map(function ($customer) {
                /** @var Customer $customer */
                return $customer->getPhone() ? $customer->getPhone()->getValue() : '';
            }, $customers))
        ];
    }

    /**
     * @param $appointment
     *
     * @return array
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws NotFoundException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function getEmployeeData($appointment)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('domain.users.repository');
        /** @var LocationRepository $locationRepository */
        $locationRepository = $this->container->get('domain.locations.repository');
        /** @var ProviderLocationRepository $providerLocationRepo */
        $providerLocationRepo = $this->container->get('domain.bookable.service.providerLocation.repository');

        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        /** @var Provider $user */
        $user = $userRepository->getById($appointment['providerId']);
        /** @var Location $location */
        if ($locationId = $providerLocationRepo->getLocationIdByUserId($appointment['providerId'])) {
            $location = $locationRepository->getById($locationId);
        }

        return [
            'employee_email'      => $user->getEmail()->getValue(),
            'employee_first_name' => $user->getFirstName()->getValue(),
            'employee_last_name'  => $user->getLastName()->getValue(),
            'employee_full_name'  => $user->getFirstName()->getValue() . ' ' . $user->getLastName()->getValue(),
            'employee_phone'      => $user->getPhone()->getValue(),
            'location_address'    => $locationId === null ?
                $settingsService->getSetting('company', 'address') : $location->getAddress()->getValue(),
            'location_name'       => $locationId === null ?
                $settingsService->getSetting('company', 'address') : $location->getName()->getValue()
        ];
    }

    /**
     * @param array $appointment
     * @param null  $bookingKey
     *
     * @return array
     */
    private function getCustomFieldsData($appointment, $bookingKey = null)
    {
        $customFieldsData = [];

        if ($bookingKey === null) {
            foreach ($appointment['bookings'] as $booking) {
                $bookingCustomFields = json_decode($booking['customFields']);
                foreach ($bookingCustomFields as $bookingCustomFieldKey => $bookingCustomField) {
                    if ($bookingCustomField->value) {
                        if (array_key_exists(
                            'custom_field_' . $bookingCustomFieldKey,
                            $customFieldsData
                        )) {
                            $customFieldsData['custom_field_' . $bookingCustomFieldKey]
                                .= is_array($bookingCustomField->value)
                                ? '; ' . implode('; ', $bookingCustomField->value) : '; ' . $bookingCustomField->value;
                        } else {
                            $customFieldsData['custom_field_' . $bookingCustomFieldKey] =
                                is_array($bookingCustomField->value)
                                    ? implode('; ', $bookingCustomField->value) : $bookingCustomField->value;
                        }
                    }
                }
            }
        } else {
            $bookingCustomFields = json_decode($appointment['bookings'][$bookingKey]['customFields']);
            foreach ($bookingCustomFields as $bookingCustomFieldKey => $bookingCustomField) {
                $customFieldsData['custom_field_' . $bookingCustomFieldKey] = is_array($bookingCustomField->value)
                    ? implode('; ', $bookingCustomField->value) : $bookingCustomField->value;
            }
        }

        return $customFieldsData;
    }
}
