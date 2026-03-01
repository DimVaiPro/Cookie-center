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
	// Εμφάνιση dialog μόνο αν δεν υπάρχει αποθηκευμένη συγκατάθεση
	if ( localStorage.getItem( 'consentMode' ) === null ) {
		dialog.show();
		dialog.focus(); // Εστίαση στο dialog ώστε ο screen reader να διαβάσει τον τίτλο πριν την πλοήγηση

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
			const consentMode = {};

			checkboxes.forEach( ( cb ) => {
				consentMode[ cb.name ] = cb.checked ? 'granted' : 'denied';
			} );

			// Ενημέρωση Google Tag Manager consent (αν υπάρχει)
			if ( typeof gtag === 'function' ) {
				gtag( 'consent', 'update', consentMode );
			}

			// Αποθήκευση στο localStorage
			localStorage.setItem( 'consentMode', JSON.stringify( consentMode ) );
			console.log( 'Cookie consent saved:', dialog.returnValue, consentMode );
		} );
	}
}
