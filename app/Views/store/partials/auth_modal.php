<?php
// If already logged in, no modal needed
if (!empty($currentCustomer)) {
    return;
}
$googleClientId = store_setting('google_client_id', '');
$storeName = store_setting('store_name', 'Okina Craft');
?>

<!-- Customer Auth Popup Modal (Okina Craft - Premium Reference Style) -->
<div id="auth-modal" class="auth-modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="auth-modal-title">
  <div class="auth-modal__backdrop" data-close-auth-modal></div>
  <div class="auth-modal__dialog otp-modal__dialog">
    
    <!-- Top Header Banner (Dark Navy with Warm Glowing Orb) -->
    <div class="otp-modal__header">
      <div class="otp-modal__header-content">
        <h2 class="otp-modal__title" id="auth-modal-title">Create Account</h2>
        <p class="otp-modal__subtitle" id="auth-modal-subtitle">Join Okina Craft for faster checkout &amp; order tracking.</p>
      </div>

      <!-- Circular Close Button -->
      <button type="button" class="otp-modal__close" data-close-auth-modal aria-label="Close dialog">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- White Card Body -->
    <div class="otp-modal__body">
      
      <!-- Segmented Tab Switcher (Create Account | Sign In) -->
      <div class="auth-modal__tabs" role="tablist">
        <button type="button" class="auth-modal__tab active" id="auth-tab-btn-signup" data-switch-auth="signup" role="tab" aria-selected="true">
          Create Account
        </button>
        <button type="button" class="auth-modal__tab" id="auth-tab-btn-login" data-switch-auth="login" role="tab" aria-selected="false">
          Sign In
        </button>
      </div>

      <!-- Inline Alert -->
      <div id="auth-modal-alert" class="auth-modal__alert" style="display:none;" role="alert"></div>

      <!-- ================= SIGN UP PANE ================= -->
      <div id="auth-pane-signup" class="auth-modal__pane active">
        
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
            <span>or sign up with email / mobile</span>
          </div>
        <?php endif; ?>

        <form id="auth-signup-form" action="<?= base_url('/account/register') ?>" method="post" autocomplete="on">
          <?= csrf_field() ?>
          <input type="hidden" name="redirect" class="auth-modal-redirect-input" value="">

          <div class="auth-modal__field">
            <label class="auth-modal__label" for="modal-reg-name">Full Name *</label>
            <input type="text" id="modal-reg-name" name="name" class="field__input" required placeholder="Enter your full name" autocomplete="name">
          </div>

          <!-- Mobile Number with +91 Flag Group -->
          <div class="auth-modal__field">
            <label class="auth-modal__label" for="modal-reg-phone">Mobile / WhatsApp Number *</label>
            <div class="otp-phone-input-group">
              <div class="otp-phone-prefix">
                <svg class="otp-flag-svg" viewBox="0 0 640 480" width="22" height="15" aria-hidden="true">
                  <path fill="#f93" d="M0 0h640v160H0z"/>
                  <path fill="#fff" d="M0 160h640v160H0z"/>
                  <path fill="#138808" d="M0 320h640v160H0z"/>
                  <circle cx="320" cy="240" r="40" fill="none" stroke="#000080" stroke-width="6"/>
                  <circle cx="320" cy="240" r="10" fill="#000080"/>
                </svg>
                <span class="otp-dial-code">+91</span>
              </div>
              <div class="otp-prefix-divider"></div>
              <input 
                type="tel" 
                id="modal-reg-phone" 
                name="phone" 
                class="otp-phone-field" 
                placeholder="98765 43210" 
                maxlength="10" 
                pattern="[0-9]{10}" 
                inputmode="numeric" 
                autocomplete="tel" 
                required
              >
            </div>
            <span class="auth-modal__hint">Used for doorstep delivery updates and dispatch notices.</span>
          </div>

          <div class="auth-modal__field">
            <label class="auth-modal__label" for="modal-reg-email">Email Address *</label>
            <input type="email" id="modal-reg-email" name="email" class="field__input" required placeholder="you@example.com" autocomplete="email">
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

          <button type="submit" class="otp-modal__primary-btn">
            <span class="btn-text">Create Account</span>
            <span class="btn-spinner" style="display:none;">
              <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
              </svg>
              Creating Account...
            </span>
          </button>
        </form>

        <!-- Perk Banner (Okina Craft Member Benefits) -->
        <div class="otp-modal__perk-card">
          <div class="otp-modal__perk-icon">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 12v10H4V12"/>
              <path d="M2 7h20v5H2z"/>
              <path d="M12 22V7"/>
              <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
              <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
            </svg>
          </div>
          <div class="otp-modal__perk-text">
            Join Okina Craft to unlock <strong class="text-accent">member perks &amp; easy tracking</strong> <span class="sparkle">✦</span>
          </div>
        </div>

      </div>

      <!-- ================= SIGN IN PANE ================= -->
      <div id="auth-pane-login" class="auth-modal__pane" style="display:none;">
        
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
            <span>or sign in with credentials</span>
          </div>
        <?php endif; ?>

        <form id="auth-login-form" action="<?= base_url('/account/login') ?>" method="post" autocomplete="on">
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

          <button type="submit" class="otp-modal__primary-btn">
            <span class="btn-text">Sign In</span>
            <span class="btn-spinner" style="display:none;">
              <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
              </svg>
              Signing in...
            </span>
          </button>
        </form>

        <div style="text-align: center; margin-top: 1.8rem;">
          <a href="<?= base_url('/account/track') ?>" style="color: #64748b; font-size: 1.25rem; text-decoration: none;">
            Looking for a guest order? <span style="text-decoration: underline;">Track with Order Number &rarr;</span>
          </a>
        </div>

      </div>

      <!-- 3-Column Okina Craft Value Props (Track orders | Wishlist | Faster checkout) -->
      <div class="otp-modal__value-grid">
        <div class="otp-value-item">
          <div class="otp-value-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m7.5 4.27 9 5.15"/>
              <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
              <path d="m3.3 7 8.7 5 8.7-5"/>
              <path d="M12 22V12"/>
            </svg>
          </div>
          <span class="otp-value-label">Track Orders</span>
        </div>

        <div class="otp-value-item">
          <div class="otp-value-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
          </div>
          <span class="otp-value-label">My Wishlist</span>
        </div>

        <div class="otp-value-item">
          <div class="otp-value-icon">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
          </div>
          <span class="otp-value-label">Fast Checkout</span>
        </div>
      </div>

    </div>
  </div>
</div>
