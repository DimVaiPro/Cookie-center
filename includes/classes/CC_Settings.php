<?php
/**
 * Κλάση CC_Settings — Διαχείριση ρυθμίσεων plugin.
 *
 * Παρέχει getters/setters για τα options του plugin
 * καθώς και τo default array κατηγοριών cookies.
 *
 * @package CookieCenter
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CC_Settings {

	/**
	 * Προεπιλεγμένη γλώσσα του plugin.
	 *
	 * Χρησιμοποιείται ως fallback όταν δεν είναι εγκατεστημένο το WPML.
	 * Άλλαξε σε 'en' για δοκιμή αγγλικής εμφάνισης.
	 */
	const DEFAULT_LANGUAGE = 'el';

	/**
	 * Option name για τις κατηγορίες cookies.
	 */
	const OPTION_COOKIE_CATEGORIES = 'cc_cookie_categories';

	/**
	 * Επιστρέφει το default array κατηγοριών cookies.
	 *
	 * Κάθε κατηγορία χρησιμοποιεί το name ως key.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_default_cookie_categories(): array {
		return [
			'required_storage'        => [
				'name'            => 'required_storage',
				'display_name'    => 'Απαραίτητα',
				'display_name_en' => 'Necessary',
				'description'     => 'Ενεργοποιεί αποθήκευση που είναι απαραίτητη για τη λειτουργία του site (π.χ. authentication, security).',
				'description_en'  => 'Enables storage that is required for the operation of the website or app, such as authentication and security.',
				'enabled'         => true,
				'preselected'     => true,
				'disabled'        => true,
			],
			'security_storage'        => [
				'name'            => 'security_storage',
				'display_name'    => 'Ασφάλεια',
				'display_name_en' => 'Security',
				'description'     => 'Ενεργοποιεί αποθήκευση σχετική με ασφάλεια (authentication, fraud prevention κλπ).',
				'description_en'  => 'Enables storage related to security such as authentication functionality, fraud prevention, and other user protection.',
				'enabled'         => false,
				'preselected'     => false,
				'disabled'        => false,
			],
			'functionality_storage'   => [
				'name'            => 'functionality_storage',
				'display_name'    => 'Λειτουργικότητα',
				'display_name_en' => 'Functionality',
				'description'     => 'Ενεργοποιεί αποθήκευση που υποστηρίζει λειτουργικότητα του site (π.χ. ρυθμίσεις γλώσσας).',
				'description_en'  => 'Enables storage that supports the functionality of the website or app e.g. language settings.',
				'enabled'         => false,
				'preselected'     => false,
				'disabled'        => false,
			],
			'personalization_storage' => [
				'name'            => 'personalization_storage',
				'display_name'    => 'Εξατομίκευση',
				'display_name_en' => 'Preferences',
				'description'     => 'Ενεργοποιεί αποθήκευση σχετική με εξατομίκευση (π.χ. προτάσεις video), προτιμήσεις χρήστη κ.λπ.',
				'description_en'  => 'Enables storage related to personalization e.g. video recommendations, user preferences, etc.',
				'enabled'         => false,
				'preselected'     => false,
				'disabled'        => false,
			],
			'analytics_storage'       => [
				'name'            => 'analytics_storage',
				'display_name'    => 'Στατιστικά',
				'display_name_en' => 'Analytics',
				'description'     => 'Ενεργοποιεί αποθήκευση σχετική με στατιστικά χρήσης (π.χ. πλήθος προβολών σελίδων, διάρκεια παραμονής).',
				'description_en'  => 'Enables storage (such as cookies) related to analytics e.g. pages visited, visit duration.',
				'enabled'         => true,
				'preselected'     => false,
				'disabled'        => false,
			],
			'ad_storage'              => [
				'name'            => 'ad_storage',
				'display_name'    => 'Διαφημίσεις',
				'display_name_en' => 'Marketing',
				'description'     => 'Ενεργοποιεί αποθήκευση σχετική με διαφημίσεις.',
				'description_en'  => 'Enables storage (such as cookies) related to advertising.',
				'enabled'         => false,
				'preselected'     => false,
				'disabled'        => false,
			],
			'ad_user_data'            => [
				'name'            => 'ad_user_data',
				'display_name'    => 'Δεδομένα χρήστη για διαφημίσεις',
				'display_name_en' => 'Ad User Data',
				'description'     => 'Συγκατάθεση για αποστολή δεδομένων χρήστη στη Google για διαφημιστικούς σκοπούς.',
				'description_en'  => 'Sets consent for sending user data related to advertising to Google.',
				'enabled'         => false,
				'preselected'     => false,
				'disabled'        => false,
			],
			'ad_personalization'      => [
				'name'            => 'ad_personalization',
				'display_name'    => 'Εξατομικευμένες διαφημίσεις',
				'display_name_en' => 'Ad Personalization',
				'description'     => 'Συγκατάθεση για εξατομικευμένες διαφημίσεις.',
				'description_en'  => 'Sets consent for personalized advertising.',
				'enabled'         => false,
				'preselected'     => false,
				'disabled'        => false,
			],
		];
	}

	/**
	 * Επιστρέφει τις αποθηκευμένες κατηγορίες cookies.
	 *
	 * Αν δεν υπάρχουν αποθηκευμένες, επιστρέφει τα defaults.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_cookie_categories(): array {
		$saved = get_option( self::OPTION_COOKIE_CATEGORIES, [] );

		if ( empty( $saved ) || ! is_array( $saved ) ) {
			return self::get_default_cookie_categories();
		}

		return $saved;
	}

	/**
	 * Αποθηκεύει τις κατηγορίες cookies.
	 *
	 * @param array<string, array<string, mixed>> $categories Οι κατηγορίες cookies.
	 * @return bool True αν η αποθήκευση ήταν επιτυχής.
	 */
	public static function save_cookie_categories( array $categories ): bool {
		return update_option( self::OPTION_COOKIE_CATEGORIES, $categories );
	}

	/**
	 * Option name για τα κείμενα του banner.
	 */
	const OPTION_BANNER_TEXTS = 'cc_banner_texts';

	/**
	 * Επιστρέφει τα default κείμενα του banner.
	 *
	 * @return array<string, string>
	 */
	public static function get_default_banner_texts(): array {
		return [
			'banner_text'          => "<p>Χρησιμοποιούμε cookies για να βελτιώσουμε την εμπειρία σας στον ιστότοπό μας.</p>\n<p>Διαβάστε περισσότερα <a href=\"/cookie-policy\">εδώ.</a></p>",
			'banner_text_en'       => "<p>We use cookies to improve your experience on our website.</p>\n<p>Read more <a href=\"/cookie-policy\">here.</a></p>",
			'btn_accept_all'       => 'Αποδοχή Όλων',
			'btn_accept_all_en'    => 'Accept All',
			'btn_accept_selected'    => 'Αποδοχή Επιλεγμένων',
			'btn_accept_selected_en' => 'Accept Selected',
			'btn_reject_all'       => 'Απόρριψη Όλων',
			'btn_reject_all_en'    => 'Reject All',
			'bg_color'             => '#ffffff',
			'accent_color'         => '#0073aa',
		];
	}

	/**
	 * Επιστρέφει τα αποθηκευμένα κείμενα του banner.
	 *
	 * Αν δεν υπάρχουν αποθηκευμένα, επιστρέφει τα defaults.
	 *
	 * @return array<string, string>
	 */
	public static function get_banner_texts(): array {
		$saved = get_option( self::OPTION_BANNER_TEXTS, [] );

		if ( empty( $saved ) || ! is_array( $saved ) ) {
			return self::get_default_banner_texts();
		}

		// Συμπλήρωση τυχόν απόντων κλειδιών με defaults
		return array_merge( self::get_default_banner_texts(), $saved );
	}

	/**
	 * Αποθηκεύει τα κείμενα του banner.
	 *
	 * @param array<string, string> $texts Τα κείμενα του banner.
	 * @return bool True αν η αποθήκευση ήταν επιτυχής.
	 */
	public static function save_banner_texts( array $texts ): bool {
		return update_option( self::OPTION_BANNER_TEXTS, $texts );
	}

	/**
	 * Option name για τον κώδικα Google Tag Manager.
	 */
	const OPTION_GTM_CODE = 'cc_gtm_code';

	/**
	 * Επιστρέφει τον αποθηκευμένο κώδικα Google Tag Manager.
	 *
	 * @return string Ο GTM κώδικας (HTML/JS).
	 */
	public static function get_gtm_code(): string {
		return (string) get_option( self::OPTION_GTM_CODE, '' );
	}

	/**
	 * Αποθηκεύει τον κώδικα Google Tag Manager.
	 *
	 * @param string $code Ο GTM κώδικας (HTML/JS).
	 * @return bool True αν η αποθήκευση ήταν επιτυχής.
	 */
	public static function save_gtm_code( string $code ): bool {
		return update_option( self::OPTION_GTM_CODE, $code );
	}

	/**
	 * Επιστρέφει τον τρέχοντα κωδικό γλώσσας (2 χαρακτήρες).
	 *
	 * Αν είναι εγκατεστημένο το WPML, χρησιμοποιεί την τρέχουσα γλώσσα του.
	 * Αλλιώς, επιστρέφει το DEFAULT_LANGUAGE.
	 *
	 * @return string Κωδικός γλώσσας, π.χ. 'el' ή 'en'.
	 */
	public static function get_current_language(): string {
		// Έλεγχος WPML
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			return (string) ICL_LANGUAGE_CODE;
		}

		return self::DEFAULT_LANGUAGE;
	}

	/**
	 * Επιστρέφει τα localized κείμενα banner βάσει τρέχουσας γλώσσας.
	 *
	 * Για 'el' επιστρέφει τα πεδία χωρίς suffix,
	 * για 'en' επιστρέφει τα πεδία με suffix '_en'.
	 *
	 * @return array<string, string> Κείμενα banner στην τρέχουσα γλώσσα.
	 */
	public static function get_localized_banner_texts(): array {
		$texts = self::get_banner_texts();
		$lang  = self::get_current_language();

		if ( 'el' === $lang ) {
			return [
				'banner_text'         => $texts['banner_text'],
				'btn_accept_all'      => $texts['btn_accept_all'],
				'btn_accept_selected' => $texts['btn_accept_selected'],
				'btn_reject_all'      => $texts['btn_reject_all'],
			];
		}

		// Αγγλικά (ή οποιαδήποτε μη-ελληνική γλώσσα) — fallback σε _en
		return [
			'banner_text'         => $texts['banner_text_en'],
			'btn_accept_all'      => $texts['btn_accept_all_en'],
			'btn_accept_selected' => $texts['btn_accept_selected_en'],
			'btn_reject_all'      => $texts['btn_reject_all_en'],
		];
	}

	/**
	 * Επιστρέφει τα localized πεδία μιας κατηγορίας cookies βάσει γλώσσας.
	 *
	 * Αντικαθιστά τα display_name/description με τα αντίστοιχα _en αν χρειάζεται.
	 *
	 * @param array<string, mixed> $cat Η κατηγορία cookie.
	 * @return array<string, mixed> Η κατηγορία με localized πεδία.
	 */
	public static function localize_category( array $cat ): array {
		$lang = self::get_current_language();

		if ( 'el' !== $lang ) {
			$cat['display_name'] = $cat['display_name_en'] ?? $cat['display_name'];
			$cat['description']  = $cat['description_en'] ?? $cat['description'];
		}

		return $cat;
	}
}
