<?php

namespace AmeliaBooking\Application\Services\User;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Entity\Bookable\Service\Service;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Factory\Location\ProviderLocationFactory;
use AmeliaBooking\Domain\Repository\User\UserRepositoryInterface;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Entity\Schedule\TimeOut;
use AmeliaBooking\Domain\Entity\Schedule\WeekDay;
use AmeliaBooking\Domain\Entity\Schedule\DayOff;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\Id;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Repository\Bookable\Service\ProviderServiceRepository;
use AmeliaBooking\Infrastructure\Repository\Booking\Appointment\AppointmentRepository;
use AmeliaBooking\Infrastructure\Repository\Google\GoogleCalendarRepository;
use AmeliaBooking\Infrastructure\Repository\Location\ProviderLocationRepository;
use AmeliaBooking\Infrastructure\Repository\Schedule\DayOffRepository;
use AmeliaBooking\Infrastructure\Repository\Schedule\TimeOutRepository;
use AmeliaBooking\Infrastructure\Repository\Schedule\WeekDayRepository;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use AmeliaBooking\Infrastructure\Repository\User\UserRepository;

/**
 * Class ProviderApplicationService
 *
 * @package AmeliaBooking\Application\Services\User
 */
class ProviderApplicationService
{
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
     * @param Provider $user
     *
     * @return boolean
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function add($user)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('domain.users.repository');

        /** @var ProviderServiceRepository $providerServiceRepo */
        $providerServiceRepo = $this->container->get('domain.bookable.service.providerService.repository');

        /** @var ProviderLocationRepository $providerLocationRepo */
        $providerLocationRepo = $this->container->get('domain.bookable.service.providerLocation.repository');

        /** @var DayOffRepository $dayOffRepository */
        $dayOffRepository = $this->container->get('domain.schedule.dayOff.repository');

        /** @var WeekDayRepository $weekDayRepository */
        $weekDayRepository = $this->container->get('domain.schedule.weekDay.repository');

        /** @var TimeOutRepository $timeOutRepository */
        $timeOutRepository = $this->container->get('domain.schedule.timeOut.repository');

        // add provider
        $userId = $userRepository->add($user);

        $user->setId(new Id($userId));


        if ($user->getLocationId()) {
            $providerLocation = ProviderLocationFactory::create([
                'userId'     => $userId,
                'locationId' => $user->getLocationId()->getValue()
            ]);

            $providerLocationRepo->add($providerLocation);
        }


        /**
         * Add provider services
         */
        foreach ((array)$user->getServiceList()->keys() as $key) {
            if (!($service = $user->getServiceList()->getItem($key)) instanceof Service) {
                throw new InvalidArgumentException('Unknown type');
            }

            $providerServiceRepo->add($service, $user->getId()->getValue());
        }


        // add provider day off
        foreach ((array)$user->getDayOffList()->keys() as $key) {
            if (!($providerDayOff = $user->getDayOffList()->getItem($key)) instanceof DayOff) {
                throw new InvalidArgumentException('Unknown type');
            }

            $providerDayOffId = $dayOffRepository->add($providerDayOff, $user->getId()->getValue());

            $providerDayOff->setId(new Id($providerDayOffId));
        }


        // add provider week day / time out
        foreach ((array)$user->getWeekDayList()->keys() as $weekDayKey) {
            // add day work hours
            if (!($weekDay = $user->getWeekDayList()->getItem($weekDayKey)) instanceof WeekDay) {
                throw new InvalidArgumentException('Unknown type');
            }

            $weekDayId = $weekDayRepository->add($weekDay, $user->getId()->getValue());

            $weekDay->setId(new Id($weekDayId));


            // add day time out values
            foreach ((array)$weekDay->getTimeOutList()->keys() as $timeOutKey) {
                if (!($timeOut = $weekDay->getTimeOutList()->getItem($timeOutKey)) instanceof TimeOut) {
                    throw new InvalidArgumentException('Unknown type');
                }

                $timeOutId = $timeOutRepository->add($timeOut, $weekDayId);

                $timeOut->setId(new Id($timeOutId));
            }
        }

        return $userId;
    }

    /**
     * @param Provider $oldUser
     * @param Provider $newUser
     *
     * @return boolean
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function update($oldUser, $newUser)
    {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->container->get('domain.users.repository');

        // update provider
        $userRepository->update($oldUser->getId()->getValue(), $newUser);

        $this->updateProviderLocations($oldUser, $newUser);
        $this->updateProviderServices($newUser);
        $this->updateProviderDaysOff($oldUser, $newUser);
        $this->updateProviderWorkDays($oldUser, $newUser);

        if ($newUser->getGoogleCalendar() && $newUser->getGoogleCalendar()->getId()) {
            $this->updateProviderGoogleCalendar($newUser);
        }

        return true;
    }

    /**
     * Update provider week day / time out
     *
     * @param Provider $oldUser
     * @param Provider $newUser
     *
     * @return boolean
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function updateProviderWorkDays($oldUser, $newUser)
    {
        /** @var WeekDayRepository $weekDayRepository */
        $weekDayRepository = $this->container->get('domain.schedule.weekDay.repository');

        /** @var TimeOutRepository $timeOutRepository */
        $timeOutRepository = $this->container->get('domain.schedule.timeOut.repository');

        $existingWeekDayIds = [];

        foreach ((array)$newUser->getWeekDayList()->keys() as $newUserWeekDayKey) {
            // add day work hours
            $newWeekDay = $newUser->getWeekDayList()->getItem($newUserWeekDayKey);

            // update week day if ID exist
            if ($newWeekDay->getId() && $newWeekDay->getId()->getValue()) {
                $weekDayRepository->update($newWeekDay, $newWeekDay->getId()->getValue());
            }

            // add week day off if ID does not exist
            if (!$newWeekDay->getId()) {
                $newWeekDayId = $weekDayRepository->add($newWeekDay, $newUser->getId()->getValue());

                $newWeekDay->setId(new Id($newWeekDayId));
            }

            $existingWeekDayIds[] = $newWeekDay->getId()->getValue();

            $existingTimeOutIds[$newWeekDay->getId()->getValue()] = [];

            // add day time out values
            foreach ((array)$newWeekDay->getTimeOutList()->keys() as $newTimeOutKey) {
                if (!($newTimeOut = $newWeekDay->getTimeOutList()->getItem($newTimeOutKey)) instanceof TimeOut) {
                    throw new InvalidArgumentException('Unknown type');
                }

                // update week day time out if ID exist
                if ($newTimeOut->getId() && $newTimeOut->getId()->getValue()) {
                    $timeOutRepository->update($newTimeOut, $newTimeOut->getId()->getValue());
                }

                // add week day time out if ID does not exist
                if (!$newTimeOut->getId()) {
                    $newTimeOutId = $timeOutRepository->add($newTimeOut, $newWeekDay->getId()->getValue());

                    $newTimeOut->setId(new Id($newTimeOutId));
                }

                $existingTimeOutIds[$newWeekDay->getId()->getValue()][] = $newTimeOut->getId()->getValue();
            }
        }

        // delete week day time out if not exist in new week day time out list
        foreach ((array)$oldUser->getWeekDayList()->keys() as $oldUserKey) {
            if (!($oldWeekDay = $oldUser->getWeekDayList()->getItem($oldUserKey)) instanceof WeekDay) {
                throw new InvalidArgumentException('Unknown type');
            }

            if (!in_array($oldWeekDay->getId()->getValue(), $existingWeekDayIds, true)) {
                $weekDayRepository->delete($oldWeekDay->getId()->getValue());
            }

            foreach ((array)$oldWeekDay->getTimeOutList()->keys() as $oldTimeOutKey) {
                if (!($oldTimeOut = $oldWeekDay->getTimeOutList()->getItem($oldTimeOutKey)) instanceof TimeOut) {
                    throw new InvalidArgumentException('Unknown type');
                }

                if (isset($existingTimeOutIds[$oldWeekDay->getId()->getValue()]) &&
                    !in_array(
                        $oldTimeOut->getId()->getValue(),
                        $existingTimeOutIds[$oldWeekDay->getId()->getValue()],
                        true
                    )) {
                    $timeOutRepository->delete($oldTimeOut->getId()->getValue());
                }
            }
        }

        return true;
    }

    /**
     * @param array $providers
     * @param bool  $companyDayOff
     *
     * @return array
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function manageProvidersActivity($providers, $companyDayOff)
    {

        if ($companyDayOff === false) {
            /** @var ProviderRepository $providerRepository */
            $providerRepository = $this->container->get('domain.users.providers.repository');
            /** @var AppointmentRepository $appointmentRepo */
            $appointmentRepo = $this->container->get('domain.booking.appointment.repository');

            $availableProviders = $providerRepository->getAvailable((int)date('w'));
            $onBreakProviders = $providerRepository->getOnBreak((int)date('w'));
            $onVacationProviders = $providerRepository->getOnVacation();
            $busyProviders = $appointmentRepo->getCurrentAppointments();

            foreach ($providers as &$provider) {
                if (array_key_exists($provider['id'], $availableProviders)) {
                    $provider['activity'] = 'available';
                } else {
                    $provider['activity'] = 'away';
                }

                if (array_key_exists($provider['id'], $onBreakProviders)) {
                    $provider['activity'] = 'break';
                }

                if (array_key_exists($provider['id'], $busyProviders)) {
                    $provider['activity'] = 'busy';
                }

                if (array_key_exists($provider['id'], $onVacationProviders)) {
                    $provider['activity'] = 'dayoff';
                }
            }
        } else {
            foreach ($providers as &$provider) {
                $provider['activity'] = 'dayoff';
            }
        }

        return $providers;
    }

    /**
     * @param $companyDaysOff
     *
     * @return bool
     */
    public function checkIfTodayIsCompanyDayOff($companyDaysOff)
    {
        $currentDate = DateTimeService::getNowDateTimeObject()->setTime(0, 0, 0);

        $dayOff = false;
        foreach ((array)$companyDaysOff as $companyDayOff) {
            if ($currentDate >= DateTimeService::getCustomDateTimeObject($companyDayOff['startDate']) &&
                $currentDate <= DateTimeService::getCustomDateTimeObject($companyDayOff['endDate'])) {
                $dayOff = true;
                break;
            }
        }

        return $dayOff;
    }

    /**
     * @param array $providers
     *
     * @return array
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function removeAllExceptCurrentUser($providers)
    {
        /** @var Provider $currentUser */
        $currentUser = $this->container->get('logged.in.user');

        if ($currentUser !== null &&
            $currentUser->getType() === 'provider' &&
            !$this->container->getPermissionsService()->currentUserCanReadOthers(Entities::APPOINTMENTS)
        ) {
            if ($currentUser->getId() === null) {
                return [];
            }

            $currentUserId = $currentUser->getId()->getValue();
            foreach ($providers as $key => $provider) {
                if ($provider['id'] !== $currentUserId) {
                    unset($providers[$key]);
                }
            }
        }

        return array_values($providers);
    }

    /**
     * Add appointments to provider's appointments list
     *
     * @param Collection $providers
     * @param Collection $appointments
     *
     * @throws InvalidArgumentException
     */
    public function addAppointmentsToAppointmentList($providers, $appointments)
    {
        foreach ($appointments->keys() as $appointmentKey) {
            $appointment = $appointments->getItem($appointmentKey);

            foreach ($providers->keys() as $providerKey) {
                $provider = $providers->getItem($providerKey);

                if ($appointment->getProviderId()->getValue() === $provider->getId()->getValue()) {
                    $provider->getAppointmentList()->addItem($appointment);
                    break;
                }
            }
        }
    }

    /**
     * @param Provider $newUser
     *
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function updateProviderGoogleCalendar($newUser)
    {
        /** @var GoogleCalendarRepository $googleCalendarRepository */
        $googleCalendarRepository = $this->container->get('domain.google.calendar.repository');

        $googleCalendarRepository->update(
            $newUser->getGoogleCalendar(),
            $newUser->getGoogleCalendar()->getId()->getValue()
        );
    }

    /**
     * Update provider locations
     *
     * @param Provider $oldUser
     * @param Provider $newUser
     *
     * @return boolean
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws InvalidArgumentException
     */
    private function updateProviderLocations($oldUser, $newUser)
    {
        /** @var ProviderLocationRepository $providerLocationRepo */
        $providerLocationRepo = $this->container->get('domain.bookable.service.providerLocation.repository');

        if ($oldUser->getLocationId() && $newUser->getLocationId()) {
            $providerLocation = ProviderLocationFactory::create([
                'userId'     => $newUser->getId()->getValue(),
                'locationId' => $newUser->getLocationId()->getValue()
            ]);

            $providerLocationRepo->update($providerLocation);
        } elseif ($newUser->getLocationId()) {
            $providerLocation = ProviderLocationFactory::create([
                'userId'     => $newUser->getId()->getValue(),
                'locationId' => $newUser->getLocationId()->getValue()
            ]);

            $providerLocationRepo->add($providerLocation);
        } elseif ($oldUser->getLocationId()) {
            $providerLocationRepo->delete($oldUser->getId()->getValue());
        }

        return true;
    }

    /**
     * Update provider services
     *
     * @param Provider $newUser
     *
     * @return boolean
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function updateProviderServices($newUser)
    {
        /** @var ProviderServiceRepository $providerServiceRepo */
        $providerServiceRepo = $this->container->get('domain.bookable.service.providerService.repository');

        $servicesIds = [];
        $services = $newUser->getServiceList();

        /** @var Service $service */
        foreach ($services->getItems() as $service) {
            $servicesIds[] = $service->getId()->getValue();
        }

        $providerServiceRepo->deleteAllNotInServicesArrayForProvider($servicesIds, $newUser->getId()->getValue());

        $existingServices = $providerServiceRepo->getAllForProvider($newUser->getId()->getValue());

        $existingServicesIds = [];

        foreach ($existingServices as $existingService) {
            $existingServicesIds[] = $existingService['serviceId'];
        }

        foreach ($services->getItems() as $service) {
            if (!in_array($service->getId()->getValue(), $existingServicesIds, true)) {
                $providerServiceRepo->add($service, $newUser->getId()->getValue());
            } else {
                foreach ($existingServices as $providerService) {
                    if ($providerService['serviceId'] === $service->getId()->getValue()) {
                        $providerServiceRepo->update($service, $providerService['id']);
                        break;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Update provider days off
     *
     * @param Provider $oldUser
     * @param Provider $newUser
     *
     * @return boolean
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function updateProviderDaysOff($oldUser, $newUser)
    {
        /** @var DayOffRepository $dayOffRepository */
        $dayOffRepository = $this->container->get('domain.schedule.dayOff.repository');

        $existingDayOffIds = [];

        foreach ((array)$newUser->getDayOffList()->keys() as $newUserKey) {
            $newDayOff = $newUser->getDayOffList()->getItem($newUserKey);

            // update day off if ID exist
            if ($newDayOff->getId() && $newDayOff->getId()->getValue()) {
                $dayOffRepository->update($newDayOff, $newDayOff->getId()->getValue());
            }

            // add new day off if ID does not exist
            if ($newDayOff->getId() === null || $newDayOff->getId()->getValue() === 0) {
                $newDayOffId = $dayOffRepository->add($newDayOff, $newUser->getId()->getValue());

                $newDayOff->setId(new Id($newDayOffId));
            }

            $existingDayOffIds[] = $newDayOff->getId()->getValue();
        }

        // delete day off if not exist in new day off list
        foreach ((array)$oldUser->getDayOffList()->keys() as $oldUserKey) {
            $oldDayOff = $oldUser->getDayOffList()->getItem($oldUserKey);

            if (!in_array($oldDayOff->getId()->getValue(), $existingDayOffIds, true)) {
                $dayOffRepository->delete($oldDayOff->getId()->getValue());
            }
        }

        return true;
    }
}
