<?php
/**
 * Σελίδα ρυθμίσεων του plugin στο admin panel.
 *
 * @package CookieCenter
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<p><?php _e( 'Καλώς ήρθατε στις ρυθμίσεις του Κέντρου Συγκατάθεσης Cookies.', 'cookie-center' ); ?></p>

	<?php // Εδώ θα προστεθούν οι ρυθμίσεις στα επόμενα βήματα (2Β, 2Γ κ.λπ.). ?>
</div>
