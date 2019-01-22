<?php

namespace AmeliaBooking\Infrastructure\WP\Translations;

/**
 * Class BackendStrings
 *
 * @package AmeliaBooking\Infrastructure\WP\Translations
 *
 * @SuppressWarnings(ExcessiveMethodLength)
 */
class BackendStrings
{
    /**
     * Returns the array for the common strings
     *
     * @return array
     * @phpcs:disable
     */
    public static function getCommonStrings()
    {
        return [
            'all_locations'           => __('All Locations', 'wpamelia'),
            'all_services'            => __('All Services', 'wpamelia'),
            'approved'                => __('Approved', 'wpamelia'),
            'cancel'                  => __('Cancel', 'wpamelia'),
            'canceled'                => __('Canceled', 'wpamelia'),
            'close'                   => __('Close', 'wpamelia'),
            'csv_delimiter'           => __('Select Delimiter', 'wpamelia'),
            'csv_delimiter_comma'     => __('Comma (,)', 'wpamelia'),
            'csv_delimiter_semicolon' => __('Semicolon (;)', 'wpamelia'),
            'customer'                => __('Customer', 'wpamelia'),
            'date'                    => __('Date', 'wpamelia'),
            'description'             => __('Description', 'wpamelia'),
            'details'                 => __('Details', 'wpamelia'),
            'duration'                => __('Duration', 'wpamelia'),
            'edit'                    => __('Edit', 'wpamelia'),
            'email_placeholder'       => __('example@mail.com', 'wpamelia'),
            'employee'                => __('Employee', 'wpamelia'),
            'employees'               => __('Employees', 'wpamelia'),
            'error'                   => __('Error', 'wpamelia'),
            'export'                  => __('Export', 'wpamelia'),
            'extras'                  => __('Extras', 'wpamelia'),
            'google_calendar'         => __('Google Calendar', 'wpamelia'),
            'h'                       => __('h', 'wpamelia'),
            'location'                => __('Location', 'wpamelia'),
            'locations'               => __('Locations', 'wpamelia'),
            'min'                     => __('min', 'wpamelia'),
            'name'                    => __('Name', 'wpamelia'),
            'name_ascending'          => __('Name Ascending', 'wpamelia'),
            'name_descending'         => __('Name Descending', 'wpamelia'),
            'need_help'               => __('Need Help', 'wpamelia'),
            'no'                      => __('No', 'wpamelia'),
            'no_results'              => __('There are no results...', 'wpamelia'),
            'note'                    => __('Note', 'wpamelia'),
            'note_internal'           => __('Note (Internal)', 'wpamelia'),
            'of'                      => __('of', 'wpamelia'),
            'ok'                      => __('OK', 'wpamelia'),
            'on_site'                 => __('On-site', 'wpamelia'),
            'paid'                    => __('Paid', 'wpamelia'),
            'payment'                 => __('Payment', 'wpamelia'),
            'pending'                 => __('Pending', 'wpamelia'),
            'period'                  => __('Period', 'wpamelia'),
            'phone'                   => __('Phone', 'wpamelia'),
            'php_version_message'     => __('<p>The <strong>Amelia</strong> plugin requires PHP version 5.5 or greater.</p>', 'wpamelia'),
            'php_version_title'       => __('Plugin Activation Error', 'wpamelia'),
            'pick_a_date'             => __('Pick a date range', 'wpamelia'),
            'rejected'                => __('Rejected', 'wpamelia'),
            'save'                    => __('Save', 'wpamelia'),
            'select_time'             => __('Select Time', 'wpamelia'),
            'service'                 => __('Service', 'wpamelia'),
            'services'                => __('Services', 'wpamelia'),
            'settings_saved'          => __('Settings has been saved', 'wpamelia'),
            'showing'                 => __('Showing', 'wpamelia'),
            'sort'                    => __('Sort', 'wpamelia'),
            'status'                  => __('Status', 'wpamelia'),
            'success'                 => __('Success', 'wpamelia'),
            'to'                      => __('to', 'wpamelia'),
            'today'                   => __('Today', 'wpamelia'),
            'tomorrow'                => __('Tomorrow', 'wpamelia'),
            'total'                   => __('Total', 'wpamelia'),
            'view'                    => __('View', 'wpamelia'),
            'weekday_friday'          => __('Friday', 'wpamelia'),
            'weekday_monday'          => __('Monday', 'wpamelia'),
            'weekday_saturday'        => __('Saturday', 'wpamelia'),
            'weekday_sunday'          => __('Sunday', 'wpamelia'),
            'weekday_thursday'        => __('Thursday', 'wpamelia'),
            'weekday_tuesday'         => __('Tuesday', 'wpamelia'),
            'weekday_wednesday'       => __('Wednesday', 'wpamelia'),
            'yes'                     => __('Yes', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the settings strings
     *
     * @return array
     */
    public static function getSettingsStrings()
    {
        return [
            'add_new_role'                          => __('Add New Role', 'wpamelia'),
            'add_to_calendar'                       => __('Show Add To Calendar option to customers', 'wpamelia'),
            'add_to_calendar_tooltip'               => __('Suggest customers to add an appointment to their calendar<br/>when booking is finalized.', 'wpamelia'),
            'address'                               => __('Address', 'wpamelia'),
            'after'                                 => __('After', 'wpamelia'),
            'after_with_space'                      => __('After with space', 'wpamelia'),
            'allow_configure_schedule'              => __('Allow employees configuring their own schedule', 'wpamelia'),
            'allow_write_appointments'              => __('Allow employees managing their appointments', 'wpamelia'),
            'amelia_role'                           => __('Amelia Role', 'wpamelia'),
            'appointments'                          => __('Appointments', 'wpamelia'),
            'automatically_create_customer'         => __('Automatically create Amelia Customer user', 'wpamelia'),
            'automatically_create_customer_tooltip' => __('If you enable this option every time a new customer schedules the appointment<br/>he will get Amelia Customer user role and automatic email with login details', 'wpamelia'),
            'before'                                => __('Before', 'wpamelia'),
            'before_with_space'                     => __('Before with space', 'wpamelia'),
            'calendar'                              => __('Calendar', 'wpamelia'),
            'cancel_error_url'                      => __('Unsuccessful Cancellation Redirect URL', 'wpamelia'),
            'cancel_error_url_tooltip'              => __('URL on which will user be redirected if appointment can\'t be canceled<br/>because of \'Minimum time required before canceling\' value', 'wpamelia'),
            'cancel_success_url'                    => __('Successful Cancellation Redirect URL', 'wpamelia'),
            'cancel_url_placeholder'                => __('Please enter URL', 'wpamelia'),
            'codecanyon_purchase_code'              => __('CodeCanyon Purchase Code', 'wpamelia'),
            'comma_dot'                             => __('Comma-Dot', 'wpamelia'),
            'company'                               => __('Company', 'wpamelia'),
            'company_settings'                      => __('Company Settings', 'wpamelia'),
            'company_settings_description'          => __('Use these settings to set up picture, name, address, phone and website of your company', 'wpamelia'),
            'coupons'                               => __('Coupons', 'wpamelia'),
            'currency'                              => __('Currency', 'wpamelia'),
            'custom_fields'                         => __('Custom Fields', 'wpamelia'),
            'custom_fields_description'             => __('Add/edit custom fields', 'wpamelia'),
            'custom_fields_settings'                => __('Custom fields settings', 'wpamelia'),
            'customers_as_attendees'                => __('Add Event\'s Attendees', 'wpamelia'),
            'customers_as_attendees_tooltip'        => __('Enable this option if you want your employees to see<br/>in the event customers that attend the appointment.', 'wpamelia'),
            'dashboard'                             => __('Dashboard', 'wpamelia'),
            'day1'                                  => __('1 day', 'wpamelia'),
            'days2'                                 => __('2 days', 'wpamelia'),
            'days3'                                 => __('3 days', 'wpamelia'),
            'days4'                                 => __('4 days', 'wpamelia'),
            'days5'                                 => __('5 days', 'wpamelia'),
            'days6'                                 => __('6 days', 'wpamelia'),
            'days_off_settings'                     => __('Days Off Settings', 'wpamelia'),
            'days_off_settings_description'         => __('Use these settings to set company working hours and days off which will be applied for every employee', 'wpamelia'),
            'default_appointment_status'            => __('Default Appointment Status', 'wpamelia'),
            'default_appointment_status_tooltip'    => __('All appointments will be scheduled with the<br/>status you choose here.', 'wpamelia'),
            'default_items_per_page'                => __('Default items per page', 'wpamelia'),
            'default_page_on_backend'               => __('Default page on back-end', 'wpamelia'),
            'default_payment_method'                => __('Default Payment Method', 'wpamelia'),
            'default_phone_country_code'            => __('Default phone country code', 'wpamelia'),
            'default_time_slot_step'                => __('Default Time Slot Step', 'wpamelia'),
            'default_time_slot_step_tooltip'        => __('The Time Slot Step you define here will be applied<br/>for all time slots in the plugin.', 'wpamelia'),
            'disabled'                              => __('Disabled', 'wpamelia'),
            'dot_comma'                             => __('Dot-Comma', 'wpamelia'),
            'enabled'                               => __('Enabled', 'wpamelia'),
            'general'                               => __('General', 'wpamelia'),
            'general_settings'                      => __('General Settings', 'wpamelia'),
            'general_settings_description'          => __('Use these settings to define plugin general settings and default settings for your services and appointments', 'wpamelia'),
            'gMap_api_key'                          => __('Google Map API Key', 'wpamelia'),
            'gMap_api_key_tooltip'                  => __('Add Google Map API Key to show Google static map on<br/>"Locations" page.', 'wpamelia'),
            'google_calendar_settings'              => __('Google Calendar Settings', 'wpamelia'),
            'google_calendar_settings_description'  => __("Allow synchronizing employee's calendar with Google Calendar for smoother personal scheduling", 'wpamelia'),
            'google_client_id'                      => __('Client ID', 'wpamelia'),
            'google_client_secret'                  => __('Client Secret', 'wpamelia'),
            'google_credentials_obtain'             => __('Click here to see how to obtain<br/>Google Client ID and Secret', 'wpamelia'),
            'google_redirect_uri'                   => __('Redirect URI', 'wpamelia'),
            'google_redirect_uri_tooltip'           => __('This is the path in your application that users are redirected to after<br/>they have authenticated with Google. Add this URI in your Google<br/>project credentials under "Authorized redirect URIs".', 'wpamelia'),
            'h1'                                    => __('1h', 'wpamelia'),
            'h10'                                   => __('10h', 'wpamelia'),
            'h11'                                   => __('11h', 'wpamelia'),
            'h12'                                   => __('12h', 'wpamelia'),
            'h1min30'                               => __('1h 30min', 'wpamelia'),
            'h2'                                    => __('2h', 'wpamelia'),
            'h3'                                    => __('3h', 'wpamelia'),
            'h4'                                    => __('4h', 'wpamelia'),
            'h6'                                    => __('6h', 'wpamelia'),
            'h8'                                    => __('8h', 'wpamelia'),
            'h9'                                    => __('9h', 'wpamelia'),
            'insert_pending_appointments'           => __('Insert Pending Appointments', 'wpamelia'),
            'insert_pending_appointments_tooltip'   => __('Enable this option if you want your employees to see<br/>appointments with pending status in their calendar.', 'wpamelia'),
            'inspect_customer_info'                 => __('Check customer\'s name for existing email when booking', 'wpamelia'),
            'inspect_customer_info_tooltip'         => __('Enable this option if you don\'t want to allow "existing customer"<br/>to use different first and last name when booking.', 'wpamelia'),
            'instructions'                          => __('Instructions', 'wpamelia'),
            'label_employee'                        => __('Employee', 'wpamelia'),
            'label_employees'                       => __('Employees', 'wpamelia'),
            'label_service'                         => __('Service', 'wpamelia'),
            'label_services'                        => __('Services', 'wpamelia'),
            'labels'                                => __('Labels', 'wpamelia'),
            'labels_settings'                       => __('Labels Settings', 'wpamelia'),
            'labels_settings_description'           => __('Use these settings to change labels on frontend pages', 'wpamelia'),
            'limit_number_of_fetched_events'        => __('Limit Number of Fetched Events', 'wpamelia'),
            'live_client_id'                        => __('Live Client ID', 'wpamelia'),
            'live_publishable_key'                  => __('Live Publishable Key', 'wpamelia'),
            'live_secret'                           => __('Live Secret', 'wpamelia'),
            'live_secret_key'                       => __('Live Secret Key', 'wpamelia'),
            'mail_service'                          => __('Mail Service', 'wpamelia'),
            'mailgun'                               => __('Mailgun', 'wpamelia'),
            'mailgun_api_key'                       => __('Mailgun API Key', 'wpamelia'),
            'mailgun_api_key_warning'               => __('Please enter Mailgun API key', 'wpamelia'),
            'mailgun_domain'                        => __('Mailgun Domain', 'wpamelia'),
            'mailgun_domain_warning'                => __('Please enter Mailgun Domain', 'wpamelia'),
            'min10'                                 => __('10min', 'wpamelia'),
            'min12'                                 => __('12min', 'wpamelia'),
            'min15'                                 => __('15min', 'wpamelia'),
            'min20'                                 => __('20min', 'wpamelia'),
            'min30'                                 => __('30min', 'wpamelia'),
            'min45'                                 => __('45min', 'wpamelia'),
            'min5'                                  => __('5min', 'wpamelia'),
            'minimum_time_before_booking'           => __('Minimum time required before booking', 'wpamelia'),
            'minimum_time_before_booking_tooltip'   => __('Set the time before the appointment when customers<br/>will not be able to book the appointment.', 'wpamelia'),
            'minimum_time_before_canceling'         => __('Minimum time required before canceling', 'wpamelia'),
            'minimum_time_before_canceling_tooltip' => __('Set the time before the appointment when customers<br/>will not be able to cancel the appointment.', 'wpamelia'),
            'notifications'                         => __('Notification', 'wpamelia'),
            'notifications_settings'                => __('Notification Settings', 'wpamelia'),
            'notifications_settings_description'    => __('Use these settings to set your mail settings which will be used to notify your customers and employees', 'wpamelia'),
            'notify_customers_default'              => __('Notify the customer(s) by default', 'wpamelia'),
            'number_of_events_returned'             => __('Maximum Number Of Events Returned', 'wpamelia'),
            'number_of_events_returned_tooltip'     => __('Maximum number of events returned on one result page.<br/>It is recommended to use smaller number of returned<br/>events if your server performance is not so good.', 'wpamelia'),
            'payments'                              => __('Payments', 'wpamelia'),
            'payments_settings'                     => __('Payments Settings', 'wpamelia'),
            'payments_settings_description'         => __('Use these settings to set price format, payment method and coupons that will be used in all bookings', 'wpamelia'),
            'payPal'                                => __('PayPal', 'wpamelia'),
            'payPal_live_client_id_error'           => __('Please enter live ClientId', 'wpamelia'),
            'payPal_live_secret_error'              => __('Please enter live Secret', 'wpamelia'),
            'payPal_service'                        => __('PayPal Service', 'wpamelia'),
            'payPal_test_client_id_error'           => __('Please enter test ClientId', 'wpamelia'),
            'payPal_test_secret_error'              => __('Please enter test Secret', 'wpamelia'),
            'period_available_for_booking'          => __('Period available for booking in advance', 'wpamelia'),
            'period_available_for_booking_tooltip'  => __('Set how far customers can book.', 'wpamelia'),
            'php_mail'                              => __('PHP Mail', 'wpamelia'),
            'price_number_of_decimals'              => __('Price Number Of Decimals', 'wpamelia'),
            'price_separator'                       => __('Price Separator', 'wpamelia'),
            'price_symbol_position'                 => __('Price Symbol Position', 'wpamelia'),
            'purchase_code'                         => __('Purchase Code', 'wpamelia'),
            'purchase_code_settings'                => __('Purchase Code Settings', 'wpamelia'),
            'purchase_code_settings_description'    => __('Use this setting to enter the purchase code so you can have access to auto updates of Amelia', 'wpamelia'),
            'remove_google_busy_slots'              => __('Remove Google Calendar Busy Slots', 'wpamelia'),
            'remove_google_busy_slots_tooltip'      => __('Enable this option if you want to remove busy slots in<br/>Google Calendar from Employee\'s working schedule.', 'wpamelia'),
            'required_phone_number_field'           => __('Set a phone number as a mandatory field', 'wpamelia'),
            'rest_api_clientid'                     => __('Rest App Client ID', 'wpamelia'),
            'rest_api_secret'                       => __('Rest App Secret', 'wpamelia'),
            'roles_settings'                        => __('Roles Settings', 'wpamelia'),
            'roles_settings_description'            => __('Use these settings to define settings that will be applied for the specific Amelia roles.', 'wpamelia'),
            'sandbox_mode'                          => __('Sandbox Mode', 'wpamelia'),
            'send_event_invitation_email'           => __('Send Event Invitation Email', 'wpamelia'),
            'send_event_invitation_email_tooltip'   => __('Enable this option if you want your customers to<br/>receive an email about the event.', 'wpamelia'),
            'sender_email'                          => __('Sender Email', 'wpamelia'),
            'sender_email_warning'                  => __('Please enter sender email', 'wpamelia'),
            'sender_name'                           => __('Sender Name', 'wpamelia'),
            'sender_name_warning'                   => __('Please enter sender name', 'wpamelia'),
            'service_duration_as_slot'              => __('Use service duration for booking a time slot', 'wpamelia'),
            'service_duration_as_slot_tooltip'      => __('Enable this option if you want to make time slot step<br/>the same as service duration in the booking process', 'wpamelia'),
            'settings'                              => __('Settings', 'wpamelia'),
            'smtp'                                  => __('SMTP', 'wpamelia'),
            'smtp_host'                             => __('SMTP Host', 'wpamelia'),
            'smtp_host_warning'                     => __('Please enter SMTP host', 'wpamelia'),
            'smtp_password'                         => __('SMTP Password', 'wpamelia'),
            'smtp_password_warning'                 => __('Please enter SMTP password', 'wpamelia'),
            'smtp_port'                             => __('SMTP Port', 'wpamelia'),
            'smtp_port_warning'                     => __('Please enter SMTP port', 'wpamelia'),
            'smtp_username'                         => __('SMTP Username', 'wpamelia'),
            'smtp_username_warning'                 => __('Please enter SMTP username', 'wpamelia'),
            'space_comma'                           => __('Space-Comma', 'wpamelia'),
            'space_dot'                             => __('Space-Dot', 'wpamelia'),
            'stripe'                                => __('Stripe', 'wpamelia'),
            'stripe_live_publishable_key_error'     => __('Please enter live publishable key', 'wpamelia'),
            'stripe_live_secret_key_error'          => __('Please enter live secret key', 'wpamelia'),
            'stripe_service'                        => __('Stripe Service', 'wpamelia'),
            'stripe_ssl_warning'                    => __('SSL (HTTPS) is not enabled. You will not be able to process live Stripe transactions until SSL is enabled.', 'wpamelia'),
            'stripe_test_publishable_key_error'     => __('Please enter test publishable key', 'wpamelia'),
            'stripe_test_secret_key_error'          => __('Please enter test secret key', 'wpamelia'),
            'template_for_event_title'              => __('Template for Event Title', 'wpamelia'),
            'test_client_id'                        => __('Test Client ID', 'wpamelia'),
            'test_mode'                             => __('Test Mode', 'wpamelia'),
            'test_publishable_key'                  => __('Test Publishable Key', 'wpamelia'),
            'test_secret'                           => __('Test Secret', 'wpamelia'),
            'test_secret_key'                       => __('Test Secret Key', 'wpamelia'),
            'two_way_sync'                          => __('2 Way Sync', 'wpamelia'),
            'update_for_all'                        => __('Update for all', 'wpamelia'),
            'wc'                                    => __('Enable integration with WooCommerce', 'wpamelia'),
            'website'                               => __('Website', 'wpamelia'),
            'week1'                                 => __('1 week', 'wpamelia'),
            'weeks2'                                => __('2 weeks', 'wpamelia'),
            'weeks3'                                => __('3 weeks', 'wpamelia'),
            'weeks4'                                => __('4 weeks', 'wpamelia'),
            'work_hours_days_off'                   => __('Working Hours & Days Off', 'wpamelia'),
            'wp_mail'                               => __('WP Mail', 'wpamelia'),
            'wp_role'                               => __('WP Role', 'wpamelia'),
            'view_general_settings'                 => __('View General Settings', 'wpamelia'),
            'view_company_settings'                 => __('View Company Settings', 'wpamelia'),
            'view_notifications_settings'           => __('View Notifications Settings', 'wpamelia'),
            'view_days_off_settings'                => __('View Working Hours & Days Off Settings', 'wpamelia'),
            'view_payments_settings'                => __('View Payments Settings', 'wpamelia'),
            'view_google_calendar_settings'         => __('View Google Calendar Settings', 'wpamelia'),
            'view_labels_settings'                  => __('View Labels Settings', 'wpamelia'),
            'view_purchase_code_settings'           => __('View Purchase Code Settings', 'wpamelia'),
            'view_roles_settings_description'       => __('View Roles Settings', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the email notifications strings
     *
     * @return array
     */
    public static function getEmailNotificationsStrings()
    {
        return [
            'cron_instruction'              => __('To send this notification please add the following line in your cron', 'wpamelia'),
            'email_notifications'           => __('Email Notifications', 'wpamelia'),
            'email_placeholders'            => __('Email Placeholders', 'wpamelia'),
            'email_template'                => __('Email Template', 'wpamelia'),
            'enter_recipient_email_warning' => __('Please enter recipient email', 'wpamelia'),
            'enter_valid_email_warning'     => __('Please enter a valid email address', 'wpamelia'),
            'message'                       => __('Message', 'wpamelia'),
            'notification_not_saved'        => __('Notification has not been saved', 'wpamelia'),
            'notification_saved'            => __('Notification has been saved', 'wpamelia'),
            'ph_appointment_cancel_url'     => __('Cancel Appointment Link', 'wpamelia'),
            'ph_appointment_date'           => __('Date of the appointment', 'wpamelia'),
            'ph_appointment_date_time'      => __('Date & Time of the appointment', 'wpamelia'),
            'ph_appointment_end_time'       => __('End time of the appointment', 'wpamelia'),
            'ph_appointment_notes'          => __('Appointment notes', 'wpamelia'),
            'ph_appointment_price'          => __('Appointment price', 'wpamelia'),
            'ph_appointment_start_time'     => __('Start time of the appointment', 'wpamelia'),
            'ph_category_name'              => __('Category name', 'wpamelia'),
            'ph_company_address'            => __('Company address', 'wpamelia'),
            'ph_company_name'               => __('Company name', 'wpamelia'),
            'ph_company_phone'              => __('Company phone', 'wpamelia'),
            'ph_company_website'            => __('Company website', 'wpamelia'),
            'ph_customer_email'             => __('Customer email', 'wpamelia'),
            'ph_customer_first_name'        => __('Customer first name', 'wpamelia'),
            'ph_customer_full_name'         => __('Customer full name', 'wpamelia'),
            'ph_customer_last_name'         => __('Customer last name', 'wpamelia'),
            'ph_customer_phone'             => __('Customer phone', 'wpamelia'),
            'ph_employee_email'             => __('Employee email', 'wpamelia'),
            'ph_employee_first_name'        => __('Employee first name', 'wpamelia'),
            'ph_employee_full_name'         => __('Employee full name', 'wpamelia'),
            'ph_employee_last_name'         => __('Employee last name', 'wpamelia'),
            'ph_employee_phone'             => __('Employee phone', 'wpamelia'),
            'ph_employee_photo'             => __('Employee photo', 'wpamelia'),
            'ph_location_address'           => __('Location address', 'wpamelia'),
            'ph_location_name'              => __('Location name', 'wpamelia'),
            'ph_service_duration'           => __('Service duration', 'wpamelia'),
            'ph_service_name'               => __('Service name', 'wpamelia'),
            'ph_service_price'              => __('Service price', 'wpamelia'),
            'recipient_email'               => __('Recipient Email', 'wpamelia'),
            'requires_scheduling_setup'     => __('Requires Scheduling Setup', 'wpamelia'),
            'scheduled_after'               => __('Scheduled After Appointment', 'wpamelia'),
            'scheduled_before'              => __('Scheduled For Before Appointment', 'wpamelia'),
            'scheduled_for'                 => __('Scheduled For', 'wpamelia'),
            'select_email_template_warning' => __('Please select email template', 'wpamelia'),
            'send'                          => __('Send', 'wpamelia'),
            'send_test_email'               => __('Send Test Email', 'wpamelia'),
            'show_email_codes'              => __('</> Show Email Codes', 'wpamelia'),
            'subject'                       => __('Subject', 'wpamelia'),
            'test_email_error'              => __('Email has not been sent', 'wpamelia'),
            'test_email_success'            => __('Email has been sent', 'wpamelia'),
            'test_email_warning'            => __('To be able to send test email please configure "Sender Email" in Notification Settings.', 'wpamelia'),
            'to_customer'                   => __('To Customer', 'wpamelia'),
            'to_employee'                   => __('To Employee', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the common strings
     *
     * @return array
     */
    public static function getDashboardStrings()
    {
        return [
            'approved_appointments'         => __('Approved Appointments', 'wpamelia'),
            'approved_appointments_tooltip' => __('Indicates the number of approved appointments<br/>for a chosen date range.', 'wpamelia'),
            'average_bookings'              => __('Average Bookings', 'wpamelia'),
            'average_bookings_tooltip'      => __('Shows the average number of bookings per day<br/>for the selected date range.', 'wpamelia'),
            'conversions'                   => __('Interests / Conversions', 'wpamelia'),
            'conversions_tooltip'           => __('Shows the number of views for the employee/service/location<br/>vs. the number of times they were booked during<br/>the selected date range.', 'wpamelia'),
            'customers_tooltip'             => __('Indicates the number of new and returning customers<br/>for the selected date range.', 'wpamelia'),
            'dashboard'                     => __('Dashboard', 'wpamelia'),
            'new'                           => __('New', 'wpamelia'),
            'no_today_appointments'         => __('There are no appointments for today', 'wpamelia'),
            'pending_appointments'          => __('Pending Appointments', 'wpamelia'),
            'pending_appointments_tooltip'  => __('Shows the number of pending appointments<br/>for the selected date range.', 'wpamelia'),
            'returning'                     => __('Returning', 'wpamelia'),
            'revenue'                       => __('Revenue', 'wpamelia'),
            'revenue_tooltip'               => __('Shows the total income for paid appointments<br/>for the chosen date range.', 'wpamelia'),
            'scheduled_appointments'        => __('Scheduled Appointments', 'wpamelia'),
            'time'                          => __('Time', 'wpamelia'),
            'today_appointments'            => __('Today\'s appointments', 'wpamelia'),
            'views'                         => __('Views', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the schedule modal
     *
     * @return array
     */
    public static function getScheduleStrings()
    {
        return [
            'add_break'                           => __('Add Break', 'wpamelia'),
            'add_day_off'                         => __('Add Day Off', 'wpamelia'),
            'add_day_off_placeholder'             => __('Enter holiday or day off name', 'wpamelia'),
            'apply_to_all_days'                   => __('Apply to All Days', 'wpamelia'),
            'breaks'                              => __('Breaks', 'wpamelia'),
            'company_days_off'                    => __('Company Days off', 'wpamelia'),
            'company_days_off_settings'           => __('Company Days Off Settings', 'wpamelia'),
            'company_work_hours_settings'         => __('Company Working Hours Settings', 'wpamelia'),
            'confirm_global_change_working_hours' => __('You will change working hours setting which is also set for each employee separately. Do you want to update it for all employees?', 'wpamelia'),
            'day_off_name'                        => __('Day Off name', 'wpamelia'),
            'days_off'                            => __('Days Off', 'wpamelia'),
            'days_off_add'                        => __('Add Day Off', 'wpamelia'),
            'days_off_date_warning'               => __('Please enter date', 'wpamelia'),
            'days_off_name_warning'               => __('Please enter name', 'wpamelia'),
            'days_off_repeat_yearly'              => __('Repeat Yearly', 'wpamelia'),
            'edit_company_days_off'               => __('Edit Company Days off', 'wpamelia'),
            'employee_days_off'                   => __('Employee Days off', 'wpamelia'),
            'once_off'                            => __('Once Off', 'wpamelia'),
            'pick_a_date_or_range'                => __('Pick a date or range', 'wpamelia'),
            'pick_a_year'                         => __('Pick a year', 'wpamelia'),
            'repeat_every_year'                   => __('Repeat Every Year', 'wpamelia'),
            'work_hours'                          => __('Work Hours', 'wpamelia'),
            'work_hours_days_off_settings'        => __('Working Hours & Days Off Settings', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the entities modal
     *
     * @return array
     */
    public static function getEntityFormStrings()
    {
        return [
            'delete'          => __('Delete', 'wpamelia'),
            'duplicate'       => __('Duplicate', 'wpamelia'),
            'loader_message'  => __('Please Wait', 'wpamelia'),
            'visibility_hide' => __('Hide', 'wpamelia'),
            'visibility_show' => __('Show', 'wpamelia'),
            'visible'         => __('Visible', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the location page
     *
     * @return array
     */
    public static function getLocationStrings()
    {
        return [
            'add_location'                   => __('Add Location', 'wpamelia'),
            'address'                        => __('Address', 'wpamelia'),
            'click_add_locations'            => __('Start by clicking the Add Location button', 'wpamelia'),
            'confirm_delete_location'        => __('Are you sure you want to delete this location?', 'wpamelia'),
            'confirm_duplicate_location'     => __('Are you sure you want to duplicate this location?', 'wpamelia'),
            'confirm_hide_location'          => __('Are you sure you want to hide this location?', 'wpamelia'),
            'confirm_show_location'          => __('Are you sure you want to show this location?', 'wpamelia'),
            'edit_location'                  => __('Edit Location', 'wpamelia'),
            'enter_location_address_warning' => __('Please enter address', 'wpamelia'),
            'enter_location_name_warning'    => __('Please enter name', 'wpamelia'),
            'latitude'                       => __('Latitude', 'wpamelia'),
            'location_address'               => __('Location Address', 'wpamelia'),
            'location_deleted'               => __('Location has been deleted', 'wpamelia'),
            'location_hidden'                => __('Your Location is hidden', 'wpamelia'),
            'location_saved'                 => __('Location has been saved', 'wpamelia'),
            'location_visible'               => __('Your Location is visible', 'wpamelia'),
            'locations_lower'                => __('locations', 'wpamelia'),
            'locations_search_placeholder'   => __('Search Locations...', 'wpamelia'),
            'longitude'                      => __('Longitude', 'wpamelia'),
            'map'                            => __('Map', 'wpamelia'),
            'new_location'                   => __('New Location', 'wpamelia'),
            'no_locations_yet'               => __('You don\'t have any locations here yet...', 'wpamelia'),
            'not_right_address'              => __('This is not the right address?', 'wpamelia'),
            'pin_icon'                       => __('Pin Icon', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the service page
     *
     * @return array
     */
    public static function getServiceStrings()
    {
        return [
            'add_category'                       => __('Add Category', 'wpamelia'),
            'add_extra'                          => __('Add Extra', 'wpamelia'),
            'add_image'                          => __('Add Image', 'wpamelia'),
            'add_service'                        => __('Add Service', 'wpamelia'),
            'available_images'                   => __('Available Images', 'wpamelia'),
            'bringing_anyone'                    => __('Show "Bringing anyone with you" option', 'wpamelia'),
            'bringing_anyone_tooltip'            => __('Hide this option to allow only individual people to<br/>book a group appointment without the possibility<br/>to come with somebody.', 'wpamelia'),
            'categories'                         => __('Categories', 'wpamelia'),
            'categories_delete_fail'             => __('Unable to delete category', 'wpamelia'),
            'categories_positions_saved'         => __('Categories positions has been saved', 'wpamelia'),
            'categories_positions_saved_fail'    => __('Unable to save categories positions', 'wpamelia'),
            'category'                           => __('Category', 'wpamelia'),
            'category_add_fail'                  => __('Unable to add category', 'wpamelia'),
            'category_deleted'                   => __('Category has been deleted', 'wpamelia'),
            'category_duplicated'                => __('Category has been duplicated', 'wpamelia'),
            'category_saved'                     => __('Category has been saved', 'wpamelia'),
            'category_saved_fail'                => __('Unable to save category', 'wpamelia'),
            'click_add_category'                 => __('Start by clicking the Add Category button', 'wpamelia'),
            'click_add_service'                  => __('Start by clicking the Add Service button', 'wpamelia'),
            'confirm_delete_service'             => __('Are you sure you want to delete this service?', 'wpamelia'),
            'confirm_duplicate_service'          => __('Are you sure you want to duplicate this service?', 'wpamelia'),
            'confirm_global_change_service'      => __('You will change a setting which is also set for each employee separately. Do you want to update it for all employees?', 'wpamelia'),
            'confirm_hide_service'               => __('Are you sure you want to hide this service?', 'wpamelia'),
            'confirm_show_service'               => __('Are you sure you want to show this service?', 'wpamelia'),
            'delete_category_confirmation'       => __('Are you sure you want to delete this category', 'wpamelia'),
            'delete_extra_confirmation'          => __('Are you sure you want to delete this extra', 'wpamelia'),
            'edit_service'                       => __('Edit Service', 'wpamelia'),
            'enter_extra_name_warning'           => __('Please enter extra name', 'wpamelia'),
            'enter_extra_price_warning'          => __('Please enter extra price', 'wpamelia'),
            'enter_non_negative_price_warning'   => __('Price must be non-negative number', 'wpamelia'),
            'enter_service_name_warning'         => __('Please enter name', 'wpamelia'),
            'enter_service_price_warning'        => __('Please enter price', 'wpamelia'),
            'extra_delete_fail'                  => __('Unable to delete extra', 'wpamelia'),
            'gallery'                            => __('Gallery', 'wpamelia'),
            'hex'                                => __('Hex', 'wpamelia'),
            'maximum_capacity'                   => __('Maximum Capacity', 'wpamelia'),
            'maximum_capacity_tooltip'           => __('Here you can set the maximum number of persons<br/>per one appointment.', 'wpamelia'),
            'maximum_quantity'                   => __('Maximum Quantity', 'wpamelia'),
            'minimum_capacity'                   => __('Minimum Capacity', 'wpamelia'),
            'minimum_capacity_tooltip'           => __('Here you can set the minimum number of persons<br/>per one booking of this service.', 'wpamelia'),
            'new_category'                       => __('New Category', 'wpamelia'),
            'new_service'                        => __('New Service', 'wpamelia'),
            'no_categories_yet'                  => __('You don\'t have any categories here yet...', 'wpamelia'),
            'no_services_yet'                    => __('You don\'t have any services here yet...', 'wpamelia'),
            'price'                              => __('Price', 'wpamelia'),
            'select_service_category_warning'    => __('Please select category', 'wpamelia'),
            'select_service_duration_warning'    => __('Please select duration', 'wpamelia'),
            'select_service_employee_warning'    => __('Please select select at least one employee', 'wpamelia'),
            'service_buffer_time_after'          => __('Buffer Time After', 'wpamelia'),
            'service_buffer_time_after_tooltip'  => __('Time after the appointment (rest, clean up, etc.),<br/>when another booking for same service and<br/>employee cannot be made.', 'wpamelia'),
            'service_buffer_time_before'         => __('Buffer Time Before', 'wpamelia'),
            'service_buffer_time_before_tooltip' => __('Time needed to prepare for the appointment, when<br/>another booking for same service and employee<br/>cannot be made.', 'wpamelia'),
            'service_deleted'                    => __('Service has been deleted', 'wpamelia'),
            'service_details'                    => __('Service Details', 'wpamelia'),
            'service_hidden'                     => __('Service is hidden', 'wpamelia'),
            'service_provider_remove_fail'       => __('Provider has appointments for this service', 'wpamelia'),
            'service_saved'                      => __('Service has been saved', 'wpamelia'),
            'service_visible'                    => __('Service is visible', 'wpamelia'),
            'update_for_all'                     => __('Update for all', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the user page
     *
     * @return array
     */
    public static function getUserStrings()
    {
        return [
            'create_new'                => __('Create New', 'wpamelia'),
            'email'                     => __('Email', 'wpamelia'),
            'enter_email_warning'       => __('Please enter email', 'wpamelia'),
            'enter_first_name_warning'  => __('Please enter first name', 'wpamelia'),
            'enter_last_name_warning'   => __('Please enter last name', 'wpamelia'),
            'enter_valid_email_warning' => __('Please enter a valid email address', 'wpamelia'),
            'first_name'                => __('First Name', 'wpamelia'),
            'last_name'                 => __('Last Name', 'wpamelia'),
            'select_wp_user'            => __('Select or Create New', 'wpamelia'),
            'wp_user'                   => __('WordPress User', 'wpamelia'),
            'wp_user_customer_tooltip'  => __('Here you can map a WordPress user to the customer if<br/>you want to give customers access to the list of their<br/>appointments in the back-end of the plugin.', 'wpamelia'),
            'wp_user_employee_tooltip'  => __('Here you can map a WordPress user to the employee if<br/>you want to give employee access to the list of their<br/>appointments in the back-end of the plugin.', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the employee page
     *
     * @return array
     */
    public static function getEmployeeStrings()
    {
        return [
            'activity'                         => __('Status', 'wpamelia'),
            'add_employee'                     => __('Add Employee', 'wpamelia'),
            'assigned_services'                => __('Assigned Services', 'wpamelia'),
            'available'                        => __('Available', 'wpamelia'),
            'away'                             => __('Away', 'wpamelia'),
            'break'                            => __('On Break', 'wpamelia'),
            'busy'                             => __('Busy', 'wpamelia'),
            'capacity'                         => __('Capacity', 'wpamelia'),
            'click_add_employee'               => __('Start by clicking the Add Employee button', 'wpamelia'),
            'confirm_delete_employee'          => __('Are you sure you want to delete this employee?', 'wpamelia'),
            'confirm_duplicate_employee'       => __('Are you sure you want to duplicate this employee?', 'wpamelia'),
            'confirm_hide_employee'            => __('Are you sure you want to hide this employee?', 'wpamelia'),
            'confirm_show_employee'            => __('Are you sure you want to show this employee?', 'wpamelia'),
            'connect'                          => __('Connect', 'wpamelia'),
            'dayoff'                           => __('Day Off', 'wpamelia'),
            'disconnect'                       => __('Disconnect', 'wpamelia'),
            'edit_employee'                    => __('Edit Employee', 'wpamelia'),
            'employee_deleted'                 => __('Employee has been deleted', 'wpamelia'),
            'employee_hidden'                  => __('Employee is hidden', 'wpamelia'),
            'employee_not_deleted'             => __('Employee can not be deleted because of the future appointment', 'wpamelia'),
            'employee_saved'                   => __('Employee has been saved', 'wpamelia'),
            'employee_search_placeholder'      => __('Search Employees...', 'wpamelia'),
            'employee_visible'                 => __('Employee is visible', 'wpamelia'),
            'employees_deleted'                => __('Employees have been deleted', 'wpamelia'),
            'employees_lower'                  => __('employees', 'wpamelia'),
            'employees_not_deleted'            => __('Employees could not be deleted because of the future appointment', 'wpamelia'),
            'enter_location_warning'           => __('Please select location', 'wpamelia'),
            'google_calendar_error'            => __('Unable to connect to Google Calendar', 'wpamelia'),
            'google_calendar_tooltip'          => __('Here you can connect with your Google Calendar,<br/>so once the appointment is scheduled it will be<br/>automatically added to your calendar.', 'wpamelia'),
            'grid_view'                        => __('Grid View', 'wpamelia'),
            'new_employee'                     => __('New Employee', 'wpamelia'),
            'no_employees_yet'                 => __('You don\'t have any employees here yet...', 'wpamelia'),
            'price'                            => __('Price', 'wpamelia'),
            'service_provider_remove_fail'     => __('Provider has appointments for this service', 'wpamelia'),
            'service_provider_remove_fail_all' => __('Provider has appointments for', 'wpamelia'),
            'table_view'                       => __('Table View', 'wpamelia')
        ];
    }

    /**
     * Returns the array for the customer page
     *
     * @return array
     */
    public static function getCustomerStrings()
    {
        return [
            'add_customer'                 => __('Add Customer', 'wpamelia'),
            'click_add_customers'          => __('Start by clicking the Add Customer button', 'wpamelia'),
            'confirm_delete_customer'      => __('Are you sure you want to delete this customer?', 'wpamelia'),
            'customer_deleted'             => __('Customer has been deleted', 'wpamelia'),
            'customer_not_deleted'         => __('Customer can not be deleted because of the future appointment', 'wpamelia'),
            'customer_note'                => __('Note', 'wpamelia'),
            'customer_saved'               => __('Customer has been saved', 'wpamelia'),
            'customers'                    => __('Customers', 'wpamelia'),
            'customers_deleted'            => __('Customers have been deleted', 'wpamelia'),
            'customers_lower'              => __('customers', 'wpamelia'),
            'customers_not_deleted'        => __('Customers could not be deleted because of the future appointment', 'wpamelia'),
            'customers_search_placeholder' => __('Search Customers...', 'wpamelia'),
            'date_of_birth'                => __('Date of Birth', 'wpamelia'),
            'edit_customer'                => __('Edit Customer', 'wpamelia'),
            'email_placeholder'            => __('example@mail.com', 'wpamelia'),
            'export_tooltip_customers'     => __('You can use this option to export customers in CSV file.', 'wpamelia'),
            'gender'                       => __('Gender', 'wpamelia'),
            'last_appointment'             => __('Last Appointment', 'wpamelia'),
            'last_appointment_ascending'   => __('Last Appointment Ascending', 'wpamelia'),
            'last_appointment_descending'  => __('Last Appointment Descending', 'wpamelia'),
            'new_customer'                 => __('New Customer', 'wpamelia'),
            'no_customers_yet'             => __('You don\'t have any customers here yet...', 'wpamelia'),
            'note_internal'                => __('Note (Internal)', 'wpamelia'),
            'pending_appointments'         => __('Pending Appointments', 'wpamelia'),
            'select_date_of_birth'         => __('Select Date of Birth', 'wpamelia'),
            'total_appointments'           => __('Total Appointments', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the finance page
     *
     * @return array
     */
    public static function getFinanceStrings()
    {
        return [
            'amount'                             => __('Amount', 'wpamelia'),
            'booking_start'                      => __('Booking Start', 'wpamelia'),
            'code'                               => __('Code', 'wpamelia'),
            'code_tooltip'                       => __('Here you need to define a coupon code which customers will<br/>enter in their booking so they can get a discount.', 'wpamelia'),
            'confirm_delete_coupon'              => __('Are you sure you want to delete this coupon?', 'wpamelia'),
            'confirm_duplicate_coupon'           => __('Are you sure you want to duplicate this coupon?', 'wpamelia'),
            'confirm_hide_coupon'                => __('Are you sure you want to hide this coupon?', 'wpamelia'),
            'confirm_show_coupon'                => __('Are you sure you want to show this coupon?', 'wpamelia'),
            'coupon_deleted'                     => __('Coupon has been deleted', 'wpamelia'),
            'coupon_hidden'                      => __('Your Coupon is hidden', 'wpamelia'),
            'coupon_not_deleted'                 => __('Coupon has not been deleted', 'wpamelia'),
            'coupon_saved'                       => __('Coupon has been saved', 'wpamelia'),
            'coupon_usage_limit_validation'      => __('Coupon usage limit must be at least 1', 'wpamelia'),
            'coupon_visible'                     => __('Your Coupon is active', 'wpamelia'),
            'coupons'                            => __('Coupons', 'wpamelia'),
            'coupons_deleted'                    => __('Coupons have been deleted', 'wpamelia'),
            'coupons_lower'                      => __('coupons', 'wpamelia'),
            'coupons_multiple_services_text'     => __(' & Other Services', 'wpamelia'),
            'coupons_not_deleted'                => __('Coupons have not been deleted', 'wpamelia'),
            'customer_email'                     => __('Customer Email', 'wpamelia'),
            'deduction'                          => __('Deduction', 'wpamelia'),
            'edit_coupon'                        => __('Edit Coupon', 'wpamelia'),
            'employee_email'                     => __('Employee Email', 'wpamelia'),
            'enter_coupon_code_warning'          => __('Please enter code', 'wpamelia'),
            'export_tooltip_coupons'             => __('You can use this option to export coupons in CSV file.', 'wpamelia'),
            'export_tooltip_payments'            => __('You can use this option to export payments in CSV file<br/>for the selected date range.', 'wpamelia'),
            'finance_coupons_search_placeholder' => __('Search Coupons', 'wpamelia'),
            'limit'                              => __('Limit', 'wpamelia'),
            'method'                             => __('Method', 'wpamelia'),
            'new_coupon'                         => __('New Coupon', 'wpamelia'),
            'no_coupon_amount'                   => __('Coupon needs to have discount or deduction', 'wpamelia'),
            'no_coupons_yet'                     => __('You don\'t have any coupons here yet', 'wpamelia'),
            'no_payments_yet'                    => __('You don\'t have any payments here yet', 'wpamelia'),
            'no_services_selected'               => __('Select at least one service', 'wpamelia'),
            'paid'                               => __('Paid', 'wpamelia'),
            'payment_date'                       => __('Payment date', 'wpamelia'),
            'payments'                           => __('Payments', 'wpamelia'),
            'payments_lower'                     => __('payments', 'wpamelia'),
            'pending'                            => __('Pending', 'wpamelia'),
            'select_all_services'                => __('Select All Service', 'wpamelia'),
            'services_tooltip'                   => __('Select the services for which the coupon can be used.', 'wpamelia'),
            'times_used'                         => __('Times Used', 'wpamelia'),
            'usage_limit'                        => __('Usage Limit', 'wpamelia'),
            'usage_limit_tooltip'                => __('Here you need to define the number of coupons for use. After the<br/>limit is reached your coupon will become unavailable.', 'wpamelia'),
            'used'                               => __('Used', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the finance page
     *
     * @return array
     */
    public static function getPaymentStrings()
    {
        return [
            'appointment_date'         => __('Appointment Date', 'wpamelia'),
            'appointment_info'         => __('Appointment Info', 'wpamelia'),
            'confirm_delete_payment'   => __('Are you sure you want to delete this payment?', 'wpamelia'),
            'discount'                 => __('Discount (%)', 'wpamelia'),
            'discount_amount'          => __('Discount', 'wpamelia'),
            'due'                      => __('Due', 'wpamelia'),
            'enter_new_payment_amount' => __('Enter new payment amount', 'wpamelia'),
            'finance'                  => __('Finance', 'wpamelia'),
            'paid'                     => __('Paid', 'wpamelia'),
            'payment_deleted'          => __('Payment has been deleted', 'wpamelia'),
            'payment_details'          => __('Payment Details', 'wpamelia'),
            'payment_method'           => __('Payment Method', 'wpamelia'),
            'payment_not_deleted'      => __('Payment has not been deleted', 'wpamelia'),
            'payment_saved'            => __('Payment has been saved', 'wpamelia'),
            'payments_deleted'         => __('Payments have been deleted', 'wpamelia'),
            'payments_not_deleted'     => __('Payments have not been deleted', 'wpamelia'),
            'service_price'            => __('Service Price', 'wpamelia'),
            'subtotal'                 => __('Subtotal', 'wpamelia')
        ];
    }

    /**
     * Returns the array for the appointment strings
     *
     * @return array
     */
    public static function getAppointmentStrings()
    {
        return [
            'appointment_deleted'               => __('Appointment has been deleted', 'wpamelia'),
            'appointment_not_deleted'           => __('Appointment has not been deleted', 'wpamelia'),
            'appointment_saved'                 => __('Appointment has been saved', 'wpamelia'),
            'appointment_status_changed'        => __('Appointment status has been changed to ', 'wpamelia'),
            'appointments'                      => __('Appointments', 'wpamelia'),
            'appointments_deleted'              => __('Appointments have been deleted', 'wpamelia'),
            'appointments_not_deleted'          => __('Appointment have not been deleted', 'wpamelia'),
            'appointments_search_placeholder'   => __('Search for Customers, Employees, Services...', 'wpamelia'),
            'assigned'                          => __('Assigned', 'wpamelia'),
            'assigned_to'                       => __('Assigned to', 'wpamelia'),
            'cancel_appointment'                => __('Cancel Appointment', 'wpamelia'),
            'change_group_status'               => __('Change group status', 'wpamelia'),
            'choose_a_group_service'            => __('Choose a group service', 'wpamelia'),
            'click_add_appointments'            => __('Start by clicking the New Appointment button', 'wpamelia'),
            'confirm_delete_appointment'        => __('Are you sure you want to delete this appointment?', 'wpamelia'),
            'confirm_duplicate_appointment'     => __('Are you sure you want to duplicate this appointment?', 'wpamelia'),
            'create_customer'                   => __('Create Customer', 'wpamelia'),
            'create_new'                        => __('Create New', 'wpamelia'),
            'custom_fields'                     => __('Custom Fields', 'wpamelia'),
            'customer_email'                    => __('Customer Email', 'wpamelia'),
            'customer_phone'                    => __('Customer Phone', 'wpamelia'),
            'customers'                         => __('Customers', 'wpamelia'),
            'customers_singular_plural'         => __('Customer(s)', 'wpamelia'),
            'customers_tooltip'                 => __('Here you can define the number of people that are coming<br/>with this customer. The number you can choose depends<br/>on the service and employee capacity.', 'wpamelia'),
            'edit_appointment'                  => __('Edit Appointment', 'wpamelia'),
            'end_time'                          => __('End Time', 'wpamelia'),
            'export_tooltip_appointments'       => __('You can use this option to export appointments in CSV file<br/>for the selected date range.', 'wpamelia'),
            'minimum_number_of_persons'         => __('Minimum number of persons for bookings to approve appointment is', 'wpamelia'),
            'multiple_emails'                   => __('Multiple Emails', 'wpamelia'),
            'new_appointment'                   => __('New Appointment', 'wpamelia'),
            'no_appointments_yet'               => __('You don\'t have any appointments here yet...', 'wpamelia'),
            'no_selected_customers'             => __('There are no selected customers', 'wpamelia'),
            'no_selected_extras_requirements'   => __('Select customer, employee and service', 'wpamelia'),
            'no_selected_fields_requirements'   => __('Select customer and service', 'wpamelia'),
            'notify_customers'                  => __('Notify the customer(s)', 'wpamelia'),
            'notify_customers_tooltip'          => __('Check this checkbox if you want your customer to<br/>receive an email about the scheduled appointment.', 'wpamelia'),
            'notify_free_subject'               => __('Amelia booking limit notification', 'wpamelia'),
            'notify_free_body'                  => __('You have 15 appointments left available for booking. If you need more please upgrade to full plugin version.', 'wpamelia'),
            'required_field'                    => __('This field is required', 'wpamelia'),
            'schedule'                          => __('Schedule', 'wpamelia'),
            'select_customer_warning'           => __('Please select at least one customer', 'wpamelia'),
            'select_customers'                  => __('Select Customer(s)', 'wpamelia'),
            'select_date_warning'               => __('Please select date', 'wpamelia'),
            'select_employee'                   => __('Select Employee', 'wpamelia'),
            'select_employee_warning'           => __('Please select employee', 'wpamelia'),
            'select_location'                   => __('Select Location', 'wpamelia'),
            'select_max_customer_count_warning' => __('Maximum number of places is', 'wpamelia'),
            'select_service'                    => __('Select Service', 'wpamelia'),
            'select_service_category'           => __('Select Service Category', 'wpamelia'),
            'select_service_warning'            => __('Please select service', 'wpamelia'),
            'select_time_warning'               => __('Please select time', 'wpamelia'),
            'selected_customers'                => __('Selected Customers', 'wpamelia'),
            'service_category'                  => __('Service Category', 'wpamelia'),
            'service_no_extras'                 => __('This service does not have any extras', 'wpamelia'),
            'start_time'                        => __('Start Time', 'wpamelia'),
            'time'                              => __('Time', 'wpamelia'),
            'time_slot_unavailable'             => __('Time slot is unavailable', 'wpamelia'),
            'view_payment_details'              => __('View Payment Details', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the calendar strings
     *
     * @return array
     */
    public static function getCalendarStrings()
    {
        return [
            'add_customer'                   => __('Add Customer', 'wpamelia'),
            'add_employee'                   => __('Add Employee', 'wpamelia'),
            'add_location'                   => __('Add Location', 'wpamelia'),
            'add_service'                    => __('Add Service', 'wpamelia'),
            'all'                            => __('All', 'wpamelia'),
            'all_employees'                  => __('All employees', 'wpamelia'),
            'appointment_change_time'        => __('This will change the time of the appointment. Continue?', 'wpamelia'),
            'appointment_drag_breaks'        => __('Appointment can\'t be moved because of employee break in the selected period', 'wpamelia'),
            'appointment_drag_exist'         => __('There is already an appointment for this employee in selected time period', 'wpamelia'),
            'appointment_drag_past'          => __('Appointment can\'t be moved in past time period', 'wpamelia'),
            'appointment_drag_working_hours' => __('Appointment can\'t be moved out of employee working hours', 'wpamelia'),
            'appointment_rescheduled'        => __('Appointment has been rescheduled', 'wpamelia'),
            'calendar'                       => __('Calendar', 'wpamelia'),
            'confirm'                        => __('Confirm', 'wpamelia'),
            'day'                            => __('Day', 'wpamelia'),
            'group_booking'                  => __('Group booking', 'wpamelia'),
            'list'                           => __('List', 'wpamelia'),
            'month'                          => __('Month', 'wpamelia'),
            'new_coupon'                     => __('New Coupon', 'wpamelia'),
            'no_appointments_to_display'     => __('No appointments to display', 'wpamelia'),
            'today'                          => __('Today', 'wpamelia'),
            'total'                          => __('Total', 'wpamelia'),
            'timelineDay'                    => __('Timeline', 'wpamelia'),
            'week'                           => __('Week', 'wpamelia'),
        ];
    }

    /**
     * Returns the array for the Customize page
     *
     * @return array
     */
    public static function getCustomizeStrings()
    {
        return [
            'add_custom_field'                   => __('Add Custom Field', 'wpamelia'),
            'add_extra'                          => __('Add Extra', 'wpamelia'),
            'add_option'                         => __('Add Option', 'wpamelia'),
            'all_services'                       => __('All services', 'wpamelia'),
            'any_employee'                       => __('Any Employee', 'wpamelia'),
            'bringing_anyone_with_you'           => __('Bringing Anyone with You?', 'wpamelia'),
            'checkbox'                           => __('Checkbox', 'wpamelia'),
            'click_add_custom_field'             => __('Start by clicking the Add Custom Field button', 'wpamelia'),
            'colors_and_fonts'                   => __('Colors & Fonts', 'wpamelia'),
            'content'                            => __('Text Content', 'wpamelia'),
            'continue'                           => __('Continue', 'wpamelia'),
            'custom_fields'                      => __('Custom Fields', 'wpamelia'),
            'custom_fields_added'                => __('Custom field has been added', 'wpamelia'),
            'custom_fields_deleted'              => __('Custom field has been deleted', 'wpamelia'),
            'custom_fields_positions_saved_fail' => __('Unable to save custom fields positions', 'wpamelia'),
            'customize'                          => __('Customize', 'wpamelia'),
            'font'                               => __('Font', 'wpamelia'),
            'label'                              => __('Label', 'wpamelia'),
            'no_custom_fields_yet'               => __('You don\'t have any custom fields here yet...', 'wpamelia'),
            'notification_placeholder'           => __('Notification Placeholder', 'wpamelia'),
            'options'                            => __('Options', 'wpamelia'),
            'pick_date_and_time'                 => __('Pick date & time', 'wpamelia'),
            'please_select_service'              => __('Please select service', 'wpamelia'),
            'primary_color'                      => __('Primary Color', 'wpamelia'),
            'primary_gradient'                   => __('Primary Gradient', 'wpamelia'),
            'radio'                              => __('Radio Buttons', 'wpamelia'),
            'required'                           => __('Required', 'wpamelia'),
            'reset'                              => __('Reset', 'wpamelia'),
            'select'                             => __('Selectbox', 'wpamelia'),
            'text'                               => __('Text', 'wpamelia'),
            'text-area'                          => __('Text Area', 'wpamelia'),
            'text_color'                         => __('Text Color', 'wpamelia'),
            'text_color_on_background'           => __('Text Color on Background', 'wpamelia'),
        ];
    }

    /**
     * @return array
     */
    public static function getWordPressStrings()
    {
        return [
            'filter'                  => __('Preselect Booking Parameters', 'wpamelia'),
            'insert_amelia_shortcode' => __('Insert Amelia Booking Shortcode', 'wpamelia'),
            'booking'                 => __('Booking', 'wpamelia'),
            'search'                  => __('Search', 'wpamelia'),
            'catalog'                 => __('Catalog', 'wpamelia'),
            'search_date'             => __('Preselect Current Date', 'wpamelia'),
            'select_catalog_view'     => __('Select Catalog View', 'wpamelia'),
            'select_category'         => __('Select Category', 'wpamelia'),
            'select_service'          => __('Select Service', 'wpamelia'),
            'select_location'         => __('Select Location', 'wpamelia'),
            'select_employee'         => __('Select Employee', 'wpamelia'),
            'show_catalog'            => __('Show catalog of all categories', 'wpamelia'),
            'show_category'           => __('Show specific category', 'wpamelia'),
            'select_view'             => __('Select View', 'wpamelia'),
            'show_service'            => __('Show specific service', 'wpamelia'),
            'show_all_categories'     => __('Show all categories', 'wpamelia'),
            'show_all_services'       => __('Show all services', 'wpamelia'),
            'show_all_employees'      => __('Show all employees', 'wpamelia'),
            'show_all_locations'      => __('Show all locations', 'wpamelia'),

        ];
    }
}
