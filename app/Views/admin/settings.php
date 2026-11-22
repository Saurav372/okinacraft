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
                    <input type="text" class="form-control" name="instagram_handle" value="<?= e($settings['instagram_handle'] ?? 'okina_craft') ?>" placeholder="okina_craft">
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
                  <label class="form-label fw-semibold">Storefront Cash on Delivery (COD)</label>
                  <select class="form-select" name="cod_enabled">
                    <option value="0" <?= ($settings['cod_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>Disabled (Prepaid UPI only)</option>
                    <option value="1" <?= ($settings['cod_enabled'] ?? '0') === '1' ? 'selected' : '' ?>>Enabled (Allow COD at checkout)</option>
                  </select>
                  <small class="form-hint">Currently <strong>Disabled</strong> on storefront. Preserved here for future activation.</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Store UPI ID (shown at checkout)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-qrcode"></i></span>
                    <input type="text" class="form-control" name="upi_id" value="<?= e($settings['upi_id'] ?? '') ?>" placeholder="okinacraft@upi">
                  </div>
                  <small class="form-hint">Primary UPI ID for customer prepaid transfers.</small>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Homepage Video Section Heading</label>
                  <input type="text" class="form-control" name="video_heading" value="<?= e($settings['video_heading'] ?? 'How to Buy from Okina Craft') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Support Hours</label>
                  <input type="text" class="form-control" name="business_hours" value="<?= e($settings['business_hours'] ?? '') ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-12">
                  <label class="form-label fw-semibold">Google OAuth Client ID (Optional for 1-Click Google Sign-In)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-brand-google"></i></span>
                    <input type="text" class="form-control" name="google_client_id" value="<?= e($settings['google_client_id'] ?? '') ?>" placeholder="e.g. 1234567890-abcdefg.apps.googleusercontent.com">
                  </div>
                  <small class="form-hint">Obtain free from Google Cloud Console &rarr; APIs &amp; Services &rarr; Credentials. When empty, customers sign in with Email &amp; Password.</small>
                </div>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-12">
                  <label class="form-label fw-semibold">Business Physical Address</label>
                  <input type="text" class="form-control" name="business_address" value="<?= e($settings['business_address'] ?? '') ?>">
                </div>
              </div>

              <button type="submit" class="btn btn-dark">
                <i class="ti ti-check me-1"></i> Save Store Settings
              </button>
            </form>
          </div>
        </div>

        <!-- cPanel SMTP & Business Email Card -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent py-3">
            <h3 class="card-title fw-bold mb-0">
              <i class="ti ti-mail me-1 text-primary"></i> cPanel Business Email &amp; SMTP Configuration
            </h3>
          </div>
          <div class="card-body">
            <form action="<?= base_url('/admin/settings') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="section" value="smtp">

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">SMTP Host</label>
                  <input type="text" class="form-control" name="smtp_host" value="<?= e($settings['smtp_host'] ?? '') ?>" placeholder="e.g. mail.okinacraft.com">
                  <small class="form-hint">Usually <code>mail.yourdomain.com</code> in cPanel.</small>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold">SMTP Port</label>
                  <input type="number" class="form-control" name="smtp_port" value="<?= e($settings['smtp_port'] ?? '465') ?>" placeholder="465">
                  <small class="form-hint">465 (SSL) or 587 (TLS).</small>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold">Encryption</label>
                  <select class="form-select" name="smtp_crypto">
                    <option value="ssl" <?= ($settings['smtp_crypto'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                    <option value="tls" <?= ($settings['smtp_crypto'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                  </select>
                </div>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">cPanel Email Username</label>
                  <input type="email" class="form-control" name="smtp_user" value="<?= e($settings['smtp_user'] ?? '') ?>" placeholder="orders@okinacraft.com">
                  <small class="form-hint">Your full cPanel email address.</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">cPanel Email Password</label>
                  <input type="password" class="form-control" name="smtp_pass" value="<?= e($settings['smtp_pass'] ?? '') ?>" placeholder="••••••••">
                  <small class="form-hint">Your email account password created in cPanel.</small>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-dark">
                  <i class="ti ti-check me-1"></i> Save SMTP Settings
                </button>
              </div>
            </form>

            <hr class="my-4">

            <!-- Send Test Email Tool -->
            <form action="<?= base_url('/admin/settings') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="section" value="smtp_test">
              <label class="form-label fw-semibold">Test SMTP Connectivity</label>
              <div class="input-group" style="max-width: 48rem;">
                <input type="email" class="form-control" name="test_email" value="<?= e($admin['email']) ?>" placeholder="Enter recipient email">
                <button type="submit" class="btn btn-outline-primary">
                  <i class="ti ti-send me-1"></i> Send Test Email
                </button>
              </div>
              <small class="form-hint">Sends an instant test email to verify your cPanel SMTP outgoing server.</small>
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
                <span class="text-secondary small"><?= e($admin['email'] ?? 'admin@okinacraft.com') ?></span>
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
