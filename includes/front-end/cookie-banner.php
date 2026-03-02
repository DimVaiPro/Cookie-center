<?php
/**
 * Template για το cookie consent banner.
 *
 * Εμφανίζει ένα native HTML <dialog> με φόρμα για τη συγκατάθεση cookies.
 * Χρησιμοποιεί <form method="dialog"> ώστε τα κουμπιά submit να κλείνουν αυτόματα το dialog.
 *
 * @package CookieCenter
 * @var array<string, array<string, mixed>> $categories Ενεργοποιημένες κατηγορίες cookies (μόνο enabled:true)
 * @var array<string, string> $texts Κείμενα banner
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
// Χρώματα banner από τις ρυθμίσεις
$all_texts  = CC_Settings::get_banner_texts();
$bg_color     = $all_texts['bg_color'] ?? '#ffffff';
$accent_color = $all_texts['accent_color'] ?? '#0073aa';
?>
<dialog id="cookie-consent-banner" aria-labelledby="cc-dialog-title" aria-describedby="cc-banner-text" tabindex="-1" style="--cc-bg-color: <?php echo esc_attr( $bg_color ); ?>; --cc-accent-color: <?php echo esc_attr( $accent_color ); ?>;">
	<form method="dialog">
		<h2 id="cc-dialog-title" class="cc-sr-only"><?php _e( 'Επιλογές Συγκατάθεσης Cookies', 'cookie-center' ); ?></h2>
		<div id="cc-banner-text" class="cc-banner-text"><?php echo wp_kses_post( $texts['banner_text'] ); ?></div>

		<fieldset class="cc-categories">
			<legend class="cc-sr-only"><?php _e( 'Κατηγορίες Cookies', 'cookie-center' ); ?></legend>
			<?php foreach ( $categories as $key => $cat ) : ?>
				<label class="cc-category" title="<?php echo esc_attr( $cat['description'] ); ?>">
					<input type="checkbox"
						name="<?php echo esc_attr( $cat['name'] ); ?>"
						aria-describedby="cc-desc-<?php echo esc_attr( $cat['name'] ); ?>"
						<?php checked( ! empty( $cat['preselected'] ) ); ?>
						<?php disabled( ! empty( $cat['disabled'] ) ); ?>
					/>
					<?php echo esc_html( $cat['display_name'] ); ?>
				</label>
				<span id="cc-desc-<?php echo esc_attr( $cat['name'] ); ?>" class="cc-sr-only"><?php echo esc_html( $cat['description'] ); ?></span>
			<?php endforeach; ?>
		</fieldset>

		<div class="cc-buttons">
            <button type="submit" value="reject-all">
                <?php echo esc_html( $texts['btn_reject_all'] ); ?>
            </button>
            <button type="submit" value="accept-selected">
                <?php echo esc_html( $texts['btn_accept_selected'] ); ?>
            </button>
			<button type="submit" value="accept-all">
				<?php echo esc_html( $texts['btn_accept_all'] ); ?>
			</button>
		</div>
	</form>
</dialog>
