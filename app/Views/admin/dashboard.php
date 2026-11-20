<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle text-secondary">Overview & Performance</div>
        <h2 class="page-title fw-bold">Store Dashboard</h2>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="<?= base_url('/admin/products/new') ?>" class="btn btn-dark d-none d-sm-inline-flex align-items-center">
            <i class="ti ti-plus me-1"></i> Add Product
          </a>
          <a href="<?= base_url('/admin/orders') ?>" class="btn btn-outline-secondary d-none d-sm-inline-flex align-items-center">
            <i class="ti ti-shopping-cart me-1"></i> View Orders
          </a>
          <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="ti ti-external-link me-1"></i> Storefront
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <!-- Stat Cards Grid -->
    <div class="row row-deck row-cards mb-4">
      <!-- Total Revenue -->
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-primary text-white avatar avatar-md rounded-3">
                  <i class="ti ti-currency-rupee fs-2"></i>
                </span>
              </div>
              <div class="col">
                <div class="text-secondary small fw-medium">Total Revenue</div>
                <div class="fw-bold fs-2 text-dark lh-1 my-1"><?= e(money_short_inr($stats['revenue'])) ?></div>
                <div class="text-secondary small">Processed storefront sales</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-green text-white avatar avatar-md rounded-3">
                  <i class="ti ti-shopping-bag fs-2"></i>
                </span>
              </div>
              <div class="col">
                <div class="text-secondary small fw-medium">Total Orders</div>
                <div class="fw-bold fs-2 text-dark lh-1 my-1"><?= (int)$stats['total_orders'] ?></div>
                <div class="text-secondary small">
                  <?php if ((int)$stats['pending_orders'] > 0): ?>
                    <span class="badge bg-yellow-lt text-yellow"><?= (int)$stats['pending_orders'] ?> pending</span>
                  <?php else: ?>
                    <span class="text-muted">All orders fulfilled</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Products in Catalog -->
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-azure text-white avatar avatar-md rounded-3">
                  <i class="ti ti-shirt fs-2"></i>
                </span>
              </div>
              <div class="col">
                <div class="text-secondary small fw-medium">Catalog Products</div>
                <div class="fw-bold fs-2 text-dark lh-1 my-1"><?= (int)$stats['total_products'] ?></div>
                <div class="text-secondary small">
                  <?php if ((int)$stats['sold_out'] > 0): ?>
                    <span class="badge bg-red-lt text-red"><?= (int)$stats['sold_out'] ?> sold out</span>
                  <?php else: ?>
                    <span class="text-muted">All items in stock</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Customer Messages -->
      <div class="col-sm-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="bg-yellow text-white avatar avatar-md rounded-3">
                  <i class="ti ti-messages fs-2"></i>
                </span>
              </div>
              <div class="col">
                <div class="text-secondary small fw-medium">Inquiries & Quotes</div>
                <div class="fw-bold fs-2 text-dark lh-1 my-1"><?= (int)$stats['unread_messages'] ?></div>
                <div class="text-secondary small">
                  <?php if ((int)$stats['unread_messages'] > 0): ?>
                    <span class="badge bg-blue-lt text-blue">Unread inquiries</span>
                  <?php else: ?>
                    <span class="text-muted">Inbox up to date</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Section Grid -->
    <div class="row row-deck row-cards">
      <!-- Recent Orders Card -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center bg-transparent py-3">
            <div>
              <h3 class="card-title fw-bold mb-0">Recent Orders</h3>
              <div class="card-subtitle text-secondary small">Latest customer purchases and checkout submissions</div>
            </div>
            <a href="<?= base_url('/admin/orders') ?>" class="btn btn-sm btn-outline-secondary">
              View All Orders <i class="ti ti-arrow-right ms-1"></i>
            </a>
          </div>

          <?php if (empty($recentOrders)): ?>
            <div class="card-body text-center py-5">
              <div class="text-secondary mb-3">
                <i class="ti ti-shopping-cart-x fs-1 text-muted" style="font-size: 3rem;"></i>
              </div>
              <h4 class="text-secondary fw-semibold">No Orders Recorded Yet</h4>
              <p class="text-muted small mb-0">When customers place orders on your storefront, they will be listed here.</p>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-vcenter table-hover card-table">
                <thead>
                  <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Fulfillment</th>
                    <th>Date</th>
                    <th class="w-1"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentOrders as $o): ?>
                    <tr>
                      <td>
                        <span class="badge bg-dark-lt fw-bold font-monospace"><?= e($o['order_number']) ?></span>
                      </td>
                      <td>
                        <div class="fw-medium text-dark"><?= e($o['customer_name']) ?></div>
                        <div class="text-secondary small"><?= e($o['customer_phone']) ?></div>
                      </td>
                      <td>
                        <strong class="text-dark"><?= e(money_short_inr($o['total'])) ?></strong>
                      </td>
                      <td>
                        <span class="text-uppercase small fw-semibold"><?= e($o['payment_method']) ?></span>
                        <div>
                          <?php if ($o['payment_status'] === 'paid'): ?>
                            <span class="badge bg-success-lt">Paid</span>
                          <?php elseif ($o['payment_status'] === 'awaiting_verification'): ?>
                            <span class="badge bg-info-lt">Verifying</span>
                          <?php else: ?>
                            <span class="badge bg-warning-lt">Pending</span>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td>
                        <?php
                          $st = $o['order_status'];
                          $badgeClass = 'bg-secondary-lt';
                          if ($st === 'delivered') $badgeClass = 'bg-success-lt';
                          elseif ($st === 'shipped') $badgeClass = 'bg-primary-lt';
                          elseif ($st === 'printing' || $st === 'confirmed') $badgeClass = 'bg-info-lt';
                          elseif ($st === 'pending') $badgeClass = 'bg-warning-lt';
                          elseif ($st === 'cancelled') $badgeClass = 'bg-danger-lt';
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= ucfirst(e($st)) ?></span>
                      </td>
                      <td class="text-secondary small">
                        <?= date('d M, H:i', strtotime($o['created_at'])) ?>
                      </td>
                      <td>
                        <a href="<?= base_url('/admin/orders/' . (int)$o['id']) ?>" class="btn btn-sm btn-outline-secondary">
                          <i class="ti ti-eye me-1"></i> Manage
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right Column: Shortcuts & Store Status -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">Quick Operations</h3>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="<?= base_url('/admin/products/new') ?>" class="btn btn-dark text-start d-flex justify-content-between align-items-center">
                <span><i class="ti ti-plus me-2"></i> Add New Product</span>
                <i class="ti ti-chevron-right text-white-50"></i>
              </a>
              <a href="<?= base_url('/admin/collections') ?>" class="btn btn-outline-secondary text-start d-flex justify-content-between align-items-center">
                <span><i class="ti ti-category me-2"></i> Manage Collections</span>
                <i class="ti ti-chevron-right text-muted"></i>
              </a>
              <a href="<?= base_url('/admin/messages') ?>" class="btn btn-outline-secondary text-start d-flex justify-content-between align-items-center">
                <span><i class="ti ti-mail me-2"></i> View Inquiries</span>
                <?php if ((int)$stats['unread_messages'] > 0): ?>
                  <span class="badge bg-blue text-white"><?= (int)$stats['unread_messages'] ?></span>
                <?php else: ?>
                  <i class="ti ti-chevron-right text-muted"></i>
                <?php endif; ?>
              </a>
              <a href="<?= base_url('/admin/settings') ?>" class="btn btn-outline-secondary text-start d-flex justify-content-between align-items-center">
                <span><i class="ti ti-settings me-2"></i> Store Settings</span>
                <i class="ti ti-chevron-right text-muted"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- System & Store Environment Info -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">System Environment</h3>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                <span class="text-secondary small">Framework</span>
                <span class="badge bg-dark-lt fw-medium">CodeIgniter 4.7.6</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                <span class="text-secondary small">Admin UI Kit</span>
                <span class="badge bg-primary-lt fw-medium">Tabler 1.0 (Bootstrap 5)</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                <span class="text-secondary small">Database</span>
                <span class="badge bg-azure-lt fw-medium">SQLite (Shared-Hosting Ready)</span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                <span class="text-secondary small">Timezone</span>
                <span class="badge bg-secondary-lt fw-medium"><?= date_default_timezone_get() ?></span>
              </div>
              <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                <span class="text-secondary small">Server Status</span>
                <span class="badge bg-success-lt fw-semibold"><i class="ti ti-check me-1"></i> Running</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
