'use strict';

// Pure diagnostic core: consumes supplied observations, performs no I/O.
function validate(input) {
  if (!input || typeof input !== 'object' || Array.isArray(input)) throw new TypeError('input must be an object');
  for (const key of ['site_id', 'url', 'locale']) {
    if (typeof input[key] !== 'string' || !input[key].trim()) throw new TypeError(`${key} is required`);
  }
  let url;
  try { url = new URL(input.url); } catch { throw new TypeError('url must be an absolute HTTP(S) URL'); }
  if (!['http:', 'https:'].includes(url.protocol) || url.username || url.password) throw new TypeError('url must be an absolute HTTP(S) URL without credentials');
  const observations = input.observations ?? {};
  if (typeof observations !== 'object' || observations === null || Array.isArray(observations)) throw new TypeError('observations must be an object');
  const findings = [];
  const add = (code, outcome, observed) => findings.push({code, outcome, observed});
  function number(key, check) {
    const value = observations[key];
    if (value === undefined || value === null) return add(key, 'unknown', null);
    if (!Number.isInteger(value) || value < 0) throw new TypeError(`${key} must be a non-negative integer`);
    add(key, check(value) ? 'pass' : 'review', value);
  }
  number('http_status', n => n === 200);
  number('breadcrumb_list_count', n => n === 1);
  number('h1_count', n => n === 1);
  number('main_count', n => n >= 1);
  const mime = observations.og_image_type;
  if (mime === undefined || mime === null) add('og_image_type', 'unknown', null);
  else if (typeof mime !== 'string') throw new TypeError('og_image_type must be a string');
  else add('og_image_type', /^image\/(jpeg|png|webp|avif|gif)$/i.test(mime.trim()) ? 'pass' : 'review', mime);
  return {
    contract_version: '0.1.0', mode: 'diagnostic', site_id: input.site_id,
    url: url.href, locale: input.locale,
    classification: 'REVIEW_REQUIRED', publication_decision: null,
    classification_reason: 'Policy and required coverage not yet approved.',
    findings,
  };
}
module.exports = { validate };
