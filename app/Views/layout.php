<?php
$storeName = store_setting('store_name', 'Okina Craft');
$whatsappNo = store_setting('whatsapp_number', '918380808435');
$whatsappPrefill = store_setting('whatsapp_prefill', 'Hi Okina Craft, I need help with a product or order.');
$instagram = store_setting('instagram_handle', 'okinacraft');
$cartCount = (new \App\Libraries\CartService())->count();
$activeNav = $activeNav ?? '';
$session = session();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? $storeName) ?></title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/media/logo.png') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/mask-blobs.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/theme.css') ?>">
  <script>window.CS_BASE = <?= json_encode(rtrim(base_url(), '/')) ?>;</script>
</head>
<body class="gradient">

  <!-- Exact Dawn Arch SVG ClipPath Definition -->
  <svg class="shape--mask" width="0" height="0" viewBox="0 0 100 100" aria-hidden="true">
    <clipPath id="Shape-Arch" clipPathUnits="objectBoundingBox" transform="scale(0.01, 0.01)">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M100 50C100 22.3858 77.6142 0 50 0C22.3858 0 0 22.3858 0 50V100H50H100V50Z"/>
    </clipPath>
  </svg>

  <!-- Sticky Header (Okina Craft Navigation) -->
  <div class="section-header">
    <header class="header page-width">
      <button type="button" id="open-mobile-menu" class="header__icon header__mobile-toggle" aria-label="Menu">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16"><path fill="currentColor" d="M1 .5a.5.5 0 1 0 0 1h15.71a.5.5 0 0 0 0-1zM.5 8a.5.5 0 0 1 .5-.5h15.71a.5.5 0 0 1 0 1H1A.5.5 0 0 1 .5 8m0 7a.5.5 0 0 1 .5-.5h15.71a.5.5 0 0 1 0 1H1a.5.5 0 0 1-.5-.5"/></svg>
      </button>

      <a href="<?= base_url('/') ?>" class="header__heading-link" aria-label="<?= e($storeName) ?>">
        <div class="header__heading-logo-wrapper">
          <img src="<?= base_url('assets/media/logo.png') ?>" alt="<?= e($storeName) ?>" width="145" height="52" class="header__heading-logo" style="max-height:54px;width:100%;height:auto;object-fit:contain;display:block;">
        </div>
      </a>

      <nav class="header__inline-menu" aria-label="Main navigation">
        <ul class="list-menu--inline" role="list">
          <li>
            <a href="<?= base_url('/') ?>" class="header__menu-item">
              <span class="<?= $activeNav === 'home' ? 'header__active-menu-item' : '' ?>">Home</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('/collections/all') ?>" class="header__menu-item">
              <span class="<?= $activeNav === 'catalog' ? 'header__active-menu-item' : '' ?>">Catalog</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('/pages/contact') ?>" class="header__menu-item">
              <span class="<?= $activeNav === 'contact' ? 'header__active-menu-item' : '' ?>">Contact</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="header__icons">
        <button type="button" class="header__icon" data-open-search aria-label="Search">
          <svg fill="none" viewBox="0 0 18 19"><path fill="currentColor" fill-rule="evenodd" d="M11.03 11.68A5.784 5.784 0 1 1 2.85 3.5a5.784 5.784 0 0 1 8.18 8.18m.26 1.12a6.78 6.78 0 1 1 .72-.7l5.4 5.4a.5.5 0 1 1-.71.7z" clip-rule="evenodd"/></svg>
        </button>

        <a href="<?= base_url('/account') ?>" class="header__icon" aria-label="Track Order / Account" title="Track Order">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 19"><path fill="currentColor" fill-rule="evenodd" d="M6 4.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-4a4 4 0 1 0 0 8 4 4 0 0 0 0-8m5.58 12.15c1.12.82 1.83 2.24 1.91 4.85H1.51c.08-2.6.79-4.03 1.9-4.85C4.66 11.75 6.5 11.5 9 11.5s4.35.26 5.58 1.15M9 10.5c-2.5 0-4.65.24-6.17 1.35C1.27 12.98.5 14.93.5 18v.5h17V18c0-3.07-.77-5.02-2.33-6.15-1.52-1.1-3.67-1.35-6.17-1.35" clip-rule="evenodd"/></svg>
        </a>

        <a href="<?= base_url('/cart') ?>" class="header__icon header__icon--cart" aria-label="Cart">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 40 40"><path fill="currentColor" fill-rule="evenodd" d="M15.75 11.8h-3.16l-.77 11.6a5 5 0 0 0 4.99 5.34h7.38a5 5 0 0 0 4.99-5.33L28.4 11.8zm0 1h-2.22l-.71 10.67a4 4 0 0 0 3.99 4.27h7.38a4 4 0 0 0 4-4.27l-.72-10.67h-2.22v.63a4.75 4.75 0 1 1-9.5 0zm8.5 0h-7.5v.63a3.75 3.75 0 1 0 7.5 0z"/></svg>
          <span id="cart-count-bubble" class="cart-count-bubble <?= $cartCount <= 0 ? 'hidden' : '' ?>"><?= $cartCount ?></span>
        </a>
      </div>
    </header>
  </div>

  <!-- Mobile Menu Drawer -->
  <div id="mobile-drawer-backdrop" class="menu-drawer-backdrop"></div>
  <aside id="mobile-menu-drawer" class="menu-drawer" aria-label="Mobile navigation">
    <div class="menu-drawer__header">
      <strong style="font-size:1.7rem;color:#000;"><?= e($storeName) ?></strong>
      <button type="button" id="close-mobile-menu" class="header__icon" aria-label="Close menu">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 17"><path fill="currentColor" d="M.865 15.978a.5.5 0 0 0 .707.707l7.433-7.431 7.579 7.282a.501.501 0 0 0 .846-.37.5.5 0 0 0-.153-.351L9.712 8.546l7.417-7.416a.5.5 0 1 0-.707-.708L8.991 7.853 1.413.573a.5.5 0 1 0-.693.72l7.563 7.268z"/></svg>
      </button>
    </div>
    <ul class="menu-drawer__list">
      <li><a href="<?= base_url('/') ?>" class="menu-drawer__link <?= $activeNav === 'home' ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= base_url('/collections/all') ?>" class="menu-drawer__link <?= $activeNav === 'catalog' ? 'active' : '' ?>">Catalog</a></li>
      <li><a href="<?= base_url('/pages/contact') ?>" class="menu-drawer__link <?= $activeNav === 'contact' ? 'active' : '' ?>">Contact</a></li>
      <li><a href="<?= base_url('/account') ?>" class="menu-drawer__link">Track Order</a></li>
      <li><a href="<?= base_url('/cart') ?>" class="menu-drawer__link">Cart (<?= $cartCount ?>)</a></li>
    </ul>
  </aside>

  <!-- Predictive Search Modal -->
  <div id="search-modal" class="search-modal" role="dialog" aria-modal="true" aria-label="Search">
    <div class="search-modal__overlay" data-close-search></div>
    <div class="search-modal__bar">
      <div class="search-modal__inner">
        <form action="<?= base_url('/search') ?>" method="get" class="search-modal__form" role="search">
          <input
            type="search"
            name="q"
            id="predictive-search-input"
            class="search-modal__input"
            placeholder="Search products, t-shirts, hoodies, mugs, mouse pads..."
            autocomplete="off"
          >
          <button type="submit" class="search-modal__submit" aria-label="Search">
            <svg width="18" height="19" fill="none" viewBox="0 0 18 19"><path fill="currentColor" fill-rule="evenodd" d="M11.03 11.68A5.784 5.784 0 1 1 2.85 3.5a5.784 5.784 0 0 1 8.18 8.18m.26 1.12a6.78 6.78 0 1 1 .72-.7l5.4 5.4a.5.5 0 1 1-.71.7z" clip-rule="evenodd"/></svg>
          </button>
        </form>
        <button type="button" class="header__icon" data-close-search aria-label="Close search">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 17"><path fill="currentColor" d="M.865 15.978a.5.5 0 0 0 .707.707l7.433-7.431 7.579 7.282a.501.501 0 0 0 .846-.37.5.5 0 0 0-.153-.351L9.712 8.546l7.417-7.416a.5.5 0 1 0-.707-.708L8.991 7.853 1.413.573a.5.5 0 1 0-.693.72l7.563 7.268z"/></svg>
        </button>
      </div>
      <div id="predictive-search-results" class="predictive-results"></div>
    </div>
  </div>

  <!-- Exact Dawn Cart Notification Popup -->
  <div id="cart-notification" class="cart-notification" role="dialog" aria-label="Item added to your cart">
    <div class="cart-notification__header">
      <h2 class="cart-notification__heading">
        <svg width="12" height="9" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 9"><path fill="currentColor" fill-rule="evenodd" d="M11.35.643a.5.5 0 0 1 .006.707l-6.77 6.886a.5.5 0 0 1-.719-.006L.638 4.845a.5.5 0 1 1 .724-.69l2.872 3.011 6.41-6.517a.5.5 0 0 1 .707-.006z" clip-rule="evenodd"/></svg>
        Item added to your cart
      </h2>
      <button type="button" class="header__icon" style="width:3rem;height:3rem;" data-close-cart-notif aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 17"><path fill="currentColor" d="M.865 15.978a.5.5 0 0 0 .707.707l7.433-7.431 7.579 7.282a.501.501 0 0 0 .846-.37.5.5 0 0 0-.153-.351L9.712 8.546l7.417-7.416a.5.5 0 1 0-.707-.708L8.991 7.853 1.413.573a.5.5 0 1 0-.693.72l7.563 7.268z"/></svg>
      </button>
    </div>
    <div id="cart-notification-product" class="cart-notification__product"></div>
    <div class="cart-notification__links">
      <a href="<?= base_url('/cart') ?>" id="cart-notification-view-btn" class="button button--secondary button--full-width">View cart</a>
      <a href="<?= base_url('/checkout') ?>" class="button button--full-width">Check out</a>
      <button type="button" class="link--underline" style="border:0;background:none;cursor:pointer;font:inherit;font-size:1.4rem;margin-top:0.4rem;" data-close-cart-notif>Continue shopping</button>
    </div>
  </div>

  <?php if ($session->getFlashdata('error')): ?>
    <div class="flash-stack">
      <div class="flash flash--error">
        <span><?= e($session->getFlashdata('error')) ?></span>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($session->getFlashdata('success')): ?>
    <div class="flash-stack">
      <div class="flash flash--success">
        <span><?= e($session->getFlashdata('success')) ?></span>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($session->getFlashdata('info')): ?>
    <div class="flash-stack">
      <div class="flash flash--info">
        <span><?= e($session->getFlashdata('info')) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <main id="MainContent" class="content-for-layout" role="main">
    <?= $this->renderSection('content') ?>
  </main>

  <!-- Okina Craft Custom "Need Help?" WhatsApp & Instagram Section -->
  <section class="cs-help-section">
    <div class="cs-contact-wrap">
      <a class="cs-contact-link"
         href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode($whatsappPrefill) ?>"
         target="_blank" rel="noopener noreferrer">
        <span class="cs-contact-icon cs-whatsapp-icon">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L0 24l6.5-1.7a11.8 11.8 0 0 0 5.6 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4ZM12.1 21.7a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.9 1 1-3.8-.3-.4a9.8 9.8 0 1 1 8.6 4.8Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-1-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.8 4.3.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.1-1.3-.1-.2-.3-.3-.6-.4Z"/>
          </svg>
        </span>
        <span>Need Help?</span>
      </a>

      <a class="cs-contact-link"
         href="https://ig.me/m/<?= e($instagram) ?>"
         target="_blank" rel="noopener noreferrer">
        <span class="cs-contact-icon cs-instagram-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5"/>
            <circle cx="12" cy="12" r="4"/>
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
          </svg>
        </span>
        <span>Need Help?</span>
      </a>
    </div>
  </section>

  <!-- Okina Craft Minimal Footer -->
  <footer class="footer">
    <div class="page-width">
      <div class="footer__copyright">
        <small>&copy; <?= date('Y') ?>, <a href="<?= base_url('/') ?>"><?= e($storeName) ?></a></small>
        <ul class="footer__policies" role="list">
          <li><small><a href="<?= base_url('/policies/privacy-policy') ?>">Privacy policy</a></small></li>
          <li><small><a href="<?= base_url('/policies/refund-policy') ?>">Refund policy</a></small></li>
          <li><small><a href="<?= base_url('/policies/shipping-policy') ?>">Shipping policy</a></small></li>
          <li><small><a href="<?= base_url('/policies/terms-of-service') ?>">Terms of service</a></small></li>
          <li><small><a href="<?= base_url('/policies/contact-information') ?>">Contact information</a></small></li>
        </ul>
      </div>
    </div>
  </footer>

  <script src="<?= base_url('assets/js/storefront.js') ?>" defer></script>
</body>
</html>
