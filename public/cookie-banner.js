/**
 * Cookie Consent Banner — Διαχείριση εμφάνισης, αλληλεπίδρασης και αποθήκευσης συγκατάθεσης.
 *
 * Module script: εκτελείται μετά το DOM parsing (deferred by default).
 * Χρησιμοποιεί localStorage('consentMode') για αποθήκευση επιλογών χρήστη.
 *
 * @package CookieCenter
 */

const dialog = document.getElementById( 'cookie-consent-banner' );

if ( dialog ) {
	const checkboxes = dialog.querySelectorAll( 'input[type="checkbox"]' );

	// Αποδοχή Όλων — τσεκάρει όλα τα checkboxes
	dialog.querySelector( 'button[value="accept-all"]' )?.addEventListener( 'click', () => {
		checkboxes.forEach( ( cb ) => {
			cb.checked = true;
		} );
	} );

	// Απόρριψη Όλων — αποεπιλέγει όλα τα checkboxes (ακόμα και τα disabled)
	dialog.querySelector( 'button[value="reject-all"]' )?.addEventListener( 'click', () => {
		checkboxes.forEach( ( cb ) => {
			cb.checked = false;
		} );
	} );

	// Αποθήκευση συγκατάθεσης κατά το κλείσιμο του dialog
	dialog.addEventListener( 'close', () => {
		const userConsent = {};

		checkboxes.forEach( ( cb ) => {
			userConsent[ cb.name ] = cb.checked ? 'granted' : 'denied';
		} );

		// Συγχώνευση με το ccNullConsent (όλα denied) ώστε οι κατηγορίες που δεν
		// εμφανίζονται στο banner να παραμένουν denied στο αποθηκευμένο αντικείμενο
		const consentMode = Object.assign( {}, window.ccNullConsent || {}, userConsent );

		// Ενημέρωση Google Tag Manager consent (αν υπάρχει)
		if ( typeof gtag === 'function' ) {
			gtag( 'consent', 'update', consentMode );
		}

		// Αποθήκευση στο localStorage
		localStorage.setItem( 'consentMode', JSON.stringify( consentMode ) );
		console.log( 'Cookie consent saved:', dialog.returnValue, consentMode );
	} );
}

/**
 * Εμφανίζει το cookie consent banner.
 *
 * Μπορεί να κληθεί οποιαδήποτε στιγμή για επανεμφάνιση του banner,
 * π.χ. από σύνδεσμο "Διαχείριση cookies" στο footer.
 *
 * @returns {void}
 */
function ccShowBanner() {
	if ( ! dialog || dialog.open ) {
		return;
	}

	dialog.show();
	dialog.focus(); // Εστίαση στο dialog ώστε ο screen reader να διαβάσει τον τίτλο πριν την πλοήγηση
}

// Έκθεση της συνάρτησης globally ώστε να καλείται από οπουδήποτε στη σελίδα
window.ccShowBanner = ccShowBanner;

// Αυτόματη εμφάνιση μόνο αν δεν υπάρχει αποθηκευμένη συγκατάθεση
if ( dialog && localStorage.getItem( 'consentMode' ) === null ) {
	ccShowBanner();
}
