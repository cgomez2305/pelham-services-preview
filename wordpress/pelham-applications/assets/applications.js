(() => {
	'use strict';
	const app = document.querySelector('.pa-app');
	if (!app) return;
	const schema = JSON.parse(app.querySelector('.pa-schema').textContent),
		kind = app.dataset.kind,
		base = app.dataset.api;
	const gate = app.querySelector('.pa-gate'),
		workspace = app.querySelector('.pa-workspace'),
		form = app.querySelector('.pa-form'),
		status = app.querySelector('.pa-status'),
		errors = app.querySelector('.pa-errors');
	let state = null,
		step = 0,
		saveTimer, chain = Promise.resolve(),
		dirty = false,
		busy = false;
	const T = (() => {
			try {
				return JSON.parse(app.querySelector('.pa-i18n')?.textContent || '{}')
			} catch {
				return {}
			}
		})(),
		t = s => T[s] || s,
		lang = document.documentElement.lang.startsWith('es') ? 'es' : 'en';
	const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;'
	} [c]));

	function message(text) {
		status.textContent = text;
	}
	message(t('Opening your secure form…'));

	function clearErrors() {
		errors.hidden = true;
		errors.textContent = '';
		form.querySelectorAll('[aria-invalid]').forEach(e => e.removeAttribute('aria-invalid'));
		form.querySelectorAll('.pa-field-error').forEach(e => e.textContent = '');
	}

	// Step that holds a field, an attachment or a signature named in a server error.
	function stepOf(key) {
		let el = form.elements.namedItem(key) || form.querySelector(`[data-attachment="${key}"]`);
		if (!el && key.startsWith('signature_')) {
			const slot = key.slice(10);
			el = form.querySelector(`[data-signature="${kind !== 'claim' && ['customer', 'terms'].includes(slot) ? 'main' : slot}"]`);
		}
		const panel = el?.closest?.('.pa-step');
		return panel ? Number(panel.dataset.panel) : null;
	}

	function fail(error) {
		errors.hidden = false;
		errors.textContent = t(error.message || 'Please try again.');
		const fields = error.fields || {};
		const problems = [];
		for (const [key, text] of Object.entries(fields)) {
			const el = form.elements.namedItem(key),
				label = form.querySelector('#pa-' + key + '-error');
			if (el && el.setAttribute) {
				el.setAttribute('aria-invalid', 'true');
				if (label) {
					label.textContent = t(text);
					el.setAttribute('aria-describedby', label.id);
				}
			}
			const where = stepOf(key);
			if (!problems.some(p => p.text === t(text))) problems.push({
				key,
				text: t(text),
				step: where
			});
		}
		// List every problem with a way to reach it: errors on other steps are otherwise invisible.
		if (problems.length) {
			const list = document.createElement('ul');
			list.className = 'pa-error-list';
			for (const problem of problems) {
				const item = document.createElement('li');
				item.append(problem.text);
				if (problem.step !== null && problem.step !== step) {
					const go = document.createElement('button');
					go.type = 'button';
					go.className = 'pa-error-go';
					go.textContent = t('Go to step %s').replace('%s', problem.step + 1);
					go.onclick = () => {
						setStep(problem.step);
						const target = form.elements.namedItem(problem.key) || form.querySelector(`[data-attachment="${problem.key}"]`);
						target?.scrollIntoView?.({
							block: 'center'
						});
					};
					item.append(' ', go);
				}
				list.append(item);
			}
			errors.append(list);
			const steps = problems.map(p => p.step).filter(n => n !== null);
			if (steps.length && !steps.includes(step)) setStep(Math.min(...steps), false);
		}
		errors.focus();
	}

	async function api(op, data) {
		const response = await fetch(base + op, {
			method: op === 'session' ? 'GET' : 'POST',
			credentials: 'same-origin',
			signal: AbortSignal.timeout(20000),
			headers: {
				'Content-Type': 'application/json',
				'X-Pelham-CSRF': state?.csrf || ''
			},
			...(op === 'session' ? {} : {
				body: JSON.stringify(data || {})
			})
		});
		let result;
		try {
			result = await response.json()
		} catch {
			throw new Error(t('The server returned an unexpected response. Please retry.'))
		}
		if (!response.ok) {
			const error = new Error(t(result.message || 'Request failed.'));
			error.fields = result.data?.fields;
			error.status = response.status;
			throw error;
		}
		return result;
	}

	function serial(fn) {
		const task = chain.then(fn);
		chain = task.catch(() => {});
		return task;
	}

	function values() {
		const data = {};
		for (const f of schema) {
			const el = form.elements.namedItem(f.key);
			data[f.key] = el.type === 'checkbox' ? (el.checked ? '1' : '') : el.value;
		}
		return data;
	}

	function fieldsChanged() {
		if (Object.keys(state?.signatures || {}).length) message(t('You changed your information, so your signatures were cleared. Sign again in step 4.'));
		app.querySelectorAll('.pa-sign-confirm input').forEach(input => input.checked = false);
		dirty = true;
		app.querySelector('.pa-save-state').textContent = t('Unsaved changes');
		state.signatures = {};
		renderSignatureStates();
		clearTimeout(saveTimer);
		saveTimer = setTimeout(() => save().catch(fail), 800);
		conditional();
	}

	function conditional() {
		// Claims: the type of claim decides whether lost pieces and damage must be described.
		if (kind === 'claim') {
			const incident = (form.elements.namedItem('incident')?.value || '').toLowerCase();
			for (const [key, needed] of [
					['lost_pieces', incident.includes('loss')],
					['damage', incident.includes('damage')]
				]) {
				const el = form.elements.namedItem(key);
				if (!el) continue;
				el.required = needed;
				const label = el.closest('label');
				let mark = label?.querySelector('.pa-conditional-mark');
				if (label && !mark) {
					mark = document.createElement('span');
					mark.className = 'pa-conditional-mark';
					mark.setAttribute('aria-hidden', 'true');
					mark.textContent = ' *';
					label.insertBefore(mark, el);
				}
				if (mark) mark.hidden = !needed;
			}
		}
		const credit = form.elements.namedItem('request_credit')?.value === 'Yes';
		form.querySelectorAll('[data-credit]').forEach(el => {
			el.hidden = !credit;
			el.querySelectorAll('input,select,textarea').forEach(i => i.disabled = !credit)
		});
		const same = form.elements.namedItem('billing_same')?.checked;
		for (const key of ['address', 'city', 'state', 'zip']) {
			const el = form.elements.namedItem('billing_' + key);
			if (!el) continue;
			if (same) el.value = form.elements.namedItem(key).value;
			el.readOnly = !!same;
		}
		const guarantor = form.elements.namedItem('guarantor'),
			sameGuarantor = form.elements.namedItem('guarantor_same')?.checked;
		if (guarantor) {
			if (sameGuarantor) guarantor.value = form.elements.namedItem('signer_name')?.value || '';
			guarantor.readOnly = !!sameGuarantor;
		}
		const companyName = form.elements.namedItem('company')?.value;
		app.querySelectorAll('[data-company-name]').forEach(el => el.textContent = companyName || t('your company'));
		termsGate();
		const docs = app.querySelector('[data-document=credit]');
		if (docs) docs.hidden = !credit;
		form.querySelectorAll('[data-signature=credit],[data-signature=guarantee]').forEach(e => e.hidden = !credit);
	}

	function save() {
		clearTimeout(saveTimer);
		return serial(async () => {
			if (!state || !['draft', 'needs_information'].includes(state.status)) return state;
			app.querySelector('.pa-save-state').textContent = t('Saving…');
			state = await api('save', {
				data: values()
			});
			dirty = false;
			app.querySelector('.pa-save-state').textContent = t('Saved securely');
			renderSignatureStates();
			return state;
		});
	}

	function renderSignatureStates() {
		for (const box of form.querySelectorAll('[data-signature]')) {
			const signed = signatureOf(box.dataset.signature);
			box.querySelector('.pa-signed').textContent = signed ? t('Signed by') + ' ' + signed.name + ' · ' + new Date(signed.signed_at).toLocaleString(lang === 'es' ? 'es-US' : undefined) : t('Not signed');
			box.classList.toggle('is-signed', !!signed);
			const preview = box.querySelector('.pa-signed-image');
			preview.hidden = !signed;
			if (signed) preview.src = signed.image;
		}
	}

	function signatureOf(slot) {
		const signatures = state?.signatures || {};
		if (slot !== 'main') return signatures[slot];
		return signatures.customer && signatures.terms ? signatures.customer : null;
	}

	function signaturesNeeded() {
		// "main" signs the account application and the transport terms at once.
		return (kind === 'claim' ? [
			['claim', 'Freight claim']
		] : [
			['main', 'Account and transport agreement'],
			['credit', 'Credit applicant signature'],
			['guarantee', 'Personal guarantor signature']
		]).map(([slot, label]) => [slot, t(label)]);
	}

	// What each signature signs, for the confirmation sentence.
	const signedDocument = {
		main: 'account application and transport terms',
		credit: 'credit application',
		guarantee: 'personal guaranty',
		claim: 'freight claim'
	};

	function buildSignatures() {
		const holder = form.querySelector('.pa-signatures');
		holder.innerHTML = kind === 'claim' ? '<h4>' + esc(t('Your signatures')) + '</h4><p>' + esc(t('Draw with your finger or mouse, or upload a signature image (JPG, PNG or WebP, up to 1 MB). Confirm each document separately.')) + '</p>' : '<h4 class="pa-section-title">' + esc(t('D. Signatures')) + '</h4><p class="pa-help">' + esc(t('Draw with your finger or mouse, or upload a signature image (JPG, PNG or WebP, up to 1 MB). The date is recorded automatically when you sign.')) + '</p><p class="pa-locked-note">' + esc(t('Signatures unlock after you initial the Terms and Conditions (section B).')) + '</p>';
		for (const [slot, label] of signaturesNeeded()) {
			const box = document.createElement('section');
			box.className = 'pa-signature';
			box.dataset.signature = slot;
			box.innerHTML = `<h4>${esc(label)}</h4><p class="pa-signed" aria-live="polite"></p><img class="pa-signed-image" alt="${esc(t('Your saved signature'))}" hidden><div class="pa-sign-tools"><button type="button" class="pa-draw">${esc(t('Draw signature'))}</button><label class="pa-upload-label">${esc(t('Upload signature'))}<input type="file" accept=".png,.jpg,.jpeg,.webp" class="pa-sign-upload"></label><button type="button" class="pa-clear">${esc(t('Clear drawing'))}</button></div><canvas width="1000" height="280" aria-label="${esc(t('Draw your signature for %s').replace('%s',label))}"></canvas><p class="pa-help">${esc(t('Drawing is optional: use Upload signature if you use a keyboard or already have a signature image.'))}</p><label class="pa-sign-confirm"><input type="checkbox"> ${esc(t('I am the named signer and intend to sign the %s with this signature.').replace('%s',t(signedDocument[slot]||label)))}</label><div class="pa-sign-actions"><button type="button" class="pa-sign-button">${esc(t('Sign this document'))}</button>${['customer','main','claim'].includes(slot)?'':'<button type="button" class="pa-reuse">'+esc(t('Use my account signature'))+'</button>'}</div>`;
			holder.append(box);
			const canvas = box.querySelector('canvas'),
				ctx = canvas.getContext('2d');
			let drawing = false,
				hasInk = false,
				method = 'draw';

			function reset() {
				ctx.fillStyle = '#fff';
				ctx.fillRect(0, 0, canvas.width, canvas.height);
				ctx.strokeStyle = '#10291d';
				ctx.lineWidth = 3;
				ctx.lineCap = 'round';
				hasInk = false;
				method = 'draw';
			}
			reset();

			function point(e) {
				const r = canvas.getBoundingClientRect();
				return [(e.clientX - r.left) * canvas.width / r.width, (e.clientY - r.top) * canvas.height / r.height];
			}
			canvas.addEventListener('pointerdown', e => {
				e.preventDefault();
				drawing = true;
				method = 'draw';
				canvas.setPointerCapture(e.pointerId);
				ctx.beginPath();
				ctx.moveTo(...point(e));
			});
			canvas.addEventListener('pointermove', e => {
				if (drawing) {
					ctx.lineTo(...point(e));
					ctx.stroke();
					hasInk = true;
				}
			});
			for (const event of ['pointerup', 'pointercancel', 'lostpointercapture']) canvas.addEventListener(event, () => drawing = false);
			box.querySelector('.pa-clear').onclick = () => {
				reset();
				box.querySelector('.pa-sign-confirm input').checked = false;
			};
			box.querySelector('.pa-draw').onclick = () => {
				canvas.scrollIntoView({
					block: 'center',
					behavior: 'auto'
				});
			};
			box.querySelector('.pa-sign-upload').onchange = async e => {
				try {
					const file = e.target.files[0];
					if (!file) return;
					if (file.size > 1000000 || !['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) throw new Error(t('Use a JPG, PNG or WebP signature image up to 1 MB.'));
					const image = await createImageBitmap(file);
					if (image.width > 4096 || image.height > 4096) {
						image.close();
						throw new Error(t('Use a signature image no larger than 4096 × 4096 pixels.'))
					}
					reset();
					const ratio = Math.min(canvas.width / image.width, canvas.height / image.height);
					ctx.drawImage(image, (canvas.width - image.width * ratio) / 2, (canvas.height - image.height * ratio) / 2, image.width * ratio, image.height * ratio);
					image.close();
					hasInk = true;
					method = 'upload';
					message(t('Signature image loaded. Confirm and sign this document.'));
				} catch (error) {
					fail(error)
				}
			};
			async function sign(reuse) {
				clearErrors();
				try {
					if (!box.querySelector('.pa-sign-confirm input').checked) throw new Error(t('Confirm your intention to sign this document.'));
					await save();
					let image;
					if (reuse) {
						image = signatureOf('main')?.image;
						if (!image) throw new Error(t('Sign the account application first.'))
					} else {
						if (!hasInk) throw new Error(t('Draw or upload your signature first.'));
						image = canvas.toDataURL('image/jpeg', .9);
					}
					state = await serial(() => api('sign', {
						slot,
						image,
						method: reuse ? 'reuse' : method,
						confirm: true,
						data_hash: state.data_hash
					}));
					renderSignatureStates();
					message(t('%s signed and saved.').replace('%s', label));
				} catch (error) {
					fail(error)
				}
			}
			box.querySelector('.pa-sign-button').onclick = () => sign(false);
			box.querySelector('.pa-reuse')?.addEventListener('click', () => sign(true));
		}
	}

	function previewUrl(document) {
		return base + 'preview' + (base.includes('?') ? '&' : '?') + 'document=' + encodeURIComponent(document);
	}

	function readText(item, doc, key) {
		const text = item.querySelector('.pa-document-text');
		if (key === 'terms') {
			const pages = doc.text.split('\f').filter(p => p.trim());
			pages.forEach((page, i) => {
				const section = document.createElement('section'),
					heading = document.createElement('h5'),
					copy = document.createElement('div');
				heading.textContent = t('Page %1 of %2').replace('%1', i + 1).replace('%2', pages.length);
				copy.textContent = page;
				section.append(heading, copy);
				text.append(section);
			});
		} else {
			text.textContent = doc.text;
		}
	}

	// Initials of the authorized signer: the first name's initial, then initials of the following names in order (GR, GMR, MJL…). The server applies the same rule.
	function initialsMatch(initials, name) {
		const plain = text => String(text || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase();
		const letters = plain(initials).replace(/[^A-Z]/g, '');
		const words = plain(name).split(/[\s-]+/).map(w => w.replace(/[^A-Z]/g, '')).filter(Boolean);
		if (!words.length || !letters || letters.length > 6 || letters[0] !== words[0][0]) return false;
		if (words.length > 1 && letters.length < 2) return false;
		let next = 1;
		for (const letter of letters.slice(1)) {
			while (next < words.length && words[next][0] !== letter) next++;
			if (next >= words.length) return false;
			next++;
		}
		return true;
	}

	let termsRead = false;

	// The Terms and Conditions are read in full and initialed before the agreements and signatures unlock.
	function buildTermsReader() {
		const holder = form.querySelector('.pa-terms-reader'),
			source = app.querySelector('.pa-terms-text');
		if (!holder || !source) return;
		const terms = JSON.parse(source.textContent),
			field = form.elements.namedItem('terms_initials')?.closest('.pa-field');
		field?.remove();
		holder.innerHTML = `<div class="pa-document-head"><strong>${esc(t('Terms and Conditions of Contract'))}</strong><button type="button" class="pa-preview">${esc(t('Open PDF'))}</button></div><div class="pa-terms-scroll" tabindex="0" role="region" aria-label="${esc(t('Terms and Conditions of Contract'))}"><ol></ol><p class="pa-terms-ack"></p></div><p class="pa-terms-progress" aria-live="polite"></p><div class="pa-terms-initials"></div>`;
		const list = holder.querySelector('ol');
		for (const clause of terms.clauses) {
			const item = document.createElement('li');
			item.textContent = clause;
			list.append(item);
		}
		holder.querySelector('.pa-terms-ack').textContent = terms.acknowledgement;
		if (field) holder.querySelector('.pa-terms-initials').append(field);
		const box = holder.querySelector('.pa-terms-scroll');
		termsRead = !!form.elements.namedItem('terms_initials')?.value;
		const reached = () => {
			if (!termsRead && box.scrollTop + box.clientHeight >= box.scrollHeight - 24) {
				termsRead = true;
				termsGate();
			}
		};
		box.addEventListener('scroll', reached, {
			passive: true
		});
		holder.querySelector('.pa-preview').addEventListener('click', async () => {
			const tab = window.open('', '_blank');
			try {
				await save();
				if (tab) tab.location.href = previewUrl('terms');
				else window.location.href = previewUrl('terms');
			} catch (error) {
				if (tab) tab.close();
				fail(error);
			}
		});
	}

	function termsInitialed() {
		const initials = form.elements.namedItem('terms_initials')?.value,
			signer = form.elements.namedItem('signer_name')?.value;
		return !!initials && initialsMatch(initials, signer);
	}

	function termsGate() {
		const reader = form.querySelector('.pa-terms-reader');
		if (!reader) return;
		const input = form.elements.namedItem('terms_initials'),
			credit = form.elements.namedItem('request_credit')?.value === 'Yes',
			done = termsInitialed();
		if (input) {
			input.disabled = !termsRead;
			if (done) input.removeAttribute('aria-invalid');
		}
		reader.classList.toggle('is-initialed', done);
		const progress = reader.querySelector('.pa-terms-progress');
		const error = form.querySelector('#pa-terms_initials-error');
		if (!termsRead) progress.textContent = t('Scroll to the end of the Terms and Conditions to add your initials.');
		else if (done) progress.textContent = t('Initialed. Thank you for reading the Terms and Conditions.');
		else progress.textContent = t('Add the initials of the authorized signer to confirm you read and accept these terms.');
		if (error) error.textContent = input?.value && !done ? t('Your initials must match the authorized signer (for example GR for Gabriel Reverón).') : '';
		for (const key of ['accept_terms', 'accept_credit', 'accept_guarantee', 'consent_signature']) {
			const el = form.elements.namedItem(key);
			if (el) el.disabled = !done || (!!el.closest('[data-credit]') && !credit);
		}
		const signatures = form.querySelector('.pa-signatures');
		signatures?.classList.toggle('pa-locked', !done);
		signatures?.querySelectorAll('button, input').forEach(el => el.disabled = !done);
		form.querySelectorAll('.pa-locked-note').forEach(note => note.hidden = done);
	}

	function buildDocuments() {
		const holder = form.querySelector('.pa-documents');
		holder.innerHTML = '';
		if (kind === 'claim') {
			const doc = state.documents.claim,
				item = document.createElement('details');
			item.dataset.document = 'claim';
			item.innerHTML = `<summary>${esc(t('Read the complete %s').replace('%s',t(doc.title)))}</summary><div class="pa-document-text"></div>`;
			readText(item, doc, 'claim');
			holder.append(item);
			return;
		}
		// The applicant's own documents, filled with the saved information (DRAFT until the request is sent).
		const titles = {
			customer: 'Completed customer application',
			credit: 'Completed credit application',
			terms: 'Terms and agreements'
		};
		buildTermsReader();
		for (const key of ['customer', 'credit']) {
			const doc = state.documents[key];
			const item = document.createElement('div');
			item.className = 'pa-document';
			item.dataset.document = key;
			item.innerHTML = `<div class="pa-document-head"><strong>${esc(t(titles[key]))}</strong><button type="button" class="pa-preview">${esc(t('Open PDF'))}</button></div>` + (key === 'terms' ? `<details><summary>${esc(t('Read the complete text'))}</summary><div class="pa-document-text"></div></details>` : '');
			if (key === 'terms') readText(item, doc, key);
			item.querySelector('.pa-preview').addEventListener('click', async () => {
				// Open the tab during the click so browsers allow it, then load the PDF once the latest answers are saved.
				const tab = window.open('', '_blank');
				try {
					await save();
					if (tab) tab.location.href = previewUrl(key);
					else window.location.href = previewUrl(key);
				} catch (error) {
					if (tab) tab.close();
					fail(error);
				}
			});
			holder.append(item);
		}
	}

	function fill() {
		for (const f of schema) {
			const el = form.elements.namedItem(f.key);
			if (el.type === 'checkbox') el.checked = state.data[f.key] === '1';
			else el.value = state.data[f.key] || '';
		}
		buildDocuments();
		buildSignatures();
		conditional();
		termsGate();
		renderSignatureStates();
		renderFiles();
	}

	function renderFiles() {
		for (const el of form.querySelectorAll('[data-file-state]')) {
			const file = state.files?.[el.dataset.fileState];
			el.textContent = '';
			if (file) {
				el.textContent = file.name + ' · ' + Math.ceil(file.size / 1024) + ' KB · ' + t(file.scan_status === 'clean' ? 'Security check passed' : file.scan_status === 'blocked' ? 'File blocked' : 'Saved — security review pending') + ' ';
				el.setAttribute('role', 'status');
				const remove = document.createElement('button');
				remove.type = 'button';
				remove.textContent = t('Remove');
				remove.onclick = async () => {
					try {
						await save();
						state = await serial(() => api('file', {
							slot: el.dataset.fileState,
							remove: true
						}));
						renderFiles();
						renderSignatureStates()
					} catch (error) {
						fail(error)
					}
				};
				el.append(remove);
			}
		}
	}

	// The contact person is often the signer: suggest it when the signing step opens, until the applicant changes it.
	function suggestSigner() {
		let changed = false;
		for (const [target, source] of [
				['signer_name', 'contact_name'],
				['signer_title', 'contact_title']
			]) {
			const el = form.elements.namedItem(target),
				value = form.elements.namedItem(source)?.value || '';
			if (el && !el.value && value) {
				el.value = value;
				changed = true;
			}
		}
		if (changed) fieldsChanged();
	}

	function setStep(index, focus = true) {
		step = Math.max(0, Math.min(4, index));
		if (step === 3 && kind !== 'claim') suggestSigner();
		form.querySelectorAll('.pa-step').forEach((el, i) => el.hidden = i !== step);
		app.querySelectorAll('[data-step]').forEach((el, i) => {
			if (i === step) el.setAttribute('aria-current', 'step');
			else el.removeAttribute('aria-current');
			el.closest('li').classList.toggle('is-complete', i < step);
		});
		app.querySelector('.pa-back').hidden = step === 0;
		app.querySelector('.pa-next').hidden = step === 4;
		app.querySelector('.pa-submit').hidden = step !== 4;
		if (step === 4) review();
		if (focus) form.querySelector(`[data-panel="${step}"] h3`).focus({
			preventScroll: false
		});
	}

	function fieldFormat(input) {
		const f = schema.find(f => f.key === input.name);
		if (!f) return;
		const v = input.value;
		let error = '';
		if (v && f.type === 'tel') {
			const base = v.replace(/\s*(?:ext\.?|x|#)\s*\d{1,6}$/i, '');
			let digits = base.replace(/\D/g, '');
			if (digits.length === 11 && digits[0] === '1') digits = digits.slice(1);
			if (!/^\+?[0-9().\s-]+(?:\s*(?:ext\.?|x|#)\s*\d{1,6})?$/i.test(v) || !/^[2-9][0-9]{2}[2-9][0-9]{6}$/.test(digits) || digits.slice(1, 3) === '11' || digits.slice(4, 6) === '11' || /^(\d)\1{9}$/.test(digits)) error = t('Enter a 10-digit US/NANP number, optionally +1 and an extension.');
		}
		if (v && /(^|_)zip$/.test(f.key) && (!/^\d{5}(?:-\d{4})?$/.test(v) || v.slice(0, 5) === '00000')) error = t('Enter a five-digit ZIP code or ZIP+4, such as 90248 or 90248-1234.');
		input.setCustomValidity(error);
	}
	form.addEventListener('input', e => {
		if (e.target.setCustomValidity) fieldFormat(e.target)
	});
	form.addEventListener('focusout', e => {
		const input = e.target;
		if (!input.name || !input.setCustomValidity) return;
		fieldFormat(input);
		const label = form.querySelector('#pa-' + input.name + '-error');
		if (label) {
			label.textContent = input.validity.valid ? '' : input.validationMessage;
			input.setAttribute('aria-invalid', String(!input.validity.valid));
		}
	});
	for (const f of schema) {
		const input = form.elements.namedItem(f.key);
		if (f.type === 'tel') {
			input.placeholder = '(323) 201-4995';
			input.maxLength = 40;
		}
		if (/(^|_)zip$/.test(f.key)) {
			input.placeholder = t('90248 or 90248-1234');
			input.inputMode = 'numeric';
			input.maxLength = 10;
		}
	}

	function validateStep() {
		clearErrors();
		const panel = form.querySelector(`[data-panel="${step}"]`);
		if (panel.querySelector('.pa-terms-reader') && !termsInitialed()) {
			fail({
				message: t('Read the Terms and Conditions of Contract and add your initials before continuing.'),
				fields: {
					terms_initials: termsRead ? 'Your initials must match the authorized signer (for example GR for Gabriel Reverón).' : 'Scroll to the end of the Terms and Conditions to add your initials.'
				}
			});
			panel.querySelector('.pa-terms-reader').scrollIntoView({
				block: 'start'
			});
			return false;
		}
		// The cost invoice is required for a claim; file inputs are not part of the browser's form validation.
		const invoice = panel.querySelector('[data-attachment="invoice"]');
		if (invoice && !state?.files?.invoice) {
			fail({
				message: t('Please complete the highlighted items before sending.'),
				fields: {
					invoice: 'Attach the cost invoice supporting the claim value.'
				}
			});
			invoice.scrollIntoView({
				block: 'center'
			});
			return false;
		}
		for (const input of panel.querySelectorAll('input,select,textarea')) {
			if (input.disabled || input.closest('[hidden]') || input.type === 'file' || input.closest('.pa-signature')) continue;
			fieldFormat(input);
			if (!input.checkValidity()) {
				input.setAttribute('aria-invalid', 'true');
				input.reportValidity();
				return false;
			}
		}
		return true;
	}

	function review() {
		const holder = form.querySelector('.pa-review');
		holder.innerHTML = '';
		const dl = document.createElement('dl');
		for (const f of schema) {
			const value = values()[f.key];
			if (!value || (f.condition === 'credit' && values().request_credit !== 'Yes')) continue;
			const dt = document.createElement('dt'),
				dd = document.createElement('dd');
			dt.textContent = t(f.label);
			dd.textContent = f.type === 'checkbox' ? t('Confirmed') : (['tax_id', 'bank_account'].includes(f.key) ? '•••• ' + value.slice(-4) : (f.type === 'select' ? t(value) : value));
			dl.append(dt, dd);
		}
		holder.append(dl);
		const h = document.createElement('h4');
		h.textContent = t('Signed documents');
		holder.append(h);
		for (const [slot, label] of signaturesNeeded()) {
			if (['credit', 'guarantee'].includes(slot) && values().request_credit !== 'Yes') continue;
			const line = document.createElement('p');
			const signed = signatureOf(slot);
			line.textContent = label + ': ' + (signed ? t('Signed by') + ' ' + signed.name : t('Signature required'));
			holder.append(line);
		}
	}

	function complete() {
		message('');
		gate.hidden = true;
		workspace.hidden = true;
		const el = app.querySelector('.pa-complete');
		el.hidden = false;
		el.innerHTML = `${state.test?'<p><strong>'+esc(t('TEST COMPLETE — no emails were sent. The Pelham team can review this test request in WordPress → Pelham Applications.'))+'</strong></p>':''}<span class="pa-eyebrow">${esc(state.reference)}</span><h3>${esc(t('Your request is with our team.'))}</h3><p>${esc(t('Your information and signatures have been saved. Our team will review your request and contact you. Email notifications may arrive separately.'))}</p><p><strong>${esc(t('Status:'))}</strong> ${esc(t(state.status.replaceAll('_',' ')))}</p><p>${esc(t('This is not an automatic approval or a customer portal account.'))}</p><button class="pa-logout" type="button">${esc(t('Sign out'))}</button>`;
		el.querySelector('button').onclick = logout;
	}

	function open() {
		if (state.kind !== kind) {
			state = null;
			gate.hidden = false;
			message(t('Verify your email to open this type of request.'));
			return;
		}
		gate.hidden = true;
		if (!['draft', 'needs_information'].includes(state.status)) {
			complete();
			return;
		}
		workspace.hidden = false;
		app.querySelector('.pa-reference').textContent = state.reference + (state.test ? ' · ' + t('TEST — no emails sent') : ' · ' + t('verified email'));
		fill();
		setStep(0, false);
		message(state.correction ? t('The team needs more information:') + ' ' + state.correction : t('Your draft is ready. Required fields are marked *.'));
		app.querySelector('.pa-save-state').textContent = t('Draft saved securely');
	}
	async function logout() {
		try {
			if (dirty) await save();
			await serial(() => api('logout'));
			location.href = location.pathname;
		} catch (error) {
			fail(error)
		}
	}
	gate.onsubmit = async e => {
		e.preventDefault();
		clearErrors();
		if (!gate.reportValidity()) return;
		const button = gate.querySelector('button');
		button.disabled = true;
		try {
			const result = await api('start', {
				email: gate.elements.email.value,
				kind,
				website: gate.elements.website.value,
				lang
			});
			message(t(result.message));
		} catch (error) {
			fail(error)
		} finally {
			button.disabled = false
		}
	};
	form.addEventListener('input', e => {
		if (e.target.name && schema.some(f => f.key === e.target.name)) fieldsChanged()
	});
	form.addEventListener('change', e => {
		if (e.target.name && schema.some(f => f.key === e.target.name)) conditional()
	});
	form.querySelectorAll('[data-attachment]').forEach(input => input.onchange = async () => {
		try {
			const file = input.files[0];
			if (!file) return;
			if (file.size > 2 * 1024 * 1024) throw new Error(t('Keep each attachment under 2 MB.'));
			const data = await new Promise((resolve, reject) => {
				const r = new FileReader();
				r.onload = () => resolve(r.result);
				r.onerror = reject;
				r.readAsDataURL(file)
			});
			message(t('Uploading and checking your file…'));
			input.disabled = true;
			await save();
			const wasSigned = Object.keys(state.signatures || {}).length > 0;
			state = await serial(() => api('file', {
				slot: input.dataset.attachment,
				name: file.name,
				file: data
			}));
			renderFiles();
			renderSignatureStates();
			message(t(state.files[input.dataset.attachment]?.scan_status === 'clean' ? 'File saved. Security check passed.' : 'File saved. Security review is pending; Pelham cannot download it until it is approved.'));
			if (wasSigned) message(t('File saved. Your signature covers the evidence, so it was cleared: sign again in step 4.'));
		} catch (error) {
			fail(error)
		} finally {
			input.value = '';
			input.disabled = false
		}
	});
	app.querySelector('.pa-next').onclick = async () => {
		if (!validateStep()) return;
		try {
			await save();
			setStep(step + 1)
		} catch (error) {
			fail(error)
		}
	};
	app.querySelector('.pa-back').onclick = () => setStep(step - 1);
	app.querySelectorAll('[data-step]').forEach(button => button.onclick = async () => {
		const target = Number(button.dataset.step);
		if (target > step && !validateStep()) return;
		try {
			await save();
			setStep(target)
		} catch (error) {
			fail(error)
		}
	});
	app.querySelector('.pa-signout').onclick = logout;
	form.onsubmit = async e => {
		e.preventDefault();
		if (busy) return;
		busy = true;
		const button = app.querySelector('.pa-submit');
		button.disabled = true;
		clearErrors();
		try {
			await save();
			state = await serial(() => api('submit'));
			complete();
			message(t('Request received:') + ' ' + state.reference);
		} catch (error) {
			fail(error);
			if (error.fields) {
				const first = Object.keys(error.fields)[0];
				const input = form.elements.namedItem(first);
				if (input) setStep(Number(input.closest('[data-panel]').dataset.panel));
				else if (first.startsWith('signature_')) setStep(3);
			}
		} finally {
			busy = false;
			button.disabled = false
		}
	};
	window.addEventListener('beforeunload', e => {
		if (dirty) {
			e.preventDefault();
			e.returnValue = '';
		}
	});
	(async () => {
		try {
			const token = new URLSearchParams(location.hash.slice(1)).get('application-token');
			if (token) {
				history.replaceState(null, '', location.pathname + location.search);
				state = await api('exchange', {
					token
				});
			} else state = await api('session');
			open();
		} catch (error) {
			gate.hidden = false;
			message('');
			if (error.status !== 401) fail(error)
		}
	})();
})();
