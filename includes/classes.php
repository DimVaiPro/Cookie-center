<?php
/**
 * Φόρτωση όλων των κλάσεων του plugin.
 *
 * Αυτό το αρχείο κάνει require_once όλα τα αρχεία κλάσεων.
 *
 * @package CookieCenter
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CC_PLUGIN_DIR . 'includes/classes/CC_Settings.php';
