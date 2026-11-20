<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<?php
$cleanPhone = preg_replace('/\D+/', '', (string)$order['customer_phone']);
if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;
?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/admin/orders') ?>" class="text-secondary">Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($order['order_number']) ?></li>
          </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">
          <h2 class="page-title fw-bold">Order <?= e($order['order_number']) ?></h2>
          <?php
            $st = $order['order_status'];
            $badgeClass = 'bg-secondary-lt';
            if ($st === 'delivered') $badgeClass = 'bg-success-lt';
            elseif ($st === 'shipped') $badgeClass = 'bg-primary-lt';
            elseif ($st === 'printing' || $st === 'confirmed') $badgeClass = 'bg-info-lt';
            elseif ($st === 'pending') $badgeClass = 'bg-warning-lt';
            elseif ($st === 'cancelled') $badgeClass = 'bg-danger-lt';
          ?>
          <span class="badge <?= $badgeClass ?> fs-4"><?= ucfirst(e($st)) ?></span>
        </div>
      </div>
      <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
          <a href="https://wa.me/<?= e($cleanPhone) ?>?text=<?= rawurlencode('Hi ' . $order['customer_name'] . ', update regarding your Okina Craft order ' . $order['order_number'] . ':') ?>" target="_blank" class="btn btn-success d-inline-flex align-items-center">
            <i class="ti ti-brand-whatsapp me-1 fs-2"></i> WhatsApp Customer
          </a>
          <a href="<?= base_url('/admin/orders') ?>" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Orders
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row row-cards">
      <!-- Main Order Details (Left Column) -->
      <div class="col-lg-8">
        <!-- Order Items Card -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">Purchased Items (<?= count($order['items']) ?>)</h3>
          </div>
          <div class="table-responsive">
            <table class="table table-vcenter card-table">
              <thead>
                <tr>
                  <th style="width: 70px;">Item</th>
                  <th>Product Details</th>
                  <th class="text-center">Price × Qty</th>
                  <th class="text-end">Line Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($order['items'] as $item): ?>
                  <tr>
                    <td>
                      <?php if (!empty($item['image_src'])): ?>
                        <img class="table-thumb" src="<?= base_url($item['image_src']) ?>" alt="">
                      <?php else: ?>
                        <div class="table-thumb d-flex align-items-center justify-content-center text-muted">
                          <i class="ti ti-photo"></i>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= e($item['product_title']) ?></div>
                      <?php if (!empty($item['variant_title'])): ?>
                        <div class="text-secondary small">Option: <span class="badge bg-light text-dark border"><?= e($item['variant_title']) ?></span></div>
                      <?php endif; ?>

                      <!-- Custom Artwork / Logo Attachment -->
                      <?php if (!empty($item['custom_design_path'])): ?>
                        <div class="mt-2 p-2 rounded bg-yellow-lt border border-warning d-flex align-items-center gap-2" style="max-width: 360px;">
                          <img src="<?= base_url($item['custom_design_path']) ?>" style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px; border: 1px solid rgba(0,0,0,0.1);" alt="Custom artwork">
                          <div>
                            <div class="fw-bold small text-dark"><i class="ti ti-upload me-1"></i> Customer Artwork Attached</div>
                            <a href="<?= base_url($item['custom_design_path']) ?>" target="_blank" download class="btn btn-sm btn-dark py-0 px-2 mt-1">
                              <i class="ti ti-download me-1"></i> Download File
                            </a>
                          </div>
                        </div>
                      <?php endif; ?>

                      <?php if (!empty($item['custom_note'])): ?>
                        <div class="mt-1 small text-dark">
                          <strong>Note:</strong> <span class="text-secondary"><?= e($item['custom_note']) ?></span>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td class="text-center text-secondary">
                      <?= e(money_short_inr($item['unit_price'])) ?> × <?= (int)$item['quantity'] ?>
                    </td>
                    <td class="text-end fw-bold text-dark">
                      <?= e(money_short_inr($item['line_total'])) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Totals Footer -->
          <div class="card-footer bg-transparent py-3">
            <div class="row justify-content-end">
              <div class="col-md-5">
                <div class="d-flex justify-content-between mb-1 text-secondary small">
                  <span>Subtotal:</span>
                  <span class="fw-medium text-dark"><?= e(money_short_inr($order['subtotal'])) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-secondary small">
                  <span>Shipping Fee:</span>
                  <span class="fw-medium text-dark"><?= (float)$order['shipping_cost'] == 0 ? '<span class="badge bg-success-lt">FREE</span>' : e(money_short_inr($order['shipping_cost'])) ?></span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                  <span class="fw-bold text-dark fs-3">Grand Total:</span>
                  <span class="fw-bold text-dark fs-2"><?= e(money_format_inr($order['total'])) ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Customer & Shipping Information Card -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">Customer & Delivery Address</h3>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="text-secondary small fw-medium">Customer Name</div>
                <div class="fw-bold text-dark fs-3 mb-2"><?= e($order['customer_name']) ?></div>

                <div class="text-secondary small fw-medium">Contact Details</div>
                <div class="d-flex flex-column gap-1 mb-2">
                  <a href="mailto:<?= e($order['customer_email']) ?>" class="text-reset text-decoration-none">
                    <i class="ti ti-mail me-1 text-muted"></i> <?= e($order['customer_email']) ?>
                  </a>
                  <a href="tel:<?= e($order['customer_phone']) ?>" class="text-reset text-decoration-none">
                    <i class="ti ti-phone me-1 text-muted"></i> <?= e($order['customer_phone']) ?>
                  </a>
                </div>
              </div>

              <div class="col-md-6">
                <div class="text-secondary small fw-medium">Shipping Address</div>
                <div class="bg-light p-3 rounded text-dark">
                  <div class="fw-semibold"><?= nl2br(e($order['shipping_address'])) ?></div>
                  <div><?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?></div>
                  <div>Pincode: <strong><?= e($order['shipping_pincode']) ?></strong></div>
                  <div>Country: <?= e($order['shipping_country'] ?? 'India') ?></div>
                </div>
              </div>

              <?php if (!empty($order['customer_note'])): ?>
                <div class="col-12">
                  <div class="alert alert-info mb-0">
                    <div class="fw-bold"><i class="ti ti-notes me-1"></i> Customer Order Note:</div>
                    <div><?= e($order['customer_note']) ?></div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions & Tracking (Right Column) -->
      <div class="col-lg-4">
        <!-- Update Status Card -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">Update Order Status</h3>
          </div>
          <div class="card-body">
            <form action="<?= base_url('/admin/orders/' . (int)$order['id']) ?>" method="post">
              <?= csrf_field() ?>

              <div class="mb-3">
                <label class="form-label fw-semibold" for="order_status">Fulfillment Status</label>
                <select class="form-select" id="order_status" name="order_status">
                  <?php foreach (['pending' => 'Pending', 'confirmed' => 'Confirmed', 'printing' => 'Printing / Production', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $stKey => $stLbl): ?>
                    <option value="<?= $stKey ?>" <?= $order['order_status'] === $stKey ? 'selected' : '' ?>><?= $stLbl ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold" for="payment_status">
                  Payment Status
                  <span class="badge bg-light text-muted border text-uppercase ms-1"><?= e($order['payment_method']) ?></span>
                </label>
                <select class="form-select" id="payment_status" name="payment_status">
                  <?php foreach (['pending' => 'Pending Payment', 'awaiting_verification' => 'Awaiting Verification', 'paid' => 'Paid / Confirmed', 'refunded' => 'Refunded'] as $psKey => $psLbl): ?>
                    <option value="<?= $psKey ?>" <?= $order['payment_status'] === $psKey ? 'selected' : '' ?>><?= $psLbl ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold" for="tracking_number">Courier AWB / Tracking #</label>
                <div class="input-icon">
                  <span class="input-icon-addon"><i class="ti ti-truck-delivery"></i></span>
                  <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="<?= e($order['tracking_number'] ?? '') ?>" placeholder="e.g. DTDC-987654321">
                </div>
              </div>

              <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
                <i class="ti ti-check me-1"></i> Update Order Status
              </button>
            </form>
          </div>
        </div>

        <!-- Order Metadata Card -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">Order Metadata</h3>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <div class="list-group-item d-flex justify-content-between py-2 px-3">
                <span class="text-secondary small">Order ID</span>
                <span class="font-monospace small">#<?= (int)$order['id'] ?></span>
              </div>
              <div class="list-group-item d-flex justify-content-between py-2 px-3">
                <span class="text-secondary small">Payment Mode</span>
                <span class="fw-semibold text-uppercase small"><?= e($order['payment_method']) ?></span>
              </div>
              <div class="list-group-item d-flex justify-content-between py-2 px-3">
                <span class="text-secondary small">Placed At</span>
                <span class="text-secondary small"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span>
              </div>
              <div class="list-group-item d-flex justify-content-between py-2 px-3">
                <span class="text-secondary small">Last Updated</span>
                <span class="text-secondary small"><?= date('d M Y, H:i', strtotime($order['updated_at'] ?? $order['created_at'])) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
