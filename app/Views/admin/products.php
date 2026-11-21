<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle text-secondary">Catalog Management</div>
        <h2 class="page-title fw-bold">
          Products
          <span class="badge bg-secondary-lt ms-2 font-monospace fs-4"><?= count($products) ?></span>
        </h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="<?= base_url('/admin/products/new') ?>" class="btn btn-dark d-inline-flex align-items-center">
            <i class="ti ti-plus me-1"></i> Add New Product
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body py-3">
        <form method="get" class="row g-2 align-items-center">
          <div class="col-md-5">
            <div class="input-icon">
              <span class="input-icon-addon">
                <i class="ti ti-search"></i>
              </span>
              <input type="text" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Search by title, handle, or product type...">
            </div>
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
            <?php if ($q !== ''): ?>
              <a href="<?= base_url('/admin/products') ?>" class="btn btn-link text-secondary">Clear filter</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <!-- Products Table Card -->
    <div class="card border-0 shadow-sm">
      <?php if (empty($products)): ?>
        <div class="card-body text-center py-5">
          <div class="text-secondary mb-3">
            <i class="ti ti-box-off fs-1 text-muted" style="font-size: 3rem;"></i>
          </div>
          <h4 class="text-secondary fw-semibold">No Products Found</h4>
          <p class="text-muted small mb-3">No products match your current search criteria.</p>
          <a href="<?= base_url('/admin/products/new') ?>" class="btn btn-dark btn-sm">
            <i class="ti ti-plus me-1"></i> Create First Product
          </a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-vcenter table-hover card-table">
            <thead>
              <tr>
                <th style="width: 70px;">Image</th>
                <th>Product Details</th>
                <th>Price (INR)</th>
                <th>Variants</th>
                <th>Stock Status</th>
                <th class="text-end pe-3" style="min-width: 160px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p): ?>
                <tr>
                  <td>
                    <?php if (!empty($p['featured_image'])): ?>
                      <img class="table-thumb" src="<?= base_url($p['featured_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                    <?php else: ?>
                      <div class="table-thumb d-flex align-items-center justify-content-center text-muted">
                        <i class="ti ti-photo"></i>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="fw-bold text-dark mb-1">
                      <a href="<?= base_url('/admin/products/' . (int)$p['id'] . '/edit') ?>" class="text-reset text-decoration-none hover-underline">
                        <?= e($p['title']) ?>
                      </a>
                    </div>
                    <div class="text-secondary small d-flex flex-wrap align-items-center gap-2">
                      <span class="badge bg-light text-muted border"><?= e($p['product_type'] ?: 'General') ?></span>
                      <span class="text-muted">Vendor: <?= e($p['vendor']) ?></span>
                      <?php if (!empty($p['allow_custom_upload'])): ?>
                        <span class="badge bg-blue-lt"><i class="ti ti-upload me-1"></i> Custom Artwork</span>
                      <?php endif; ?>
                      <?php if (!empty($p['free_delivery'])): ?>
                        <span class="badge bg-green-lt"><i class="ti ti-truck me-1"></i> Free Delivery</span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td>
                    <div class="fw-bold text-dark fs-3"><?= e(money_short_inr($p['price'])) ?></div>
                    <div class="small text-muted">GST: <?= (float)($p['gst_rate'] ?? 5.0) ?>%</div>
                    <?php if (!empty($p['compare_at_price']) && (float)$p['compare_at_price'] > (float)$p['price']): ?>
                      <div class="text-decoration-line-through text-muted small">MRP: <?= e(money_short_inr($p['compare_at_price'])) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-secondary-lt font-monospace"><?= count($p['variants'] ?? []) ?> Option(s)</span>
                  </td>
                  <td>
                    <?php if (!empty($p['available'])): ?>
                      <span class="badge bg-success-lt fw-semibold"><i class="ti ti-check me-1"></i> In Stock</span>
                    <?php else: ?>
                      <span class="badge bg-danger-lt fw-semibold"><i class="ti ti-x me-1"></i> Sold Out</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end pe-3">
                    <div class="btn-group btn-group-sm">
                      <a href="<?= base_url('/admin/products/' . (int)$p['id'] . '/edit') ?>" class="btn btn-outline-secondary" title="Edit Product">
                        <i class="ti ti-pencil me-1"></i> Edit
                      </a>
                      <a href="<?= base_url('/products/' . $p['handle']) ?>" target="_blank" class="btn btn-outline-secondary" title="View on Storefront">
                        <i class="ti ti-external-link"></i>
                      </a>
                      <form action="<?= base_url('/admin/products/' . (int)$p['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this product permanently? This action cannot be undone.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger" title="Delete Product">
                          <i class="ti ti-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
