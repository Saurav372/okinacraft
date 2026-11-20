<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle text-secondary">Configuration & Legal</div>
        <h2 class="page-title fw-bold">Store Settings & Policies</h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row row-cards">
      <!-- Left Column: Store Details & Policies -->
      <div class="col-lg-8">
        <!-- General Store Settings Card -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">
              <i class="ti ti-building-store me-1 text-primary"></i> General Store Configuration
            </h3>
          </div>
          <div class="card-body">
            <form action="<?= base_url('/admin/settings') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="section" value="general">

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Store Brand Name</label>
                  <input type="text" class="form-control" name="store_name" value="<?= e($settings['store_name'] ?? 'Okina Craft') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Customer Support Email</label>
                  <input type="email" class="form-control" name="store_email" value="<?= e($settings['store_email'] ?? '') ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">WhatsApp Number (with country code)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-brand-whatsapp text-success"></i></span>
                    <input type="text" class="form-control" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '918380808435') ?>" placeholder="918380808435">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Instagram Handle</label>
                  <div class="input-group">
                    <span class="input-group-text">@</span>
                    <input type="text" class="form-control" name="instagram_handle" value="<?= e($settings['instagram_handle'] ?? 'clusterstudio1') ?>" placeholder="clusterstudio1">
                  </div>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Flat Shipping Fee (INR)</label>
                  <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="1" class="form-control" name="shipping_flat_rate" value="<?= e($settings['shipping_flat_rate'] ?? '60') ?>">
                  </div>
                  <small class="form-hint">Applied to orders below the free shipping threshold.</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Free Shipping Threshold (INR)</label>
                  <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="1" class="form-control" name="free_shipping_threshold" value="<?= e($settings['free_shipping_threshold'] ?? '999') ?>">
                  </div>
                  <small class="form-hint">Orders at or above this cart value receive ₹0 shipping.</small>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Store UPI ID (shown at checkout)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-qrcode"></i></span>
                    <input type="text" class="form-control" name="upi_id" value="<?= e($settings['upi_id'] ?? '') ?>" placeholder="clusterstudio@upi">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Homepage Video Section Heading</label>
                  <input type="text" class="form-control" name="video_heading" value="<?= e($settings['video_heading'] ?? 'How to Buy from Okina Craft') ?>">
                </div>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Business Physical Address</label>
                  <input type="text" class="form-control" name="business_address" value="<?= e($settings['business_address'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Support Hours</label>
                  <input type="text" class="form-control" name="business_hours" value="<?= e($settings['business_hours'] ?? '') ?>">
                </div>
              </div>

              <button type="submit" class="btn btn-dark">
                <i class="ti ti-check me-1"></i> Save Store Settings
              </button>
            </form>
          </div>
        </div>

        <!-- Store Legal Policies Editor -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">
              <i class="ti ti-file-text me-1 text-primary"></i> Legal Policies & Static Pages
            </h3>
          </div>
          <div class="card-body">
            <div class="accordion" id="policies-accordion">
              <?php foreach ($policies as $idx => $pol): ?>
                <div class="accordion-item mb-2 border rounded">
                  <h2 class="accordion-header" id="heading-<?= $idx ?>">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $idx ?>" aria-expanded="false" aria-controls="collapse-<?= $idx ?>">
                      <?= e($pol['title']) ?>
                      <span class="badge bg-secondary-lt font-monospace ms-2">/policies/<?= e($pol['slug']) ?></span>
                    </button>
                  </h2>
                  <div id="collapse-<?= $idx ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $idx ?>" data-bs-parent="#policies-accordion">
                    <div class="accordion-body">
                      <form action="<?= base_url('/admin/settings') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="section" value="policy">
                        <input type="hidden" name="policy_slug" value="<?= e($pol['slug']) ?>">

                        <div class="mb-3">
                          <label class="form-label fw-semibold">Policy Page Title</label>
                          <input type="text" class="form-control" name="policy_title" value="<?= e($pol['title']) ?>">
                        </div>

                        <div class="mb-3">
                          <label class="form-label fw-semibold">HTML Content</label>
                          <textarea class="form-control font-monospace" name="policy_body" rows="8"><?= e($pol['body_html']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-sm btn-dark">
                          <i class="ti ti-check me-1"></i> Save "<?= e($pol['title']) ?>"
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Admin Password & Security -->
      <div class="col-lg-4">
        <!-- Change Password Card -->
        <div class="card border-0 shadow-sm mb-3" id="security">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">
              <i class="ti ti-key me-1 text-primary"></i> Change Admin Password
            </h3>
          </div>
          <div class="card-body">
            <form action="<?= base_url('/admin/settings') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="section" value="password">

              <div class="mb-3">
                <label class="form-label fw-semibold" for="new_password">New Password (min 8 chars)</label>
                <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required placeholder="••••••••">
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold" for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required placeholder="••••••••">
              </div>

              <button type="submit" class="btn btn-dark w-100">
                <i class="ti ti-lock me-1"></i> Update Password
              </button>
            </form>
          </div>
        </div>

        <!-- Active Admin Session Card -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">Admin Session</h3>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <div class="list-group-item d-flex justify-content-between py-2 px-3">
                <span class="text-secondary small">Logged In User</span>
                <span class="fw-semibold text-dark small"><?= e($admin['name'] ?? 'Admin') ?></span>
              </div>
              <div class="list-group-item d-flex justify-content-between py-2 px-3">
                <span class="text-secondary small">Email</span>
                <span class="text-secondary small"><?= e($admin['email'] ?? 'demo@insignia.com') ?></span>
              </div>
              <div class="list-group-item d-flex justify-content-between py-2 px-3">
                <span class="text-secondary small">Session Security</span>
                <span class="badge bg-green-lt">Active · Rate Limited</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
