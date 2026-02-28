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
