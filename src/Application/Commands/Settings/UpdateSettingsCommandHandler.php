<?php

namespace AmeliaBooking\Application\Commands\Settings;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Location\CurrentLocation;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Factory\Schedule\TimeOutFactory;
use AmeliaBooking\Domain\Factory\Schedule\WeekDayFactory;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Repository\User\ProviderRepository;
use AmeliaBooking\Infrastructure\Services\Frontend\LessParserService;
use AmeliaBooking\Infrastructure\WP\Integrations\WooCommerce\WooCommerceService;

/**
 * Class UpdateSettingsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Settings
 */
class UpdateSettingsCommandHandler extends CommandHandler
{
    /**
     * @param UpdateSettingsCommand $command
     *
     * @return CommandResult
     * @throws \Less_Exception_Parser
     * @throws \Slim\Exception\ContainerValueNotFoundException
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Exception
     */
    public function handle(UpdateSettingsCommand $command)
    {
        $result = new CommandResult();

        /** @var SettingsService $settingsService */
        $settingsService = $this->getContainer()->get('domain.settings.service');

        /** @var CurrentLocation $locationService */
        $locationService = $this->getContainer()->get('application.location.service');

        /** @var LessParserService $lessParserService */
        $lessParserService = $this->getContainer()->get('infrastructure.frontend.lessParser.service');

        $additionalParams = $command->getField('additionalParams');

        if ($command->getField('customization')) {
            $customizationData = $command->getField('customization');

            $lessParserService->compileAndSave([
                'color-accent'      => $customizationData['primaryColor'],
                'color-gradient1'   => $customizationData['primaryGradient1'],
                'color-gradient2'   => $customizationData['primaryGradient2'],
                'color-text-prime'  => $customizationData['textColor'],
                'color-text-second' => $customizationData['textColor'],
                'color-white'       => $customizationData['textColorOnBackground'],
                'font'              => $customizationData['font']
            ]);
        }

        if (isset($additionalParams['weekScheduleSettingsApplyGlobally']) &&
            $additionalParams['weekScheduleSettingsApplyGlobally'] === true
        ) {
            $weekDayList = [];

            foreach ((array)$command->getField('weekSchedule') as $index => $weekDay) {
                $timeOutList = [];

                if ($weekDay['breaks']) {
                    foreach ((array)$weekDay['breaks'] as $timeOut) {
                        if ($timeOut['time'][0] && $timeOut['time'][1]) {
                            $timeOutList[] = TimeOutFactory::create([
                                'startTime' => $timeOut['time'][0] . ':00',
                                'endTime'   => $timeOut['time'][1] . ':00'
                            ]);
                        }
                    }
                }

                if (isset($weekDay['time'][0], $weekDay['time'][1])) {
                    $weekDayList[] = WeekDayFactory::create([
                        'dayIndex'    => $index + 1,
                        'startTime'   => $weekDay['time'][0] . ':00',
                        'endTime'     => $weekDay['time'][1] . ':00',
                        'timeOutList' => $timeOutList
                    ]);
                }
            }

            /** @var ProviderRepository $providerRepository */
            $providerRepository = $this->container->get('domain.users.providers.repository');

            /** @var ProviderApplicationService $providerAS */
            $providerAS = $this->container->get('application.user.provider.service');

            $providerRepository->beginTransaction();

            $providers = $providerRepository->getAll();

            foreach ($providers->keys() as $providerKey) {
                $oldUser = $providers->getItem($providerKey);

                $newUser = unserialize(serialize($oldUser));
                $newUser->setWeekDayList(unserialize(serialize(new Collection($weekDayList))));

                if (!$providerAS->updateProviderWorkDays($oldUser, $newUser)) {
                    $providerRepository->rollback();

                    $result->setResult(CommandResult::RESULT_ERROR);
                    $result->setMessage('Could not update settings.');

                    return $result;
                }
            }

            $providerRepository->commit();
        }

        $command->removeField('additionalParams');

        $settingsFields = $command->getFields();

        if (!AMELIA_LITE_VERSION && WooCommerceService::isEnabled() && $command->getField('payments')['wc']['enabled']) {
            $settingsFields['payments']['wc']['productId'] = WooCommerceService::getIdForExistingOrNewProduct(
                $settingsService->getCategorySettings('payments')['wc']['productId']
            );
        }

        $settingsService->setAllSettings($settingsFields);

        $settings = $settingsService->getAllSettingsCategorized();
        $settings['general']['phoneDefaultCountryCode'] = $settings['general']['phoneDefaultCountryCode'] === 'auto' ?
            $locationService->getCurrentLocationCountryIso() : $settings['general']['phoneDefaultCountryCode'];

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated settings.');
        $result->setData([
            'settings' => $settings
        ]);

        return $result;
    }
}
