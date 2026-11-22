<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="account-page" style="padding: 4rem 1.6rem 6rem;">
  <div class="page-width--narrow" style="max-width: 46rem; margin: 0 auto;">
    
    <div class="checkout-card" style="padding: 3.2rem 2.8rem; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
      
      <div style="text-align: center; margin-bottom: 2.6rem;">
        <h1 class="h2" style="margin-bottom: 0.6rem; font-weight: 700;">Create an Account</h1>
        <p style="color: var(--color-muted); font-size: 1.45rem; margin: 0;">
          Join Okina Craft for fast checkout, saved addresses & order tracking.
        </p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="flash flash--error" style="margin-bottom: 2rem; border-radius: 8px;">
          <?= e($error) ?>
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
               data-text="signup_with"
               data-size="large"
               data-logo_alignment="left"
               data-width="100%">
          </div>
        </div>

        <div style="display: flex; align-items: center; text-align: center; margin: 1.8rem 0; color: #94a3b8; font-size: 1.3rem;">
          <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
          <span style="padding: 0 1.2rem; font-weight: 500;">or register with email</span>
          <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
        </div>
      <?php endif; ?>

      <!-- Registration Form -->
      <form action="<?= base_url('/account/register') ?>" method="post">
        <?= csrf_field() ?>
        <?php if (!empty($redirect)): ?>
          <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <?php endif; ?>

        <div class="form-row">
          <label class="form-label" for="reg-name" style="font-weight: 600;">Full Name *</label>
          <input id="reg-name" name="name" type="text" class="field__input" required value="<?= e($old['name'] ?? '') ?>" placeholder="Saurav Shinde" style="border-radius: 8px;">
        </div>

        <div class="form-row">
          <label class="form-label" for="reg-email" style="font-weight: 600;">Email Address *</label>
          <input id="reg-email" name="email" type="email" class="field__input" required value="<?= e($old['email'] ?? '') ?>" placeholder="saurav@example.com" style="border-radius: 8px;">
        </div>

        <div class="form-row">
          <label class="form-label" for="reg-phone" style="font-weight: 600;">Mobile / WhatsApp Number *</label>
          <input id="reg-phone" name="phone" type="tel" pattern="[0-9]{10}" maxlength="10" class="field__input" required value="<?= e($old['phone'] ?? '') ?>" placeholder="9876543210" style="border-radius: 8px;">
          <small style="color: var(--color-muted); font-size: 1.25rem;">Used for delivery updates and dispatch notices.</small>
        </div>

        <div class="form-row">
          <label class="form-label" for="reg-password" style="font-weight: 600;">Password (Min. 8 characters) *</label>
          <input id="reg-password" name="password" type="password" minlength="8" class="field__input" required placeholder="••••••••" style="border-radius: 8px;">
        </div>

        <div style="margin-top: 2.4rem;">
          <button type="submit" class="button button--full-width" style="border-radius: 8px; font-weight: 600; padding: 1.4rem;">
            Create Account
          </button>
        </div>
      </form>

      <div style="text-align: center; margin-top: 2.4rem; padding-top: 1.8rem; border-top: 1px solid #f1f5f9; font-size: 1.4rem; color: var(--color-muted);">
        Already have an account? 
        <a href="<?= base_url('/account/login' . (!empty($redirect) ? '?redirect=' . rawurlencode($redirect) : '')) ?>" data-open-auth-modal="login" style="color: #000; font-weight: 700; text-decoration: underline;">
          Sign in
        </a>
      </div>

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
      window.location.href = data.redirect;
    } else {
      alert(data.error || 'Google signup failed.');
    }
  })
  .catch(err => {
    console.error(err);
    alert('Failed to connect with Google login.');
  });
}
</script>

<?= $this->endSection() ?>
