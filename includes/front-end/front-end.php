<?php
/**
 * Frontend λειτουργίες: φόρτωση assets, GTM scripts και rendering του cookie banner.
 *
 * @package CookieCenter
 */

declare(strict_types=1);

// Αποτροπή απευθείας πρόσβασης
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'cc_enqueue_frontend_assets' );
add_action( 'wp_head', 'cc_output_gtm_head_scripts', 3 );   // Προτεραιότητα 3 ώστε να είναι πριν τα περισσότερα άλλα scripts
add_action( 'wp_footer', 'cc_render_cookie_banner' );
add_filter( 'script_loader_tag', 'cc_add_module_type_attribute', 10, 3 );

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
 * Τοποθέτηση Consent Default script και Google Tag Manager κώδικα στο <head>.
 *
 * Εκτελείται με priority 3 στο wp_head ώστε ο GTM κώδικας
 * να βρίσκεται όσο πιο ψηλά γίνεται στο <head>.
 *
 * @return void
 */
function cc_output_gtm_head_scripts(): void {
	$gtm_code = CC_Settings::get_gtm_code();

	if ( empty( trim( $gtm_code ) ) ) {
		return;
	}

	// Consent Default script — πρέπει να εκτελεστεί πριν τον GTM
	// Δημιουργία JS object με 'denied' για όλα τα κλειδιά βάσει των default κατηγοριών
	$denied_defaults = array_fill_keys(
		array_keys( CC_Settings::get_default_cookie_categories() ),
		'denied'
	);
	$denied_json = wp_json_encode( $denied_defaults );
	?>
<script>
window.dataLayer = window.dataLayer || [];
window.gtag = window.gtag || function() { dataLayer.push(arguments); };
if (localStorage.getItem('consentMode') === null) {
	gtag('consent', 'default', <?php echo $denied_json; ?>);
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

	include CC_PLUGIN_DIR . 'includes/front-end/cookie-banner.php';
}
