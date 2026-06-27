document.addEventListener('DOMContentLoaded', () => {

  const copyButton = document.querySelector('.copy-btn');

  if (!copyButton) return;

  copyButton.addEventListener('click', () => {

    const icon = copyButton.querySelector('.material-symbols-outlined');

    if (!icon) return;

    icon.textContent = 'check';

    setTimeout(() => {
      icon.textContent = 'content_copy';
    }, 1500);
  });

});