<?php
/**
 * Ορισμός admin menu και action links για το plugin.
 *
 * @package CookieCenter
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Καταχώρηση σελίδας στο μενού Εργαλεία (Tools).
 *
 * @return void
 */
function cc_register_admin_menu(): void {
	add_management_page(
		__( 'Κέντρο Συγκατάθεσης Cookies', 'cookie-center' ), // Τίτλος σελίδας
		__( 'Κέντρο Cookies', 'cookie-center' ),               // Τίτλος στο μενού
		'cc_manage_cookie_center_settings',                     // Custom capability
		'cookie-center',                                        // Slug σελίδας
		'cc_render_settings_page'                               // Callback εμφάνισης
	);
}
add_action( 'admin_menu', 'cc_register_admin_menu' );

/**
 * Προσθήκη link "Προτιμήσεις" στη λίστα plugins.
 *
 * @param array<string> $links Υπάρχοντα action links.
 * @return array<string> Ενημερωμένα action links.
 */
function cc_add_plugin_action_links( array $links ): array {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'tools.php?page=cookie-center' ) ),
		__( 'Προτιμήσεις', 'cookie-center' )
	);

	// Προσθήκη στην αρχή της λίστας
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links_' . CC_PLUGIN_BASENAME, 'cc_add_plugin_action_links' );

/**
 * Φόρτωση admin scripts/styles μόνο στη σελίδα ρυθμίσεων του plugin.
 *
 * @param string $hook_suffix Το hook suffix της τρέχουσας admin σελίδας.
 * @return void
 */
function cc_admin_enqueue_scripts( string $hook_suffix ): void {
	// Φόρτωση μόνο στη σελίδα tools_page_cookie-center
	if ( 'tools_page_cookie-center' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_script(
		'cc-admin',
		CC_PLUGIN_URL . 'admin/assets/admin.js',
		[],
		CC_PLUGIN_VERSION,
		true
	);

	wp_localize_script( 'cc-admin', 'ccAdmin', [
		'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
		'nonce'                => wp_create_nonce( 'cc_save_cookie_categories' ),
		'nonceResetCategories' => wp_create_nonce( 'cc_reset_cookie_categories' ),
		'nonceBannerTexts'     => wp_create_nonce( 'cc_save_banner_texts' ),
		'nonceGtmCode'         => wp_create_nonce( 'cc_save_gtm_code' ),
		'confirmReset'         => __( 'Είσαι σίγουρος/η ότι θέλεις να επαναφέρεις όλες τις κατηγορίες cookies στις προεπιλεγμένες τιμές; Τυχόν αλλαγές σου θα χαθούν.', 'cookie-center' ),
	] );

	// Ενεργοποίηση CodeMirror (native WP) για HTML syntax highlighting στο GTM textarea
	$cm_settings = wp_enqueue_code_editor( [ 'type' => 'text/html' ] );
	if ( false !== $cm_settings ) {
		// Προσθήκη placeholder στον CodeMirror editor
		$cm_settings['codemirror']['placeholder'] = "<!-- Google Tag Manager -->\n<script></script>";
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery(function() { if (document.getElementById("cc-gtm-code")) { window.ccGtmEditor = wp.codeEditor.initialize("cc-gtm-code", %s); } });',
				wp_json_encode( $cm_settings )
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'cc_admin_enqueue_scripts' );

/**
 * AJAX handler: αποθήκευση κατηγοριών cookies.
 *
 * @return void
 */
function cc_ajax_save_cookie_categories(): void {
	// Έλεγχος nonce
	check_ajax_referer( 'cc_save_cookie_categories', 'nonce' );

	// Έλεγχος δικαιωμάτων
	if ( ! current_user_can( 'cc_manage_cookie_center_settings' ) ) {
		wp_send_json_error( __( 'Δεν έχετε δικαίωμα αποθήκευσης.', 'cookie-center' ) );
	}

	$categories_json = isset( $_POST['categories'] ) ? wp_unslash( $_POST['categories'] ) : '';
	$categories      = json_decode( $categories_json, true );

	if ( ! is_array( $categories ) ) {
		wp_send_json_error( __( 'Μη έγκυρα δεδομένα.', 'cookie-center' ) );
	}

	$result = CC_Settings::save_cookie_categories( $categories );

	// Η update_option() επιστρέφει false και όταν η τιμή δεν άλλαξε,
	// οπότε θεωρούμε επιτυχία τόσο το true (ενημερώθηκε) όσο και το false-χωρίς-αλλαγή.
	$existing = CC_Settings::get_cookie_categories();
	if ( $result || ( $existing === $categories ) ) {
		wp_send_json_success( __( 'Οι κατηγορίες cookies αποθηκεύτηκαν επιτυχώς.', 'cookie-center' ) );
	} else {
		wp_send_json_error( __( 'Σφάλμα κατά την αποθήκευση.', 'cookie-center' ) );
	}
}
add_action( 'wp_ajax_cc_save_cookie_categories', 'cc_ajax_save_cookie_categories' );

/**
 * AJAX handler: επαναφορά κατηγοριών cookies στις προεπιλεγμένες τιμές.
 *
 * @return void
 */
function cc_ajax_reset_cookie_categories(): void {
	// Έλεγχος nonce
	check_ajax_referer( 'cc_reset_cookie_categories', 'nonce' );

	// Έλεγχος δικαιωμάτων
	if ( ! current_user_can( 'cc_manage_cookie_center_settings' ) ) {
		wp_send_json_error( __( 'Δεν έχετε δικαίωμα αποθήκευσης.', 'cookie-center' ) );
	}

	$defaults = CC_Settings::get_default_cookie_categories();
	CC_Settings::save_cookie_categories( $defaults );

	wp_send_json_success( __( 'Οι κατηγορίες cookies επαναφέρθηκαν στις προεπιλεγμένες τιμές.', 'cookie-center' ) );
}
add_action( 'wp_ajax_cc_reset_cookie_categories', 'cc_ajax_reset_cookie_categories' );

/**
 * AJAX handler: Αποθήκευση Κειμένων και Εμφάνισης.
 *
 * @return void
 */
function cc_ajax_save_banner_texts(): void {
	// Έλεγχος nonce
	check_ajax_referer( 'cc_save_banner_texts', 'nonce' );

	// Έλεγχος δικαιωμάτων
	if ( ! current_user_can( 'cc_manage_cookie_center_settings' ) ) {
		wp_send_json_error( __( 'Δεν έχετε δικαίωμα αποθήκευσης.', 'cookie-center' ) );
	}

	$allowed_keys = [ 'banner_text', 'banner_text_en', 'btn_accept_all', 'btn_accept_all_en', 'btn_accept_selected', 'btn_accept_selected_en', 'btn_reject_all', 'btn_reject_all_en', 'bg_color', 'accent_color' ];
	$texts        = [];

	// Κλειδιά που δέχονται HTML (κύριο κείμενο banner) — χρήση wp_kses_post αντί sanitize_textarea_field
	$html_keys = [ 'banner_text', 'banner_text_en' ];

	foreach ( $allowed_keys as $key ) {
		$raw           = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$texts[ $key ] = in_array( $key, $html_keys, true )
			? wp_kses_post( $raw )
			: sanitize_textarea_field( $raw );
	}

	$result   = CC_Settings::save_banner_texts( $texts );
	$existing = CC_Settings::get_banner_texts();

	if ( $result || ( $existing === $texts ) ) {
		wp_send_json_success( __( 'Η εμφάνιση του banner αποθηκεύτηκε επιτυχώς.', 'cookie-center' ) );
	} else {
		wp_send_json_error( __( 'Σφάλμα κατά την αποθήκευση.', 'cookie-center' ) );
	}
}
add_action( 'wp_ajax_cc_save_banner_texts', 'cc_ajax_save_banner_texts' );

/**
 * AJAX handler: αποθήκευση κώδικα Google Tag Manager.
 *
 * @return void
 */
function cc_ajax_save_gtm_code(): void {
	// Έλεγχος nonce
	check_ajax_referer( 'cc_save_gtm_code', 'nonce' );

	// Έλεγχος δικαιωμάτων
	if ( ! current_user_can( 'cc_manage_cookie_center_settings' ) ) {
		wp_send_json_error( __( 'Δεν έχετε δικαίωμα αποθήκευσης.', 'cookie-center' ) );
	}

	// Χρήση wp_unslash χωρίς sanitize για να διατηρηθεί ο HTML/JS κώδικας
	$gtm_code = isset( $_POST['gtm_code'] ) ? wp_unslash( $_POST['gtm_code'] ) : '';

	$result   = CC_Settings::save_gtm_code( $gtm_code );
	$existing = CC_Settings::get_gtm_code();

	if ( $result || ( $existing === $gtm_code ) ) {
		wp_send_json_success( __( 'Ο κώδικας Google Tag Manager αποθηκεύτηκε επιτυχώς.', 'cookie-center' ) );
	} else {
		wp_send_json_error( __( 'Σφάλμα κατά την αποθήκευση.', 'cookie-center' ) );
	}
}
add_action( 'wp_ajax_cc_save_gtm_code', 'cc_ajax_save_gtm_code' );

/**
 * Εμφάνιση της σελίδας ρυθμίσεων.
 *
 * @return void
 */
function cc_render_settings_page(): void {
	// Έλεγχος δικαιωμάτων
	if ( ! current_user_can( 'cc_manage_cookie_center_settings' ) ) {
		wp_die( esc_html__( 'Δεν έχετε δικαίωμα πρόσβασης σε αυτή τη σελίδα.', 'cookie-center' ) );
	}

	require_once CC_PLUGIN_DIR . 'admin/pages/settings.php';
}
