<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle text-secondary">Taxonomy & Storefront Layout</div>
        <h2 class="page-title fw-bold">Collections (<?= count($collections) ?>)</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <!-- Add Collection Card -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-transparent py-3">
        <h3 class="card-title fw-bold mb-0">
          <i class="ti ti-plus me-1 text-primary"></i> Add New Collection
        </h3>
      </div>
      <div class="card-body">
        <form action="<?= base_url('/admin/collections') ?>" method="post" class="row g-3 align-items-end">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="create">

          <div class="col-md-3">
            <label class="form-label fw-semibold">Collection Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" required placeholder="e.g. Anime Oversized Tees">
          </div>

          <div class="col-md-2">
            <label class="form-label fw-semibold">Card Shape</label>
            <select class="form-select" name="card_shape">
              <option value="standard">Standard Rounded</option>
              <option value="arch">Arch Mask</option>
              <option value="blob">Organic Blob Mask</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-semibold">Desktop Columns</label>
            <select class="form-select" name="desktop_cols">
              <option value="4" selected>4 Columns</option>
              <option value="3">3 Columns</option>
              <option value="2">2 Columns</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-semibold">Homepage Order</label>
            <input type="number" class="form-control" name="home_order" value="10">
          </div>

          <div class="col-md-2">
            <label class="form-check mb-2">
              <input type="checkbox" class="form-check-input" name="show_on_home" value="1" checked>
              <span class="form-check-label fw-semibold small">Show on Home</span>
            </label>
          </div>

          <div class="col-md-1">
            <button type="submit" class="btn btn-dark w-100">Create</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Existing Collections Card -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent py-3">
        <h3 class="card-title fw-bold mb-0">Active Collections</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table">
          <thead>
            <tr>
              <th style="min-width: 180px;">Collection Title</th>
              <th style="min-width: 150px;">Handle / Slug</th>
              <th>Items</th>
              <th style="min-width: 140px;">Card Shape</th>
              <th style="min-width: 120px;">Columns</th>
              <th>Homepage</th>
              <th style="min-width: 90px;">Order</th>
              <th class="text-end pe-3" style="min-width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($collections as $c): ?>
              <tr>
                <form action="<?= base_url('/admin/collections') ?>" method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">

                  <td>
                    <input type="text" name="title" value="<?= e($c['title']) ?>" class="form-control form-control-sm fw-medium">
                  </td>

                  <td>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text text-muted">/</span>
                      <input type="text" name="handle" value="<?= e($c['handle']) ?>" class="form-control">
                    </div>
                  </td>

                  <td>
                    <span class="badge bg-secondary-lt font-monospace"><?= (int)$c['product_count'] ?> products</span>
                  </td>

                  <td>
                    <select name="card_shape" class="form-select form-select-sm">
                      <option value="standard" <?= $c['card_shape'] === 'standard' ? 'selected' : '' ?>>Standard</option>
                      <option value="arch" <?= $c['card_shape'] === 'arch' ? 'selected' : '' ?>>Arch</option>
                      <option value="blob" <?= $c['card_shape'] === 'blob' ? 'selected' : '' ?>>Blob</option>
                    </select>
                  </td>

                  <td>
                    <select name="desktop_cols" class="form-select form-select-sm">
                      <option value="2" <?= (int)$c['desktop_cols'] === 2 ? 'selected' : '' ?>>2 Cols</option>
                      <option value="3" <?= (int)$c['desktop_cols'] === 3 ? 'selected' : '' ?>>3 Cols</option>
                      <option value="4" <?= (int)$c['desktop_cols'] === 4 ? 'selected' : '' ?>>4 Cols</option>
                    </select>
                  </td>

                  <td>
                    <label class="form-check form-switch m-0">
                      <input type="checkbox" name="show_on_home" value="1" class="form-check-input" <?= !empty($c['show_on_home']) ? 'checked' : '' ?>>
                    </label>
                  </td>

                  <td>
                    <input type="number" name="home_order" value="<?= (int)$c['home_order'] ?>" class="form-control form-control-sm text-center">
                  </td>

                  <td class="text-end pe-3">
                    <div class="btn-group btn-group-sm">
                      <button type="submit" name="action" value="update" class="btn btn-outline-secondary" title="Save Collection">
                        <i class="ti ti-check me-1"></i> Save
                      </button>
                      <a href="<?= base_url('/collections/' . $c['handle']) ?>" target="_blank" class="btn btn-outline-secondary" title="View on Store">
                        <i class="ti ti-external-link"></i>
                      </a>
                      <button type="submit" name="action" value="delete" class="btn btn-outline-danger" title="Delete Collection" onclick="return confirm('Delete collection <?= e(addslashes($c['title'])) ?>?');">
                        <i class="ti ti-trash"></i>
                      </button>
                    </div>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
