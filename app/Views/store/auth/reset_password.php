<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="account-page" style="padding: 4rem 1.6rem 6rem;">
  <div class="page-width--narrow" style="max-width: 44rem; margin: 0 auto;">
    
    <div class="checkout-card" style="padding: 3.2rem 2.8rem; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
      
      <div style="text-align: center; margin-bottom: 2.6rem;">
        <h1 class="h2" style="margin-bottom: 0.6rem; font-weight: 700;">Set New Password</h1>
        <p style="color: var(--color-muted); font-size: 1.45rem; margin: 0;">
          Create a new, strong password for your account.
        </p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="flash flash--error" style="margin-bottom: 2rem; border-radius: 8px;">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('/account/reset-password/' . $token) ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-row">
          <label class="form-label" for="np-pass" style="font-weight: 600;">New Password (Min. 8 characters)</label>
          <input id="np-pass" name="password" type="password" minlength="8" class="field__input" required placeholder="••••••••" style="border-radius: 8px;">
        </div>

        <div class="form-row">
          <label class="form-label" for="np-confirm" style="font-weight: 600;">Confirm New Password</label>
          <input id="np-confirm" name="password_confirm" type="password" minlength="8" class="field__input" required placeholder="••••••••" style="border-radius: 8px;">
        </div>

        <div style="margin-top: 2rem;">
          <button type="submit" class="button button--full-width" style="border-radius: 8px; font-weight: 600; padding: 1.4rem;">
            Save Password & Log In
          </button>
        </div>
      </form>

    </div>

  </div>
</section>

<?= $this->endSection() ?>
