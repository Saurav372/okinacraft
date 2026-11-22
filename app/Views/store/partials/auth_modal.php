<?php
// If already logged in, no modal needed
if (!empty($currentCustomer)) {
    return;
}
$googleClientId = store_setting('google_client_id', '');
$storeName = store_setting('store_name', 'Okina Craft');
?>

<script>window.OC_GOOGLE_CLIENT_ID = <?= json_encode($googleClientId) ?>;</script>

<?php if (!empty($googleClientId)): ?>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <div id="g_id_onload"
       data-client_id="<?= e($googleClientId) ?>"
       data-callback="handleGoogleLogin"
       data-auto_prompt="false">
  </div>
  <div id="gsi-hidden-wrap" style="position:absolute;left:-9999px;opacity:0;pointer-events:none;">
    <div class="g_id_signin" data-type="standard"></div>
  </div>
<?php endif; ?>

<!-- Customer Auth Popup Modal (Compact Modern UI - No bulky header space, dedicated Google button) -->
<div id="auth-modal" class="auth-modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="auth-modal-title">
  <div class="auth-modal__backdrop" data-close-auth-modal></div>
  <div class="auth-modal__dialog auth-modal__compact">
    
    <!-- Modal Head (Compact Title + Close Button) -->
    <div class="auth-modal__head">
      <div>
        <h2 class="auth-modal__head-title" id="auth-modal-title">Sign In</h2>
        <p class="auth-modal__head-subtitle" id="auth-modal-subtitle">Welcome back to Okina Craft</p>
      </div>

      <!-- Clean Circular Close Button -->
      <button type="button" class="auth-modal__close-btn" data-close-auth-modal aria-label="Close dialog">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="auth-modal__body-wrap">
      
      <!-- Modern Segmented Pill Switcher -->
      <div class="auth-modal__tabs" role="tablist">
        <button type="button" class="auth-modal__tab active" id="auth-tab-btn-login" data-switch-auth="login" role="tab" aria-selected="true">
          Sign In
        </button>
        <button type="button" class="auth-modal__tab" id="auth-tab-btn-signup" data-switch-auth="signup" role="tab" aria-selected="false">
          Create Account
        </button>
      </div>

      <!-- Prominent Google Login Button -->
      <div class="auth-google-wrap">
        <button type="button" class="auth-google-btn" id="auth-google-btn" aria-label="Continue with Google">
          <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
          </svg>
          <span>Continue with Google</span>
        </button>
      </div>

      <!-- Clean Divider -->
      <div class="auth-divider">
        <span>or continue with email</span>
      </div>

      <!-- Inline Alert Notice -->
      <div id="auth-modal-alert" class="auth-modal__alert" style="display:none;" role="alert"></div>

      <!-- ================= PANE 1: SIGN IN ================= -->
      <div id="auth-pane-login" class="auth-modal__pane active">
        <form id="auth-login-form" action="<?= base_url('/account/login') ?>" method="post" autocomplete="on">
          <?= csrf_field() ?>
          <input type="hidden" name="redirect" class="auth-modal-redirect-input" value="">

          <div class="auth-form-field">
            <label class="auth-label" for="modal-login-id">Email or Mobile Number *</label>
            <input type="text" id="modal-login-id" name="identifier" class="auth-input" required placeholder="you@example.com or 10-digit mobile" autocomplete="username">
          </div>

          <div class="auth-form-field">
            <div class="auth-label-row">
              <label class="auth-label" for="modal-login-pw" style="margin-bottom:0;">Password *</label>
              <a href="<?= base_url('/account/forgot-password') ?>" class="auth-forgot-link">Forgot password?</a>
            </div>
            <div class="auth-input-wrap">
              <input type="password" id="modal-login-pw" name="password" class="auth-input" required placeholder="••••••••" autocomplete="current-password">
              <button type="button" class="auth-pw-toggle-btn" data-toggle-pw="modal-login-pw" aria-label="Toggle password visibility">
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

          <label class="auth-checkbox-row">
            <input type="checkbox" name="remember" value="1" checked>
            <span>Remember me on this device</span>
          </label>

          <button type="submit" class="auth-primary-btn">
            <span class="btn-text">Sign In</span>
            <span class="btn-spinner" style="display:none;">
              <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
              </svg>
              Signing in...
            </span>
          </button>
        </form>

        <div class="auth-switch-text">
          Don't have an account? 
          <button type="button" class="auth-switch-btn" data-switch-auth="signup">Create account</button>
        </div>

        <a href="<?= base_url('/account/track') ?>" class="auth-guest-link">
          Looking for a guest order? <span>Track with Order Number &rarr;</span>
        </a>
      </div>

      <!-- ================= PANE 2: CREATE ACCOUNT ================= -->
      <div id="auth-pane-signup" class="auth-modal__pane" style="display:none;">
        <form id="auth-signup-form" action="<?= base_url('/account/register') ?>" method="post" autocomplete="on">
          <?= csrf_field() ?>
          <input type="hidden" name="redirect" class="auth-modal-redirect-input" value="">

          <div class="auth-form-field">
            <label class="auth-label" for="modal-reg-name">Full Name *</label>
            <input type="text" id="modal-reg-name" name="name" class="auth-input" required placeholder="Enter your full name" autocomplete="name">
          </div>

          <div class="auth-form-field">
            <label class="auth-label" for="modal-reg-phone">Mobile / WhatsApp Number *</label>
            <div class="auth-phone-group">
              <div class="auth-phone-prefix">
                <svg class="otp-flag-svg" viewBox="0 0 640 480" width="22" height="15" aria-hidden="true">
                  <path fill="#f93" d="M0 0h640v160H0z"/>
                  <path fill="#fff" d="M0 160h640v160H0z"/>
                  <path fill="#138808" d="M0 320h640v160H0z"/>
                  <circle cx="320" cy="240" r="40" fill="none" stroke="#000080" stroke-width="6"/>
                  <circle cx="320" cy="240" r="10" fill="#000080"/>
                </svg>
                <span class="auth-dial-code">+91</span>
              </div>
              <div class="auth-phone-divider"></div>
              <input 
                type="tel" 
                id="modal-reg-phone" 
                name="phone" 
                class="auth-phone-field" 
                placeholder="98765 43210" 
                maxlength="10" 
                pattern="[0-9]{10}" 
                inputmode="numeric" 
                autocomplete="tel" 
                required
              >
            </div>
            <span class="auth-hint">Used for delivery updates &amp; dispatch tracking.</span>
          </div>

          <div class="auth-form-field">
            <label class="auth-label" for="modal-reg-email">Email Address *</label>
            <input type="email" id="modal-reg-email" name="email" class="auth-input" required placeholder="you@example.com" autocomplete="email">
          </div>

          <div class="auth-form-field">
            <div class="auth-label-row">
              <label class="auth-label" for="modal-reg-password" style="margin-bottom:0;">Password *</label>
              <span class="auth-hint" style="margin:0;">Min. 8 characters</span>
            </div>
            <div class="auth-input-wrap">
              <input type="password" id="modal-reg-password" name="password" minlength="8" class="auth-input" required placeholder="••••••••" autocomplete="new-password">
              <button type="button" class="auth-pw-toggle-btn" data-toggle-pw="modal-reg-password" aria-label="Toggle password visibility">
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

          <button type="submit" class="auth-primary-btn" style="margin-top: 14px;">
            <span class="btn-text">Create Account</span>
            <span class="btn-spinner" style="display:none;">
              <svg class="spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="31.4 31.4"/>
              </svg>
              Creating Account...
            </span>
          </button>
        </form>

        <div class="auth-switch-text">
          Already have an account? 
          <button type="button" class="auth-switch-btn" data-switch-auth="login">Sign In</button>
        </div>
      </div>

      <!-- 3-Column Okina Craft Value Props (Track Orders | Wishlist | Fast Checkout) -->
      <div class="auth-value-props">
        <div class="auth-prop-item">
          <div class="auth-prop-icon">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m7.5 4.27 9 5.15"/>
              <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
              <path d="m3.3 7 8.7 5 8.7-5"/>
              <path d="M12 22V12"/>
            </svg>
          </div>
          <span class="auth-prop-label">Track Orders</span>
        </div>

        <div class="auth-prop-item">
          <div class="auth-prop-icon">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
          </div>
          <span class="auth-prop-label">My Wishlist</span>
        </div>

        <div class="auth-prop-item">
          <div class="auth-prop-icon">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
          </div>
          <span class="auth-prop-label">Fast Checkout</span>
        </div>
      </div>

    </div>
  </div>
</div>
