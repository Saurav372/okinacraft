<?php
$shape = $cardShape ?? 'standard';
$isShaped = in_array($shape, ['arch', 'blob'], true);
$shapeClass = $shape === 'arch' ? 'shape--arch' : ($shape === 'blob' ? 'shape--blob' : '');
$ratio = !empty($product['image_ratio']) ? $product['image_ratio'] : '100%';
$primaryImg = !empty($product['featured_image']) ? base_url($product['featured_image']) : '';
$hoverImg = !empty($product['hover_image']) ? base_url($product['hover_image']) : '';
$isSoldOut = empty($product['available']);
$productUrl = base_url('/products/' . $product['handle']);
?>
<li class="grid__item">
  <div class="card-wrapper">
    <div class="card__inner <?= $isShaped ? 'card__inner--shaped' : '' ?>">
      <div class="card__media <?= e($shapeClass) ?>" style="--ratio-percent: <?= e($ratio) ?>;">
        <?php if ($primaryImg !== ''): ?>
          <img
            src="<?= e($primaryImg) ?>"
            alt="<?= e($product['title']) ?>"
            loading="lazy"
            class="card__img-primary <?= $hoverImg !== '' ? 'has-hover' : '' ?>"
          >
        <?php endif; ?>
        <?php if ($hoverImg !== ''): ?>
          <img
            src="<?= e($hoverImg) ?>"
            alt="<?= e($product['title']) ?>"
            loading="lazy"
            class="card__img-hover"
          >
        <?php endif; ?>
      </div>
      <!-- Floating Wishlist Button -->
      <button
        type="button"
        class="card__wishlist-btn"
        data-wishlist-btn
        data-product-id="<?= (int)$product['id'] ?>"
        aria-label="Save to Wishlist"
        title="Save to Wishlist"
      >
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
      </button>
      <?php if ($isSoldOut): ?>
        <div class="card__badge">
          <span class="badge badge--sold-out">Sold out</span>
        </div>
      <?php endif; ?>
    </div>

    <div class="card__information">
      <h3 class="card__heading">
        <a href="<?= e($productUrl) ?>"><?= e($product['title']) ?></a>
      </h3>
      <div class="price">
        <?php if (!empty($product['compare_at_price']) && (float)$product['compare_at_price'] > (float)$product['price']): ?>
          <span class="price--compare"><?= e(money_clean_inr($product['compare_at_price'])) ?></span>
        <?php endif; ?>
        <?php if (!empty($product['enable_bulk_pricing']) && !empty($product['min_bulk_price']) && (float)$product['min_bulk_price'] < (float)$product['max_bulk_price']): ?>
          <span><?= e(money_clean_inr($product['min_bulk_price'])) ?> &ndash; <?= e(money_clean_inr($product['max_bulk_price'])) ?></span>
        <?php else: ?>
          <span><?= e(money_clean_inr($product['price'])) ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</li>
