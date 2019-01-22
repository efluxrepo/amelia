<?php
/*
Plugin Name: Amelia
Plugin URI: https://wpamelia.com/
Description: Amelia is a simple yet powerful automated booking specialist, working 24/7 to make sure your customers can make appointments even while you sleep!
Version: 1.0
Author: TMS
Author URI: https://tms-outsource.com/
Text Domain: wpamelia
Domain Path: /languages
*/

namespace AmeliaBooking;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Routes\Routes;
use AmeliaBooking\Infrastructure\WP\ButtonService\ButtonService;
use AmeliaBooking\Infrastructure\WP\config\Menu;
use AmeliaBooking\Infrastructure\WP\Integrations\WooCommerce\WooCommerceService;
use AmeliaBooking\Infrastructure\WP\SettingsService\SettingsStorage;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use AmeliaBooking\Infrastructure\WP\WPMenu\Submenu;
use AmeliaBooking\Infrastructure\WP\WPMenu\SubmenuPageHandler;
use AmeliaBooking\Infrastructure\WP\ShortcodeService;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\App;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

// Const for path root
if (!defined('AMELIA_PATH')) {
    define('AMELIA_PATH', __DIR__);
}

// Const for uploads path
if (!defined('UPLOADS_PATH')) {
    $uploadDir = wp_upload_dir();
    define('UPLOADS_PATH', $uploadDir['basedir']);
}

// Const for uploads url
if (!defined('UPLOADS_URL')) {
    $uploadUrl = wp_upload_dir();
    define('UPLOADS_URL', set_url_scheme($uploadUrl['baseurl']));
}

// Const for URL root
if (!defined('AMELIA_URL')) {
    define('AMELIA_URL', plugin_dir_url(__FILE__));
}

// Const for URL Actions identifier
if (!defined('AMELIA_ACTION_SLUG')) {
    define('AMELIA_ACTION_SLUG', 'action=wpamelia_api&call=');
}

// Const for URL Actions identifier
if (!defined('AMELIA_ACTION_URL')) {
    define('AMELIA_ACTION_URL', get_site_url() . '/wp-admin/admin-ajax.php?' . AMELIA_ACTION_SLUG);
}

// Const for URL Actions identifier
if (!defined('AMELIA_PAGE_URL')) {
    define('AMELIA_PAGE_URL', get_site_url() . '/wp-admin/admin.php?page=');
}

// Const for URL Actions identifier
if (!defined('AMELIA_LOGIN_URL')) {
    define('AMELIA_LOGIN_URL', get_site_url() . '/wp-login.php?redirect_to=');
}

// Const for Amelia version
if (!defined('AMELIA_VERSION')) {
    define('AMELIA_VERSION', '1.0');
}

// Const for site URL
if (!defined('AMELIA_SITE_URL')) {
    define('AMELIA_SITE_URL', get_site_url());
}

// Const for path root
if (!defined('AMELIA_LOCALE')) {
    define('AMELIA_LOCALE', get_locale());
}

// Const for plugin basename
if (!defined('AMELIA_PLUGIN_SLUG')) {
    define('AMELIA_PLUGIN_SLUG', plugin_basename(__FILE__));
}

// Const for plugin basename
if (!defined('AMELIA_LITE_VERSION')) {
    define('AMELIA_LITE_VERSION', true);
}

require_once AMELIA_PATH . '/vendor/autoload.php';

/**
 * @noinspection AutoloadingIssuesInspection
 *
 * Class Plugin
 *
 * @package AmeliaBooking
 *
 * @phpcs:ignoreFile
 * @SuppressWarnings(PHPMD)
 */
class Plugin
{

    /**
     * API Call
     *
     * @throws \InvalidArgumentException
     */
    public static function wpAmeliaApiCall()
    {
        try {
            /** @var Container $container */
            $container = require AMELIA_PATH . '/src/Infrastructure/ContainerConfig/container.php';

            $app = new App($container);

            // Initialize all API routes
            Routes::routes($app);

            $app->run();

            exit();
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * Initialize the plugin
     */
    public static function init()
    {
        load_plugin_textdomain('wpamelia', false, plugin_basename(__DIR__) . '/languages/' . get_locale() . '/');

        $settingsService = new SettingsService(new SettingsStorage());

        if (class_exists('WooCommerce')) {
            add_filter('woocommerce_prevent_admin_access', '__return_false');

            if ($settingsService->getCategorySettings('payments')['wc']['enabled']) {
                try {
                    WooCommerceService::init($settingsService);
                } catch (ContainerException $e) {
                }
            }
        }

        $menuItems = new Menu($settingsService);
        if (is_admin()) {
            // Init admin menu
            $wpMenu = new Submenu(
                new SubmenuPageHandler($settingsService),
                $menuItems()
            );
            $wpMenu->init();

            // Add TinyMCE button for shortcode generator
            ButtonService::renderButton();
        } else {
            add_shortcode('ameliabooking', [ShortcodeService\BookingShortcodeService::class, 'shortcodeHandler']);
            add_shortcode('ameliasearch', [ShortcodeService\SearchShortcodeService::class, 'shortcodeHandler']);
            add_shortcode('ameliacatalog', [ShortcodeService\CatalogShortcodeService::class, 'shortcodeHandler']);
        }
    }

    public static function adminInit()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        if (AMELIA_VERSION !== $settingsService->getSetting('activation', 'version')) {
            $settingsService->setSetting('activation', 'version', AMELIA_VERSION);

            require_once ABSPATH . 'wp-admin/includes/plugin.php';

            deactivate_plugins(AMELIA_PLUGIN_SLUG);
            activate_plugin(AMELIA_PLUGIN_SLUG);
        }
    }

    /**
     * @param $networkWide
     */
    public static function activation($networkWide)
    {
        load_plugin_textdomain('wpamelia', false, plugin_basename(__DIR__) . '/languages/' . get_locale() . '/');

        // Check PHP version
        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50500) {
            deactivate_plugins(AMELIA_PLUGIN_SLUG);
            wp_die(
                BackendStrings::getCommonStrings()['php_version_message'],
                BackendStrings::getCommonStrings()['php_version_title'],
                ['response' => 200, 'back_link' => TRUE]
            );
        }
        //Network activation
        if ($networkWide && function_exists('is_multisite') && is_multisite()) {
            Infrastructure\WP\InstallActions\ActivationMultisite::init();
        }

        Infrastructure\WP\InstallActions\ActivationDatabaseHook::init();
    }
}

/** Isolate API calls */
add_action('wp_ajax_wpamelia_api', [Plugin::class, 'wpAmeliaApiCall']);
add_action('wp_ajax_nopriv_wpamelia_api', [Plugin::class, 'wpAmeliaApiCall']);

/** Init the plugin */
add_action('plugins_loaded', [Plugin::class, 'init']);

add_action('admin_init', [Plugin::class, 'adminInit']);

/** Activation hooks */
register_activation_hook(__FILE__, [Plugin::class, 'activation']);
register_activation_hook(__FILE__, [Infrastructure\WP\InstallActions\ActivationRolesHook::class, 'init']);
register_activation_hook(__FILE__, [Infrastructure\WP\InstallActions\ActivationSettingsHook::class, 'init']);

/** Activation hook for new site on multisite setup */
add_action('wpmu_new_blog', [Infrastructure\WP\InstallActions\ActivationNewSiteMultisite::class, 'init']);

/** Define the API for updating checking */
add_filter('pre_set_site_transient_update_plugins', [Infrastructure\WP\InstallActions\AutoUpdateHook::class, 'checkUpdate'], 21, 1);

/** Define the alternative response for information checking */
add_filter('plugins_api', [Infrastructure\WP\InstallActions\AutoUpdateHook::class, 'checkInfo'], 20, 3);