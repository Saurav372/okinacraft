<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="account-page" style="padding: 4rem 1.6rem 6rem;">
  <div class="page-width--narrow" style="max-width: 50rem; margin: 0 auto;">
    
    <div style="text-align: center; margin-bottom: 2.6rem;">
      <h1 class="h2" style="margin-bottom: 0.8rem; font-weight: 700;">Track Guest Order</h1>
      <p style="color: var(--color-muted); font-size: 1.45rem; margin: 0;">
        Enter your Okina Craft Order Number and the email or phone number used at checkout.
      </p>
    </div>

    <div class="checkout-card" style="padding: 3rem 2.6rem; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); margin-bottom: 3rem;">
      <form action="<?= base_url('/account/track') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="form-row">
          <label class="form-label" for="tr-order" style="font-weight: 600;">Order Number</label>
          <input id="tr-order" name="order_number" type="text" class="field__input" required value="<?= e($orderNo ?? '') ?>" placeholder="e.g., OC-2026/27-1001" style="border-radius: 8px;">
        </div>

        <div class="form-row">
          <label class="form-label" for="tr-contact" style="font-weight: 600;">Email or Mobile Number</label>
          <input id="tr-contact" name="contact" type="text" class="field__input" required value="<?= e($contact ?? '') ?>" placeholder="you@example.com or 10-digit phone" style="border-radius: 8px;">
        </div>

        <div style="margin-top: 2rem;">
          <button type="submit" class="button button--full-width" style="border-radius: 8px; font-weight: 600; padding: 1.4rem;">
            Check Order Status
          </button>
        </div>
      </form>
    </div>

    <?php if (!empty($searched) && empty($order)): ?>
      <div class="flash flash--error" style="border-radius: 8px;">
        No matching order found. Please verify your Order Number and Contact Details.
      </div>
    <?php elseif (!empty($order)): ?>
      <div class="checkout-card" style="padding: 2.8rem; border-radius: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.6rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.2rem;">
          <strong style="font-size: 1.8rem; font-family: monospace;">#<?= e($order['order_number']) ?></strong>
          <span style="background: #000; color: #fff; font-size: 1.2rem; font-weight: 700; padding: 0.3rem 0.8rem; border-radius: 4px; text-transform: uppercase;">
            <?= e($order['order_status']) ?>
          </span>
        </div>

        <div style="font-size: 1.4rem; color: var(--color-muted); margin-bottom: 1.8rem;">
          Placed on <?= e($order['created_at']) ?> · Total: <strong><?= e(money_format_inr($order['total'])) ?></strong>
          <?php if (!empty($order['tracking_number'])): ?>
            <br>Tracking AWB: <strong style="color: #000;"><?= e($order['tracking_number']) ?></strong>
          <?php endif; ?>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <?php foreach ($order['items'] as $item): ?>
            <div style="display: flex; justify-content: space-between; font-size: 1.4rem; padding: 0.8rem 0; border-top: 1px solid #f8fafc;">
              <span><?= e($item['product_title']) ?> × <?= (int)$item['quantity'] ?></span>
              <strong><?= e(money_short_inr($item['line_total'])) ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 2.6rem;">
      <a href="<?= base_url('/account/login') ?>" style="color: #000; font-weight: 600; text-decoration: underline; font-size: 1.4rem;">
        Sign in to view all your past orders &rarr;
      </a>
    </div>

  </div>
</section>

<?= $this->endSection() ?>
