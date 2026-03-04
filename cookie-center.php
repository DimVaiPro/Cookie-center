<?php
/**
 * Plugin Name: Κέντρο Συγκατάθεσης Cookies
 * Plugin URI:  https://computerstudio.gr/cookie-center TODO: Update with actual github URL
 * Description: Ολοκληρωμένο σύστημα διαχείρισης συγκατάθεσης cookies για τον ιστότοπό σας. Παρέχει cookie consent banner, διαχείριση κατηγοριών cookies και ενσωμάτωση Google Tag Manager.
 * Version:     1.3
 * Author:      Computer Studio
 * Author URI:  https://computerstudio.gr
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cookie-center
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

declare(strict_types=1);

// Αποτροπή απευθείας πρόσβασης
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Σταθερές plugin
define( 'CC_PLUGIN_VERSION', '1.3' );
define( 'CC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Φόρτωση κλάσεων
require_once CC_PLUGIN_DIR . 'includes/classes.php';

// Φόρτωση admin αρχείων μόνο στο admin panel
if ( is_admin() ) {
	require_once CC_PLUGIN_DIR . 'admin/menu.php';
}

// Activation hook
register_activation_hook( __FILE__, 'cc_activate_plugin' );

// Deactivation hook
register_deactivation_hook( __FILE__, 'cc_deactivate_plugin' );

/**
 * Εκτελείται κατά την ενεργοποίηση του plugin.
 *
 * @return void
 */
function cc_activate_plugin(): void {
	require_once CC_PLUGIN_DIR . 'includes/install.php';
	cc_install();
}

/**
 * Εκτελείται κατά την απενεργοποίηση του plugin.
 *
 * @return void
 */
function cc_deactivate_plugin(): void {
	require_once CC_PLUGIN_DIR . 'includes/install.php';
	cc_remove_custom_capabilities();
}



// Φόρτωση frontend λειτουργιών (cookie banner)
if ( ! is_admin() ) {
	require_once CC_PLUGIN_DIR . 'includes/front-end/front-end.php';
}
