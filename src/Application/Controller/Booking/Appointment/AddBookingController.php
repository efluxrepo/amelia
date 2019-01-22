<?php

namespace AmeliaBooking\Application\Controller\Booking\Appointment;

use AmeliaBooking\Application\Commands\Booking\Appointment\AddBookingCommand;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Controller\Controller;
use AmeliaBooking\Domain\Events\DomainEventBus;
use Slim\Http\Request;

/**
 * Class AddBookingController
 *
 * @package AmeliaBooking\Application\Controller\Booking\Appointment
 */
class AddBookingController extends Controller
{
    /**
     * Fields for booking that can be received from front-end
     *
     * @var array
     */
    public $allowedFields = [
        'bookings',
        'bookingStart',
        'notifyParticipants',
        'serviceId',
        'providerId',
        'couponCode',
        'payment'
    ];

    /**
     * Instantiates the Add Booking command to hand it over to the Command Handler
     *
     * @param Request $request
     * @param         $args
     *
     * @return AddBookingCommand
     * @throws \RuntimeException
     */
    protected function instantiateCommand(Request $request, $args)
    {
        $command = new AddBookingCommand($args);
        $requestBody = $request->getParsedBody();
        $this->setCommandFields($command, $requestBody);

        return $command;
    }

    /**
     * @param DomainEventBus $eventBus
     * @param CommandResult  $result
     *
     * @return void
     */
    protected function emitSuccessEvent(DomainEventBus $eventBus, CommandResult $result)
    {
        $eventBus->emit('BookingAdded', $result);
    }
}
