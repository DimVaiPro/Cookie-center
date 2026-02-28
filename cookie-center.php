<?php
/**
 * Plugin Name: Κέντρο Συγκατάθεσης Cookies
 * Plugin URI:  https://computerstudio.gr/cookie-center TODO: Update with actual github URL
 * Description: Ολοκληρωμένο σύστημα διαχείρισης συγκατάθεσης cookies για τον ιστότοπό σας. Παρέχει cookie consent banner, διαχείριση κατηγοριών cookies και ενσωμάτωση Google Tag Manager.
 * Version:     1.0.0
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
define( 'CC_PLUGIN_VERSION', '1.0.0' );
define( 'CC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Φόρτωση κλάσεων
require_once CC_PLUGIN_DIR . 'includes/classes.php';

// Φόρτωση admin αρχείων μόνο στο admin panel
if ( is_admin() ) {
	require_once CC_PLUGIN_DIR . 'admin/menu.php';
}

// Φόρτωση frontend λειτουργιών (cookie banner)
if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'cc_enqueue_frontend_assets' );
	add_action( 'wp_head', 'cc_output_gtm_head_scripts', 3 );   // Priority 3
	add_action( 'wp_footer', 'cc_render_cookie_banner' );
	add_filter( 'script_loader_tag', 'cc_add_module_type_attribute', 10, 3 );
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

/**
 * Φόρτωση CSS/JS για το cookie consent banner στο frontend.
 *
 * @return void
 */
function cc_enqueue_frontend_assets(): void {
	wp_enqueue_style(
		'cc-cookie-banner',
		CC_PLUGIN_URL . 'public/cookie-banner.css',
		[],
		CC_PLUGIN_VERSION
	);

	wp_enqueue_script(
		'cc-cookie-banner',
		CC_PLUGIN_URL . 'public/cookie-banner.js',
		[],
		CC_PLUGIN_VERSION,
		true
	);
}

/**
 * Προσθήκη type="module" στο script tag του cookie banner.
 *
 * @param string $tag    Το HTML tag του script.
 * @param string $handle Το handle του script.
 * @param string $src    Η διεύθυνση URL του script.
 * @return string Το τροποποιημένο tag.
 */
function cc_add_module_type_attribute( string $tag, string $handle, string $src ): string {
	if ( 'cc-cookie-banner' !== $handle ) {
		return $tag;
	}

	return str_replace( '<script ', '<script type="module" ', $tag );
}

/**
 * Τοποθέτηση consent default script και Google Tag Manager κώδικα στο <head>.
 *
 * Εκτελείται με priority 1 στο wp_head ώστε ο GTM κώδικας
 * να βρίσκεται όσο πιο ψηλά γίνεται στο <head>.
 *
 * @return void
 */
function cc_output_gtm_head_scripts(): void {
	$gtm_code = CC_Settings::get_gtm_code();

	if ( empty( trim( $gtm_code ) ) ) {
		return;
	}

	// Consent default script — πρέπει να εκτελεστεί πριν τον GTM
	?>
<script>
window.dataLayer = window.dataLayer || [];
function gtag() { dataLayer.push(arguments); }
if (localStorage.getItem('consentMode') === null) {
	gtag('consent', 'default', {});
} else {
	gtag('consent', 'default', JSON.parse(localStorage.getItem('consentMode')));
}
</script>
<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ο κώδικας GTM εισάγεται από τον διαχειριστή
	echo $gtm_code . "\n";
}

/**
 * Εμφάνιση του cookie consent banner στο frontend (wp_footer).
 *
 * Φιλτράρει μόνο τις enabled κατηγορίες και φορτώνει το template.
 *
 * @return void
 */
function cc_render_cookie_banner(): void {
	$all_categories = CC_Settings::get_cookie_categories();

	// Εμφάνιση μόνο κατηγοριών με enabled:true
	$categories = array_filter( $all_categories, function ( $cat ) {
		return ! empty( $cat['enabled'] );
	} );

	// Localize κατηγορίες βάσει γλώσσας (WPML ή default)
	$categories = array_map( [ 'CC_Settings', 'localize_category' ], $categories );

	// Localized κείμενα banner
	$texts = CC_Settings::get_localized_banner_texts();

	include CC_PLUGIN_DIR . 'templates/cookie-banner.php';
}
