<?php
if(!defined('ABSPATH'))exit;
/**
 * Quote form behavior, printed inline after the form (no separate .js file in the plugin package).
 * - LTL: shows the transit level and its coverage note. The server preselects ?ltl_level=.
 * - LTL and FTL: show the shipment details; the message is optional with these details.
 * - Commodity: "Other" needs a description; some categories show a note.
 */
function pelham_quote_form_script(){
 return <<<'JS'
(function () {
	'use strict';

	var form = document.querySelector('.pelham-form');
	var service = form ? form.querySelector('[name="service"]') : null;
	var box = form ? form.querySelector('.pelham-ltl-choice') : null;
	if (!service || !box) {
		return;
	}

	var level = box.querySelector('select');
	var note = box.querySelector('#ltl-coverage');
	var translations = window.pelhamI18n || {};
	function translate(text) {
		return translations[text] || text;
	}

	var levelNotes = {
		expedited: translate('Expedited: estimated 3 calendar days from pickup. South Florida only, from Homestead to West Palm Beach. Not guaranteed. Confirm your delivery address with our team.'),
		regular: translate('Regular: estimated 7 calendar days from pickup. All Florida destinations. Not guaranteed.'),
		economy: translate('Economy: estimated 10 calendar days from pickup. All Florida destinations. Our lowest-cost LTL option. Not guaranteed.')
	};
	var genericNote = note.textContent;

	var details = form.querySelector('[data-quote-details]');
	var message = form.querySelector('textarea[name="message"]');
	var messageLabel = message ? message.closest('label') : null;
	var messageMark = messageLabel ? messageLabel.querySelector('.field-requirement') : null;
	var weightLabel = details ? details.querySelector('[data-label-ltl]') : null;

	var commodity = details ? details.querySelector('select[name="commodity"]') : null;
	var commodityDetail = details ? details.querySelector('input[name="commodity_detail"]') : null;
	var commodityNote = details ? details.querySelector('[data-commodity-note]') : null;
	var commodityOptional = details ? details.querySelector('[data-commodity-optional]') : null;

	function updateDetails(slug) {
		if (!details) {
			return;
		}
		var isLtl = slug === 'freight-consolidation';
		var shown = isLtl || slug === 'dedicated-transport';
		details.hidden = !shown;

		details.querySelectorAll('label').forEach(function (label) {
			var active = shown && (!label.hasAttribute('data-ltl-only') || isLtl);
			label.hidden = !active;
			label.querySelectorAll('input, select').forEach(function (field) {
				field.disabled = !active;
				field.required = active && !field.hasAttribute('data-optional');
			});
		});

		if (weightLabel) {
			weightLabel.textContent = isLtl ? weightLabel.dataset.labelLtl : weightLabel.dataset.labelFtl;
		}
		if (message) {
			message.required = !shown;
			if (messageMark) {
				messageMark.hidden = shown;
			}
		}
	}

	function updateCommodity() {
		if (!commodity || !commodityDetail) {
			return;
		}
		var isOther = commodity.value === 'other';
		commodityDetail.required = isOther && !commodityDetail.disabled;
		if (commodityOptional) {
			commodityOptional.hidden = isOther;
		}
		if (commodityNote) {
			var option = commodity.options[commodity.selectedIndex];
			commodityNote.textContent = option && option.dataset.note ? option.dataset.note : '';
		}
	}

	function update() {
		var option = service.options[service.selectedIndex];
		var slug = option && option.dataset.service ? option.dataset.service : '';
		var isLtl = slug === 'freight-consolidation';
		box.hidden = !isLtl;
		level.disabled = !isLtl;
		if (!isLtl) {
			level.value = '';
		}
		note.textContent = levelNotes[level.value] || genericNote;
		updateDetails(slug);
		updateCommodity();
	}

	if (commodity) {
		commodity.addEventListener('change', updateCommodity);
	}
	service.addEventListener('change', update);
	level.addEventListener('change', update);
	window.addEventListener('pageshow', update);
	update();
})();
JS;
}

/**
 * Clear error messages for the quote and contact forms. The browser's own bubble disappears at once and says
 * "Please match the requested format", so a 6-digit postal code looked like a form that did nothing.
 */
function pelham_form_validation_script(){
 return <<<'JS'
(function () {
	'use strict';

	var form = document.querySelector('.pelham-form');
	if (!form) {
		return;
	}
	var translations = window.pelhamI18n || {};
	function translate(text) {
		return translations[text] || text;
	}

	function messageFor(field) {
		var v = field.validity;
		if (field.name === 'origin' || field.name === 'destination') {
			return translate('Enter a 5-digit US ZIP code, for example 90248.');
		}
		if (v.valueMissing) {
			return field.type === 'checkbox' ? translate('Check this box to continue.') : translate('This field is required.');
		}
		if (v.typeMismatch && field.type === 'email') {
			return translate('Enter a valid email address.');
		}
		if (v.badInput || v.rangeUnderflow || v.rangeOverflow || v.stepMismatch) {
			return translate('Enter a valid number.');
		}
		return field.validationMessage;
	}

	function holderOf(field) {
		return field.closest('label') || field.parentNode;
	}

	function showError(field) {
		var holder = holderOf(field);
		var note = holder.querySelector('.field-error-live');
		if (!note) {
			note = document.createElement('small');
			note.className = 'field-error-live';
			note.id = 'error-' + field.name;
			holder.appendChild(note);
		}
		note.textContent = messageFor(field);
		field.setAttribute('aria-invalid', 'true');
		field.setAttribute('aria-describedby', note.id);
	}

	function clearError(field) {
		var note = holderOf(field).querySelector('.field-error-live');
		if (note) {
			note.remove();
		}
		field.removeAttribute('aria-invalid');
	}

	var summary = null;
	var firstInvalid = null;

	// The browser reports each invalid field on submit: show the message under it, then one notice and the first field.
	form.addEventListener('invalid', function (event) {
		event.preventDefault();
		var field = event.target;
		showError(field);
		if (!firstInvalid) {
			firstInvalid = field;
			window.setTimeout(function () {
				if (!summary) {
					summary = document.createElement('p');
					summary.className = 'form-live-summary';
					summary.setAttribute('role', 'alert');
					var button = form.querySelector('button[type="submit"]');
					button.parentNode.insertBefore(summary, button);
				}
				summary.textContent = translate('Please correct the highlighted fields before sending.');
				firstInvalid.scrollIntoView({block: 'center'});
				firstInvalid.focus({preventScroll: true});
				firstInvalid = null;
			}, 0);
		}
	}, true);

	form.addEventListener('input', function (event) {
		var field = event.target;
		if (field.getAttribute('aria-invalid') === 'true' && field.checkValidity()) {
			clearError(field);
			if (summary && !form.querySelector('[aria-invalid="true"]')) {
				summary.remove();
				summary = null;
			}
		}
	});
	form.addEventListener('change', function (event) {
		var field = event.target;
		if (field.getAttribute('aria-invalid') === 'true' && field.checkValidity()) {
			clearError(field);
		}
	});
})();
JS;
}
