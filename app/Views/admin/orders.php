<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle text-secondary">Fulfillment & Sales</div>
        <h2 class="page-title fw-bold">
          Customer Orders
          <span class="badge bg-secondary-lt ms-2 font-monospace fs-4"><?= count($orders) ?></span>
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <!-- Filter Pills Card -->
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <span class="text-secondary small fw-medium me-2">Status:</span>
          <?php
          $statusTabs = [
              'all'       => 'All Orders',
              'pending'   => 'Pending',
              'confirmed' => 'Confirmed',
              'printing'  => 'Printing / In Production',
              'shipped'   => 'Shipped',
              'delivered' => 'Delivered',
              'cancelled' => 'Cancelled',
          ];
          foreach ($statusTabs as $k => $lbl):
              $isActive = ($status === $k);
          ?>
            <a href="<?= base_url('/admin/orders?status=' . $k) ?>" class="btn btn-sm <?= $isActive ? 'btn-dark' : 'btn-outline-secondary' ?>">
              <?= e($lbl) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Orders Table Card -->
    <div class="card border-0 shadow-sm">
      <?php if (empty($orders)): ?>
        <div class="card-body text-center py-5">
          <div class="text-secondary mb-3">
            <i class="ti ti-shopping-cart-x fs-1 text-muted" style="font-size: 3rem;"></i>
          </div>
          <h4 class="text-secondary fw-semibold">No Orders Found</h4>
          <p class="text-muted small mb-0">No orders currently match the "<?= e($status) ?>" filter criteria.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-vcenter table-hover card-table">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Destination</th>
                <th>Total (INR)</th>
                <th>Payment</th>
                <th>Fulfillment</th>
                <th>Order Date</th>
                <th class="w-1"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
                <tr>
                  <td>
                    <a href="<?= base_url('/admin/orders/' . (int)$o['id']) ?>" class="badge bg-dark-lt text-dark fw-bold font-monospace text-decoration-none fs-4">
                      <?= e($o['order_number']) ?>
                    </a>
                  </td>
                  <td>
                    <div class="fw-bold text-dark"><?= e($o['customer_name']) ?></div>
                    <div class="text-secondary small"><?= e($o['customer_phone']) ?> · <?= e($o['customer_email']) ?></div>
                  </td>
                  <td class="text-secondary">
                    <?= e($o['shipping_city']) ?>, <?= e($o['shipping_state']) ?>
                  </td>
                  <td>
                    <div class="fw-bold text-dark fs-3"><?= e(money_short_inr($o['total'])) ?></div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border text-uppercase small"><?= e($o['payment_method']) ?></span>
                    <div class="mt-1">
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
                    <span class="badge <?= $badgeClass ?> fw-semibold"><?= ucfirst(e($st)) ?></span>
                  </td>
                  <td class="text-secondary small">
                    <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
                  </td>
                  <td>
                    <a href="<?= base_url('/admin/orders/' . (int)$o['id']) ?>" class="btn btn-sm btn-outline-secondary">
                      <i class="ti ti-eye me-1"></i> Details
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
</div>

<?= $this->endSection() ?>
