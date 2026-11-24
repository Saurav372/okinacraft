<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<?php
$isEdit = !empty($product);
$actionUrl = $isEdit ? base_url('/admin/products/' . (int)$product['id'] . '/edit') : base_url('/admin/products/new');
$variants = $product['variants'] ?? [['title' => 'Default Title', 'price' => $product['price'] ?? '0', 'sku' => '', 'inventory_qty' => 50, 'available' => 1]];
$images = $product['images'] ?? [];
$selectedCols = $product['collection_ids'] ?? [];
$options = $product['options'] ?? [];
$opt1Name = $options[0]['name'] ?? 'Color';
$opt2Name = $options[1]['name'] ?? 'Size';
?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/admin/products') ?>" class="text-secondary">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Edit Product' : 'Add New' ?></li>
          </ol>
        </nav>
        <h2 class="page-title fw-bold">
          <?= $isEdit ? 'Edit: ' . e($product['title']) : 'Add New Product' ?>
        </h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="<?= base_url('/admin/products') ?>" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Products
          </a>
          <button type="submit" form="product-form" class="btn btn-dark" onclick="if(typeof tinymce!=='undefined')tinymce.triggerSave();">
            <i class="ti ti-check me-1"></i> <?= $isEdit ? 'Save Changes' : 'Create Product' ?>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <form id="product-form" action="<?= $actionUrl ?>" method="post" enctype="multipart/form-data" onsubmit="if(typeof tinymce!=='undefined')tinymce.triggerSave();">
      <?= csrf_field() ?>

      <div class="row row-cards">
        <!-- Main Form Column -->
        <div class="col-lg-8">
          <!-- Basic Info Card -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent py-3">
              <h3 class="card-title fw-bold mb-0">Product Details</h3>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label fw-semibold" for="title">Product Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required value="<?= e($product['title'] ?? '') ?>" placeholder="e.g. Customized Anime Oversized T-Shirt">
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="handle">URL Slug / Handle</label>
                  <div class="input-group">
                    <span class="input-group-text text-muted small">/products/</span>
                    <input type="text" class="form-control" id="handle" name="handle" value="<?= e($product['handle'] ?? '') ?>" placeholder="leave blank to auto-generate">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="vendor">Brand / Vendor</label>
                  <input type="text" class="form-control" id="vendor" name="vendor" value="<?= e($product['vendor'] ?? 'Okina Craft') ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="product_type">Product Category / Type</label>
                  <input type="text" class="form-control" id="product_type" name="product_type" value="<?= e($product['product_type'] ?? '') ?>" placeholder="Oversized T-Shirt, Mouse Pad, Mug, etc.">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="tags">Tags (comma separated)</label>
                  <input type="text" class="form-control" id="tags" name="tags" value="<?= e($product['tags'] ?? '') ?>" placeholder="anime, oversized, cotton, bestseller">
                </div>
              </div>

              <div class="mb-0">
                <label class="form-label fw-semibold" for="body_html">Product Description (Visual Rich Text Editor)</label>
                <textarea class="form-control tinymce-editor" id="body_html" name="body_html" rows="10" placeholder="Type your product description, bullet points, fabric details..."><?= e($product['body_html'] ?? '') ?></textarea>
              </div>
            </div>
          </div>

          <!-- Product Media / Images Card -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent py-3">
              <h3 class="card-title fw-bold mb-0">Product Images</h3>
            </div>
            <div class="card-body">
              <?php if (!empty($images)): ?>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Current Images</label>
                  <div class="row g-2" id="existing-images-container">
                    <?php foreach ($images as $im): ?>
                      <div class="col-auto existing-image-item">
                        <div class="card p-1 text-center position-relative shadow-none border">
                          <img src="<?= base_url($im['src']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;" alt="">
                          <input type="hidden" name="existing_images[]" value="<?= e($im['src']) ?>">
                          <button type="button" class="btn btn-sm btn-outline-danger btn-icon position-absolute top-0 end-0 m-1" onclick="this.closest('.existing-image-item').remove()" title="Remove image">
                            <i class="ti ti-x"></i>
                          </button>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <div class="mb-3">
                <label class="form-label fw-semibold">Upload New Photos (multiple allowed)</label>
                <input type="file" class="form-control" name="new_images[]" multiple accept="image/*">
                <small class="form-hint">Accepted formats: JPG, PNG, WebP. Recommended size: 1000×1000px.</small>
              </div>
            </div>
          </div>

          <!-- Variants & Pricing Matrix Card -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-transparent py-3">
              <div>
                <h3 class="card-title fw-bold mb-0">Variants & Options</h3>
                <div class="card-subtitle text-secondary small">Color, size, pricing, and stock quantity</div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small fw-semibold">Opt 1:</span>
                <input type="text" name="option1_name" value="<?= e($opt1Name) ?>" class="form-control form-control-sm" style="width: 90px;" placeholder="Color">
                <span class="text-secondary small fw-semibold">Opt 2:</span>
                <input type="text" name="option2_name" value="<?= e($opt2Name) ?>" class="form-control form-control-sm" style="width: 90px;" placeholder="Size">
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-vcenter card-table" id="variants-table">
                <thead>
                  <tr>
                    <th>Option 1 (Color)</th>
                    <th>Option 2 (Size)</th>
                    <th style="width: 130px;">Price (₹)</th>
                    <th style="width: 140px;">SKU</th>
                    <th style="width: 100px;">Stock</th>
                    <th style="width: 80px;">Avail</th>
                    <th class="w-1"></th>
                  </tr>
                </thead>
                <tbody id="variants-tbody">
                  <?php foreach ($variants as $idx => $v): ?>
                    <tr class="variant-row">
                      <td>
                        <input type="text" name="variant_opt1[]" value="<?= e($v['option1'] ?? $v['title'] ?? '') ?>" class="form-control form-control-sm" placeholder="e.g. Beige, Maroon, Black">
                      </td>
                      <td>
                        <input type="text" name="variant_opt2[]" value="<?= e($v['option2'] ?? '') ?>" class="form-control form-control-sm" placeholder="e.g. S, M, L, XL (optional)">
                      </td>
                      <td>
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">₹</span>
                          <input type="number" step="0.01" min="0" name="variant_price[]" value="<?= e((string)($v['price'] ?? '0')) ?>" class="form-control">
                        </div>
                      </td>
                      <td>
                        <input type="text" name="variant_sku[]" value="<?= e($v['sku'] ?? '') ?>" class="form-control form-control-sm" placeholder="SKU-123">
                      </td>
                      <td>
                        <input type="number" min="0" name="variant_qty[]" value="<?= (int)($v['inventory_qty'] ?? 50) ?>" class="form-control form-control-sm">
                      </td>
                      <td>
                        <label class="form-check form-switch m-0">
                          <input type="checkbox" name="variant_available[<?= $idx ?>]" value="1" class="form-check-input" <?= !empty($v['available']) ? 'checked' : '' ?>>
                        </label>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removeVariantRow(this)" title="Remove Variant">
                          <i class="ti ti-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="card-footer bg-transparent py-2">
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addVariantRow()">
                <i class="ti ti-plus me-1"></i> Add Another Variant Row
              </button>
            </div>
          </div>

          <!-- Tiered Bulk Pricing Card -->
          <?php
            $hasBulk = !empty($product['enable_bulk_pricing']);
            $tiers = !empty($product['bulk_tiers']) ? $product['bulk_tiers'] : (!empty($product['bulk_tiers_json']) ? (json_decode((string)$product['bulk_tiers_json'], true) ?: []) : []);
          ?>
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent py-3">
              <div>
                <h3 class="card-title fw-bold mb-0">Tiered Bulk Pricing</h3>
                <div class="card-subtitle text-secondary small">Volume-based discount intervals (e.g. 10, 50, 100, 200, 500 units)</div>
              </div>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" name="enable_bulk_pricing" id="enable_bulk_pricing" value="1" <?= $hasBulk ? 'checked' : '' ?> onchange="toggleBulkTiers(this.checked)">
                <label class="form-check-label fw-semibold" for="enable_bulk_pricing">Enable Bulk Pricing</label>
              </div>
            </div>

            <div class="card-body" id="bulk-tiers-container" style="<?= $hasBulk ? '' : 'display:none;' ?>">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 bg-light rounded mb-3 border">
                <div>
                  <div class="text-secondary small fw-semibold">STOREFRONT PRICE DISPLAY PREVIEW</div>
                  <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="fs-2 fw-bold text-dark" id="preview-range-text">₹299.00 – ₹549.00 each</span>
                    <span class="badge bg-green-lt fw-bold px-2 py-1" id="preview-max-discount">Up to 45.5% OFF</span>
                  </div>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadDefaultTiers()">
                    <i class="ti ti-wand me-1"></i> Load Standard Intervals (10, 50, 100, 200, 500)
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addTierRow()">
                    <i class="ti ti-plus me-1"></i> Add Tier
                  </button>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-vcenter card-table" id="tiers-table">
                  <thead>
                    <tr>
                      <th style="width: 140px;">Min Qty <span class="text-danger">*</span></th>
                      <th style="width: 140px;">Max Qty</th>
                      <th style="width: 180px;">Unit Price (INR) <span class="text-danger">*</span></th>
                      <th style="width: 140px;">Discount %</th>
                      <th style="width: 50px;"></th>
                    </tr>
                  </thead>
                  <tbody id="tiers-tbody">
                    <?php if (!empty($tiers)): ?>
                      <?php foreach ($tiers as $tIdx => $t): ?>
                        <tr class="tier-row">
                          <td>
                            <input type="number" min="1" name="tier_min_qty[]" value="<?= (int)($t['min_qty'] ?? 1) ?>" class="form-control form-control-sm tier-min" required oninput="calcTierDiscounts()">
                          </td>
                          <td>
                            <input type="number" min="1" name="tier_max_qty[]" value="<?= isset($t['max_qty']) && $t['max_qty'] !== null ? (int)$t['max_qty'] : '' ?>" class="form-control form-control-sm tier-max" placeholder="and above" oninput="calcTierDiscounts()">
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text">₹</span>
                              <input type="number" step="0.01" min="0" name="tier_unit_price[]" value="<?= e((string)($t['unit_price'] ?? '0.00')) ?>" class="form-control tier-price" required oninput="onTierPriceInput(this)">
                            </div>
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <input type="number" step="0.1" min="0" max="100" name="tier_discount_percent[]" value="<?= e((string)($t['discount_percent'] ?? '0.0')) ?>" class="form-control tier-discount" oninput="onTierDiscountInput(this)">
                              <span class="input-group-text">%</span>
                            </div>
                          </td>
                          <td>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removeTierRow(this)" title="Delete Tier">
                              <i class="ti ti-trash"></i>
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr class="tier-row">
                        <td><input type="number" min="1" name="tier_min_qty[]" value="1" class="form-control form-control-sm tier-min" required oninput="calcTierDiscounts()"></td>
                        <td><input type="number" min="1" name="tier_max_qty[]" value="9" class="form-control form-control-sm tier-max" placeholder="and above" oninput="calcTierDiscounts()"></td>
                        <td>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" min="0" name="tier_unit_price[]" value="<?= e((string)($product['price'] ?? '549.00')) ?>" class="form-control tier-price" required oninput="onTierPriceInput(this)">
                          </div>
                        </td>
                        <td>
                          <div class="input-group input-group-sm">
                            <input type="number" step="0.1" min="0" max="100" name="tier_discount_percent[]" value="0.0" class="form-control tier-discount" oninput="onTierDiscountInput(this)">
                            <span class="input-group-text">%</span>
                          </div>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removeTierRow(this)" title="Delete Tier">
                            <i class="ti ti-trash"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <small class="text-secondary mt-2 d-block">
                Leave "Max Qty" empty on the highest tier to indicate unlimited units (e.g., 500+). Prices apply automatically when customers reach the quantity threshold on the storefront.
              </small>
            </div>
          </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
          <!-- Pricing Card -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent py-3">
              <h3 class="card-title fw-bold mb-0">Base Pricing</h3>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label fw-semibold" for="price">Selling Price (INR) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">₹</span>
                  <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required value="<?= e((string)($product['price'] ?? '0.00')) ?>">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold" for="compare_at_price">Compare-at Price (MRP)</label>
                <div class="input-group">
                  <span class="input-group-text">₹</span>
                  <input type="number" step="0.01" min="0" class="form-control" id="compare_at_price" name="compare_at_price" value="<?= e((string)($product['compare_at_price'] ?? '')) ?>" placeholder="Original price">
                </div>
                <small class="form-hint">Displayed crossed-out to show customer savings.</small>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold" for="gst_rate">GST Rate (%)</label>
                <div class="input-group">
                  <input type="number" step="0.01" min="0" max="100" class="form-control" id="gst_rate" name="gst_rate" value="<?= e((string)($product['gst_rate'] ?? '5.00')) ?>">
                  <span class="input-group-text">%</span>
                </div>
                <div class="d-flex flex-wrap gap-1 mt-1">
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="document.getElementById('gst_rate').value='0.00'">0%</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="document.getElementById('gst_rate').value='5.00'">5% (Apparel)</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="document.getElementById('gst_rate').value='12.00'">12%</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="document.getElementById('gst_rate').value='18.00'">18%</button>
                </div>
                <small class="form-hint">Decided per product in admin, calculated at checkout.</small>
              </div>

              <div class="mb-0">
                <label class="form-label fw-semibold" for="hsn_code">HSN Code (Optional)</label>
                <input type="text" class="form-control" id="hsn_code" name="hsn_code" value="<?= e($product['hsn_code'] ?? '6109') ?>" placeholder="e.g. 6109">
                <small class="form-hint">HSN code for GST invoices (e.g. 6109 for T-shirts).</small>
              </div>
            </div>
          </div>

          <!-- Status & Availability Card -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent py-3">
              <h3 class="card-title fw-bold mb-0">Visibility & Settings</h3>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label fw-semibold" for="status">Catalog Status</label>
                <select class="form-select" id="status" name="status">
                  <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active (Visible in Store)</option>
                  <option value="draft" <?= ($product['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft (Hidden)</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-check form-switch">
                  <input type="checkbox" class="form-check-input" name="available" value="1" <?= ($product['available'] ?? 1) ? 'checked' : '' ?>>
                  <span class="form-check-label fw-semibold">In Stock</span>
                </label>
                <small class="text-secondary d-block ps-4">When toggled off, shows "Sold Out" button on storefront.</small>
              </div>

              <div class="mb-3">
                <label class="form-check form-switch">
                  <input type="checkbox" class="form-check-input" name="allow_custom_upload" value="1" <?= !empty($product['allow_custom_upload']) ? 'checked' : '' ?>>
                  <span class="form-check-label fw-semibold">Customer Custom Artwork Upload</span>
                </label>
                <small class="text-secondary d-block ps-4">Allows buyer to upload custom design / logo on PDP before adding to cart.</small>
              </div>

              <div class="mb-0">
                <label class="form-check form-switch">
                  <input type="checkbox" class="form-check-input" name="free_delivery" value="1" <?= !empty($product['free_delivery']) ? 'checked' : '' ?>>
                  <span class="form-check-label fw-semibold">FREE Delivery Badge</span>
                </label>
                <small class="text-secondary d-block ps-4">Displays green "FREE Delivery" tag and waives shipping fees.</small>
              </div>
            </div>
          </div>

          <!-- Collections Card -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent py-3">
              <h3 class="card-title fw-bold mb-0">Collections Assignment</h3>
            </div>
            <div class="card-body">
              <div class="text-secondary small mb-2">Select collections where this product will appear:</div>
              <div class="d-flex flex-column gap-2">
                <?php foreach ($collections as $c): ?>
                  <label class="form-check m-0">
                    <input type="checkbox" class="form-check-input" name="collection_ids[]" value="<?= (int)$c['id'] ?>" <?= in_array((int)$c['id'], $selectedCols, true) ? 'checked' : '' ?>>
                    <span class="form-check-label"><?= e($c['title']) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <!-- Save Actions Card -->
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold mb-2" onclick="if(typeof tinymce!=='undefined')tinymce.triggerSave();">
                <i class="ti ti-check me-1"></i> <?= $isEdit ? 'Save Changes' : 'Publish Product' ?>
              </button>
              <?php if ($isEdit): ?>
                <a href="<?= base_url('/products/' . $product['handle']) ?>" target="_blank" class="btn btn-outline-secondary w-100">
                  <i class="ti ti-external-link me-1"></i> View on Storefront
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function removeVariantRow(btn) {
    var rows = document.querySelectorAll('#variants-tbody .variant-row');
    if (rows.length <= 1) {
      alert('A product must have at least one variant/option row.');
      return;
    }
    btn.closest('.variant-row').remove();
  }

  function addVariantRow() {
    var tbody = document.getElementById('variants-tbody');
    var idx = tbody.querySelectorAll('.variant-row').length;
    var basePrice = document.getElementById('price').value || '0.00';
    var tr = document.createElement('tr');
    tr.className = 'variant-row';
    tr.innerHTML = `
      <td><input type="text" name="variant_opt1[]" value="" class="form-control form-control-sm" placeholder="e.g. Color"></td>
      <td><input type="text" name="variant_opt2[]" value="" class="form-control form-control-sm" placeholder="e.g. Size"></td>
      <td>
        <div class="input-group input-group-sm">
          <span class="input-group-text">₹</span>
          <input type="number" step="0.01" min="0" name="variant_price[]" value="${basePrice}" class="form-control">
        </div>
      </td>
      <td><input type="text" name="variant_sku[]" value="" class="form-control form-control-sm" placeholder="SKU"></td>
      <td><input type="number" min="0" name="variant_qty[]" value="50" class="form-control form-control-sm"></td>
      <td>
        <label class="form-check form-switch m-0">
          <input type="checkbox" name="variant_available[${idx}]" value="1" class="form-check-input" checked>
        </label>
      </td>
      <td>
        <button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removeVariantRow(this)" title="Remove Variant">
          <i class="ti ti-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  }

  // Tiered Bulk Pricing Admin Logic
  function toggleBulkTiers(enabled) {
    var c = document.getElementById('bulk-tiers-container');
    if (c) c.style.display = enabled ? '' : 'none';
    if (enabled) calcTierDiscounts();
  }

  function onTierPriceInput(input) {
    var row = input.closest('.tier-row');
    var basePrice = parseFloat(document.getElementById('price').value) || 0;
    var price = parseFloat(input.value) || 0;
    var discInput = row.querySelector('.tier-discount');
    if (basePrice > 0 && price <= basePrice) {
      discInput.value = (((basePrice - price) / basePrice) * 100).toFixed(1);
    } else {
      discInput.value = '0.0';
    }
    calcTierDiscounts();
  }

  function onTierDiscountInput(input) {
    var row = input.closest('.tier-row');
    var basePrice = parseFloat(document.getElementById('price').value) || 0;
    var disc = parseFloat(input.value) || 0;
    var priceInput = row.querySelector('.tier-price');
    if (basePrice > 0) {
      var calcPrice = basePrice * (1 - (disc / 100));
      priceInput.value = Math.max(0, calcPrice).toFixed(2);
    }
    calcTierDiscounts();
  }

  function removeTierRow(btn) {
    var tbody = document.getElementById('tiers-tbody');
    if (tbody.querySelectorAll('.tier-row').length <= 1) {
      alert('You must have at least one pricing tier.');
      return;
    }
    btn.closest('.tier-row').remove();
    calcTierDiscounts();
  }

  function addTierRow(minQ = '', maxQ = '', price = '', disc = '0.0') {
    var tbody = document.getElementById('tiers-tbody');
    var basePrice = parseFloat(document.getElementById('price').value) || 0;
    if (price === '') price = basePrice.toFixed(2);
    var tr = document.createElement('tr');
    tr.className = 'tier-row';
    tr.innerHTML = `
      <td><input type="number" min="1" name="tier_min_qty[]" value="${minQ}" class="form-control form-control-sm tier-min" required oninput="calcTierDiscounts()"></td>
      <td><input type="number" min="1" name="tier_max_qty[]" value="${maxQ}" class="form-control form-control-sm tier-max" placeholder="and above" oninput="calcTierDiscounts()"></td>
      <td>
        <div class="input-group input-group-sm">
          <span class="input-group-text">₹</span>
          <input type="number" step="0.01" min="0" name="tier_unit_price[]" value="${price}" class="form-control tier-price" required oninput="onTierPriceInput(this)">
        </div>
      </td>
      <td>
        <div class="input-group input-group-sm">
          <input type="number" step="0.1" min="0" max="100" name="tier_discount_percent[]" value="${disc}" class="form-control tier-discount" oninput="onTierDiscountInput(this)">
          <span class="input-group-text">%</span>
        </div>
      </td>
      <td>
        <button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removeTierRow(this)" title="Delete Tier">
          <i class="ti ti-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    calcTierDiscounts();
  }

  function loadDefaultTiers() {
    var basePrice = parseFloat(document.getElementById('price').value) || 549.00;
    var tbody = document.getElementById('tiers-tbody');
    tbody.innerHTML = '';
    var presets = [
      { min: 1,   max: 9,   disc: 0.0 },
      { min: 10,  max: 49,  disc: 10.0 },
      { min: 50,  max: 99,  disc: 18.0 },
      { min: 100, max: 199, disc: 25.0 },
      { min: 200, max: 499, disc: 35.0 },
      { min: 500, max: '',  disc: 45.0 }
    ];
    presets.forEach(p => {
      var price = (basePrice * (1 - (p.disc / 100))).toFixed(2);
      addTierRow(p.min, p.max, price, p.disc.toFixed(1));
    });
  }

  function calcTierDiscounts() {
    var rows = document.querySelectorAll('#tiers-tbody .tier-row');
    var prices = [];
    var maxDisc = 0;
    rows.forEach(r => {
      var pEl = r.querySelector('.tier-price');
      var dEl = r.querySelector('.tier-discount');
      var p = pEl ? parseFloat(pEl.value) : NaN;
      var d = dEl ? parseFloat(dEl.value) : NaN;
      if (!isNaN(p) && p > 0) prices.push(p);
      if (!isNaN(d) && d > maxDisc) maxDisc = d;
    });
    if (prices.length > 0) {
      var minP = Math.min(...prices);
      var maxP = Math.max(...prices);
      var rangeEl = document.getElementById('preview-range-text');
      var discEl = document.getElementById('preview-max-discount');
      if (rangeEl) rangeEl.textContent = `₹${minP.toFixed(2)} – ₹${maxP.toFixed(2)} each`;
      if (discEl) discEl.textContent = `Up to ${maxDisc.toFixed(1)}% OFF`;
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    calcTierDiscounts();
  });
</script>

<?= $this->endSection() ?>
