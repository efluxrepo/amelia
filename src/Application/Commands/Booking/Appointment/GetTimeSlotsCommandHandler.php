<?php

namespace AmeliaBooking\Application\Commands\Booking\Appointment;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\TimeSlot\TimeSlotService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use Exception;
use Interop\Container\Exception\ContainerException;

/**
 * Class GetTimeSlotsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Booking\Appointment
 */
class GetTimeSlotsCommandHandler extends CommandHandler
{
    /**
     * @var array
     */
    public $mandatoryFields = [
        'serviceId'
    ];

    /**
     * @param GetTimeSlotsCommand $command
     *
     * @return CommandResult
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws ContainerException
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function handle(GetTimeSlotsCommand $command)
    {
        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var TimeSlotService $timeSlotService */
        $timeSlotService = $this->container->get('application.timeSlot.service');
        /** @var \AmeliaBooking\Domain\Services\Settings\SettingsService $settingsDS */
        $settingsDS = $this->container->get('domain.settings.service');

        // Get general settings
        $generalSettings = $settingsDS->getCategorySettings('general');

        // Get available time for making the appointment
        $offset = DateTimeService::getNowDateTimeObject()
            ->modify("+{$generalSettings['minimumTimeRequirementPriorToBooking']} seconds");

        $startDateTime = DateTimeService::getCustomDateTimeObject($command->getField('startDateTime'));
        $startDateTime = $offset > $startDateTime ? $offset : $startDateTime;

        if ($command->getField('endDateTime')) {
            $endDateTime = DateTimeService::getCustomDateTimeObject($command->getField('endDateTime'));
        } else {
            $endDateTime = DateTimeService::getNowDateTimeObject()
                ->modify("+{$generalSettings['numberOfDaysAvailableForBooking']} day");
        }

        $freeSlots = $timeSlotService->getFreeSlots(
            $command->getField('serviceId'),
            $command->getField('weekDays'),
            $startDateTime,
            $endDateTime,
            $command->getField('providerIds'),
            $command->getField('extras'),
            $command->getField('excludeAppointmentId'),
            $command->getField('group') ? $command->getField('persons') : null
        );

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully retrieved free slots');
        $result->setData([
            'slots' => $freeSlots
        ]);

        return $result;
    }
}
