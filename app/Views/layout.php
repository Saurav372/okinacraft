<?php
$storeName = store_setting('store_name', 'Okina Craft');
$storeEmail = store_setting('store_email', 'support@okinacraft.com');
$whatsappNo = store_setting('whatsapp_number', '7827747556');
$whatsappPrefill = store_setting('whatsapp_prefill', 'Hi Okina Craft, I need help with a product or order.');
$instagram = store_setting('instagram_handle', 'okina_craft');
$businessAddress = store_setting('business_address', 'Hingoli, Maharashtra');
$cartCount = (new \App\Libraries\CartService())->count();
$activeNav = $activeNav ?? '';
$session = session();
$customerAuth = new \App\Libraries\CustomerAuthService();
$currentCustomer = $customerAuth->customer();
$wishlistCount = $currentCustomer ? count((new \App\Models\CustomerWishlistModel())->getProductIds((int)$currentCustomer['id'])) : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
  <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
  <title><?= e($pageTitle ?? $storeName) ?></title>
  <!-- Favicon & Touch Icons -->
  <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon-32x32.png') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('favicon-16x16.png') ?>">
  <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('apple-touch-icon.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/mask-blobs.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/theme.css') ?>">
  <script>window.OC_BASE = <?= json_encode(rtrim(base_url(), '/')) ?>; window.CS_BASE = window.OC_BASE;</script>
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
      <div class="header__left">
        <button type="button" id="open-mobile-menu" class="header__icon header__mobile-toggle" aria-label="Menu">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16"><path fill="currentColor" d="M1 .5a.5.5 0 1 0 0 1h15.71a.5.5 0 0 0 0-1zM.5 8a.5.5 0 0 1 .5-.5h15.71a.5.5 0 0 1 0 1H1A.5.5 0 0 1 .5 8m0 7a.5.5 0 0 1 .5-.5h15.71a.5.5 0 0 1 0 1H1a.5.5 0 0 1-.5-.5"/></svg>
        </button>

        <a href="<?= base_url('/') ?>" class="header__heading-link" aria-label="<?= e($storeName) ?>">
          <div class="header__heading-logo-wrapper">
            <img src="<?= base_url('assets/media/logo.png') ?>" alt="<?= e($storeName) ?>" width="145" height="52" class="header__heading-logo">
          </div>
        </a>
      </div>

      <nav class="header__inline-menu" aria-label="Main navigation">
        <ul class="list-menu--inline" role="list">
          <li>
            <a href="<?= base_url('/') ?>" class="header__menu-item">
              <span class="<?= $activeNav === 'home' ? 'header__active-menu-item' : '' ?>">Home</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('/collections/all') ?>" class="header__menu-item">
              <span class="<?= $activeNav === 'catalog' ? 'header__active-menu-item' : '' ?>">Products</span>
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

        <!-- Wishlist Icon with Dynamic Count Bubble -->
        <a href="<?= base_url('/wishlist') ?>" class="header__icon" aria-label="My Wishlist" title="My Wishlist" style="position: relative;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
          <span id="wishlist-count-bubble" class="cart-count-bubble <?= $wishlistCount <= 0 ? 'hidden' : '' ?>"><?= $wishlistCount ?></span>
        </a>

        <!-- Customer Account Icon -->
        <a href="<?= $currentCustomer ? base_url('/account') : base_url('/account/login') ?>" class="header__icon" <?= $currentCustomer ? '' : 'data-open-auth-modal="login"' ?> aria-label="<?= $currentCustomer ? 'My Account (' . e($currentCustomer['name']) . ')' : 'Sign In' ?>" title="<?= $currentCustomer ? 'My Account (' . e($currentCustomer['name']) . ')' : 'Sign In' ?>">
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
      <li><a href="<?= base_url('/collections/all') ?>" class="menu-drawer__link <?= $activeNav === 'catalog' ? 'active' : '' ?>">Products</a></li>
      <li><a href="<?= base_url('/pages/contact') ?>" class="menu-drawer__link <?= $activeNav === 'contact' ? 'active' : '' ?>">Contact</a></li>
      <li><a href="<?= base_url('/wishlist') ?>" class="menu-drawer__link <?= $activeNav === 'wishlist' ? 'active' : '' ?>">My Wishlist (<span id="mobile-wishlist-count"><?= $wishlistCount ?></span>)</a></li>
      <?php if ($currentCustomer): ?>
        <li><a href="<?= base_url('/account') ?>" class="menu-drawer__link <?= $activeNav === 'account' ? 'active' : '' ?>">My Account (<?= e($currentCustomer['name']) ?>)</a></li>
        <li><a href="<?= base_url('/account/logout') ?>" class="menu-drawer__link" style="color: #dc2626;">Sign Out</a></li>
      <?php else: ?>
        <li><a href="<?= base_url('/account/login') ?>" class="menu-drawer__link <?= $activeNav === 'account' ? 'active' : '' ?>" data-open-auth-modal="login">Sign In / Register</a></li>
        <li><a href="<?= base_url('/account/track') ?>" class="menu-drawer__link">Track Guest Order</a></li>
      <?php endif; ?>
      <li><a href="<?= base_url('/cart') ?>" class="menu-drawer__link">Cart (<?= $cartCount ?>)</a></li>
      <li>
        <a href="https://www.instagram.com/<?= e($instagram) ?>/" target="_blank" rel="noopener noreferrer" class="menu-drawer__link" style="display:flex;align-items:center;gap:0.8rem;color:#e1306c;">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
          </svg>
          <span>Instagram (@<?= e($instagram) ?>)</span>
        </a>
      </li>
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

  <!-- Dedicated Help & Support Bar (WhatsApp & Instagram) -->
  <section class="need-help-bar" aria-label="Customer Support">
    <div class="page-width">
      <div class="need-help-bar__wrap">
        <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode($whatsappPrefill) ?>" target="_blank" rel="noopener noreferrer" class="need-help-item" title="Need Help? Chat with us on WhatsApp">
          <div class="need-help-icon need-help-icon--wa">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
              <path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L0 24l6.5-1.7a11.8 11.8 0 0 0 5.6 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4ZM12.1 21.7a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.9 1 1-3.8-.3-.4a9.8 9.8 0 1 1 8.6 4.8Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-1-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.8 4.3.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.1-1.3-.1-.2-.3-.3-.6-.4Z"/>
            </svg>
          </div>
          <span class="need-help-text">Need Help?</span>
        </a>

        <a href="https://www.instagram.com/<?= e($instagram) ?>/" target="_blank" rel="noopener noreferrer" class="need-help-item" title="Need Help? Message us on Instagram">
          <div class="need-help-icon need-help-icon--ig">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
          </div>
          <span class="need-help-text">Need Help?</span>
        </a>
      </div>
    </div>
  </section>

  <!-- Floating E-Commerce Trust Bar (Store Guarantees) -->
  <section class="footer-trust-bar" aria-label="Store Guarantees">
    <div class="page-width">
      <div class="footer-trust-card">
        <div class="footer-trust-grid">
          <div class="footer-trust-item">
            <div class="footer-trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="m9 12 2 2 4-4"/>
              </svg>
            </div>
            <div>
              <div class="footer-trust-title">Secure Payments</div>
              <div class="footer-trust-desc">100% Encrypted &amp; Safe Checkout</div>
            </div>
          </div>

          <div class="footer-trust-item">
            <div class="footer-trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="3" width="15" height="13"/>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
              </svg>
            </div>
            <div>
              <div class="footer-trust-title">Reliable Shipping</div>
              <div class="footer-trust-desc">All-India Fast Doorstep Delivery</div>
            </div>
          </div>

          <div class="footer-trust-item">
            <div class="footer-trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
              </svg>
            </div>
            <div>
              <div class="footer-trust-title">Customer Support</div>
              <div class="footer-trust-desc">Quick Help on WhatsApp &amp; Email</div>
            </div>
          </div>

          <div class="footer-trust-item">
            <div class="footer-trust-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="6"/>
                <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
              </svg>
            </div>
            <div>
              <div class="footer-trust-title">Premium Materials</div>
              <div class="footer-trust-desc">Quality Fabrics &amp; Printing</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Complete Premium Dark Modern 5-Column E-Commerce Footer -->
  <footer class="footer footer--dark">
    <div class="page-width">
      <div class="footer-main-grid">
        <!-- Col 1: Brand & Contact & Socials -->
        <div class="footer-col footer-col--brand">
          <a href="<?= base_url('/') ?>" style="text-decoration:none;display:inline-block;" aria-label="<?= e($storeName) ?>">
            <img class="footer-brand__logo" src="<?= base_url('assets/media/logo-light.png') ?>" alt="<?= e($storeName) ?>">
          </a>
          <p class="footer-brand__text">
            Your trusted destination for quality products. Shop with confidence and enjoy a seamless shopping experience with premium streetwear, custom oversized t-shirts, and apparel.
          </p>

          <div class="footer-contact-list">
            <div class="footer-contact-item">
              <span class="footer-contact-icon">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                  <path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L0 24l6.5-1.7a11.8 11.8 0 0 0 5.6 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4ZM12.1 21.7a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.9 1 1-3.8-.3-.4a9.8 9.8 0 1 1 8.6 4.8Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-1-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.8 4.3.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.1-1.3-.1-.2-.3-.3-.6-.4Z"/>
                </svg>
              </span>
              <span>WhatsApp: <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode($whatsappPrefill) ?>" target="_blank" rel="noopener noreferrer">+91 <?= e($whatsappNo) ?></a></span>
            </div>

            <div class="footer-contact-item">
              <span class="footer-contact-icon">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="20" height="16" x="2" y="4" rx="2"/>
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
              </span>
              <span>Email: <a href="mailto:<?= e($storeEmail) ?>"><?= e($storeEmail) ?></a></span>
            </div>

            <div class="footer-contact-item">
              <span class="footer-contact-icon">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
              </span>
              <span><?= e($businessAddress) ?>, India</span>
            </div>
          </div>
        </div>

        <!-- Col 2: Shop -->
        <div class="footer-col footer-col--accordion">
          <div class="footer-col__title">Shop</div>
          <div class="footer-title-bar"></div>
          <ul class="footer-icon-links">
            <li>
              <a href="<?= base_url('/collections/all') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 9-9 9-9-9 9-9Z"/><circle cx="12" cy="7" r="1.5"/></svg>
                <span>All Products</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/collections/all?sort_by=created-descending') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                <span>New Arrivals</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/collections/all?sort_by=best-selling') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Best Sellers</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/search?q=oversized') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
                <span>Oversized T-Shirts</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/search?q=hoodie') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3H8l-4 5v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-4-5z"/><polyline points="9 3 9 9 12 11 15 9 15 3"/></svg>
                <span>Hoodies &amp; Fleece</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/collections/all') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                <span>Special Offers</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Col 3: Customer Service -->
        <div class="footer-col footer-col--accordion">
          <div class="footer-col__title">Customer Service</div>
          <div class="footer-title-bar"></div>
          <ul class="footer-icon-links">
            <li>
              <a href="<?= base_url('/contact') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>
                <span>Contact Us</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/account') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                <span>Track Your Order</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/policies/shipping-policy') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <span>Shipping Policy</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/policies/refund-policy') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                <span>Refund Policy</span>
              </a>
            </li>
            <li>
              <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode($whatsappPrefill) ?>" target="_blank" rel="noopener noreferrer" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><circle cx="9" cy="10" r="1" fill="currentColor"/><circle cx="12" cy="10" r="1" fill="currentColor"/><circle cx="15" cy="10" r="1" fill="currentColor"/></svg>
                <span>Live Chat Support</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Col 4: Policies -->
        <div class="footer-col footer-col--accordion">
          <div class="footer-col__title">Policies</div>
          <div class="footer-title-bar"></div>
          <ul class="footer-icon-links">
            <li>
              <a href="<?= base_url('/policies/privacy-policy') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Privacy Policy</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/policies/terms-of-service') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Terms &amp; Conditions</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/policies/shipping-policy') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <span>Shipping &amp; Delivery</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/policies/refund-policy') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M3 21v-5h5"/></svg>
                <span>Cancellation &amp; Refund</span>
              </a>
            </li>
            <li>
              <a href="<?= base_url('/policies/contact-information') ?>" class="footer-icon-link">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>Contact Information</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Col 5: Stay Connected & Newsletter & Perks -->
        <div class="footer-col footer-col--connect">
          <div class="footer-col__title">Stay Connected</div>
          <div class="footer-title-bar"></div>
          <p class="footer-newsletter__desc">
            Subscribe to receive updates about new products, exclusive anime prints, and volume discounts.
          </p>

          <form class="footer-newsletter__form" id="footer-newsletter-form" onsubmit="event.preventDefault(); document.getElementById('footer-newsletter-success').style.display='block'; this.querySelector('input').value='';">
            <div class="footer-newsletter-row">
              <div class="footer-newsletter-input-wrap">
                <span class="footer-newsletter-input-icon">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                  </svg>
                </span>
                <input type="email" placeholder="Enter your email address" required aria-label="Email address for newsletter">
              </div>
              <button type="submit" class="footer-newsletter-submit">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                  <path d="m22 2-7 20-4-9-9-4Z"/>
                  <path d="M22 2 11 13"/>
                </svg>
                <span>Subscribe</span>
              </button>
            </div>
            <div class="footer-newsletter__success" id="footer-newsletter-success" style="display:none; color:#4ade80; margin-top:0.8rem; font-size:1.25rem;">
              &check; Thank you for subscribing!
            </div>
          </form>
        </div>
      </div>

      <!-- Payment Methods & Quick Policies Row -->
      <div class="footer-payment-row">
        <div class="footer-payment-group">
          <div class="footer-payment-label">
            <span class="footer-payment-lock">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </span>
            <span>We Accept 100% Secure Payments:</span>
          </div>

          <div class="footer-payment-badges" aria-label="Accepted Payment Methods">
            <span class="payment-badge-dark" title="UPI (PhonePe, Google Pay, Paytm, BHIM)">
              <svg viewBox="0 0 38 22" width="38" height="22">
                <rect width="38" height="22" rx="4" fill="#141e2c"/>
                <text x="19" y="15" fill="#22c55e" font-size="10.5" font-weight="900" font-family="sans-serif" text-anchor="middle">UPI</text>
              </svg>
            </span>
            <span class="payment-badge-dark" title="RuPay Card">
              <svg viewBox="0 0 42 22" width="42" height="22">
                <rect width="42" height="22" rx="4" fill="#141e2c"/>
                <text x="21" y="15" fill="#38bdf8" font-size="9.5" font-weight="800" font-family="sans-serif" text-anchor="middle">RuPay</text>
              </svg>
            </span>
            <span class="payment-badge-dark" title="Visa">
              <svg viewBox="0 0 38 22" width="38" height="22">
                <rect width="38" height="22" rx="4" fill="#141e2c"/>
                <text x="19" y="15" fill="#60a5fa" font-size="11" font-style="italic" font-weight="900" font-family="sans-serif" text-anchor="middle">VISA</text>
              </svg>
            </span>
            <span class="payment-badge-dark" title="Mastercard">
              <svg viewBox="0 0 40 22" width="40" height="22">
                <rect width="40" height="22" rx="4" fill="#141e2c"/>
                <circle cx="16" cy="11" r="6" fill="#ef4444"/>
                <circle cx="24" cy="11" r="6" fill="#f59e0b" fill-opacity="0.85"/>
              </svg>
            </span>
            <span class="payment-badge-dark" title="Net Banking">
              <svg viewBox="0 0 56 22" width="56" height="22">
                <rect width="56" height="22" rx="4" fill="#141e2c"/>
                <text x="28" y="14" fill="#cbd5e1" font-size="8" font-weight="700" font-family="sans-serif" text-anchor="middle">NET BANKING</text>
              </svg>
            </span>
            <?php if (store_setting('cod_enabled', '0') === '1'): ?>
            <span class="payment-badge-dark" title="Cash on Delivery Available">
              <svg viewBox="0 0 38 22" width="38" height="22">
                <rect width="38" height="22" rx="4" fill="#141e2c"/>
                <text x="19" y="15" fill="#f87171" font-size="9.5" font-weight="800" font-family="sans-serif" text-anchor="middle">COD</text>
              </svg>
            </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="footer-quick-policies">
          <a href="<?= base_url('/policies/privacy-policy') ?>">Privacy Policy</a>
          <span>|</span>
          <a href="<?= base_url('/policies/terms-of-service') ?>">Terms &amp; Conditions</a>
          <span>|</span>
          <a href="<?= base_url('/policies/refund-policy') ?>">Refund Policy</a>
          <span>|</span>
          <a href="<?= base_url('/policies/shipping-policy') ?>">Shipping Policy</a>
          <span>|</span>
          <a href="<?= base_url('/policies/contact-information') ?>">Contact</a>
        </div>
      </div>

      <!-- Copyright Row -->
      <div class="footer-copyright-row">
        <div class="footer-copyright-text">
          &copy; <?= date('Y') ?> <strong><?= e($storeName) ?></strong>. All Rights Reserved.
        </div>
      </div>
    </div>
  </footer>

  <!-- Customer Authentication Popup Modal (Signup & Signin) -->
  <?= $this->include('store/partials/auth_modal') ?>

  <script src="<?= base_url('assets/js/storefront.js') ?>" defer></script>
</body>
</html>
