<?php
/**
 * Database hook for activation
 */

namespace AmeliaBooking\Infrastructure\WP\InstallActions;

use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\CategoriesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ExtrasTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ServicesViewsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\AppointmentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\CustomerBookingsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\CustomerBookingsToExtrasTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\CustomField\CustomFieldsOptionsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\CustomField\CustomFieldsServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\CustomField\CustomFieldsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Gallery\GalleriesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsToServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Location\LocationsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Location\LocationsViewsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification\NotificationsLogTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification\NotificationsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Notification\NotificationsTableInsertRows;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Payment\PaymentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersGoogleCalendarTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersLocationTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersViewsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\UsersTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersServiceTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersWeekDayTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersTimeOutTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\Provider\ProvidersDayOffTable;

/**
 * Class ActivationHook
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions
 */
class ActivationDatabaseHook
{

    /**
     * Initialize the plugin
     */
    public static function init()
    {
        UsersTable::init();

        GalleriesTable::init();

        CouponsTable::init();

        LocationsTable::init();

        NotificationsTable::init();

        NotificationsTableInsertRows::init();

        ProvidersDayOffTable::init();

        ProvidersLocationTable::init();

        ProvidersWeekDayTable::init();

        ProvidersTimeOutTable::init();

        ProvidersViewsTable::init();

        ProvidersGoogleCalendarTable::init();

        CategoriesTable::init();

        ServicesTable::init();

        ServicesViewsTable::init();

        CouponsToServicesTable::init();

        ProvidersServiceTable::init();

        ExtrasTable::init();

        AppointmentsTable::init();

        CustomerBookingsTable::init();

        CustomerBookingsToExtrasTable::init();

        PaymentsTable::init();

        LocationsViewsTable::init();

        NotificationsLogTable::init();

        CustomFieldsTable::init();

        CustomFieldsOptionsTable::init();

        CustomFieldsServicesTable::init();
    }
}
