<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$whatsappNo = store_setting('whatsapp_number', '7827747556');
?>

<section class="account-page" style="padding: 3.5rem 1.6rem 6rem;">
  <div class="page-width--narrow" style="max-width: 68rem; margin: 0 auto;">
    
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
      <a href="<?= base_url('/account') ?>" style="color: #000; text-decoration: underline; font-size: 1.45rem;">
        &larr; Back to My Orders
      </a>
      <span style="font-size: 1.35rem; color: var(--color-muted);">
        Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
      </span>
    </div>

    <div class="checkout-card" style="padding: 3rem 2.8rem; border-radius: 16px; border: 1px solid #e2e8f0;">
      
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2.4rem; padding-bottom: 1.8rem; border-bottom: 1px solid #f1f5f9;">
        <div>
          <h1 class="h2" style="margin: 0; font-size: 2.2rem;">Order #<?= e($order['order_number']) ?></h1>
          <div style="color: var(--color-muted); font-size: 1.4rem; margin-top: 0.4rem;">
            Payment: <strong><?= strtoupper(e($order['payment_method'])) ?></strong> (<?= e($order['payment_status']) ?>)
          </div>
        </div>

        <div>
          <?php
            $statusColors = [
              'pending'   => ['bg' => '#fef3c7', 'text' => '#92400e'],
              'confirmed' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
              'printing'  => ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
              'shipped'   => ['bg' => '#dcfce7', 'text' => '#166534'],
              'delivered' => ['bg' => '#d1fae5', 'text' => '#065f46'],
              'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
            ];
            $st = $statusColors[$order['order_status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
          ?>
          <span style="background: <?= $st['bg'] ?>; color: <?= $st['text'] ?>; font-size: 1.3rem; font-weight: 700; padding: 0.4rem 1.2rem; border-radius: 6px; text-transform: uppercase;">
            <?= e($order['order_status']) ?>
          </span>
        </div>
      </div>

      <?php if (!empty($order['tracking_number'])): ?>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 1.4rem 1.8rem; margin-bottom: 2.4rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
          <div>
            <div style="font-size: 1.25rem; color: #166534; font-weight: 700; text-transform: uppercase;">Courier Tracking / AWB</div>
            <div style="font-size: 1.6rem; font-weight: 700; color: #000; font-family: monospace;"><?= e($order['tracking_number']) ?></div>
          </div>
          <span style="font-size: 1.3rem; color: #15803d; font-weight: 600;">Dispatched & In Transit</span>
        </div>
      <?php endif; ?>

      <!-- Items List -->
      <h2 class="h3" style="margin-bottom: 1.4rem;">Order Items</h2>
      <div style="display: flex; flex-direction: column; gap: 1.4rem; margin-bottom: 2.4rem;">
        <?php foreach ($order['items'] as $item): ?>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.4rem; padding-bottom: 1.2rem; border-bottom: 1px solid #f8fafc;">
            <div style="display: flex; gap: 1.4rem;">
              <?php if (!empty($item['image_src'])): ?>
                <img src="<?= base_url($item['image_src']) ?>" alt="<?= e($item['product_title']) ?>" style="width: 5.6rem; height: 5.6rem; object-fit: cover; border-radius: 8px; border: 1px solid #f1f5f9;">
              <?php endif; ?>
              <div>
                <strong style="font-size: 1.5rem; color: #000; display: block; margin-bottom: 0.3rem;">
                  <?= e($item['product_title']) ?>
                </strong>
                <div style="font-size: 1.35rem; color: var(--color-muted);">
                  <?= !empty($item['variant_title']) ? e($item['variant_title']) . ' · ' : '' ?>
                  Qty: <?= (int)$item['quantity'] ?> × <?= e(money_short_inr($item['unit_price'])) ?>
                </div>
              </div>
            </div>

            <div style="font-weight: 700; font-size: 1.5rem; color: #000; white-space: nowrap;">
              <?= e(money_short_inr($item['line_total'])) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Financial Breakdown -->
      <div style="background: #fafafa; border-radius: 12px; padding: 2rem; border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 0.8rem; font-size: 1.45rem; margin-bottom: 2.4rem;">
        <div style="display: flex; justify-content: space-between; color: var(--color-muted);">
          <span>Subtotal</span>
          <span><?= e(money_short_inr($order['subtotal'])) ?></span>
        </div>
        <?php if (!empty($order['tax_amount']) && (float)$order['tax_amount'] > 0): ?>
          <div style="display: flex; justify-content: space-between; color: var(--color-muted);">
            <span>Estimated GST / Tax</span>
            <span><?= e(money_short_inr($order['tax_amount'])) ?></span>
          </div>
        <?php endif; ?>
        <div style="display: flex; justify-content: space-between; color: var(--color-muted);">
          <span>Shipping Fee</span>
          <span><?= (float)$order['shipping_cost'] == 0 ? '<strong style="color:#059669;">FREE</strong>' : e(money_short_inr($order['shipping_cost'])) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; padding-top: 1rem; border-top: 1px solid #e2e8f0; font-size: 1.8rem; font-weight: 700; color: #000;">
          <span>Total Paid</span>
          <span><?= e(money_format_inr($order['total'])) ?></span>
        </div>
      </div>

      <!-- Delivery Address Details -->
      <div style="border: 1px solid #f1f5f9; border-radius: 12px; padding: 2rem; font-size: 1.4rem; color: #334155; line-height: 1.6; margin-bottom: 2.4rem;">
        <div style="font-weight: 700; color: #000; margin-bottom: 0.6rem; text-transform: uppercase; font-size: 1.25rem;">
          Delivery Destination
        </div>
        <strong><?= e($order['customer_name']) ?></strong><br>
        <?= nl2br(e($order['shipping_address'])) ?><br>
        <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?><br>
        Phone: +91 <?= e($order['customer_phone']) ?>
      </div>

      <!-- Support / Contact Button -->
      <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode('Hi Okina Craft! Inquiry regarding my order #' . $order['order_number']) ?>" target="_blank" class="button" style="background: #059669; border-radius: 8px;">
          Need Help? Chat on WhatsApp
        </a>
      </div>

    </div>

  </div>
</section>

<?= $this->endSection() ?>
