<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="account-page">
  <div class="page-width--narrow">
    <h1 class="h1" style="margin-bottom: 1rem;">Track Your Order</h1>
    <p style="color: var(--color-muted); margin-bottom: 2.4rem;">
      Enter your Okina Craft Order Number and the email or phone number you used at checkout.
    </p>

    <div class="checkout-card" style="margin-bottom: 3rem;">
      <form action="<?= base_url('/account') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid-2">
          <div class="form-row">
            <label class="form-label" for="tr-order">Order Number</label>
            <input id="tr-order" name="order_number" type="text" class="field__input" required value="<?= e($orderNo) ?>" placeholder="e.g., CS-2026-1001">
          </div>
          <div class="form-row">
            <label class="form-label" for="tr-contact">Email or Phone Number</label>
            <input id="tr-contact" name="contact" type="text" class="field__input" required value="<?= e($contact) ?>" placeholder="you@example.com or 10-digit phone">
          </div>
        </div>
        <button type="submit" class="button">Check Order Status</button>
      </form>
    </div>

    <?php if ($searched && !$order): ?>
      <div class="flash flash--error">No matching order found. Please double-check your Order Number and Email/Phone.</div>
    <?php elseif ($order): ?>
      <div class="checkout-card">
        <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.6rem;">
          <h2 class="h2" style="margin:0;">Order <?= e($order['order_number']) ?></h2>
          <span class="badge badge--free" style="text-transform:capitalize;"><?= e($order['order_status']) ?></span>
        </div>
        <p style="font-size:1.45rem;color:var(--color-muted);margin-bottom:1.6rem;">
          Placed on <?= e($order['created_at']) ?> · Payment: <strong><?= strtoupper(e($order['payment_method'])) ?></strong> (<?= e($order['payment_status']) ?>)
          <?php if (!empty($order['tracking_number'])): ?>
            <br>Tracking Number: <strong style="color:#000;"><?= e($order['tracking_number']) ?></strong>
          <?php endif; ?>
        </p>

        <?php foreach ($order['items'] as $item): ?>
          <div style="display:flex;justify-content:space-between;padding:1rem 0;border-top:1px solid rgba(0,0,0,0.08);font-size:1.45rem;">
            <div>
              <strong style="color:#000;"><?= e($item['product_title']) ?></strong>
              <?php if (!empty($item['variant_title'])): ?>
                (<?= e($item['variant_title']) ?>)
              <?php endif; ?>
              × <?= (int)$item['quantity'] ?>
            </div>
            <div><?= e(money_short_inr($item['line_total'])) ?></div>
          </div>
        <?php endforeach; ?>

        <div style="display:flex;justify-content:space-between;padding-top:1.4rem;border-top:1px solid rgba(0,0,0,0.12);font-size:1.7rem;font-weight:700;color:#000;">
          <span>Total</span>
          <span><?= e(money_format_inr($order['total'])) ?></span>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?= $this->endSection() ?>
