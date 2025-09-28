// Run immediately without waiting DOMContentLoaded
document.querySelectorAll('.realtest-header .d-none-sm-550px').forEach(el => {
  const txt = el.textContent.trim();
  if (!txt) return;
  if (txt.startsWith('LexiPrep /') || el.querySelector('strong')?.textContent === 'LexiPrep') return;
  el.innerHTML = '<strong>LexiPrep</strong> / ' + txt;
});
