/**
 * Cookie Consent Banner — Διαχείριση εμφάνισης και αλληλεπίδρασης.
 *
 * Module script: εκτελείται μετά το DOM parsing (deferred by default).
 *
 * @package CookieCenter
 */

const dialog = document.getElementById( 'cookie-consent-banner' );

if ( dialog ) {
	// Εμφάνιση dialog (non-modal)
	dialog.show();

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

	// Console log κατά το κλείσιμο του dialog
	dialog.addEventListener( 'close', () => {
		const consent = {};
		checkboxes.forEach( ( cb ) => {
			consent[ cb.name ] = cb.checked;
		} );
		console.log( 'Cookie consent:', dialog.returnValue, consent );
	} );
}
