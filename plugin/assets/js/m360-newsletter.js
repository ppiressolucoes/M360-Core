(function () {
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form.matches('[data-m360-newsletter-form]')) return;
    event.preventDefault();
    var message = form.querySelector('[data-m360-newsletter-message]');
    var button = form.querySelector('[type="submit"]');
    if (button.disabled) return;
    button.disabled = true; form.setAttribute('aria-busy', 'true'); message.textContent = 'Enviando…';
    fetch(M360Newsletter.endpoint, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({name:form.name.value,email:form.email.value,consent:form.consent.checked,website:form.website.value,rendered_at:form.rendered_at.value,source:form.source.value,lang:form.lang.value})})
      .then(function (response) { return response.json().then(function (data) { if (!response.ok) throw new Error(data.message); return data; }); })
      .then(function (data) { message.textContent = data.message; form.reset(); document.cookie='m360_newsletter_submitted=1;path=/;max-age='+(86400*(M360Newsletter.hideDays||30))+';SameSite=Lax'; })
      .catch(function (error) { message.textContent = error.message || 'Não foi possível concluir a inscrição.'; })
      .finally(function () { button.disabled = false; form.removeAttribute('aria-busy'); message.focus(); });
  });
  document.addEventListener('DOMContentLoaded', function () {
    if (document.cookie.indexOf('m360_newsletter_submitted=1') === -1) return;
    document.querySelectorAll('[data-m360-newsletter-root]').forEach(function (root) { var form=root.querySelector('form'); var notice=root.querySelector('[data-m360-newsletter-return]'); if(form) form.hidden=true; if(notice) notice.hidden=false; });
  });
}());
