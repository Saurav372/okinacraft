<?php
// If already logged in, no modal needed
if (!empty($currentCustomer)) {
    return;
}
$googleClientId = store_setting('google_client_id', '');
$storeName = store_setting('store_name', 'Okina Craft');
?>

<!-- Customer Auth Popup Modal (Sign Up & Sign In) -->
<div id="auth-modal" class="auth-modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="auth-modal-title">
  <div class="auth-modal__backdrop" data-close-auth-modal></div>
  <div class="auth-modal__dialog">
    
    <!-- Close Button -->
    <button type="button" class="auth-modal__close" data-close-auth-modal aria-label="Close dialog">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>

    <!-- Brand Logo Header -->
    <div class="auth-modal__brand">
      <img src="<?= base_url('assets/media/logo.png') ?>" alt="<?= e($storeName) ?>" class="auth-modal__brand-img">
    </div>

    <!-- Dual Tab Switcher -->
    <div class="auth-modal__tabs" role="tablist">
      <button type="button" class="auth-modal__tab active" id="auth-tab-btn-signup" data-auth-tab="signup" role="tab" aria-selected="true" aria-controls="auth-modal-pane-signup">
        Create Account
      </button>
      <button type="button" class="auth-modal__tab" id="auth-tab-btn-login" data-auth-tab="login" role="tab" aria-selected="false" aria-controls="auth-modal-pane-login">
        Sign In
      </button>
    </div>

    <!-- ================= SIGN UP PANE ================= -->
    <div id="auth-modal-pane-signup" class="auth-modal__pane active" role="tabpanel" aria-labelledby="auth-tab-btn-signup">
      <div class="auth-modal__pane-header">
        <h2 class="auth-modal__title" id="auth-modal-title">Create your account</h2>
        <p class="auth-modal__subtitle">Join Okina Craft for fast checkout, saved addresses & order tracking.</p>
      </div>

      <div id="auth-modal-signup-alert" class="auth-modal__alert" style="display:none;" role="alert"></div>

      <?php if (!empty($googleClientId)): ?>
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <div id="g_id_onload"
             data-client_id="<?= e($googleClientId) ?>"
             data-callback="handleGoogleLogin"
             data-auto_prompt="false">
        </div>
        <div class="auth-modal__google-wrap">
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
        <div class="auth-modal__divider">
          <span>or sign up with email</span>
        </div>
      <?php endif; ?>

      <form id="auth-modal-signup-form" action="<?= base_url('/account/register') ?>" method="post" autocomplete="on">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" class="auth-modal-redirect-input" value="">

        <div class="auth-modal__field">
          <label class="auth-modal__label" for="modal-reg-name">Full Name *</label>
          <input type="text" id="modal-reg-name" name="name" class="field__input" required placeholder="Saurav Shinde" autocomplete="name">
        </div>

        <div class="auth-modal__field">
          <label class="auth-modal__label" for="modal-reg-email">Email Address *</label>
          <input type="email" id="modal-reg-email" name="email" class="field__input" required placeholder="you@example.com" autocomplete="email">
        </div>

        <div class="auth-modal__field">
          <label class="auth-modal__label" for="modal-reg-phone">Mobile / WhatsApp Number *</label>
          <input type="tel" id="modal-reg-phone" name="phone" pattern="[0-9]{10}" maxlength="10" class="field__input" required placeholder="9876543210" autocomplete="tel">
          <span class="auth-modal__hint">10-digit mobile number for order delivery updates.</span>
        </div>

        <div class="auth-modal__field">
          <div class="auth-modal__field-row">
            <label class="auth-modal__label" for="modal-reg-password">Password *</label>
            <span class="auth-modal__hint" style="margin:0;">Min. 8 characters</span>
          </div>
          <div class="auth-modal__input-wrap">
            <input type="password" id="modal-reg-password" name="password" minlength="8" class="field__input" required placeholder="••••••••" autocomplete="new-password">
            <button type="button" class="auth-modal__pw-toggle" data-toggle-pw="modal-reg-password" aria-label="Toggle password visibility">
              <svg class="pw-eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg class="pw-eye-closed" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="button button--full-width auth-modal__submit-btn" style="margin-top: 1.2rem;">
          <span class="btn-text">Create Account</span>
          <span class="btn-spinner" style="display:none;">
            <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
            </svg>
            Creating account...
          </span>
        </button>
      </form>

      <div class="auth-modal__footer-switch">
        Already have an account? 
        <button type="button" class="auth-modal__switch-link" data-switch-auth="login">Sign In</button>
      </div>
    </div>

    <!-- ================= SIGN IN PANE ================= -->
    <div id="auth-modal-pane-login" class="auth-modal__pane" role="tabpanel" aria-labelledby="auth-tab-btn-login" style="display:none;">
      <div class="auth-modal__pane-header">
        <h2 class="auth-modal__title">Customer Sign In</h2>
        <p class="auth-modal__subtitle">Access your orders, saved addresses & wishlist.</p>
      </div>

      <div id="auth-modal-login-alert" class="auth-modal__alert" style="display:none;" role="alert"></div>

      <?php if (!empty($googleClientId)): ?>
        <div class="auth-modal__google-wrap">
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
        <div class="auth-modal__divider">
          <span>or sign in with email</span>
        </div>
      <?php endif; ?>

      <form id="auth-modal-login-form" action="<?= base_url('/account/login') ?>" method="post" autocomplete="on">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" class="auth-modal-redirect-input" value="">

        <div class="auth-modal__field">
          <label class="auth-modal__label" for="modal-login-id">Email or Mobile Number *</label>
          <input type="text" id="modal-login-id" name="identifier" class="field__input" required placeholder="you@example.com or 10-digit mobile" autocomplete="username">
        </div>

        <div class="auth-modal__field">
          <div class="auth-modal__field-row">
            <label class="auth-modal__label" for="modal-login-pw">Password *</label>
            <a href="<?= base_url('/account/forgot-password') ?>" class="auth-modal__forgot-link">Forgot password?</a>
          </div>
          <div class="auth-modal__input-wrap">
            <input type="password" id="modal-login-pw" name="password" class="field__input" required placeholder="••••••••" autocomplete="current-password">
            <button type="button" class="auth-modal__pw-toggle" data-toggle-pw="modal-login-pw" aria-label="Toggle password visibility">
              <svg class="pw-eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg class="pw-eye-closed" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <div class="auth-modal__options-row">
          <label class="auth-modal__checkbox-label">
            <input type="checkbox" name="remember" value="1" checked>
            <span>Remember me on this device</span>
          </label>
        </div>

        <button type="submit" class="button button--full-width auth-modal__submit-btn" style="margin-top: 1.2rem;">
          <span class="btn-text">Sign In</span>
          <span class="btn-spinner" style="display:none;">
            <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
            </svg>
            Signing in...
          </span>
        </button>
      </form>

      <div class="auth-modal__footer-switch">
        Don't have an account? 
        <button type="button" class="auth-modal__switch-link" data-switch-auth="signup">Create account</button>
      </div>
    </div>

  </div>
</div>
