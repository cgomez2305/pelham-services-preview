(() => {
 'use strict';
 const topics=document.querySelector('.news-topics');
 if(topics){
  const cards=[...document.querySelectorAll('.news-hub .news-card')],status=document.querySelector('.news-count');
  topics.hidden=false;status.hidden=false;
  function filter(topic){let count=0;cards.forEach(card=>{card.hidden=topic!=='all'&&card.dataset.topic!==topic;if(!card.hidden)count++;});topics.querySelectorAll('button').forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.topic===topic)));status.textContent=`${count} ${count===1?((window.pelhamI18n||{})['article']||'article'):((window.pelhamI18n||{})['articles']||'articles')}`;}
  topics.addEventListener('click',e=>{const b=e.target.closest('button');if(b)filter(b.dataset.topic);});filter('all');
 }
 const form=document.querySelector('.pelham-form');if(!form)return;
 const submit=form.querySelector('button[type=submit]');
 let pending=false,submitLabel=submit?.innerHTML;
 const sending=document.createElement('div');sending.className='form-submit-state';sending.setAttribute('role','status');sending.hidden=true;form.append(sending);
 form.addEventListener('submit',event=>{
  if(event.defaultPrevented)return;
  if(pending){event.preventDefault();return;}
  pending=true;form.setAttribute('aria-busy','true');
  const i18n=(window.pelhamI18n||{});if(submit){submit.disabled=true;submit.textContent=i18n['Sending…']||'Sending…';}
  sending.textContent=i18n['Sending your request. Please wait.']||'Sending your request. Please wait.';sending.hidden=false;
 });
 window.addEventListener('pageshow',()=>{pending=false;form.removeAttribute('aria-busy');sending.hidden=true;if(submit){submit.disabled=false;submit.innerHTML=submitLabel;}});
 const key='pelham-enquiry-draft:'+location.pathname,fields=['first_name','last_name','name','email','company','phone','business','origin','destination','service','freight','message'],state=new URLSearchParams(location.search).get('pelham_status');
 // Tab-local, ten-minute recovery. Never save hidden nonces, consent, or honeypot.
 try{
  if(state==='sent')sessionStorage.removeItem(key);
  else if(['invalid','failed','limited'].includes(state)){
   const draft=JSON.parse(sessionStorage.getItem(key)||'null');
   if(draft&&Date.now()-draft.time<600000){fields.forEach(name=>{const input=form.elements.namedItem(name);if(input&&typeof draft.values[name]==='string')input.value=draft.values[name];if(input&&draft.values[name])input.closest('details.optional-fields')?.setAttribute('open','');});const note=document.createElement('p');note.className='form-help';note.textContent=(window.pelhamI18n||{})['Your details have been restored in this tab. Review them and confirm consent before trying again.']||'Your details have been restored in this tab. Review them and confirm consent before trying again.';form.querySelector('.form-status')?.after(note);}
   else sessionStorage.removeItem(key);
  }
  form.addEventListener('submit',()=>{const values={};fields.forEach(name=>{const input=form.elements.namedItem(name);if(input)values[name]=input.value.slice(0,name==='message'?5000:200);});sessionStorage.setItem(key,JSON.stringify({time:Date.now(),values}));});
 }catch(_){/* Private browsing or storage restrictions retain native form behavior. */}
 const service=form.querySelector('select[name="service"]'),guide=form.querySelector('#quote-guidance'),question=form.querySelector('#quote-message-label');
 if(service&&guide&&question){
  const genericGuide=guide.textContent.trim(),genericQuestion=question.textContent;
  // Only valid route slugs can select an option. Recovered drafts take precedence.
  const requested=new URLSearchParams(location.search).get('service');
  if(!['invalid','failed','limited'].includes(state)&&requested){const option=[...service.options].find(o=>o.dataset.service===requested);if(option)service.value=option.value;}
  const choices=document.createElement('fieldset');choices.className='quote-service-choices';
  const legend=document.createElement('legend');legend.textContent=service.closest('label').childNodes[0].textContent.trim();choices.append(legend);
  const options=document.createElement('div');options.className='quote-service-options';choices.append(options);
  const radios=[];
  [...service.options].forEach((option,index)=>{
   const label=document.createElement('label');label.className='quote-service-option';
   const radio=document.createElement('input');radio.type='radio';radio.name='quote-service-choice';radio.value=option.value;
   const text=document.createElement('span'),title=document.createElement('strong'),description=document.createElement('span');
   title.textContent=option.textContent;description.textContent=option.dataset.description||'';description.className='quote-service-description';
   text.append(title,description);label.append(radio,text);options.append(label);radios.push(radio);
   radio.addEventListener('change',()=>{if(radio.checked){service.value=radio.value;service.dispatchEvent(new Event('change',{bubbles:true}));}});
  });
  service.closest('label').before(choices);service.closest('label').hidden=true;
  function updateQuote(){
   radios.forEach(r=>{r.checked=r.value===service.value;});
   const slug=service.selectedOptions[0]?.dataset.service;
   const template=[...form.querySelectorAll('template[data-quote-guide]')].find(t=>t.dataset.quoteGuide===slug);
   guide.textContent=template?template.content.textContent.trim():genericGuide;
   question.textContent=template?template.dataset.question:genericQuestion;
   // Guidance changes; user-entered shipment details never do.
  }
  service.addEventListener('change',updateQuote);window.addEventListener('pageshow',updateQuote);updateQuote();
 }
})();
