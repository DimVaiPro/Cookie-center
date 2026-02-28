/**
 * Admin JavaScript — Κέντρο Συγκατάθεσης Cookies
 *
 * Διαχειρίζεται την αποθήκευση ρυθμίσεων μέσω AJAX.
 */
(function () {
	'use strict';

	/**
	 * Συλλέγει τα δεδομένα κατηγοριών cookies από τον πίνακα.
	 *
	 * @returns {Object} Αντικείμενο κατηγοριών με key το name.
	 */
	function collectCategoriesData() {
		const categories = {};
		const rows = document.querySelectorAll('#cc-cookie-categories-table tbody tr');

		rows.forEach(function (row) {
			const key = row.getAttribute('data-category');
			if (!key) return;

			categories[key] = {
				name:            key,
				display_name:    row.querySelector('.cc-field-display_name')?.value ?? '',
				display_name_en: row.querySelector('.cc-field-display_name_en')?.value ?? '',
				description:     row.querySelector('.cc-field-description')?.value ?? '',
				description_en:  row.querySelector('.cc-field-description_en')?.value ?? '',
				enabled:         row.querySelector('.cc-field-enabled')?.checked ?? false,
				preselected:     row.querySelector('.cc-field-preselected')?.checked ?? false,
				disabled:        row.querySelector('.cc-field-disabled')?.checked ?? false,
			};
		});

		return categories;
	}

	/**
	 * Εμφανίζει ειδοποίηση (success/error) στο admin.
	 *
	 * @param {string} containerId ID του container element.
	 * @param {string} message     Μήνυμα ειδοποίησης.
	 * @param {string} type        'success' ή 'error'.
	 */
	function showNotice(containerId, message, type) {
		const container = document.getElementById(containerId);
		if (!container) return;

		const cssClass = type === 'success' ? 'notice-success' : 'notice-error';
		container.innerHTML = '<div class="notice ' + cssClass + ' is-dismissible"><p>' + message + '</p></div>';
		container.style.display = 'block';
	}

	// Αποθήκευση κατηγοριών cookies
	const saveBtn = document.getElementById('cc-save-categories');
	if (saveBtn) {
		saveBtn.addEventListener('click', function () {
			const spinner = document.getElementById('cc-categories-spinner');
			if (spinner) spinner.classList.add('is-active');

			const data = new FormData();
			data.append('action', 'cc_save_cookie_categories');
			data.append('nonce', ccAdmin.nonce);
			data.append('categories', JSON.stringify(collectCategoriesData()));

			fetch(ccAdmin.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: data,
			})
				.then(function (response) { return response.json(); })
				.then(function (result) {
					if (spinner) spinner.classList.remove('is-active');

					if (result.success) {
						showNotice('cc-categories-notice', result.data, 'success');
					} else {
						showNotice('cc-categories-notice', result.data, 'error');
					}
				})
				.catch(function () {
					if (spinner) spinner.classList.remove('is-active');
					showNotice('cc-categories-notice', 'Σφάλμα δικτύου.', 'error');
				});
		});
	}

	// Επαναφορά προεπιλογών κατηγοριών
	const resetBtn = document.getElementById('cc-reset-categories');
	if (resetBtn) {
		resetBtn.addEventListener('click', function () {
			if (!window.confirm(ccAdmin.confirmReset)) {
				return;
			}

			const spinner = document.getElementById('cc-categories-spinner');
			if (spinner) spinner.classList.add('is-active');

			const data = new FormData();
			data.append('action', 'cc_reset_cookie_categories');
			data.append('nonce', ccAdmin.nonceResetCategories);

			fetch(ccAdmin.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: data,
			})
				.then(function (response) { return response.json(); })
				.then(function (result) {
					if (spinner) spinner.classList.remove('is-active');

					if (result.success) {
						// Επαναφόρτωση σελίδας για εμφάνιση των νέων τιμών
						window.location.reload();
					} else {
						showNotice('cc-categories-notice', result.data, 'error');
					}
				})
				.catch(function () {
					if (spinner) spinner.classList.remove('is-active');
					showNotice('cc-categories-notice', 'Σφάλμα δικτύου.', 'error');
				});
		});
	}

	// Αποθήκευση κειμένων banner
	const saveBannerBtn = document.getElementById('cc-save-banner-texts');
	if (saveBannerBtn) {
		saveBannerBtn.addEventListener('click', function () {
			const spinner = document.getElementById('cc-banner-texts-spinner');
			if (spinner) spinner.classList.add('is-active');

			const fields = [
				'banner_text', 'banner_text_en',
				'btn_accept_all', 'btn_accept_all_en',
				'btn_accept_selected', 'btn_accept_selected_en',
				'btn_reject_all', 'btn_reject_all_en',
			];

			const formData = new FormData();
			formData.append('action', 'cc_save_banner_texts');
			formData.append('nonce', ccAdmin.nonceBannerTexts);

			fields.forEach(function (field) {
				const el = document.querySelector('[name="' + field + '"]');
				formData.append(field, el ? el.value : '');
			});

			fetch(ccAdmin.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData,
			})
				.then(function (response) { return response.json(); })
				.then(function (result) {
					if (spinner) spinner.classList.remove('is-active');

					if (result.success) {
						showNotice('cc-banner-texts-notice', result.data, 'success');
					} else {
						showNotice('cc-banner-texts-notice', result.data, 'error');
					}
				})
				.catch(function () {
					if (spinner) spinner.classList.remove('is-active');
					showNotice('cc-banner-texts-notice', 'Σφάλμα δικτύου.', 'error');
				});
		});
	}
})()
