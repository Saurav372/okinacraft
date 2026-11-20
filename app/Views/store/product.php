<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$images = $product['images'] ?? [];
$variants = $product['variants'] ?? [];
$options = $product['options'] ?? [];
$isAvailable = !empty($product['available']);
$firstVariant = $variants[0] ?? null;
foreach ($variants as $v) {
    if (!empty($v['available'])) {
        $firstVariant = $v;
        break;
    }
}
$whatsappNo = store_setting('whatsapp_number', '918380808435');
$hasBulk = !empty($product['enable_bulk_pricing']) && !empty($product['bulk_tiers']);
$bulkTiers = $hasBulk ? $product['bulk_tiers'] : [];
$minBulkPrice = $product['min_bulk_price'] ?? (float)$product['price'];
$maxBulkPrice = $product['max_bulk_price'] ?? (float)$product['price'];

// Determine if product has Color and/or Size options
$colorOpt = null;
$sizeOpt = null;
if (!empty($options)) {
  foreach ($options as $opt) {
    if (stripos($opt['name'], 'color') !== false || stripos($opt['name'], 'colour') !== false) {
      $colorOpt = $opt;
    } elseif (stripos($opt['name'], 'size') !== false) {
      $sizeOpt = $opt;
    }
  }
}

// Detect color for each image for color filtering / switching
$colorImageMap = [];
$imagesWithColor = [];
$defaultColor = '';
if ($colorOpt) {
  $cPos = (int)($colorOpt['position'] ?? 1);
  $defaultColor = $firstVariant['option' . $cPos] ?? ($colorOpt['values'][0] ?? '');
}

foreach ($images as $img) {
  $imgColor = '';
  if ($colorOpt && !empty($colorOpt['values'])) {
    foreach ($colorOpt['values'] as $cVal) {
      if (stripos($img['alt'] ?? '', $cVal) !== false) {
        $imgColor = $cVal;
        break;
      }
    }
  }
  // Fallback detection from known image filenames
  if ($imgColor === '' && $colorOpt) {
    if (strpos($img['src'], '74d844') !== false) $imgColor = 'Maroon';
    elseif (strpos($img['src'], '02cfe9') !== false) $imgColor = 'Beige';
    elseif (strpos($img['src'], 'c6ed92') !== false) $imgColor = 'Black';
    elseif (strpos($img['src'], '1a6c77') !== false) $imgColor = 'White';
    elseif (strpos($img['src'], 'fa716d') !== false) $imgColor = 'Black';
    elseif (strpos($img['src'], 'fe889d') !== false) $imgColor = 'Off-White';
  }

  if ($imgColor !== '' && !isset($colorImageMap[$imgColor])) {
    $colorImageMap[$imgColor] = $img['src'];
  }
  $img['color'] = $imgColor;
  $imagesWithColor[] = $img;
}
?>
<section class="product-section">
  <div class="page-width">
    <div class="product-grid-layout">

      <!-- Left: Sticky Media Gallery -->
      <div class="product-gallery">
        <div class="product-gallery__main">
          <?php if (!empty($imagesWithColor)): ?>
            <?php
              $initialMainSrc = $imagesWithColor[0]['src'];
              if ($defaultColor && !empty($colorImageMap[$defaultColor])) {
                $initialMainSrc = $colorImageMap[$defaultColor];
              }
            ?>
            <img id="pdp-main-image" src="<?= base_url($initialMainSrc) ?>" alt="<?= e($product['title']) ?>">
          <?php endif; ?>
        </div>

        <?php if (count($imagesWithColor) > 1): ?>
          <div class="product-gallery__thumbs" id="pdp-gallery-thumbs">
            <?php foreach ($imagesWithColor as $idx => $img): ?>
              <?php
                $imgColor = $img['color'] ?? '';
                $isColorMatch = empty($defaultColor) || empty($imgColor) || ($imgColor === $defaultColor);
                $isActiveThumb = ($img['src'] === $initialMainSrc);
              ?>
              <button
                type="button"
                class="product-gallery__thumb <?= $isActiveThumb ? 'active' : '' ?>"
                data-src="<?= base_url($img['src']) ?>"
                data-color="<?= e($imgColor) ?>"
                style="<?= $isColorMatch ? '' : 'display:none;' ?>"
                aria-label="View image <?= $idx + 1 ?>"
              >
                <img src="<?= base_url($img['src']) ?>" alt="<?= e($img['alt'] ?? ($product['title'] . ' thumbnail ' . ($idx + 1))) ?>" loading="lazy">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right: Product Info, Variant Pills, Custom Upload, Actions, Description -->
      <div class="product-info">
        <!-- Send Photo & Print Details on WhatsApp Button -->
        <?php
          $storeName = store_setting('store_name', 'Okina Craft');
          $cleanWhatsApp = preg_replace('/\D+/', '', (string)$whatsappNo);
          if (strlen($cleanWhatsApp) === 10) $cleanWhatsApp = '91' . $cleanWhatsApp;
          $waMsg = 'Hi ' . $storeName . '! I would like to send photo & print details for ' . $product['title'] . ' (Link: ' . current_url() . '):';
        ?>
        <a href="https://wa.me/<?= e($cleanWhatsApp) ?>?text=<?= rawurlencode($waMsg) ?>" target="_blank" rel="noopener noreferrer" class="pdp-whatsapp-btn" title="Send Photo & Print Details on WhatsApp">
          <span class="pdp-whatsapp-btn__icon">
            <svg viewBox="0 0 24 24" fill="currentColor" width="36" height="36" aria-hidden="true">
              <path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L0 24l6.5-1.7a11.8 11.8 0 0 0 5.6 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4ZM12.1 21.7a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.9 1 1-3.8-.3-.4a9.8 9.8 0 1 1 8.6 4.8Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-1-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.8 4.3.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.1-1.3-.1-.2-.3-.3-.6-.4Z"/>
            </svg>
          </span>
          <span class="pdp-whatsapp-btn__content">
            <span class="pdp-whatsapp-btn__title">Send Photo & Print Details</span>
            <span class="pdp-whatsapp-btn__subtitle">Continue on WhatsApp</span>
          </span>
        </a>

        <?php if (!empty($product['vendor'])): ?>
          <div class="product-info__vendor"><?= e($product['vendor']) ?></div>
        <?php endif; ?>

        <h1 class="product-info__title"><?= e($product['title']) ?></h1>

        <div class="product-info__price-row <?= $hasBulk ? 'product-info__price-row--bulk' : '' ?>">
          <?php if ($hasBulk && $minBulkPrice < $maxBulkPrice): ?>
            <div class="bulk-main-price-box" id="bulk-main-price-box">
              <div class="bulk-main-price-line">
                <span id="pdp-price-display" class="product-info__price bulk-price-amount">
                  <?= e(money_format_inr($minBulkPrice)) ?> &ndash; <?= e(money_format_inr($maxBulkPrice)) ?> <span class="bulk-price-each">each</span>
                </span>
                <?php if (!empty($product['compare_at_price']) && (float)$product['compare_at_price'] > (float)$product['price']): ?>
                  <span id="pdp-compare-price" class="price--compare"><?= e(money_format_inr($product['compare_at_price'])) ?></span>
                <?php endif; ?>
              </div>
              <div class="bulk-main-sub-line" id="bulk-main-sub-line">
                <span id="bulk-unit-breakdown" class="bulk-unit-breakdown" style="display:none;"></span>
                <span id="bulk-discount-badge" class="bulk-discount-badge" style="display:none;"></span>
                <span id="bulk-default-note" class="bulk-fee-note">Volume discounts apply</span>
              </div>
            </div>
          <?php else: ?>
            <span id="pdp-price-display" class="product-info__price">
              <?= e(money_format_inr($firstVariant ? $firstVariant['price'] : $product['price'])) ?>
            </span>
            <?php if (!empty($product['compare_at_price']) && (float)$product['compare_at_price'] > (float)$product['price']): ?>
              <span class="price--compare"><?= e(money_format_inr($product['compare_at_price'])) ?></span>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (!$isAvailable): ?>
            <span class="badge badge--sold-out">Sold out</span>
          <?php elseif (!empty($product['free_delivery'])): ?>
            <span class="badge badge--free">FREE Delivery</span>
          <?php endif; ?>
        </div>

        <div class="product-info__tax-note">
          Tax 5% extra. <a href="<?= base_url('/policies/shipping-policy') ?>" class="link--underline">Shipping</a> calculated at checkout.
        </div>

        <form id="pdp-cart-form" action="<?= base_url('/cart') ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
          <input type="hidden" id="pdp-variant-id" name="variant_id" value="<?= $firstVariant ? (int)$firstVariant['id'] : '' ?>">

          <!-- Variant Options (Color Swatches, Non-Size Options) -->
          <?php
            $sizeOption = null;
            $sizePos = 1;
            $otherOptions = [];

            if (!empty($options)) {
              foreach ($options as $optIdx => $opt) {
                $pos = (int)($opt['position'] ?? ($optIdx + 1));
                if (stripos($opt['name'], 'size') !== false) {
                  $sizeOption = $opt;
                  $sizePos = $pos;
                } else {
                  $otherOptions[] = array_merge($opt, ['pos' => $pos]);
                }
              }
            }
          ?>

          <?php if (!empty($otherOptions)): ?>
            <?php foreach ($otherOptions as $opt): ?>
              <?php
                $pos = $opt['pos'];
                $optKey = 'option' . $pos;
                $defaultVal = $firstVariant[$optKey] ?? ($opt['values'][0] ?? '');
                $isColor = (stripos($opt['name'], 'color') !== false || stripos($opt['name'], 'colour') !== false);
              ?>
              <div class="variant-group" data-option-pos="<?= $pos ?>" data-option-type="<?= $isColor ? 'color' : 'other' ?>">
                <div class="variant-group__header">
                  <span class="variant-group__label">
                    <?= e($opt['name']) ?><?= $isColor ? ': <strong class="variant-group__selected-val" id="selected-opt-' . $pos . '">' . e($defaultVal) . '</strong>' : '' ?>
                  </span>
                </div>

                <?php if ($isColor): ?>
                  <!-- Visual Color Swatches (Vistaprint Style) -->
                  <div class="color-swatches" role="radiogroup" aria-label="<?= e($opt['name']) ?>">
                    <?php foreach (($opt['values'] ?? []) as $val): ?>
                      <?php
                        $colorInfo = color_hex_by_name($val);
                        $isLight = !empty($colorInfo['light']);
                        $bgHex = $colorInfo['hex'];
                        $isActive = ($val === $defaultVal);
                        $matchedImg = $colorImageMap[$val] ?? '';
                      ?>
                      <button
                        type="button"
                        class="color-swatch <?= $isActive ? 'active' : '' ?> <?= $isLight ? 'is-light' : '' ?>"
                        data-value="<?= e($val) ?>"
                        data-color-name="<?= e($val) ?>"
                        data-option-pos="<?= $pos ?>"
                        data-image="<?= !empty($matchedImg) ? base_url($matchedImg) : '' ?>"
                        title="<?= e($val) ?>"
                        aria-label="<?= e($opt['name']) . ': ' . e($val) ?>"
                      >
                        <span class="color-swatch__circle" style="background: <?= e($bgHex) ?>;"></span>
                        <span class="color-swatch__tooltip"><?= e($val) ?></span>
                      </button>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <!-- Non-color pills (e.g. Material) -->
                  <div class="variant-pills">
                    <?php foreach (($opt['values'] ?? []) as $val): ?>
                      <button
                        type="button"
                        class="variant-pill <?= $val === $defaultVal ? 'active' : '' ?> <?= !$isAvailable ? 'sold-out' : '' ?>"
                        data-value="<?= e($val) ?>"
                        data-option-pos="<?= $pos ?>"
                      >
                        <?= e($val) ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <!-- Custom Design / Photo Upload (Dropdown / Accordion for Custom Products) -->
          <?php if (!empty($product['allow_custom_upload']) && $isAvailable): ?>
            <details class="customizer-box customizer-accordion" id="customizer-accordion">
              <summary class="customizer-accordion__summary">
                <div class="customizer-accordion__header">
                  <span class="customizer-accordion__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M12 20h9"/>
                      <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                  </span>
                  <div class="customizer-accordion__titles">
                    <span class="customizer-box__title">
                      Personalize Your Design
                      <span class="customizer-box__tag">Optional</span>
                    </span>
                    <span class="customizer-accordion__hint" id="customizer-status-hint">Click to add photo, artwork, or print instructions</span>
                  </div>
                </div>
                <span class="customizer-accordion__chevron" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </span>
              </summary>

              <div class="customizer-accordion__content">
                <p class="customizer-box__sub">
                  Upload your photo, artwork, or logo right here — or share it with us on WhatsApp after placing your order.
                </p>

                <label class="customizer-dropzone" for="custom-design-file">
                  <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color:#000;margin-bottom:0.4rem;">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                  </svg>
                  <strong style="font-size:1.45rem;color:#000;">Click to upload your design image</strong>
                  <span style="font-size:1.25rem;color:var(--color-muted);">JPG, PNG, or WebP (max 10 MB)</span>
                  <input type="file" id="custom-design-file" name="custom_design" accept="image/jpeg,image/png,image/webp" class="visually-hidden">
                </label>

                <div id="custom-design-preview" class="customizer-preview">
                  <img id="custom-design-preview-img" src="" alt="Uploaded preview">
                  <div style="font-size:1.3rem;">
                    <strong style="display:block;color:#000;">Design attached</strong>
                    <span id="custom-design-preview-name" style="color:var(--color-muted);"></span>
                  </div>
                </div>

                <div style="margin-top:1.4rem;">
                  <label class="form-label" for="custom-note" style="font-weight:600;font-size:1.35rem;margin-bottom:0.6rem;display:block;">Custom text / placement instructions (optional)</label>
                  <input
                    type="text"
                    id="custom-note"
                    name="custom_note"
                    class="field__input"
                    placeholder="e.g., Back print A3, Front chest logo, Name text..."
                    style="background:#fff;border-radius:8px;font-size:1.35rem;padding:1.1rem 1.4rem;"
                  >
                </div>
              </div>
            </details>
          <?php endif; ?>

          <!-- Quantity & Volume Pricing Card (PrintMine Style) - Select Quantity First -->
          <div class="pdp-qty-card">
            <div class="pdp-qty-card__header">
              <label class="pdp-qty-card__title" for="pdp-qty">Quantity</label>
              <span class="pdp-qty-pill" id="pdp-qty-pill">1 pc</span>
            </div>

            <div class="pdp-qty-controls-row">
              <div class="pdp-qty-stepper">
                <button type="button" data-qty-dec aria-label="Decrease quantity">&minus;</button>
                <input id="pdp-qty" type="number" name="quantity" value="1" min="1" max="10000" inputmode="numeric">
                <button type="button" data-qty-inc aria-label="Increase quantity">&plus;</button>
              </div>
              <div class="pdp-qty-pricing-display">
                <div class="pdp-qty-unit-line">
                  <span class="pdp-qty-unit-price" id="pdp-qty-unit-price"><?= e(money_format_inr($firstVariant ? $firstVariant['price'] : $product['price'])) ?></span>
                  <span class="pdp-qty-unit-label">per piece</span>
                </div>
                <div class="pdp-qty-total-line">
                  <span class="pdp-qty-total-price" id="pdp-qty-total-price"><?= e(money_format_inr($firstVariant ? $firstVariant['price'] : $product['price'])) ?> total</span>
                </div>
              </div>
            </div>

            <?php if ($hasBulk): ?>
              <div class="pdp-qty-tier-section">
                <div class="pdp-qty-tier-title">QUANTITY-WISE PRICE LIST</div>
                <div class="pdp-qty-tier-grid" id="pdp-qty-tier-grid">
                  <?php foreach ($bulkTiers as $idx => $t): ?>
                    <?php
                      $minQ = (int)$t['min_qty'];
                      $maxQ = !empty($t['max_qty']) ? (int)$t['max_qty'] : null;
                      $rangeLabel = $maxQ ? "{$minQ}&ndash;{$maxQ} pcs" : "{$minQ}+ pcs";
                      $disc = (float)($t['discount_percent'] ?? 0);
                    ?>
                    <div class="pdp-tier-chip <?= $idx === 0 ? 'active' : '' ?>" data-min="<?= $minQ ?>" data-max="<?= $maxQ ?? '999999' ?>" data-price="<?= (float)$t['unit_price'] ?>">
                      <div class="pdp-tier-chip__range"><?= $rangeLabel ?></div>
                      <div class="pdp-tier-chip__price">&#8377;<?= number_format((float)$t['unit_price'], 0) ?></div>
                      <div class="pdp-tier-chip__badge <?= $disc > 0 ? 'pdp-tier-chip__badge--discount' : '' ?>">
                        <?= $disc > 0 ? round($disc) . '% less' : 'Base' ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="pdp-qty-upsell-banner" id="pdp-qty-upsell-banner">
                  <div class="pdp-qty-upsell-text" id="pdp-qty-upsell-text"></div>
                  <button type="button" class="pdp-qty-upsell-btn" id="pdp-qty-upsell-btn"></button>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <!-- PrintMine-Style Mixed Size Selector Card (Select Size According to Quantity) -->
          <?php if ($sizeOption): ?>
            <div class="variant-group variant-group--size" data-option-pos="<?= $sizePos ?>" data-option-type="size">
              <div class="mixed-size-card" id="mixed-size-card">
                <div class="mixed-size-header">
                  <div class="mixed-size-title-wrap">
                    <span class="mixed-size-title"><?= (stripos($product['title'], 'hoodie') !== false) ? 'Hoodie sizes' : 'T-shirt sizes' ?></span>
                    <span class="size-selected-badge all-selected" id="size-selected-badge">1/1 Selected</span>
                  </div>
                  <button type="button" class="size-chart-trigger-link" onclick="openSizeChartModal()" title="View Size Chart">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M21.3 15.3l-6.6-6.6a2 2 0 0 0-2.8 0L2.7 17.9a2 2 0 0 0 0 2.8l.6.6a2 2 0 0 0 2.8 0l9.2-9.2"></path>
                      <path d="M14.5 10.5l-2 2"></path>
                      <path d="M11.5 7.5l-2 2"></path>
                      <path d="M8.5 4.5l-2 2"></path>
                    </svg>
                    Size chart
                  </button>
                </div>

                <?php
                  $chestMap = [
                    'S'   => '36',
                    'M'   => '38',
                    'L'   => '40',
                    'XL'  => '42',
                    '2XL' => '44',
                    '3XL' => '46',
                    '4XL' => '48',
                    '5XL' => '50',
                  ];
                ?>
                <div class="mixed-size-grid" role="group" aria-label="Select size quantities">
                  <?php foreach (($sizeOption['values'] ?? []) as $vIdx => $val): ?>
                    <?php
                      $chest = $chestMap[strtoupper(trim($val))] ?? '';
                      $initVal = ($vIdx === 0) ? 1 : 0;
                    ?>
                    <div class="mixed-size-item <?= $initVal > 0 ? 'has-count' : '' ?>" data-size="<?= e($val) ?>" data-option-pos="<?= $sizePos ?>">
                      <div class="mixed-size-label">
                        <strong><?= e($val) ?></strong>
                        <?php if ($chest): ?>
                          <span class="mixed-size-chest">(<?= e($chest) ?>)</span>
                        <?php endif; ?>
                      </div>
                      <div class="mixed-size-stepper">
                        <button type="button" class="size-btn-dec" data-size-dec="<?= e($val) ?>" aria-label="Decrease <?= e($val) ?>">&minus;</button>
                        <span class="size-count-num" data-size-count="<?= e($val) ?>"><?= $initVal ?></span>
                        <button type="button" class="size-btn-inc" data-size-inc="<?= e($val) ?>" aria-label="Increase <?= e($val) ?>">&plus;</button>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="mixed-size-footer">
                  <div class="size-progress-track">
                    <div class="size-progress-fill is-complete" id="size-progress-fill" style="width: 100%;"></div>
                  </div>
                  <div class="size-status-text is-complete" id="size-status-text">✓ <strong>All 1 sizes selected!</strong></div>
                </div>
              </div>
              <input type="hidden" id="pdp-size-breakdown" name="size_breakdown" value="">
            </div>
          <?php endif; ?>

          <div id="pdp-form-error" class="flash flash--error" style="display:none;margin-bottom:1.4rem;"></div>

          <!-- Action Buttons -->
          <div class="product-actions">
            <button
              type="submit"
              id="pdp-add-btn"
              class="button button--secondary button--full-width"
              <?= !$isAvailable ? 'disabled' : '' ?>
            >
              <?= $isAvailable ? 'Add to cart' : 'Sold out' ?>
            </button>

            <?php if ($isAvailable): ?>
              <button
                type="submit"
                id="pdp-buy-btn"
                name="buy_now"
                value="1"
                class="button button--full-width"
              >
                Buy it now
              </button>
            <?php else: ?>
              <a
                href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode('Hi Okina Craft, when will "' . $product['title'] . '" be back in stock?') ?>"
                target="_blank" rel="noopener noreferrer"
                class="button button--full-width"
              >
                Ask on WhatsApp for Restock
              </a>
            <?php endif; ?>
          </div>
        </form>

        <!-- Product Description -->
        <?php if (!empty($product['body_html'])): ?>
          <div class="product-description rte">
            <?= sanitize_rich_html($product['body_html']) ?>
          </div>
        <?php endif; ?>

        <!-- Quick Specifications -->
        <?php if (!empty($options) && count($options) > 1): ?>
          <div class="product-specs">
            <dl>
              <?php foreach ($options as $opt): ?>
                <dt><?= e($opt['name']) ?></dt>
                <dd><?= e(implode(', ', $opt['values'] ?? [])) ?></dd>
              <?php endforeach; ?>
            </dl>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Variants JSON for JS -->
<script type="application/json" id="pdp-variants-json"><?= json_encode($variants, JSON_UNESCAPED_UNICODE) ?></script>
<!-- Bulk Pricing Tiers JSON for JS -->
<script type="application/json" id="pdp-bulk-tiers-json"><?= json_encode($bulkTiers, JSON_UNESCAPED_UNICODE) ?></script>

<!-- Related Products -->
<?php if (!empty($related)): ?>
  <section class="collection-section" style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 4rem;">
    <div class="page-width">
      <div class="collection__title">
        <h2 class="title h2">You may also like</h2>
      </div>
      <ul class="product-grid product-grid--4" role="list">
        <?php $cardShape = 'standard'; ?>
        <?php foreach ($related as $product): ?>
          <?= view('store/partials/product_card', ['product' => $product, 'cardShape' => $cardShape]) ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>

<!-- Size Chart Modal -->
<div id="size-chart-modal" class="size-chart-modal" aria-hidden="true" role="dialog" aria-labelledby="size-chart-title">
  <div class="size-chart-modal__backdrop" onclick="closeSizeChartModal()"></div>
  <div class="size-chart-modal__dialog">
    <div class="size-chart-modal__header">
      <div style="display:flex;align-items:center;gap:1.2rem;">
        <span class="size-chart-header-icon">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21.3 15.3l-6.6-6.6a2 2 0 0 0-2.8 0L2.7 17.9a2 2 0 0 0 0 2.8l.6.6a2 2 0 0 0 2.8 0l9.2-9.2"></path>
            <path d="M14.5 10.5l-2 2"></path>
            <path d="M11.5 7.5l-2 2"></path>
            <path d="M8.5 4.5l-2 2"></path>
          </svg>
        </span>
        <div>
          <h3 id="size-chart-title" class="size-chart-modal__title">Okina Craft &bull; T-Shirt Size Chart</h3>
          <span class="size-chart-modal__sub">All measurements are in inches. Suitable for Round Neck &amp; Polo T-Shirts.</span>
        </div>
      </div>
      <button type="button" class="size-chart-modal__close" onclick="closeSizeChartModal()" aria-label="Close size chart">&times;</button>
    </div>

    <div class="size-chart-modal__body">
      <!-- High Resolution Okina Craft Size Chart Image -->
      <div class="size-chart-image-wrap">
        <img src="<?= base_url('assets/media/size-chart.jpg') ?>" alt="Okina Craft T-Shirt Size Chart" loading="lazy">
      </div>
    </div>

    <div class="size-chart-modal__footer">
      <div class="size-chart-tip">
        <strong>💡 Measurement Tip:</strong> Lay your favourite t-shirt flat and measure across chest and length for the best fit.
      </div>
      <button type="button" class="button button--secondary" onclick="closeSizeChartModal()" style="min-width:110px;padding:0.9rem 1.6rem;font-size:1.35rem;">Close</button>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
