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
<dialog id="cookie-consent-banner">
	<form method="dialog">
		<p class="cc-banner-text"><?php echo esc_html( $texts['banner_text'] ); ?></p>

		<div class="cc-categories">
			<?php foreach ( $categories as $key => $cat ) : ?>
				<label class="cc-category" title="<?php echo esc_attr( $cat['description'] ); ?>">
					<input type="checkbox"
						name="<?php echo esc_attr( $cat['name'] ); ?>"
						<?php checked( ! empty( $cat['preselected'] ) ); ?>
						<?php disabled( ! empty( $cat['disabled'] ) ); ?>
					/>
					<?php echo esc_html( $cat['display_name'] ); ?>
				</label>
			<?php endforeach; ?>
		</div>

		<div class="cc-buttons">
			<button type="submit" value="accept-all">
				<?php echo esc_html( $texts['btn_accept_all'] ); ?>
			</button>
			<button type="submit" value="accept-selected">
				<?php echo esc_html( $texts['btn_accept_selected'] ); ?>
			</button>
			<button type="submit" value="reject-all">
				<?php echo esc_html( $texts['btn_reject_all'] ); ?>
			</button>
		</div>
	</form>
</dialog>
