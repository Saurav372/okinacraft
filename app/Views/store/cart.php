<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="cart-page">
  <div class="page-width">
    <div class="cart-header">
      <h1 class="h1" style="margin:0;">Your cart</h1>
      <a href="<?= base_url('/collections/all') ?>" class="link--underline" style="font-size:1.5rem;">Continue shopping</a>
    </div>

    <?php if (empty($cart['items'])): ?>
      <div style="text-align:center; padding: 6rem 0;">
        <h2 class="h2" style="margin-bottom: 2rem;">Your cart is empty</h2>
        <a href="<?= base_url('/collections/all') ?>" class="button">Continue shopping</a>
      </div>
    <?php else: ?>
      <table class="cart-table">
        <thead>
          <tr>
            <th colspan="2">Product</th>
            <th>Quantity</th>
            <th style="text-align:right;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart['items'] as $item): ?>
            <tr>
              <td class="cart-item__media">
                <a href="<?= base_url('/products/' . $item['handle']) ?>">
                  <?php if (!empty($item['image'])): ?>
                    <img src="<?= base_url($item['image']) ?>" alt="<?= e($item['title']) ?>">
                  <?php endif; ?>
                </a>
              </td>
              <td>
                <a href="<?= base_url('/products/' . $item['handle']) ?>" style="font-size:1.55rem;color:#000;display:block;line-height:1.4;">
                  <?= e($item['title']) ?>
                </a>
                <div style="font-size:1.4rem;color:var(--color-muted);margin-top:0.3rem;">
                  <?= e(money_format_inr($item['unit_price'])) ?>
                  <?php if (!empty($item['is_bulk_discounted'])): ?>
                    <span style="text-decoration:line-through;color:#999;margin-left:4px;font-size:1.25rem;"><?= e(money_format_inr($item['base_unit_price'])) ?></span>
                  <?php endif; ?>
                </div>
                <?php if (!empty($item['is_bulk_discounted'])): ?>
                  <div class="cart-tier-discount-badge">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <?= $item['tier_discount_percent'] ?>% Volume Discount (Saved <?= e(money_format_inr($item['savings'])) ?>)
                  </div>
                <?php endif; ?>
                <?php if ($item['variant_title'] !== ''): ?>
                  <div style="font-size:1.35rem;color:var(--color-muted);margin-top:0.3rem;">
                    Option: <?= e($item['variant_title']) ?>
                  </div>
                <?php endif; ?>
                <?php if (isset($item['gst_rate']) && (float)$item['gst_rate'] > 0): ?>
                  <div style="font-size:1.25rem;color:var(--color-muted);margin-top:0.2rem;">
                    GST <?= (float)$item['gst_rate'] ?>%: <?= e(money_format_inr($item['line_tax'])) ?>
                  </div>
                <?php endif; ?>
                <?php if (!empty($item['free_delivery'])): ?>
                  <span class="badge badge--free" style="margin-top:0.6rem;">FREE Delivery</span>
                <?php endif; ?>
                <?php if ($item['custom_design_path'] !== ''): ?>
                  <div style="margin-top:0.8rem;display:flex;align-items:center;gap:0.8rem;font-size:1.3rem;">
                    <img src="<?= base_url($item['custom_design_path']) ?>" alt="Custom design" style="width:4rem;height:4rem;object-fit:cover;border-radius:6px;border:1px solid rgba(0,0,0,0.15);">
                    <span>Custom design attached</span>
                  </div>
                <?php endif; ?>
                <?php if ($item['custom_note'] !== ''): ?>
                  <div style="font-size:1.3rem;color:var(--color-muted);margin-top:0.4rem;">
                    Note: <?= e($item['custom_note']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <div style="display:flex;align-items:center;gap:1.2rem;flex-wrap:wrap;">
                  <form action="<?= base_url('/cart') ?>" method="post" style="display:inline-flex;align-items:center;gap:0.8rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="key" value="<?= e($item['key']) ?>">
                    <div class="qty-stepper" style="height:4rem;">
                      <button type="submit" name="quantity" value="<?= max(0, (int)$item['quantity'] - 1) ?>" aria-label="Decrease">&minus;</button>
                      <input type="number" value="<?= (int)$item['quantity'] ?>" min="1" max="10000" readonly>
                      <button type="submit" name="quantity" value="<?= min(10000, (int)$item['quantity'] + 1) ?>" aria-label="Increase">&plus;</button>
                    </div>
                  </form>

                  <form action="<?= base_url('/cart') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="key" value="<?= e($item['key']) ?>">
                    <button type="submit" class="link--underline" style="border:0;background:none;cursor:pointer;color:var(--color-muted);font:inherit;font-size:1.35rem;">Remove</button>
                  </form>
                </div>
              </td>
              <td style="text-align:right;font-size:1.55rem;color:#000;white-space:nowrap;">
                <?= e(money_format_inr($item['line_total'])) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="cart-footer">
        <div class="cart-totals">
          <span style="font-size:1.5rem;">Estimated subtotal</span>
          <strong><?= e(money_format_inr($cart['subtotal'])) ?></strong>
        </div>
        <?php if (!empty($cart['tax_total']) && (float)$cart['tax_total'] > 0): ?>
          <div style="font-size:1.35rem;color:var(--color-muted);margin-top:0.2rem;">
            Estimated GST: <strong><?= e(money_format_inr($cart['tax_total'])) ?></strong>
          </div>
        <?php endif; ?>
        <div style="font-size:1.35rem;color:var(--color-muted);">
          <?php if ($cart['shipping'] == 0): ?>
            Shipping: <strong style="color:#065f46;">FREE</strong>
          <?php else: ?>
            Estimated shipping: <?= e(money_format_inr($cart['shipping'])) ?>
            <?php if ($cart['free_threshold'] > 0): ?>
              (Free delivery on orders above <?= e(money_short_inr($cart['free_threshold'])) ?>)
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <div style="margin-top:1.2rem; width: min(35rem, 100%);">
          <a href="<?= base_url('/checkout') ?>" class="button button--full-width">Check out</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?= $this->endSection() ?>
