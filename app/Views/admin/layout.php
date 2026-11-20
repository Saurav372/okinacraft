<?php
$adminNav = $adminNav ?? '';
$session = session();
$db = \Config\Database::connect();
$unreadCount = $db->table('contact_messages')->where('status', 'unread')->countAllResults();
$pendingOrders = $db->table('orders')->whereIn('order_status', ['pending', 'confirmed', 'printing'])->countAllResults();
$adminUser = $admin ?? [
    'name'  => 'Okina Craft Admin',
    'email' => 'admin@okinacraft.com',
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
  <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
  <title><?= e($pageTitle ?? 'Admin – Okina Craft') ?></title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/media/okina-mark.png') ?>">

  <!-- Tabler Core CSS (CDN + Local Fallback) -->
  <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" rel="stylesheet"/>
  <link href="<?= base_url('assets/vendor/tabler/tabler.min.css') ?>" rel="stylesheet"/>
  <link href="<?= base_url('assets/vendor/tabler/tabler-icons.min.css') ?>" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --tblr-font-sans-serif: 'Inter', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      --tblr-primary: #1e293b;
      --tblr-primary-rgb: 30, 41, 59;
    }
    body {
      font-feature-settings: "cv03", "cv04", "cv11";
      min-height: 100vh;
    }
    .navbar-vertical.navbar-expand-lg {
      width: 16.5rem;
    }
    @media (min-width: 992px) {
      .navbar-vertical.navbar-expand-lg ~ .page-wrapper {
        margin-left: 16.5rem;
      }
    }

    /* ---- High-Contrast Sidebar Styling for Both Light & Dark Modes ---- */
    aside.navbar-vertical {
      background-color: #111827 !important; /* Insignia / Tabler rich dark */
      border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
      --tblr-navbar-color: rgba(255, 255, 255, 0.75) !important;
      --tblr-navbar-active-color: #ffffff !important;
      --tblr-navbar-hover-color: #ffffff !important;
      --tblr-navbar-bg: #111827 !important;
    }

    /* All sidebar nav links, labels, and icons guaranteed visible on light mode */
    aside.navbar-vertical .navbar-nav .nav-link {
      color: rgba(255, 255, 255, 0.75) !important;
      font-weight: 500 !important;
      padding: 0.65rem 0.85rem !important;
      border-radius: 6px !important;
      margin-bottom: 2px !important;
      transition: all 0.15s ease-in-out !important;
    }

    aside.navbar-vertical .navbar-nav .nav-link .nav-link-title,
    aside.navbar-vertical .navbar-nav .nav-link span:not(.badge) {
      color: rgba(255, 255, 255, 0.75) !important;
      font-size: 0.875rem !important;
    }

    aside.navbar-vertical .navbar-nav .nav-link .nav-link-icon,
    aside.navbar-vertical .navbar-nav .nav-link i {
      color: rgba(255, 255, 255, 0.65) !important;
    }

    /* Hover state */
    aside.navbar-vertical .navbar-nav .nav-link:hover {
      color: #ffffff !important;
      background-color: rgba(255, 255, 255, 0.08) !important;
    }
    aside.navbar-vertical .navbar-nav .nav-link:hover .nav-link-title,
    aside.navbar-vertical .navbar-nav .nav-link:hover span:not(.badge),
    aside.navbar-vertical .navbar-nav .nav-link:hover i {
      color: #ffffff !important;
    }

    /* Active state */
    aside.navbar-vertical .navbar-nav .nav-item.active > .nav-link,
    aside.navbar-vertical .navbar-nav .nav-link.active {
      color: #ffffff !important;
      background-color: rgba(255, 255, 255, 0.14) !important;
      font-weight: 600 !important;
    }
    aside.navbar-vertical .navbar-nav .nav-item.active > .nav-link .nav-link-title,
    aside.navbar-vertical .navbar-nav .nav-item.active > .nav-link span:not(.badge),
    aside.navbar-vertical .navbar-nav .nav-item.active > .nav-link i,
    aside.navbar-vertical .navbar-nav .nav-link.active .nav-link-title,
    aside.navbar-vertical .navbar-nav .nav-link.active span:not(.badge),
    aside.navbar-vertical .navbar-nav .nav-link.active i {
      color: #ffffff !important;
    }

    /* Badges inside sidebar */
    aside.navbar-vertical .badge.badge-counter {
      color: #111827 !important;
      font-weight: 700 !important;
      font-size: 0.72rem !important;
    }

    /* Bottom Profile Box in Sidebar */
    .admin-sidebar-profile {
      background: rgba(255, 255, 255, 0.07) !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      border-radius: 8px !important;
      padding: 0.65rem 0.75rem !important;
    }
    .admin-sidebar-profile .profile-name {
      color: #ffffff !important;
      font-weight: 600 !important;
      font-size: 0.85rem !important;
    }
    .admin-sidebar-profile .profile-email {
      color: rgba(255, 255, 255, 0.55) !important;
      font-size: 0.72rem !important;
    }

    .admin-brand-logo {
      width: 36px;
      height: 36px;
      object-fit: contain;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.08);
      padding: 4px;
    }
    .badge-counter {
      font-size: 0.72rem;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
    }
    .table-thumb {
      width: 48px;
      height: 48px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid rgba(0, 0, 0, 0.08);
      background: #f8fafc;
    }
    .theme-toggle-btn {
      cursor: pointer;
    }
  </style>

  <script>
    // Theme auto-restoration
    (function () {
      var theme = localStorage.getItem('tablerTheme') || 'light';
      document.documentElement.setAttribute('data-bs-theme', theme);
    })();
  </script>
</head>
<body class="layout-fluid">
<div class="page">
  <!-- Sidebar -->
  <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Brand -->
      <h1 class="navbar-brand navbar-brand-autodark my-2">
        <a href="<?= base_url('/admin') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
          <img src="<?= base_url('assets/media/okina-mark.png') ?>" alt="Okina Craft" class="admin-brand-logo">
          <div class="text-start">
            <div class="fw-bold text-white lh-1" style="font-size: 1.05rem; letter-spacing: 0.5px;">OKINA CRAFT</div>
            <small class="text-white-50" style="font-size: 0.68rem; letter-spacing: 1.5px;">ADMIN PANEL</small>
          </div>
        </a>
      </h1>

      <!-- Mobile User menu -->
      <div class="navbar-nav flex-row d-lg-none">
        <div class="nav-item dropdown">
          <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
            <span class="avatar avatar-sm bg-primary text-white rounded-circle"><i class="ti ti-user"></i></span>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow shadow">
            <div class="dropdown-header">Signed in as <strong><?= e($adminUser['name'] ?? 'Admin') ?></strong></div>
            <a href="<?= base_url('/admin/settings') ?>" class="dropdown-item"><i class="ti ti-settings me-2"></i> Settings</a>
            <div class="dropdown-divider"></div>
            <form action="<?= base_url('/admin/logout') ?>" method="post" class="px-2 py-1">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-sm btn-danger w-100"><i class="ti ti-logout me-1"></i> Sign Out</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Navigation links -->
      <div class="collapse navbar-collapse" id="sidebar-menu">
        <ul class="navbar-nav pt-lg-3">
          <li class="nav-item <?= $adminNav === 'dashboard' ? 'active' : '' ?>">
            <a class="nav-link <?= $adminNav === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('/admin') ?>">
              <span class="nav-link-icon d-inline-block me-2">
                <i class="ti ti-layout-dashboard fs-2"></i>
              </span>
              <span class="nav-link-title">Dashboard</span>
            </a>
          </li>

          <li class="nav-item <?= $adminNav === 'orders' ? 'active' : '' ?>">
            <a class="nav-link <?= $adminNav === 'orders' ? 'active' : '' ?> d-flex justify-content-between align-items-center" href="<?= base_url('/admin/orders') ?>">
              <div class="d-flex align-items-center">
                <span class="nav-link-icon d-inline-block me-2">
                  <i class="ti ti-shopping-cart fs-2"></i>
                </span>
                <span class="nav-link-title">Orders</span>
              </div>
              <?php if ($pendingOrders > 0): ?>
                <span class="badge bg-yellow text-dark badge-counter"><?= $pendingOrders ?></span>
              <?php endif; ?>
            </a>
          </li>

          <li class="nav-item <?= $adminNav === 'products' ? 'active' : '' ?>">
            <a class="nav-link <?= $adminNav === 'products' ? 'active' : '' ?>" href="<?= base_url('/admin/products') ?>">
              <span class="nav-link-icon d-inline-block me-2">
                <i class="ti ti-shirt fs-2"></i>
              </span>
              <span class="nav-link-title">Products</span>
            </a>
          </li>

          <li class="nav-item <?= $adminNav === 'collections' ? 'active' : '' ?>">
            <a class="nav-link <?= $adminNav === 'collections' ? 'active' : '' ?>" href="<?= base_url('/admin/collections') ?>">
              <span class="nav-link-icon d-inline-block me-2">
                <i class="ti ti-category fs-2"></i>
              </span>
              <span class="nav-link-title">Collections</span>
            </a>
          </li>

          <li class="nav-item <?= $adminNav === 'messages' ? 'active' : '' ?>">
            <a class="nav-link <?= $adminNav === 'messages' ? 'active' : '' ?> d-flex justify-content-between align-items-center" href="<?= base_url('/admin/messages') ?>">
              <div class="d-flex align-items-center">
                <span class="nav-link-icon d-inline-block me-2">
                  <i class="ti ti-messages fs-2"></i>
                </span>
                <span class="nav-link-title">Messages & Quotes</span>
              </div>
              <?php if ($unreadCount > 0): ?>
                <span class="badge bg-blue text-white badge-counter"><?= $unreadCount ?></span>
              <?php endif; ?>
            </a>
          </li>

          <li class="nav-item <?= $adminNav === 'settings' ? 'active' : '' ?>">
            <a class="nav-link <?= $adminNav === 'settings' ? 'active' : '' ?>" href="<?= base_url('/admin/settings') ?>">
              <span class="nav-link-icon d-inline-block me-2">
                <i class="ti ti-settings fs-2"></i>
              </span>
              <span class="nav-link-title">Settings & Policies</span>
            </a>
          </li>

          <li class="nav-item mt-3 pt-3 border-top border-white-10">
            <a class="nav-link text-white-50" href="<?= base_url('/') ?>" target="_blank">
              <span class="nav-link-icon d-inline-block me-2">
                <i class="ti ti-external-link fs-2"></i>
              </span>
              <span class="nav-link-title">View Live Storefront</span>
            </a>
          </li>
        </ul>

        <!-- Bottom Admin Card in Sidebar -->
        <div class="mt-auto pt-3 border-top border-white-10 d-none d-lg-block pb-3">
          <div class="admin-sidebar-profile d-flex align-items-center mb-2">
            <span class="avatar avatar-sm bg-primary text-white rounded-circle me-2 flex-shrink-0">
              <i class="ti ti-user"></i>
            </span>
            <div class="flex-fill text-truncate" style="min-width: 0;">
              <div class="profile-name text-truncate"><?= e($adminUser['name'] ?? 'Admin') ?></div>
              <div class="profile-email text-truncate"><?= e($adminUser['email'] ?? 'admin@okinacraft.com') ?></div>
            </div>
          </div>
          <form action="<?= base_url('/admin/logout') ?>" method="post" class="px-1">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
              <i class="ti ti-logout me-1"></i> Sign Out
            </button>
          </form>
        </div>
      </div>
    </div>
  </aside>

  <!-- Top Navbar -->
  <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none sticky-top border-bottom bg-white">
    <div class="container-xl">
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-dark text-white text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">CodeIgniter 4 Store</span>
        <span class="text-secondary d-none d-md-inline small">Okina Craft Administration</span>
      </div>

      <div class="navbar-nav flex-row order-md-last align-items-center gap-3">
        <!-- Live store shortcut -->
        <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center">
          <i class="ti ti-external-link me-1"></i> Visit Store
        </a>

        <!-- Dark/Light Theme toggle -->
        <a href="javascript:void(0)" class="nav-link px-2 theme-toggle-btn text-dark" id="theme-toggle" title="Toggle light/dark theme">
          <i class="ti ti-moon fs-2" id="theme-icon"></i>
        </a>

        <!-- Notifications dropdown for pending orders & messages -->
        <div class="nav-item dropdown">
          <a href="#" class="nav-link px-2 text-dark" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
            <i class="ti ti-bell fs-2"></i>
            <?php if (($pendingOrders + $unreadCount) > 0): ?>
              <span class="badge bg-red badge-blink"></span>
            <?php endif; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card shadow">
            <div class="card" style="width: 320px;">
              <div class="card-header py-2">
                <h3 class="card-title fw-bold mb-0">Notifications</h3>
              </div>
              <div class="list-group list-group-flush list-group-hoverable">
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto"><span class="badge bg-yellow-lt text-yellow p-2"><i class="ti ti-shopping-cart"></i></span></div>
                    <div class="col text-truncate">
                      <a href="<?= base_url('/admin/orders') ?>" class="text-dark d-block fw-semibold text-decoration-none">Pending Orders</a>
                      <div class="text-secondary small text-truncate"><?= $pendingOrders ?> orders awaiting fulfillment</div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto"><span class="badge bg-blue-lt text-blue p-2"><i class="ti ti-mail"></i></span></div>
                    <div class="col text-truncate">
                      <a href="<?= base_url('/admin/messages') ?>" class="text-dark d-block fw-semibold text-decoration-none">Customer Inquiries</a>
                      <div class="text-secondary small text-truncate"><?= $unreadCount ?> unread customer messages</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- User profile dropdown -->
        <div class="nav-item dropdown">
          <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
            <span class="avatar avatar-sm bg-dark text-white rounded-circle"><i class="ti ti-user"></i></span>
            <div class="d-none d-xl-block ps-2 text-start">
              <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;"><?= e($adminUser['name'] ?? 'Admin') ?></div>
              <div class="small text-secondary text-truncate" style="max-width: 140px;"><?= e($adminUser['email'] ?? 'admin@okinacraft.com') ?></div>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow shadow">
            <div class="dropdown-header">Logged in as <strong><?= e($adminUser['email'] ?? '') ?></strong></div>
            <a href="<?= base_url('/admin/settings') ?>" class="dropdown-item"><i class="ti ti-settings me-2 text-muted"></i> Store Settings</a>
            <a href="<?= base_url('/admin/settings') ?>#security" class="dropdown-item"><i class="ti ti-key me-2 text-muted"></i> Change Password</a>
            <div class="dropdown-divider"></div>
            <form action="<?= base_url('/admin/logout') ?>" method="post" class="px-2 py-1">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-sm btn-danger w-100"><i class="ti ti-logout me-1"></i> Sign Out</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Page Wrapper -->
  <div class="page-wrapper">
    <!-- Flash Messages -->
    <div class="container-xl pt-3">
      <?php if ($session->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
          <div class="d-flex">
            <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
            <div>
              <h4 class="alert-title mb-1">Attention Required</h4>
              <div class="text-secondary"><?= e($session->getFlashdata('error')) ?></div>
            </div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
      <?php endif; ?>

      <?php if ($session->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
          <div class="d-flex">
            <div><i class="ti ti-check fs-2 me-2"></i></div>
            <div>
              <h4 class="alert-title mb-1">Success</h4>
              <div class="text-secondary"><?= e($session->getFlashdata('success')) ?></div>
            </div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
      <?php endif; ?>

      <?php if ($session->getFlashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
          <div class="d-flex">
            <div><i class="ti ti-info-circle fs-2 me-2"></i></div>
            <div>
              <div class="text-secondary"><?= e($session->getFlashdata('info')) ?></div>
            </div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Main Content -->
    <?= $this->renderSection('admin_content') ?>

    <!-- Footer -->
    <footer class="footer footer-transparent d-print-none mt-auto">
      <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
          <div class="col-lg-auto ms-lg-auto">
            <ul class="list-inline list-inline-dots mb-0">
              <li class="list-inline-item"><a href="<?= base_url('/policies/privacy-policy') ?>" target="_blank" class="link-secondary">Privacy</a></li>
              <li class="list-inline-item"><a href="<?= base_url('/policies/terms-of-service') ?>" target="_blank" class="link-secondary">Terms</a></li>
              <li class="list-inline-item"><a href="https://tabler.io" target="_blank" class="link-secondary">Tabler UI</a></li>
            </ul>
          </div>
          <div class="col-12 col-lg-auto mt-3 mt-lg-0">
            <ul class="list-inline list-inline-dots mb-0">
              <li class="list-inline-item">
                &copy; <?= date('Y') ?> <strong>Okina Craft</strong>. Built with CodeIgniter 4 & Tabler.
              </li>
              <li class="list-inline-item">
                <span class="badge bg-green-lt">Operational</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
  </div>
</div>

<!-- Tabler Core JS (CDN + Local Fallback) -->
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
<script src="<?= base_url('assets/vendor/tabler/tabler.min.js') ?>"></script>

<script>
  // Theme Toggle Handler
  document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('theme-toggle');
    var icon = document.getElementById('theme-icon');
    if (!toggleBtn) return;

    function updateIcon(theme) {
      if (!icon) return;
      if (theme === 'dark') {
        icon.className = 'ti ti-sun fs-2';
      } else {
        icon.className = 'ti ti-moon fs-2';
      }
    }

    var currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    updateIcon(currentTheme);

    toggleBtn.addEventListener('click', function () {
      var activeTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
      var newTheme = activeTheme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-bs-theme', newTheme);
      localStorage.setItem('tablerTheme', newTheme);
      updateIcon(newTheme);
    });
  });
</script>
</body>
</html>
