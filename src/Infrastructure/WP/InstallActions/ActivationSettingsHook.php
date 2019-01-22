<?php
/**
 * Settings hook for activation
 */

namespace AmeliaBooking\Infrastructure\WP\InstallActions;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Services\Frontend\LessParserService;
use AmeliaBooking\Infrastructure\WP\SettingsService\SettingsStorage;

/**
 * Class ActivationSettingsHook
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions
 */
class ActivationSettingsHook
{
    /**
     * Initialize the plugin
     *
     * @throws \Exception
     */
    public static function init()
    {
        self::initGeneralSettings();

        self::initCompanySettings();

        self::initNotificationsSettings();

        self::initDaysOffSettings();

        self::initWeekScheduleSettings();

        self::initGoogleCalendarSettings();

        self::initPaymentsSettings();

        self::initActivationSettings();

        self::initCustomizationSettings();

        self::initLabelsSettings();
    }

    /**
     * @param string $category
     * @param array  $settings
     */
    public static function initSettings($category, $settings)
    {
        $settingsService = new SettingsService(new SettingsStorage());

        if (!$settingsService->getCategorySettings($category)) {
            $settingsService->setCategorySettings(
                $category,
                []
            );
        }

        foreach ($settings as $key => $value) {
            if (null === $settingsService->getSetting($category, $key)) {
                $settingsService->setSetting(
                    $category,
                    $key,
                    $value
                );
            }
        }
    }

    /**
     * Init General Settings
     */
    private static function initGeneralSettings()
    {
        $settings = [
            'timeSlotLength'                         => 1800,
            'serviceDurationAsSlot'                  => false,
            'defaultAppointmentStatus'               => 'approved',
            'minimumTimeRequirementPriorToBooking'   => 0,
            'minimumTimeRequirementPriorToCanceling' => 0,
            'numberOfDaysAvailableForBooking'        => 365,
            'phoneDefaultCountryCode'                => 'auto',
            'requiredPhoneNumberField'               => false,
            'itemsPerPage'                           => 12,
            'gMapApiKey'                             => '',
            'addToCalendar'                          => !AMELIA_LITE_VERSION,
            'automaticallyCreateCustomer'            => false,
            'allowConfigureSchedule'                 => false,
            'allowWriteAppointments'                 => false,
            'defaultPageOnBackend'                   => 'Dashboard',
            'inspectCustomerInfo'                    => true
        ];

        self::initSettings('general', $settings);
    }

    /**
     * Init Company Settings
     */
    private static function initCompanySettings()
    {

        $settings = [
            'pictureFullPath'  => '',
            'pictureThumbPath' => '',
            'name'             => '',
            'address'          => '',
            'phone'            => '',
            'website'          => ''
        ];

        self::initSettings('company', $settings);
    }

    /**
     * Init Notification Settings
     */
    private static function initNotificationsSettings()
    {
        $settings = [
            'mailService'     => 'php',
            'smtpHost'        => '',
            'smtpPort'        => '',
            'smtpUsername'    => '',
            'smtpPassword'    => '',
            'mailgunApiKey'   => '',
            'mailgunDomain'   => '',
            'senderName'      => '',
            'senderEmail'     => '',
            'notifyCustomers' => true,
        ];

        self::initSettings('notifications', $settings);
    }

    /**
     * Init Days Off Settings
     */
    private static function initDaysOffSettings()
    {
        self::initSettings('daysOff', []);
    }

    /**
     * Init Work Schedule Settings
     */
    private static function initWeekScheduleSettings()
    {
        self::initSettings('weekSchedule', [
            [
                'day'    => 'Monday',
                'time'   => ['09:00', '17:00'],
                'breaks' => []
            ],
            [
                'day'    => 'Tuesday',
                'time'   => ['09:00', '17:00'],
                'breaks' => []
            ],
            [
                'day'    => 'Wednesday',
                'time'   => ['09:00', '17:00'],
                'breaks' => []
            ],
            [
                'day'    => 'Thursday',
                'time'   => ['09:00', '17:00'],
                'breaks' => []
            ],
            [
                'day'    => 'Friday',
                'time'   => ['09:00', '17:00'],
                'breaks' => []
            ],
            [
                'day'    => 'Saturday',
                'time'   => [],
                'breaks' => []
            ],
            [
                'day'    => 'Sunday',
                'time'   => [],
                'breaks' => []
            ]
        ]);
    }

    /**
     * Init Google Calendar Settings
     */
    private static function initGoogleCalendarSettings()
    {
        $settings = [
            'clientID'                      => '',
            'clientSecret'                  => '',
            'redirectURI'                   => AMELIA_SITE_URL . '/wp-admin/admin.php?page=wpamelia-employees',
            'insertPendingAppointments'     => false,
            'addAttendees'                  => false,
            'sendEventInvitationEmail'      => false,
            'removeGoogleCalendarBusySlots' => false,
            'maximumNumberOfEventsReturned' => 50
        ];

        self::initSettings('googleCalendar', $settings);
    }

    /**
     * Init Payments Settings
     */
    private static function initPaymentsSettings()
    {
        $settings = [
            'currency'              => 'USD',
            'symbol'                => '$',
            'priceSymbolPosition'   => 'before',
            'priceNumberOfDecimals' => 2,
            'priceSeparator'        => 1,
            'defaultPaymentMethod'  => 'onSite',
            'onSite'                => true,
            'coupons'               => AMELIA_LITE_VERSION ? false : true,
            'payPal'                => [
                'enabled'         => false,
                'sandboxMode'     => false,
                'liveApiClientId' => '',
                'liveApiSecret'   => '',
                'testApiClientId' => '',
                'testApiSecret'   => ''
            ],
            'stripe'                => [
                'enabled'            => false,
                'testMode'           => false,
                'livePublishableKey' => '',
                'liveSecretKey'      => '',
                'testPublishableKey' => '',
                'testSecretKey'      => ''
            ],
            'wc'                    => [
                'enabled'   => false,
                'productId' => ''
            ]
        ];

        self::initSettings('payments', $settings);
    }

    /**
     * Init Purchase Code Settings
     */
    private static function initActivationSettings()
    {
        $settings = [
            'purchaseCode' => '',
            'version'      => ''
        ];

        self::initSettings('activation', $settings);
    }

    /**
     * Init Customization Settings
     *
     * @throws \Exception
     */
    private static function initCustomizationSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $settings = $settingsService->getCategorySettings('customization');

        if (!$settings) {
            $settings = [
                'primaryColor'          => '#1A84EE',
                'primaryGradient1'      => '#1A84EE',
                'primaryGradient2'      => '#0454A2',
                'textColor'             => '#354052',
                'textColorOnBackground' => '#FFFFFF',
                'font'                  => 'Roboto'
            ];

            self::initSettings('customization', $settings);
        }

        /** @var LessParserService $lessParserService */
        $lessParserService = new LessParserService(
            AMELIA_PATH . '/assets/less/frontend/amelia-booking.less',
            'amelia-booking.css',
            UPLOADS_PATH . '/amelia/css'
        );

        $lessParserService->compileAndSave([
            'color-accent'      => $settings['primaryColor'],
            'color-gradient1'   => $settings['primaryGradient1'],
            'color-gradient2'   => $settings['primaryGradient2'],
            'color-text-prime'  => $settings['textColor'],
            'color-text-second' => $settings['textColor'],
            'color-white'       => $settings['textColorOnBackground'],
            'roboto'            => $settings['font']
        ]);
    }

    /**
     * Init Labels Settings
     */
    private static function initLabelsSettings()
    {
        $settings = [
            'employee'  => 'employee',
            'employees' => 'employees',
            'service'   => 'service',
            'services'  => 'services'
        ];

        self::initSettings('labels', $settings);
    }
}
