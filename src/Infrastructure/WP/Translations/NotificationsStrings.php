<?php

namespace AmeliaBooking\Infrastructure\WP\Translations;

/**
 * Class NotificationsStrings
 *
 * @package AmeliaBooking\Infrastructure\WP\Translations
 *
 * @SuppressWarnings(ExcessiveMethodLength)
 */
class NotificationsStrings
{
    /**
     * Array of default customer's notifications that are not time based
     *
     * @return array
     */
    public static function getCustomerNonTimeBasedNotifications()
    {
        return [
            [
                'name'       => 'customer_appointment_approved',
                'niceName'   => __('Appointment Approved', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'customer',
                'subject'    => '%service_name% Appointment Approved',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>You have successfully scheduled
                     <strong>%service_name%</strong> appointment with <strong>%employee_full_name%</strong>. We are 
                     waiting you at <strong>%location_address% </strong>on <strong>%appointment_date_time%</strong>.
                     <br><br>Thank you for choosing our company,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'customer_appointment_pending',
                'niceName'   => __('Appointment Pending', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'customer',
                'subject'    => '%service_name% Appointment Pending',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>The <strong>%service_name%</strong> appointment 
                     with <strong>%employee_full_name%</strong> at <strong>%location_address%</strong>, scheduled for
                     <strong>%appointment_date_time%</strong> is waiting for a confirmation.<br><br>Thank you for 
                     choosing our company,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'customer_appointment_rejected',
                'niceName'   => __('Appointment Rejected', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'customer',
                'subject'    => '%service_name% Appointment Rejected',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>Your <strong>%service_name%</strong> 
                     appointment, scheduled on <strong>%appointment_date_time%</strong> at <strong>%location_address%
                     </strong>has been rejected.<br><br>Thank you for choosing our company,
                     <br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'customer_appointment_canceled',
                'niceName'   => __('Appointment Canceled', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'customer',
                'subject'    => '%service_name% Appointment Canceled',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>Your <strong>%service_name%</strong> 
                     appointment, scheduled on <strong>%appointment_date_time%</strong> at <strong>%location_address%
                     </strong>has been canceled.<br><br>Thank you for choosing our company,
                     <br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'customer_appointment_rescheduled',
                'niceName'   => __('Appointment Rescheduled', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'customer',
                'subject'    => '%service_name% Appointment Rescheduled',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>The details for your 
                     <strong>%service_name%</strong> appointment with <strong>%employee_full_name%</strong> at 
                     <strong>%location_name%</strong> has been changed. The appointment is now set for 
                     <strong>%appointment_date%</strong> at <strong>%appointment_start_time%</strong>.<br><br>
                     Thank you for choosing our company,<br><strong>%company_name%</strong>'
            ]
        ];
    }

    /**
     * Array of default customer's notifications that are time based (require cron job)
     *
     * @return array
     */
    public static function getCustomerTimeBasedNotifications()
    {
        return [
            [
                'name'       => 'customer_appointment_next_day_reminder',
                'niceName'   => __('Appointment Next Day Reminder', 'wpamelia'),
                'type'       => 'email',
                'time'       => '"17:00:00"',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'customer',
                'subject'    => '%service_name% Appointment Reminder',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>We would like to remind you that you have 
                     <strong>%service_name%</strong> appointment tomorrow at <strong>%appointment_start_time%</strong>.
                     We are waiting you at <strong>%location_name%</strong>.<br><br>Thank you for 
                     choosing our company,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'customer_appointment_follow_up',
                'niceName'   => __('Appointment Follow Up', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 1800,
                'sendTo'     => 'customer',
                'subject'    => '%service_name% Appointment Follow Up',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>Thank you once again for choosing our company. 
                     We hope you were satisfied with your <strong>%service_name%</strong>.<br><br>We look forward to 
                     seeing you again soon,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'customer_birthday_greeting',
                'niceName'   => __('Birthday Greeting', 'wpamelia'),
                'type'       => 'email',
                'time'       => '"17:00:00"',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'customer',
                'subject'    => 'Happy Birthday',
                'content'    =>
                    'Dear <strong>%customer_full_name%</strong>,<br><br>Happy birthday!<br>We wish you all the best.
                    <br><br>Thank you for choosing our company,<br><strong>%company_name%</strong>'
            ]
        ];
    }


    /**
     * Array of default employee's notifications that are not time based
     *
     * @return array
     */
    public static function getProviderNonTimeBasedNotifications()
    {
        return [
            [
                'name'       => 'provider_appointment_approved',
                'niceName'   => __('Appointment Approved', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'provider',
                'subject'    => '%service_name% Appointment Approved',
                'content'    =>
                    'Hi <strong>%employee_full_name%</strong>,<br><br>You have one confirmed 
                     <strong>%service_name%</strong> appointment at <strong>%location_name%</strong> on 
                     <strong>%appointment_date%</strong> at <strong>%appointment_start_time%</strong>. The appointment 
                     is added to your schedule.<br><br>Thank you,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'provider_appointment_pending',
                'niceName'   => __('Appointment Pending', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'provider',
                'subject'    => '%service_name% Appointment Pending',
                'content'    =>
                    'Hi <strong>%employee_full_name%</strong>,<br><br>You have new appointment 
                     in <strong>%service_name%</strong>. The appointment is waiting for a confirmation.<br><br>Thank 
                     you,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'provider_appointment_rejected',
                'niceName'   => __('Appointment Rejected', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'provider',
                'subject'    => '%service_name% Appointment Rejected',
                'content'    =>
                    'Hi <strong>%employee_full_name%</strong>,<br><br>Your <strong>%service_name%</strong> appointment 
                     at <strong>%location_name%</strong>, scheduled for <strong>%appointment_date%</strong> at  
                     <strong>%appointment_start_time%</strong> has been rejected.
                     <br><br>Thank you,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'provider_appointment_canceled',
                'niceName'   => __('Appointment Canceled', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'provider',
                'subject'    => '%service_name% Appointment Canceled',
                'content'    =>
                    'Hi <strong>%employee_full_name%</strong>,<br><br>Your <strong>%service_name%</strong> appointment,
                     scheduled on <strong>%appointment_date%</strong>, at <strong>%location_name%</strong> has been 
                     canceled.<br><br>Thank you,<br><strong>%company_name%</strong>'
            ],
            [
                'name'       => 'provider_appointment_rescheduled',
                'niceName'   => __('Appointment Rescheduled', 'wpamelia'),
                'type'       => 'email',
                'time'       => 'NULL',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'provider',
                'subject'    => '%service_name% Appointment Rescheduled',
                'content'    =>
                    'Hi <strong>%employee_full_name%</strong>,<br><br>The details for your 
                     <strong>%service_name%</strong> appointment at <strong>%location_name%</strong> has been changed. 
                     The appointment is now set for <strong>%appointment_date%</strong> at 
                     <strong>%appointment_start_time%</strong>.<br><br>Thank you,<br><strong>%company_name%</strong>'
            ]
        ];
    }

    /**
     * Array of default providers's notifications that are time based (require cron job)
     *
     * @return array
     */
    public static function getProviderTimeBasedNotifications()
    {
        return [
            [
                'name'       => 'provider_appointment_next_day_reminder',
                'niceName'   => __('Appointment Next Day Reminder', 'wpamelia'),
                'type'       => 'email',
                'time'       => '"17:00:00"',
                'timeBefore' => 'NULL',
                'timeAfter'  => 'NULL',
                'sendTo'     => 'provider',
                'subject'    => '%service_name% Appointment Reminder',
                'content'    =>
                    'Dear <strong>%employee_full_name%</strong>,<br><br>We would like to remind you that you have 
                     <strong>%service_name%</strong> appointment tomorrow at <strong>%appointment_start_time%</strong>
                     at <strong>%location_name%</strong>.<br><br>Thank you, 
                     <br><strong>%company_name%</strong>'
            ]
        ];
    }
}
