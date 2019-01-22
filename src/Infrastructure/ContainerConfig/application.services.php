<?php
/**
 * Assembling application services:
 * Instantiating application services and injecting the Infrastructure layer implementations
 */

use AmeliaBooking\Application\Services\Bookable\BookableApplicationService;
use AmeliaBooking\Application\Services\Booking\BookingApplicationService;
use AmeliaBooking\Application\Services\Booking\AppointmentApplicationService;
use AmeliaBooking\Application\Services\Coupon\CouponApplicationService;
use AmeliaBooking\Application\Services\TimeSlot\TimeSlotService;
use AmeliaBooking\Application\Services\User\CustomerApplicationService;
use AmeliaBooking\Application\Services\User\ProviderApplicationService;
use AmeliaBooking\Application\Services\User\UserApplicationService;
use AmeliaBooking\Infrastructure\Common\Container;

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Customer service
 *
 * @param Container $c
 *
 * @return UserApplicationService
 */
$entries['application.user.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\User\UserApplicationService($c);
};

/**
 * Provider service
 *
 * @param Container $c
 *
 * @return ProviderApplicationService
 */
$entries['application.user.provider.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\User\ProviderApplicationService($c);
};

/**
 * Customer service
 *
 * @param Container $c
 *
 * @return CustomerApplicationService
 */
$entries['application.user.customer.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\User\CustomerApplicationService($c);
};

/**
 * Location service
 *
 * @return \AmeliaBooking\Application\Services\Location\CurrentLocation
 */
$entries['application.location.service'] = function () {
    return new AmeliaBooking\Application\Services\Location\CurrentLocation();
};

/**
 * Appointment service
 *
 * @param Container $c
 *
 * @return AppointmentApplicationService
 */
$entries['application.booking.appointment.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Booking\AppointmentApplicationService($c);
};

/**
 * Booking service
 *
 * @param Container $c
 *
 * @return BookingApplicationService
 */
$entries['application.booking.booking.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Booking\BookingApplicationService($c);
};

/**
 * Bookable service
 *
 * @param Container $c
 *
 * @return BookableApplicationService
 */
$entries['application.bookable.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Bookable\BookableApplicationService($c);
};

/**
 * Calendar service
 *
 * @param Container $c
 *
 * @return TimeSlotService
 */
$entries['application.timeSlot.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\TimeSlot\TimeSlotService($c);
};

/**
 * Coupon service
 *
 * @param Container $c
 *
 * @return CouponApplicationService
 */
$entries['application.coupon.service'] = !AMELIA_LITE_VERSION ? function ($c) {
    return new AmeliaBooking\Application\Services\Coupon\CouponApplicationService($c);
} : '';

/**
 * Notification Service
 *
 * @param Container $c
 *
 * @return \AmeliaBooking\Application\Services\Notification\NotificationService
 */
$entries['application.notification.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Notification\NotificationService($c);
};

/**
 * Notification Service
 *
 * @param Container $c
 *
 * @return \AmeliaBooking\Application\Services\Notification\NotificationDataService
 */
$entries['application.notification.data.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Notification\NotificationDataService($c);
};

/**
 * Stats Service
 *
 * @param Container $c
 *
 * @return \AmeliaBooking\Application\Services\Stats\StatsService
 */
$entries['application.stats.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Stats\StatsService($c);
};

/**
 * Helper Service
 *
 * @param Container $c
 *
 * @return \AmeliaBooking\Application\Services\Helper\HelperService
 */
$entries['application.helper.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Helper\HelperService($c);
};

/**
 * Settings Service
 *
 * @param Container $c
 *
 * @return \AmeliaBooking\Application\Services\Settings\SettingsService
 */
$entries['application.settings.service'] = function ($c) {
    return new AmeliaBooking\Application\Services\Settings\SettingsService($c);
};
