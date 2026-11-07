const allowMotion = document.documentElement.classList.contains('motion-ready')
  && window.matchMedia('(prefers-reduced-motion: no-preference)').matches;

if (allowMotion) {
  const reveal = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        reveal.unobserve(entry.target);
      }
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.01 });

  document.querySelectorAll('[data-reveal]').forEach((element) => {
    const rect = element.getBoundingClientRect();

    if (rect.top < window.innerHeight * 0.92 && rect.bottom > 0) {
      element.classList.add('is-visible');
      return;
    }

    reveal.observe(element);
  });

  document.documentElement.classList.add('has-motion');

  const figureStats = document.querySelector('.home-studio-stats');
  const figures = figureStats ? [...figureStats.querySelectorAll('[data-count]')] : [];

  if (figureStats && figures.length) {
    const countFigures = () => {
      const items = figures.flatMap((element) => {
        const match = (element.dataset.count || '').trim().match(/^(\D*)(\d[\d,]*)(.*)$/);

        if (!match) {
          return [];
        }

        const target = Number(match[2].replaceAll(',', ''));

        if (!Number.isFinite(target)) {
          return [];
        }

        const grouped = match[2].includes(',');

        return [{
          element,
          target,
          format: (value) => `${match[1]}${grouped ? value.toLocaleString('en-US') : String(value)}${match[3]}`,
        }];
      });

      if (!items.length) {
        return;
      }

      const duration = 1600;
      const start = performance.now();

      items.forEach((item) => {
        item.element.textContent = item.format(0);
      });

      const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - (1 - progress) ** 3;

        items.forEach((item) => {
          item.element.textContent = item.format(Math.round(eased * item.target));
        });

        if (progress < 1) {
          requestAnimationFrame(tick);
        }
      };

      requestAnimationFrame(tick);
    };

    const figureSeen = () => {
      const rect = figureStats.getBoundingClientRect();

      return rect.top < window.innerHeight * 0.86 && rect.bottom > window.innerHeight * 0.12;
    };

    if (figureSeen()) {
      countFigures();
    } else {
      const figuresInView = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
          countFigures();
          figuresInView.disconnect();
        }
      }, { rootMargin: '0px 0px -12% 0px', threshold: 0.2 });

      figuresInView.observe(figureStats);
    }
  }
}

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
    document.querySelector('[data-site-header]')?.classList.remove('is-header-hidden');
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

const siteHeader = document.querySelector('[data-site-header]');

if (siteHeader instanceof HTMLElement) {
  const root = document.documentElement;
  let lastY = window.scrollY;

  const measureHeader = () => {
    root.style.setProperty('--site-header-height', `${siteHeader.offsetHeight}px`);
  };

  const placeStick = () => {
    const adminBar = document.getElementById('wpadminbar');
    const admin = adminBar instanceof HTMLElement ? adminBar.offsetHeight : 0;
    const top = siteHeader.classList.contains('is-header-hidden') ? admin : siteHeader.offsetHeight;

    root.style.setProperty('--journal-stick', `${top}px`);
  };

  const revealHeader = () => {
    siteHeader.classList.remove('is-header-hidden');
  };

  measureHeader();
  placeStick();
  window.addEventListener('resize', () => {
    measureHeader();
    placeStick();
  });

  siteHeader.addEventListener('focusin', revealHeader);

  document.addEventListener('focusin', (event) => {
    const target = event.target;

    if (!(target instanceof HTMLElement) || siteHeader.contains(target) || siteMenu instanceof HTMLDialogElement && siteMenu.open) {
      return;
    }

    const limit = siteHeader.classList.contains('is-header-hidden') ? 0 : siteHeader.offsetHeight;
    const top = target.getBoundingClientRect().top;

    if (top < limit) {
      window.scrollBy({ top: top - limit - 8, behavior: 'auto' });
    }
  });

  if (siteHeader.hasAttribute('data-header-scroll')) {
    const onScroll = () => {
      const y = window.scrollY;
      const menuIsOpen = siteMenu instanceof HTMLDialogElement && siteMenu.open;
      const focused = siteHeader.contains(document.activeElement);

      siteHeader.classList.toggle('is-scrolled', y > 24);

      if (allowMotion && !menuIsOpen && !focused && y > lastY && y > siteHeader.offsetHeight) {
        siteHeader.classList.add('is-header-hidden');
      } else {
        revealHeader();
      }

      lastY = y;
      placeStick();
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }
}

if (allowMotion) {
  const quoteRails = [...document.querySelectorAll('[data-quote-rail]')];
  const quoteMotion = window.matchMedia('(min-width: 64rem) and (prefers-reduced-motion: no-preference)');

  if (quoteRails.length) {
    const quotePeek = () => parseFloat(getComputedStyle(document.documentElement).fontSize) * 5.25;

    const measureQuotes = () => {
      quoteRails.forEach((section) => {
        const cards = [...section.querySelectorAll('[data-quote-card]')];

        cards.forEach((card) => {
          card.style.width = '';
        });

        section.dataset.quoteFull = String(cards[0] ? cards[0].getBoundingClientRect().width : 0);
      });
    };

    const placeQuotes = () => {
      if (!quoteMotion.matches) {
        quoteRails.forEach((section) => {
          section.querySelectorAll('[data-quote-card]').forEach((card) => {
            card.style.width = '';
          });
        });

        return;
      }

      const peek = quotePeek();

      quoteRails.forEach((section) => {
        const cards = [...section.querySelectorAll('[data-quote-card]')];
        const full = Number(section.dataset.quoteFull) || 0;

        if (!cards.length || full <= peek) {
          return;
        }

        const travel = section.offsetHeight - window.innerHeight;
        const progress = travel > 0 ? Math.min(1, Math.max(0, -section.getBoundingClientRect().top / travel)) : 0;
        const step = 1 / cards.length;

        cards.forEach((card, index) => {
          const local = Math.min(1, Math.max(0, (progress - (index * step)) / step));

          card.style.width = `${full - ((full - peek) * local)}px`;
        });
      });
    };

    let quoteFrame = 0;

    const queueQuotes = () => {
      if (quoteFrame) {
        return;
      }

      quoteFrame = window.requestAnimationFrame(() => {
        quoteFrame = 0;
        placeQuotes();
      });
    };

    measureQuotes();
    placeQuotes();
    window.addEventListener('scroll', queueQuotes, { passive: true });
    window.addEventListener('resize', () => {
      measureQuotes();
      placeQuotes();
    });
    quoteMotion.addEventListener('change', () => {
      measureQuotes();
      placeQuotes();
    });
  }

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
