<?php
/**
 * Απεγκατάσταση plugin — Κέντρο Συγκατάθεσης Cookies.
 *
 * Εκτελείται μόνο κατά την απεγκατάσταση (delete) του plugin.
 * Καθαρίζει όλα τα δεδομένα που δημιούργησε το plugin.
 *
 * @package CookieCenter
 */

// Αν δεν κλήθηκε από το WordPress, τερμάτισε
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Αφαίρεση custom capabilities από τους ρόλους
$roles_to_clean = [ 'administrator', 'editor' ];
foreach ( $roles_to_clean as $role_name ) {
	$role = get_role( $role_name );
	if ( $role instanceof WP_Role ) {
		$role->remove_cap( 'cc_manage_cookie_center_settings' );
	}
}

// Διαγραφή options που δημιούργησε το plugin
delete_option( 'cc_cookie_categories' );
delete_option( 'cc_banner_texts' );
delete_option( 'cc_gtm_code' );
