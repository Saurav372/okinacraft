<?php
// If already logged in, no modal needed
if (!empty($currentCustomer)) {
    return;
}
$googleClientId = store_setting('google_client_id', '');
$storeName = store_setting('store_name', 'Okina Craft');
?>

<!-- Customer Auth Popup Modal (Mobile OTP First - Exactly like Reference) -->
<div id="auth-modal" class="auth-modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="auth-modal-title">
  <div class="auth-modal__backdrop" data-close-auth-modal></div>
  <div class="auth-modal__dialog otp-modal__dialog">
    
    <!-- Top Header Banner (Dark Navy with Warm Glowing Orb) -->
    <div class="otp-modal__header">
      <div class="otp-modal__header-content">
        <h2 class="otp-modal__title" id="auth-modal-title">Login or Sign up</h2>
        <p class="otp-modal__subtitle" id="auth-modal-subtitle">Let's get started with your mobile number.</p>
      </div>

      <!-- Close Button -->
      <button type="button" class="otp-modal__close" data-close-auth-modal aria-label="Close dialog">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- White Card Body -->
    <div class="otp-modal__body">
      <div id="auth-modal-alert" class="auth-modal__alert" style="display:none;" role="alert"></div>

      <!-- ================= VIEW 1: ENTER MOBILE NUMBER ================= -->
      <div id="auth-view-phone" class="otp-modal__view active">
        <form id="auth-phone-form" onsubmit="return false;">
          <input type="hidden" name="redirect" class="auth-modal-redirect-input" value="">

          <!-- Flag + Prefix + Phone Input Container -->
          <div class="otp-phone-input-group">
            <div class="otp-phone-prefix">
              <!-- Indian Flag SVG -->
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
              id="otp-phone-input" 
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

          <!-- Get OTP Button -->
          <button type="submit" id="btn-get-otp" class="otp-modal__primary-btn">
            <span class="btn-text">Get OTP</span>
            <span class="btn-spinner" style="display:none;">
              <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
              </svg>
              Sending OTP...
            </span>
          </button>
        </form>

        <!-- Perk Banner (Log in or sign up to unlock exclusive discounts) -->
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
            Log in or sign up to unlock <strong class="text-accent">exclusive discounts</strong> <span class="sparkle">✦</span>
          </div>
        </div>

        <!-- 3-Column Value Props (Track orders | GST invoices | Faster checkout) -->
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
            <span class="otp-value-label">Track orders</span>
          </div>

          <div class="otp-value-item">
            <div class="otp-value-icon">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6"/>
                <path d="M16 13H8"/>
                <path d="M16 17H8"/>
                <path d="M10 9H8"/>
              </svg>
            </div>
            <span class="otp-value-label">GST invoices</span>
          </div>

          <div class="otp-value-item">
            <div class="otp-value-icon">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
              </svg>
            </div>
            <span class="otp-value-label">Faster checkout</span>
          </div>
        </div>

        <!-- Footer Security Note -->
        <div class="otp-modal__security-note">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          <span>Secured with OTP verification</span>
        </div>

        <!-- Fallback to Password / Google login -->
        <div class="otp-modal__switch-wrap">
          <button type="button" class="otp-modal__sublink" data-switch-auth-view="password">
            Or log in with Email &amp; Password / Google
          </button>
        </div>
      </div>

      <!-- ================= VIEW 2: ENTER OTP CODE ================= -->
      <div id="auth-view-verify" class="otp-modal__view" style="display:none;">
        <form id="auth-verify-form" onsubmit="return false;">
          <div style="text-align: center; margin-bottom: 1.6rem;">
            <p style="font-size: 1.4rem; color: #475569; margin: 0;">
              Enter the 4-digit code sent to <strong id="otp-target-phone" style="color:#0f172a;">+91</strong>
            </p>
            <button type="button" id="btn-edit-phone" style="border:none; background:none; color:#ea580c; font-size:1.3rem; font-weight:600; text-decoration:underline; cursor:pointer; margin-top:0.3rem;">
              Edit number
            </button>
          </div>

          <!-- 4-Digit OTP Code Input -->
          <div class="otp-code-input-wrap">
            <input 
              type="text" 
              id="otp-code-input" 
              name="otp" 
              class="otp-code-field" 
              placeholder="••••" 
              maxlength="4" 
              pattern="[0-9]{4}" 
              inputmode="numeric" 
              autocomplete="one-time-code" 
              required
            >
          </div>

          <!-- Demo Helper Badge -->
          <div id="otp-demo-badge" class="otp-demo-badge" style="display:none;">
            Demo OTP: <strong id="otp-demo-number">1234</strong> (or enter 1234)
          </div>

          <button type="submit" id="btn-verify-otp" class="otp-modal__primary-btn" style="margin-top: 1.6rem;">
            <span class="btn-text">Verify &amp; Continue</span>
            <span class="btn-spinner" style="display:none;">
              <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
              </svg>
              Verifying...
            </span>
          </button>

          <div style="text-align: center; margin-top: 1.6rem;">
            <button type="button" id="btn-resend-otp" class="otp-resend-btn" disabled>
              Resend OTP in <span id="otp-timer">30</span>s
            </button>
          </div>
        </form>
      </div>

      <!-- ================= VIEW 3: NEW USER NAME & EMAIL ================= -->
      <div id="auth-view-new-customer" class="otp-modal__view" style="display:none;">
        <div style="text-align: center; margin-bottom: 1.8rem;">
          <h3 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0 0 0.4rem;">Almost there!</h3>
          <p style="font-size: 1.35rem; color: #64748b; margin: 0;">Enter your details to create your Okina Craft account.</p>
        </div>

        <form id="auth-complete-form" onsubmit="return false;">
          <div class="auth-modal__field">
            <label class="auth-modal__label" for="otp-customer-name">Full Name *</label>
            <input type="text" id="otp-customer-name" name="name" class="field__input" required placeholder="Saurav Shinde" autocomplete="name">
          </div>

          <div class="auth-modal__field">
            <label class="auth-modal__label" for="otp-customer-email">Email Address (Optional)</label>
            <input type="email" id="otp-customer-email" name="email" class="field__input" placeholder="you@example.com" autocomplete="email">
            <span class="auth-modal__hint">Receive order invoices and tracking links by email.</span>
          </div>

          <button type="submit" id="btn-complete-signup" class="otp-modal__primary-btn" style="margin-top: 1.6rem;">
            <span class="btn-text">Create Account &amp; Sign In</span>
            <span class="btn-spinner" style="display:none;">
              <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
              </svg>
              Creating Account...
            </span>
          </button>
        </form>
      </div>

      <!-- ================= VIEW 4: EMAIL / PASSWORD & GOOGLE ================= -->
      <div id="auth-view-password" class="otp-modal__view" style="display:none;">
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
                 data-text="signin_with"
                 data-size="large"
                 data-logo_alignment="left"
                 data-width="100%">
            </div>
          </div>
          <div class="auth-modal__divider">
            <span>or sign in with password</span>
          </div>
        <?php endif; ?>

        <form id="auth-pw-login-form" action="<?= base_url('/account/login') ?>" method="post" autocomplete="on">
          <?= csrf_field() ?>
          <input type="hidden" name="redirect" class="auth-modal-redirect-input" value="">

          <div class="auth-modal__field">
            <label class="auth-modal__label" for="pw-login-id">Email or Mobile Number *</label>
            <input type="text" id="pw-login-id" name="identifier" class="field__input" required placeholder="you@example.com or 10-digit mobile" autocomplete="username">
          </div>

          <div class="auth-modal__field">
            <div class="auth-modal__field-row">
              <label class="auth-modal__label" for="pw-login-pw">Password *</label>
              <a href="<?= base_url('/account/forgot-password') ?>" class="auth-modal__forgot-link">Forgot password?</a>
            </div>
            <div class="auth-modal__input-wrap">
              <input type="password" id="pw-login-pw" name="password" class="field__input" required placeholder="••••••••" autocomplete="current-password">
              <button type="button" class="auth-modal__pw-toggle" data-toggle-pw="pw-login-pw" aria-label="Toggle password visibility">
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
            <span class="btn-spinner" style="display:none;">Signing in...</span>
          </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; padding-top: 1.6rem; border-top: 1px solid #f1f5f9;">
          <button type="button" class="otp-modal__sublink" data-switch-auth-view="phone" style="font-weight: 600; color: #ea580c;">
            &larr; Back to Mobile Number Login
          </button>
        </div>
      </div>

    </div>
  </div>
</div>
