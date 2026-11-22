<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="account-page" style="padding: 4rem 1.6rem 6rem;">
  <div class="page-width--narrow" style="max-width: 44rem; margin: 0 auto;">
    
    <div class="checkout-card" style="padding: 3.2rem 2.8rem; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
      
      <div style="text-align: center; margin-bottom: 2.6rem;">
        <h1 class="h2" style="margin-bottom: 0.6rem; font-weight: 700;">Customer Sign In</h1>
        <p style="color: var(--color-muted); font-size: 1.45rem; margin: 0;">
          Access your orders, saved addresses, and wishlist.
        </p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="flash flash--error" style="margin-bottom: 2rem; border-radius: 8px;">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <div class="flash flash--success" style="margin-bottom: 2rem; border-radius: 8px;">
          <?= e($success) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($info)): ?>
        <div class="flash" style="background:#f0f9ff; color:#0369a1; border-color:#bae6fd; margin-bottom: 2rem; border-radius: 8px;">
          <?= e($info) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($googleClientId)): ?>
        <!-- Google 1-Click Sign-In Container -->
        <div style="margin-bottom: 2rem;">
          <script src="https://accounts.google.com/gsi/client" async defer></script>
          <div id="g_id_onload"
               data-client_id="<?= e($googleClientId) ?>"
               data-callback="handleGoogleLogin"
               data-auto_prompt="false">
          </div>
          <div class="g_id_signin"
               data-type="standard"
               data-shape="rectangular"
               data-theme="outline"
               data-text="signin_with"
               data-size="large"
               data-logo_alignment="left"
               data-width="100%">
          </div>
        </div>

        <div style="display: flex; align-items: center; text-align: center; margin: 1.8rem 0; color: #94a3b8; font-size: 1.3rem;">
          <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
          <span style="padding: 0 1.2rem; font-weight: 500;">or sign in with email</span>
          <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
        </div>
      <?php endif; ?>

      <!-- Email / Phone Login Form -->
      <form action="<?= base_url('/account/login') ?>" method="post">
        <?= csrf_field() ?>
        <?php if (!empty($redirect)): ?>
          <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <?php endif; ?>

        <div class="form-row">
          <label class="form-label" for="login-id" style="font-weight: 600;">Email or Mobile Number</label>
          <input id="login-id" name="identifier" type="text" class="field__input" required placeholder="you@example.com or 10-digit mobile" style="border-radius: 8px;">
        </div>

        <div class="form-row">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
            <label class="form-label" for="login-pw" style="margin: 0; font-weight: 600;">Password</label>
            <a href="<?= base_url('/account/forgot-password') ?>" style="font-size: 1.3rem; color: #000; text-decoration: underline;">Forgot password?</a>
          </div>
          <input id="login-pw" name="password" type="password" class="field__input" required placeholder="••••••••" style="border-radius: 8px;">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin: 1.4rem 0 2rem;">
          <label style="display: flex; align-items: center; gap: 0.8rem; cursor: pointer; font-size: 1.35rem; color: #334155; user-select: none;">
            <input type="checkbox" name="remember" value="1" checked style="width: 1.6rem; height: 1.6rem;">
            Remember me on this device
          </label>
        </div>

        <div>
          <button type="submit" class="button button--full-width" style="border-radius: 8px; font-weight: 600; padding: 1.4rem;">
            Sign In
          </button>
        </div>
      </form>

      <div style="text-align: center; margin-top: 2.4rem; padding-top: 1.8rem; border-top: 1px solid #f1f5f9; font-size: 1.4rem; color: var(--color-muted);">
        Don't have an account? 
        <a href="<?= base_url('/account/register' . (!empty($redirect) ? '?redirect=' . rawurlencode($redirect) : '')) ?>" data-open-auth-modal="signup" style="color: #000; font-weight: 700; text-decoration: underline;">
          Create account
        </a>
      </div>

    </div>

    <!-- Alternative Guest Tracker Link -->
    <div style="text-align: center; margin-top: 2rem;">
      <a href="<?= base_url('/account/track') ?>" style="color: var(--color-muted); font-size: 1.35rem; text-decoration: none;">
        Looking for a guest order? <span style="text-decoration: underline;">Track order with Order Number &rarr;</span>
      </a>
    </div>

  </div>
</section>

<script>
function handleGoogleLogin(response) {
  if (!response.credential) return;
  const redirect = <?= json_encode($redirect) ?>;
  
  fetch('<?= base_url('/account/google') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: new URLSearchParams({
      'credential': response.credential,
      'redirect': redirect,
      '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.ok && data.redirect) {
      // Sync local wishlist if present
      syncLocalWishlist(() => {
        window.location.href = data.redirect;
      });
    } else {
      alert(data.error || 'Google login failed.');
    }
  })
  .catch(err => {
    console.error(err);
    alert('Failed to connect with Google login.');
  });
}

function syncLocalWishlist(callback) {
  try {
    const ids = JSON.parse(localStorage.getItem('okina_wishlist') || '[]');
    if (ids.length > 0) {
      fetch('<?= base_url('/api/wishlist/sync') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_ids: ids })
      }).finally(() => { if (callback) callback(); });
      return;
    }
  } catch (e) {}
  if (callback) callback();
}
</script>

<?= $this->endSection() ?>
