<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$whatsappNo = store_setting('whatsapp_number', '918380808435');
$waText = "Hi Okina Craft! I just placed Order #{$order['order_number']} (Total: " . money_short_inr($order['total']) . "). Name: {$order['customer_name']}.";
?>
<section class="account-page">
  <div class="page-width--narrow">
    <div class="checkout-card" style="text-align:center; padding: 3.6rem 2.8rem;">
      <div style="width:5.4rem;height:5.4rem;border-radius:50%;background:#000;color:#fff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.6rem;font-size:2.4rem;">✓</div>
      <h1 class="h1" style="margin-bottom:0.6rem;">Thank you, <?= e($order['customer_name']) ?>!</h1>
      <p style="font-size:1.6rem;color:var(--color-muted);margin-bottom:2rem;">
        Your order <strong style="color:#000;"><?= e($order['order_number']) ?></strong> has been confirmed.
      </p>

      <div style="text-align:left;background:#fafafa;border-radius:12px;padding:2rem;margin-bottom:2.4rem;border:1px solid rgba(0,0,0,0.08);">
        <h2 class="h3" style="margin-bottom:1.2rem;">Order Summary</h2>
        <?php foreach ($order['items'] as $item): ?>
          <div style="display:flex;justify-content:space-between;gap:1.2rem;padding:0.8rem 0;border-bottom:1px solid rgba(0,0,0,0.06);font-size:1.45rem;">
            <div>
              <strong style="color:#000;"><?= e($item['product_title']) ?></strong>
              <?php if (!empty($item['variant_title'])): ?>
                <span style="color:var(--color-muted);"> — <?= e($item['variant_title']) ?></span>
              <?php endif; ?>
              <div style="font-size:1.3rem;color:var(--color-muted);">Qty: <?= (int)$item['quantity'] ?></div>
            </div>
            <div style="white-space:nowrap;color:#000;"><?= e(money_short_inr($item['line_total'])) ?></div>
          </div>
        <?php endforeach; ?>
        <div style="display:flex;justify-content:space-between;padding-top:1.2rem;font-size:1.7rem;font-weight:700;color:#000;">
          <span>Total</span>
          <span><?= e(money_format_inr($order['total'])) ?></span>
        </div>
        <div style="margin-top:1.4rem;font-size:1.35rem;color:var(--color-muted);">
          <strong>Shipping to:</strong> <?= e($order['shipping_address']) ?>, <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?><br>
          <strong>Payment:</strong> <?= strtoupper(e($order['payment_method'])) ?>
        </div>
      </div>

      <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:1.2rem;">
        <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode($waText) ?>" target="_blank" rel="noopener noreferrer" class="button">
          Confirm / Send Design on WhatsApp
        </a>
        <a href="<?= base_url('/collections/all') ?>" class="button button--secondary">
          Continue Shopping
        </a>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
