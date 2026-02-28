<?php
/**
 * Κώδικας εγκατάστασης plugin.
 *
 * Εκτελείται κατά την ενεργοποίηση του plugin μέσω του activation hook.
 *
 * @package CookieCenter
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Κύρια συνάρτηση εγκατάστασης.
 *
 * @return void
 */
function cc_install(): void {
	cc_add_custom_capabilities();
	cc_initialize_cookie_options();
	cc_initialize_banner_texts();
}

/**
 * Προσθήκη custom capability cc_manage_cookie_center_settings στους ρόλους
 * administrator και editor.
 *
 * @return void
 */
function cc_add_custom_capabilities(): void {
	$roles = [ 'administrator', 'editor' ];

	foreach ( $roles as $role_name ) {
		$role = get_role( $role_name );
		if ( $role instanceof WP_Role ) {
			$role->add_cap( 'cc_manage_cookie_center_settings' );
		}
	}
}

/**
 * Αφαίρεση custom capability cc_manage_cookie_center_settings από τους ρόλους
 * administrator και editor.
 *
 * Καλείται κατά την απενεργοποίηση του plugin.
 *
 * @return void
 */
function cc_remove_custom_capabilities(): void {
	$roles = [ 'administrator', 'editor' ];

	foreach ( $roles as $role_name ) {
		$role = get_role( $role_name );
		if ( $role instanceof WP_Role ) {
			$role->remove_cap( 'cc_manage_cookie_center_settings' );
		}
	}
}

/**
 * Αρχικοποίηση επιλογών cookies στο options table.
 *
 * Δημιουργεί τα default options αν δεν υπάρχουν ήδη,
 * ή ενημερώνει τα υπάρχοντα με τυχόν νέες κατηγορίες (union).
 *
 * @return void
 */
function cc_initialize_cookie_options(): void {
	// Χρειαζόμαστε την κλάση CC_Settings για τα defaults
	if ( ! class_exists( 'CC_Settings' ) ) {
		require_once CC_PLUGIN_DIR . 'includes/classes/CC_Settings.php';
	}


	$defaults = CC_Settings::get_default_cookie_categories();
	$saved    = get_option( CC_Settings::OPTION_COOKIE_CATEGORIES, [] );

	if ( empty( $saved ) || ! is_array( $saved ) ) {
		// Πρώτη εγκατάσταση — αποθήκευση defaults
		update_option( CC_Settings::OPTION_COOKIE_CATEGORIES, $defaults );
	} else {
		// Union: προσθήκη νέων κατηγοριών χωρίς διαγραφή παλιών
		$merged = array_merge( $defaults, $saved );
		update_option( CC_Settings::OPTION_COOKIE_CATEGORIES, $merged );
	}
}

/**
 * Αρχικοποίηση κειμένων banner στο options table.
 *
 * Δημιουργεί τα default κείμενα αν δεν υπάρχουν ήδη.
 *
 * @return void
 */
function cc_initialize_banner_texts(): void {
	if ( ! class_exists( 'CC_Settings' ) ) {
		require_once CC_PLUGIN_DIR . 'includes/classes/CC_Settings.php';
	}

	$saved = get_option( CC_Settings::OPTION_BANNER_TEXTS, [] );

	if ( empty( $saved ) || ! is_array( $saved ) ) {
		update_option( CC_Settings::OPTION_BANNER_TEXTS, CC_Settings::get_default_banner_texts() );
	}
}
