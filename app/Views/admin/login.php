<?php $session = session(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
  <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
  <title><?= e($pageTitle ?? 'Admin Sign In – Okina Craft') ?></title>
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
    }
    body {
      font-feature-settings: "cv03", "cv04", "cv11";
      background: #f4f6fa;
      min-height: 100vh;
    }
    .login-brand-logo {
      width: 48px;
      height: 48px;
      object-fit: contain;
      border-radius: 12px;
      background: #ffffff;
      padding: 6px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(0, 0, 0, 0.06);
    }
  </style>
</head>
<body class="d-flex flex-column">
<div class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="<?= base_url('/') ?>" class="navbar-brand navbar-brand-autodark">
        <img src="<?= base_url('assets/media/okina-mark.png') ?>" alt="Okina Craft" class="login-brand-logo mb-2">
      </a>
      <div class="fw-bold fs-2 text-dark" style="letter-spacing: 2px;">OKINA CRAFT</div>
      <div class="text-muted fw-semibold small" style="letter-spacing: 1.5px; font-size: 0.72rem;">ADMINISTRATION PANEL</div>
    </div>

    <div class="card card-md shadow-sm border-0">
      <div class="card-body p-4 p-md-5">
        <h2 class="h2 text-center mb-1 fw-bold">Admin Sign In</h2>
        <p class="text-secondary text-center mb-4 small">Enter your credentials to access the store management system.</p>

        <?php if ($session->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex">
              <div><i class="ti ti-alert-circle fs-2 me-2"></i></div>
              <div class="small"><?= e($session->getFlashdata('error')) ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
          </div>
        <?php endif; ?>

        <?php if ($session->getFlashdata('success')): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex">
              <div><i class="ti ti-check fs-2 me-2"></i></div>
              <div class="small"><?= e($session->getFlashdata('success')) ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
          </div>
        <?php endif; ?>

        <?php if ($session->getFlashdata('info')): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <div class="d-flex">
              <div><i class="ti ti-info-circle fs-2 me-2"></i></div>
              <div class="small"><?= e($session->getFlashdata('info')) ?></div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('/admin/login') ?>" method="post" autocomplete="on">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="form-label fw-semibold" for="email">Email Address</label>
            <div class="input-icon">
              <span class="input-icon-addon">
                <i class="ti ti-mail"></i>
              </span>
              <input type="email" class="form-control" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="admin@okinacraft.com" required autofocus autocomplete="email">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" for="password">Password</label>
            <div class="input-group input-group-flat">
              <input type="password" class="form-control" id="password" name="password" value="" placeholder="••••••••" required autocomplete="current-password">
              <span class="input-group-text">
                <a href="javascript:void(0)" class="link-secondary" id="toggle-password" title="Show password" tabindex="-1">
                  <i class="ti ti-eye" id="toggle-password-icon"></i>
                </a>
              </span>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" name="remember" value="1" checked/>
              <span class="form-check-label text-secondary small">Remember my session on this device</span>
            </label>
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
              Sign In to Admin
              <i class="ti ti-arrow-right ms-2"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="text-center text-secondary mt-4 small">
      <a href="<?= base_url('/') ?>" class="text-reset text-decoration-none">
        <i class="ti ti-arrow-left me-1"></i> Back to Live Storefront
      </a>
    </div>
  </div>
</div>

<!-- Tabler JS (CDN + Local Fallback) -->
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
<script src="<?= base_url('assets/vendor/tabler/tabler.min.js') ?>"></script>

<script>
  document.getElementById('toggle-password').addEventListener('click', function () {
    var passInput = document.getElementById('password');
    var icon = document.getElementById('toggle-password-icon');
    if (passInput.type === 'password') {
      passInput.type = 'text';
      icon.className = 'ti ti-eye-off';
    } else {
      passInput.type = 'password';
      icon.className = 'ti ti-eye';
    }
  });
</script>
</body>
</html>
