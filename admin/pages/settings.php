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

$categories = CC_Settings::get_cookie_categories();
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<!-- Κατηγορίες Cookies -->
	<h2><?php _e( 'Κατηγορίες Cookies', 'cookie-center' ); ?></h2>
	<p class="description"><?php _e( 'Ενεργοποιήστε ή απενεργοποιήστε κατηγορίες cookies και επεξεργαστείτε τα στοιχεία τους.', 'cookie-center' ); ?></p>

	<table class="widefat fixed striped" id="cc-cookie-categories-table">
		<thead>
			<tr>
				<th style="width:10ch;"><?php _e( 'Εμφάνιση στο Banner', 'cookie-center' ); ?></th>
				<th style="width:26ch;"><?php _e( 'Όνομα κατηγορίας cookie (Storage Type)', 'cookie-center' ); ?></th>
				<th ><?php _e( 'Εμφανιζόμενο Όνομα', 'cookie-center' ); ?></th>
				<th ><?php _e( 'Περιγραφή', 'cookie-center' ); ?></th>
				<th ><?php _e( 'Display Name', 'cookie-center' ); ?></th>
				<th ><?php _e( 'Description', 'cookie-center' ); ?></th>
				<th style="width:18ch;"><?php _e( 'Προεπιλεγμένο', 'cookie-center' ); ?></th>
				<th style="width:18ch;"><?php _e( 'Απενεργοποιημένο', 'cookie-center' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $categories as $key => $cat ) : ?>
				<tr data-category="<?php echo esc_attr( $key ); ?>">
					<td>
						<input type="checkbox"
							class="cc-field-enabled"
							<?php checked( ! empty( $cat['enabled'] ) ); ?>
						/>
					</td>
					<td>
						<code><?php echo esc_html( $cat['name'] ); ?></code>
					</td>
					<td>
						<input type="text"
							class="cc-field-display_name regular-text"
							value="<?php echo esc_attr( $cat['display_name'] ?? '' ); ?>"
							style="width:100%;"
						/>
					</td>
					<td>
						<textarea
							class="cc-field-description large-text"
						rows="3"
							style="width:100%;"
						><?php echo esc_textarea( $cat['description'] ?? '' ); ?></textarea>
					</td>
					<td>
						<input type="text"
							class="cc-field-display_name_en regular-text"
							value="<?php echo esc_attr( $cat['display_name_en'] ?? '' ); ?>"
							style="width:100%;"
						/>
					</td>
					<td>
						<textarea
							class="cc-field-description_en large-text"
						rows="3"
							style="width:100%;"
						><?php echo esc_textarea( $cat['description_en'] ?? '' ); ?></textarea>
					</td>
					<td>
						<input type="checkbox"
							class="cc-field-preselected"
							<?php checked( ! empty( $cat['preselected'] ) ); ?>
						/>
					</td>
					<td>
						<input type="checkbox"
							class="cc-field-disabled"
							<?php checked( ! empty( $cat['disabled'] ) ); ?>
						/>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p class="submit">
		<button type="button" class="button button-primary" id="cc-save-categories">
			<?php _e( 'Αποθήκευση Κατηγοριών', 'cookie-center' ); ?>
		</button>
		<button type="button" class="button button-secondary" id="cc-reset-categories">
			<?php _e( 'Επαναφορά Προεπιλογών', 'cookie-center' ); ?>
		</button>
		<span class="spinner" id="cc-categories-spinner"></span>
	</p>

	<div id="cc-categories-notice" style="display:none;"></div>

	<hr />

	<!-- Κείμενα και εμφάνιση Banner -->
	<h2><?php _e( 'Κείμενα και εμφάνιση', 'cookie-center' ); ?></h2>
	<p class="description"><?php _e( 'Προτιμήσεις για τα κείμενα, τα κουμπιά και τα χρώματα που εμφανίζονται στο cookie consent banner.', 'cookie-center' ); ?></p>

	<?php $banner_texts = CC_Settings::get_banner_texts(); ?>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><label for="cc_banner_text"><?php _e( 'Κύριο Κείμενο Banner', 'cookie-center' ); ?></label></th>
				<td>
					<?php
					wp_editor(
						$banner_texts['banner_text'] ?? '',
						'cc_banner_text',
						[
							'textarea_name' => 'banner_text',
							'textarea_rows' => 5,
							'media_buttons' => false,
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cc_banner_text_en"><?php _e( 'Κύριο Κείμενο Banner (English)', 'cookie-center' ); ?></label></th>
				<td>
					<?php
					wp_editor(
						$banner_texts['banner_text_en'] ?? '',
						'cc_banner_text_en',
						[
							'textarea_name' => 'banner_text_en',
							'textarea_rows' => 5,
							'media_buttons' => false,
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cc-btn-accept-all"><?php _e( 'Κουμπί «Αποδοχή Όλων»', 'cookie-center' ); ?></label></th>
				<td>
					<input type="text" id="cc-btn-accept-all" name="btn_accept_all" class="regular-text" value="<?php echo esc_attr( $banner_texts['btn_accept_all'] ?? '' ); ?>" />
					<input type="text" id="cc-btn-accept-all-en" name="btn_accept_all_en" class="regular-text" value="<?php echo esc_attr( $banner_texts['btn_accept_all_en'] ?? '' ); ?>" placeholder="English" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cc-btn-accept-selected"><?php _e( 'Κουμπί «Αποδοχή Επιλεγμένων»', 'cookie-center' ); ?></label></th>
				<td>
					<input type="text" id="cc-btn-accept-selected" name="btn_accept_selected" class="regular-text" value="<?php echo esc_attr( $banner_texts['btn_accept_selected'] ?? '' ); ?>" />
					<input type="text" id="cc-btn-accept-selected-en" name="btn_accept_selected_en" class="regular-text" value="<?php echo esc_attr( $banner_texts['btn_accept_selected_en'] ?? '' ); ?>" placeholder="English" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cc-btn-reject-all"><?php _e( 'Κουμπί «Απόρριψη Όλων»', 'cookie-center' ); ?></label></th>
				<td>
					<input type="text" id="cc-btn-reject-all" name="btn_reject_all" class="regular-text" value="<?php echo esc_attr( $banner_texts['btn_reject_all'] ?? '' ); ?>" />
					<input type="text" id="cc-btn-reject-all-en" name="btn_reject_all_en" class="regular-text" value="<?php echo esc_attr( $banner_texts['btn_reject_all_en'] ?? '' ); ?>" placeholder="English" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cc-bg-color"><?php _e( 'Χρώμα φόντου Banner', 'cookie-center' ); ?></label></th>
				<td>
					<input type="color" id="cc-bg-color" name="bg_color" value="<?php echo esc_attr( $banner_texts['bg_color'] ?? '#ffffff' ); ?>" />
					<p class="description"><?php _e( 'Το χρώμα φόντου του cookie consent banner.', 'cookie-center' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cc-accent-color"><?php _e( 'Κύριο χρώμα (accent color)', 'cookie-center' ); ?></label></th>
				<td>
					<input type="color" id="cc-accent-color" name="accent_color" value="<?php echo esc_attr( $banner_texts['accent_color'] ?? '#0073aa' ); ?>" />
					<p class="description"><?php _e( 'Χρώμα για τη γραμμή στην κορυφή του banner, το κουμπί «Αποδοχή Όλων» και το περίγραμμα του «Αποδοχή Επιλεγμένων».', 'cookie-center' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<button type="button" class="button button-primary" id="cc-save-banner-texts">
			<?php _e( 'Αποθήκευση Κειμένων και Εμφάνισης', 'cookie-center' ); ?>
		</button>
		<span class="spinner" id="cc-banner-texts-spinner"></span>
	</p>

	<div id="cc-banner-texts-notice" style="display:none;"></div>

	<hr />

	<!-- Google Tag Manager -->
	<h2><?php _e( 'Google Tag Manager', 'cookie-center' ); ?></h2>
	<p class="description">
		<?php _e( 'Επικολλήστε εδώ τον κώδικα Google Tag Manager (μόνο το &lt;script&gt; που τοποθετείται στο &lt;head&gt;). Αυτή η ενότητα λειτουργεί αποκλειστικά με κώδικα Google Tag Manager (GTM) και δεν υποστηρίζει κώδικα Google Analytics ή άλλου είδους tracking κώδικα. Χρησιμοποιείτε τον κώδικα Google Tag Manager (&lt;!-- Google Tag Manager --&gt;) κι όχι τον κώδικα gtag.js (&lt;!-- Google tag (gtag.js) --&gt;)', 'cookie-center' ); ?>
	</p>

	<?php $gtm_code = CC_Settings::get_gtm_code(); ?>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><label for="cc-gtm-code"><?php _e( 'Κώδικας GTM', 'cookie-center' ); ?></label></th>
				<td>
					<textarea id="cc-gtm-code" name="gtm_code" class="large-text" rows="12" placeholder="<!-- Google Tag Manager -->
<script></script>"><?php echo esc_textarea( $gtm_code ); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="description">
        <?php _e( 'Ο κώδικας αυτός τοποθετείται αυτόματα μετά το "Consent Default script" που διαχειρίζεται τη συγκατάθεση του χρήστη. Το πρόσθετο ορίζει ήδη το αντικείμενο window.dataLayer και τη συνάρτηση gtag(), οπότε δεν χρειάζεται να τα ορίσετε ξανά αν τυχόν τα χρησιμοποιείτε για αποστολή δεδομένων στον Tag Manager.', 'cookie-center' ); ?>
    </p>
	<p class="submit">
		<button type="button" class="button button-primary" id="cc-save-gtm-code">
			<?php _e( 'Αποθήκευση Google Tag Manager', 'cookie-center' ); ?>
		</button>
		<span class="spinner" id="cc-gtm-spinner"></span>
	</p>

	<div id="cc-gtm-notice" style="display:none;"></div>

	<hr />

	<!-- Τεκμηρίωση -->
	<h2><?php _e( 'Τεκμηρίωση', 'cookie-center' ); ?></h2>

	<h3><?php _e( 'Έλεγχος λειτουργίας', 'cookie-center' ); ?></h3>
	<p>
		<?php _e( 'Για να διαπιστώσετε ότι το πρόσθετο λειτουργεί σωστά, ανοίξτε τα Developer Tools του browser (F12) και ελέγξτε τα παρακάτω:', 'cookie-center' ); ?>
	</p>
	<ul style="list-style: disc; margin-left: 2em;">
		<li>
			<strong><?php _e( 'window.dataLayer', 'cookie-center' ); ?></strong> &mdash;
			<?php _e( 'Στην καρτέλα Console, εκτελέστε την εντολή', 'cookie-center' ); ?>
			<code>window.dataLayer</code>
			<?php _e( 'για να δείτε όλα τα events που έχουν καταγραφεί, συμπεριλαμβανομένου του event consent default (πριν το κλείσιμο του banner) και του event consent update (μετά το κλείσιμο).', 'cookie-center' ); ?>
		</li>
		<li>
			<strong><?php _e( 'localStorage – consentMode', 'cookie-center' ); ?></strong> &mdash;
			<?php _e( 'Στην καρτέλα Application &rsaquo; Local Storage, αναζητήστε το κλειδί', 'cookie-center' ); ?>
			<code>consentMode</code>
			<?php _e( 'για να επαληθεύσετε ότι οι επιλογές του χρήστη έχουν αποθηκευτεί σωστά μετά το κλείσιμο του banner.', 'cookie-center' ); ?>
		</li>
	</ul>

	<h3><?php _e( 'Google Documentation', 'cookie-center' ); ?></h3>
	<ul style="list-style: disc; margin-left: 2em;">
		<li>
			<a href="https://developers.google.com/tag-platform/security/guides/consent?consentmode=advanced" target="_blank" rel="noopener noreferrer">
				<?php _e( 'Consent Mode – Οδηγός υλοποίησης (Google Tag Platform)', 'cookie-center' ); ?>
			</a>
            <div>
                <?php _e( 'Σημειώνεται ότι αυτό το πρόσθετο χρησιμοποιεί Advanced Consent Mode και τη χρήση του Google Tag Manager (όχι το gtag.js).', 'cookie-center' ); ?>
            </div>
		</li>
		<li>
			<a href="https://support.google.com/tagmanager/answer/10718549?hl=en" target="_blank" rel="noopener noreferrer">
				<?php _e( 'Κατηγορίες cookies και τύποι συγκατάθεσης (Google Tag Manager)', 'cookie-center' ); ?>
			</a>
		</li>
		<li>
			<a href="https://support.google.com/analytics/answer/13802165?hl=en" target="_blank" rel="noopener noreferrer">
				<?php _e( 'Πώς επηρεάζουν τα Google Analytics οι επιλογές συγκατάθεσης του χρήστη (Google Analytics)', 'cookie-center' ); ?>
			</a>
		</li>
	</ul>

</div>
