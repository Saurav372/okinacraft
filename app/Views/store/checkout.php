<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$upiId = store_setting('upi_id', '');
$old = $old ?? [];
?>
<section class="checkout-page">
  <div class="page-width">
    <div class="cart-header">
      <h1 class="h1" style="margin:0;">Checkout</h1>
      <a href="<?= base_url('/cart') ?>" class="link--underline" style="font-size:1.5rem;">Return to cart</a>
    </div>

    <form action="<?= base_url('/checkout') ?>" method="post" class="checkout-grid">
      <?= csrf_field() ?>

      <div class="checkout-card">
        <h2 class="h2" style="margin-bottom: 2rem;">Contact & Shipping Details</h2>

        <div class="form-row">
          <label class="form-label" for="co-name">Full Name *</label>
          <input id="co-name" name="name" type="text" class="field__input" required value="<?= e($old['name'] ?? '') ?>" placeholder="Enter your full name">
        </div>

        <div class="form-grid-2">
          <div class="form-row">
            <label class="form-label" for="co-email">Email Address *</label>
            <input id="co-email" name="email" type="email" class="field__input" required value="<?= e($old['email'] ?? '') ?>" placeholder="you@example.com">
          </div>
          <div class="form-row">
            <label class="form-label" for="co-phone">Phone / WhatsApp Number *</label>
            <input id="co-phone" name="phone" type="tel" class="field__input" required value="<?= e($old['phone'] ?? '') ?>" placeholder="10-digit mobile number">
          </div>
        </div>

        <div class="form-row">
          <label class="form-label" for="co-address">Full Shipping Address *</label>
          <textarea id="co-address" name="address" class="textarea__input" style="min-height:8.5rem;" required placeholder="House/Flat No., Street, Landmark, Area"><?= e($old['address'] ?? '') ?></textarea>
        </div>

        <div class="form-grid-2">
          <div class="form-row">
            <label class="form-label" for="co-city">City *</label>
            <input id="co-city" name="city" type="text" class="field__input" required value="<?= e($old['city'] ?? '') ?>" placeholder="e.g., Pune">
          </div>
          <div class="form-row">
            <label class="form-label" for="co-state">State *</label>
            <input id="co-state" name="state" type="text" class="field__input" required value="<?= e($old['state'] ?? '') ?>" placeholder="e.g., Maharashtra">
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-row">
            <label class="form-label" for="co-pin">PIN Code (6 digits) *</label>
            <input id="co-pin" name="pincode" type="text" pattern="\d{6}" maxlength="6" class="field__input" required value="<?= e($old['pincode'] ?? '') ?>" placeholder="431513">
          </div>
          <div class="form-row">
            <label class="form-label" for="co-note">Order Note (Optional)</label>
            <input id="co-note" name="note" type="text" class="field__input" value="<?= e($old['note'] ?? '') ?>" placeholder="Delivery or printing instructions">
          </div>
        </div>

        <h2 class="h2" style="margin: 2.6rem 0 1.4rem;">Payment Method</h2>

        <label class="payment-option">
          <input type="radio" name="payment_method" value="upi" <?= ($old['payment_method'] ?? 'upi') === 'upi' ? 'checked' : '' ?> style="margin-top:0.5rem;">
          <div>
            <strong style="color:#000;">UPI / WhatsApp Prepaid Confirmation</strong>
            <div style="font-size:1.35rem;color:var(--color-muted);">
              Place order now and pay via UPI<?= $upiId ? ' (' . e($upiId) . ')' : '' ?> or confirm on WhatsApp for faster dispatch.
            </div>
          </div>
        </label>

        <label class="payment-option">
          <input type="radio" name="payment_method" value="cod" <?= ($old['payment_method'] ?? '') === 'cod' ? 'checked' : '' ?> style="margin-top:0.5rem;">
          <div>
            <strong style="color:#000;">Cash on Delivery (COD)</strong>
            <div style="font-size:1.35rem;color:var(--color-muted);">
              Pay in cash/UPI upon delivery at your doorstep.
            </div>
          </div>
        </label>

        <div style="margin-top: 2.4rem;">
          <button type="submit" class="button button--full-width">
            Complete Order · <?= e(money_format_inr($cart['total'])) ?>
          </button>
        </div>
      </div>

      <!-- Order Summary -->
      <aside class="checkout-summary">
        <h2 class="h2" style="margin-bottom: 1.8rem;">Order Summary</h2>
        <div style="display:flex;flex-direction:column;gap:1.4rem;margin-bottom:2rem;">
          <?php foreach ($cart['items'] as $item): ?>
            <div style="display:flex;gap:1.2rem;align-items:flex-start;">
              <?php if (!empty($item['image'])): ?>
                <img src="<?= base_url($item['image']) ?>" alt="<?= e($item['title']) ?>" style="width:6rem;height:6rem;object-fit:cover;border-radius:8px;background:#fff;border:1px solid rgba(0,0,0,0.08);flex-shrink:0;">
              <?php endif; ?>
              <div style="flex:1;min-width:0;">
                <div style="font-size:1.4rem;color:#000;line-height:1.35;"><?= e($item['title']) ?></div>
                <?php if ($item['variant_title'] !== ''): ?>
                  <div style="font-size:1.25rem;color:var(--color-muted);"><?= e($item['variant_title']) ?></div>
                <?php endif; ?>
                <div style="font-size:1.25rem;color:var(--color-muted);">
                  Qty: <?= (int)$item['quantity'] ?> &times; <?= e(money_short_inr($item['unit_price'])) ?>
                  <?php if (isset($item['gst_rate']) && (float)$item['gst_rate'] > 0): ?>
                    &middot; <span style="color:#000;font-weight:600;">GST <?= (float)$item['gst_rate'] ?>% (+<?= e(money_short_inr($item['line_tax'])) ?>)</span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($item['is_bulk_discounted'])): ?>
                  <div style="font-size:1.15rem;color:#16a34a;font-weight:600;">
                    <?= $item['tier_discount_percent'] ?>% Volume Discount (Saved <?= e(money_short_inr($item['savings'])) ?>)
                  </div>
                <?php endif; ?>
              </div>
              <div style="font-size:1.45rem;color:#000;white-space:nowrap;">
                <?= e(money_short_inr($item['line_total'])) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="border-top:1px solid rgba(0,0,0,0.1);padding-top:1.4rem;display:flex;flex-direction:column;gap:0.8rem;font-size:1.5rem;">
          <div style="display:flex;justify-content:space-between;">
            <span>Subtotal</span>
            <span><?= e(money_short_inr($cart['subtotal'])) ?></span>
          </div>
          <?php if (!empty($cart['tax_total']) && (float)$cart['tax_total'] > 0): ?>
            <?php
              $allRates = array_unique(array_map(fn($it) => (float)($it['gst_rate'] ?? 5), $cart['items']));
              $rateLabel = count($allRates) === 1 ? '(' . $allRates[0] . '%)' : '';
            ?>
            <div style="display:flex;justify-content:space-between;color:var(--color-muted,#555);">
              <span>Estimated GST <?= $rateLabel ?></span>
              <span><?= e(money_short_inr($cart['tax_total'])) ?></span>
            </div>
          <?php endif; ?>
          <div style="display:flex;justify-content:space-between;">
            <span>Shipping</span>
            <span><?= $cart['shipping'] == 0 ? '<strong style="color:#065f46;">FREE</strong>' : e(money_short_inr($cart['shipping'])) ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:1.8rem;font-weight:700;color:#000;border-top:1px solid rgba(0,0,0,0.1);padding-top:1.2rem;margin-top:0.4rem;">
            <span>Total</span>
            <span><?= e(money_format_inr($cart['total'])) ?></span>
          </div>
        </div>
      </aside>
    </form>
  </div>
</section>

<?= $this->endSection() ?>
