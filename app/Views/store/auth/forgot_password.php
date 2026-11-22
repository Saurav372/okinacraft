<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="account-page" style="padding: 4rem 1.6rem 6rem;">
  <div class="page-width--narrow" style="max-width: 44rem; margin: 0 auto;">
    
    <div class="checkout-card" style="padding: 3.2rem 2.8rem; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
      
      <div style="text-align: center; margin-bottom: 2.6rem;">
        <h1 class="h2" style="margin-bottom: 0.6rem; font-weight: 700;">Reset Your Password</h1>
        <p style="color: var(--color-muted); font-size: 1.45rem; margin: 0;">
          We'll send you an email with a secure link to reset your password.
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

      <form action="<?= base_url('/account/forgot-password') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-row">
          <label class="form-label" for="fp-email" style="font-weight: 600;">Email Address</label>
          <input id="fp-email" name="email" type="email" class="field__input" required placeholder="you@example.com" style="border-radius: 8px;">
        </div>

        <div style="margin-top: 2rem;">
          <button type="submit" class="button button--full-width" style="border-radius: 8px; font-weight: 600; padding: 1.4rem;">
            Send Reset Link
          </button>
        </div>
      </form>

      <div style="text-align: center; margin-top: 2.4rem; padding-top: 1.8rem; border-top: 1px solid #f1f5f9; font-size: 1.4rem; color: var(--color-muted);">
        Remembered your password? 
        <a href="<?= base_url('/account/login') ?>" style="color: #000; font-weight: 700; text-decoration: underline;">
          Return to login
        </a>
      </div>

    </div>

  </div>
</section>

<?= $this->endSection() ?>
