<?php

namespace AmeliaBooking\Application\Commands\Notification;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Notification\NotificationDataService;
use AmeliaBooking\Application\Services\Notification\NotificationService;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Infrastructure\Services\Notification\MailgunService;
use AmeliaBooking\Infrastructure\Services\Notification\PHPMailService;
use AmeliaBooking\Infrastructure\Services\Notification\SMTPService;

/**
 * Class SendTestEmailCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Notification
 */
class SendTestEmailCommandHandler extends CommandHandler
{
    public $mandatoryFields = [
        'emailTemplate',
        'recipientEmail'
    ];

    /**
     * @param SendTestEmailCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function handle(SendTestEmailCommand $command)
    {
        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite(Entities::NOTIFICATIONS)) {
            throw new AccessDeniedException('You are not allowed to send test email');
        }

        $result = new CommandResult();

        $this->checkMandatoryFields($command);

        /** @var PHPMailService|SMTPService|MailgunService $mailService */
        $mailService = $this->getContainer()->get('infrastructure.mail.service');
        /** @var NotificationService $notificationService */
        $notificationService = $this->getContainer()->get('application.notification.service');
        /** @var NotificationDataService $notificationDataS */
        $notificationDataS = $this->getContainer()->get('application.notification.data.service');

        $notification = $notificationService->getByName($command->getField('emailTemplate'));
        $dummyData = $notificationDataS->getPlaceholdersDummyData();

        $subject = $notificationDataS->applyPlaceholders(
            $notification->getSubject()->getValue(),
            $dummyData
        );

        $content = $notificationDataS->applyPlaceholders(
            $notification->getContent()->getValue(),
            $dummyData
        );

        $mailService->send($command->getField('recipientEmail'), $subject, $content);

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Test email successfully sent');

        return $result;
    }
}
