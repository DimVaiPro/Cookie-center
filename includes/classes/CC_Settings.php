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
				'display_name_en' => 'Required',
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
				'display_name_en' => 'Personalization',
				'description'     => 'Ενεργοποιεί αποθήκευση σχετική με εξατομίκευση (π.χ. προτάσεις video).',
				'description_en'  => 'Enables storage related to personalization e.g. video recommendations.',
				'enabled'         => false,
				'preselected'     => false,
				'disabled'        => false,
			],
			'analytics_storage'       => [
				'name'            => 'analytics_storage',
				'display_name'    => 'Στατιστικά',
				'display_name_en' => 'Analytics',
				'description'     => 'Ενεργοποιεί αποθήκευση σχετική με analytics (π.χ. διάρκεια επίσκεψης).',
				'description_en'  => 'Enables storage (such as cookies) related to analytics e.g. visit duration.',
				'enabled'         => true,
				'preselected'     => false,
				'disabled'        => false,
			],
			'ad_storage'              => [
				'name'            => 'ad_storage',
				'display_name'    => 'Διαφημίσεις',
				'display_name_en' => 'Ads',
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
			'banner_text'          => 'Χρησιμοποιούμε cookies για να βελτιώσουμε την εμπειρία σας στον ιστότοπό μας.',
			'banner_text_en'       => 'We use cookies to improve your experience on our website.',
			'btn_accept_all'       => 'Αποδοχή όλων',
			'btn_accept_all_en'    => 'Accept All',
			'btn_accept_selected'    => 'Αποδοχή επιλεγμένων',
			'btn_accept_selected_en' => 'Accept Selected',
			'btn_reject_all'       => 'Απόρριψη όλων',
			'btn_reject_all_en'    => 'Reject All',
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
}
