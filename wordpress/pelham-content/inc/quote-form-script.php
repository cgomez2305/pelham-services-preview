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
