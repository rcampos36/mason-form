const siteMenu = document.querySelector('[data-site-menu]');
const menuOpen = document.querySelector('[data-menu-open]');
const menuClose = document.querySelector('[data-menu-close]');
const menuStatus = document.querySelector('[data-menu-status]');
const desktopNav = window.matchMedia('(min-width: 64rem)');

function setMenuOpen(open) {
  menuOpen?.setAttribute('aria-expanded', open ? 'true' : 'false');
  document.documentElement.classList.toggle('overflow-hidden', open);

  if (menuStatus instanceof HTMLElement) {
    menuStatus.textContent = open ? menuStatus.dataset.opened || '' : menuStatus.dataset.closed || '';
  }
}

if (siteMenu instanceof HTMLDialogElement && menuOpen instanceof HTMLButtonElement) {
  menuOpen.addEventListener('click', () => {
    siteMenu.showModal();
    setMenuOpen(true);

    if (menuClose instanceof HTMLButtonElement) {
      menuClose.focus();
    }
  });

  siteMenu.addEventListener('close', () => {
    setMenuOpen(false);
    menuOpen.focus();
  });

  menuClose?.addEventListener('click', () => {
    siteMenu.close();
  });

  siteMenu.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && siteMenu.open) {
      siteMenu.close();
    }
  });

  siteMenu.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target : null;

    if (target?.closest('a')) {
      siteMenu.close();
    }
  });

  desktopNav.addEventListener('change', (event) => {
    if (event.matches && siteMenu.open) {
      siteMenu.close();
    }
  });
}

const dialog = document.querySelector('[data-section-lightbox]');

if (dialog instanceof HTMLDialogElement) {
  const image = dialog.querySelector('img');
  const caption = dialog.querySelector('[data-caption]');

  document.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target : null;
    const trigger = target?.closest('[data-lightbox]');

    if (trigger instanceof HTMLElement && image instanceof HTMLImageElement) {
      image.src = trigger.dataset.src || '';
      image.alt = trigger.dataset.alt || '';
      image.removeAttribute('aria-hidden');

      if (caption instanceof HTMLElement) {
        caption.textContent = trigger.dataset.caption || '';
        caption.hidden = caption.textContent === '';
      }

      dialog.showModal();
    }

    if (target?.closest('[data-lightbox-close]')) {
      dialog.close();
    }
  });

  dialog.addEventListener('click', (event) => {
    if (event.target === dialog) {
      dialog.close();
    }
  });

  dialog.addEventListener('close', () => {
    if (image instanceof HTMLImageElement) {
      image.setAttribute('aria-hidden', 'true');
      image.removeAttribute('src');
      image.alt = '';
    }
  });
}
