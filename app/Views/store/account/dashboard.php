<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$whatsappNo = store_setting('whatsapp_number', '7827747556');
?>

<section class="account-page" style="padding: 3.5rem 1.6rem 6rem;">
  <div class="page-width">
    
    <!-- Top Account Banner -->
    <div style="background: #000; color: #fff; border-radius: 16px; padding: 3rem 2.8rem; margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;">
      <div style="display: flex; align-items: center; gap: 1.8rem;">
        <div style="width: 6rem; height: 6rem; border-radius: 50%; background: #222; border: 2px solid #444; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 700; color: #fff; overflow: hidden; flex-shrink: 0;">
          <?php if (!empty($customer['avatar_url'])): ?>
            <img src="<?= e($customer['avatar_url']) ?>" alt="<?= e($customer['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
          <?php else: ?>
            <?= strtoupper(substr($customer['name'], 0, 1)) ?>
          <?php endif; ?>
        </div>
        <div>
          <h1 class="h2" style="margin: 0; color: #fff; font-weight: 700;">Welcome, <?= e($customer['name']) ?></h1>
          <div style="color: #94a3b8; font-size: 1.4rem; margin-top: 0.4rem;">
            <?= e($customer['email']) ?><?= !empty($customer['phone']) ? ' · +91 ' . e($customer['phone']) : '' ?>
          </div>
        </div>
      </div>

      <div style="display: flex; gap: 1.2rem; align-items: center;">
        <a href="<?= base_url('/wishlist') ?>" class="button button--secondary" style="background: transparent; color: #fff; border-color: rgba(255,255,255,0.3); font-size: 1.35rem; padding: 1rem 1.6rem;">
          ♥ Wishlist (<?= (int)$wishlistCount ?>)
        </a>
        <a href="<?= base_url('/account/logout') ?>" class="button button--secondary" style="background: #222; color: #fff; border-color: #333; font-size: 1.35rem; padding: 1rem 1.6rem;">
          Sign Out
        </a>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="flash flash--error" style="margin-bottom: 2.4rem; border-radius: 8px;">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="flash flash--success" style="margin-bottom: 2.4rem; border-radius: 8px;">
        <?= e($success) ?>
      </div>
    <?php endif; ?>

    <!-- Account Navigation Tabs -->
    <div style="display: flex; gap: 1rem; border-bottom: 2px solid #e2e8f0; margin-bottom: 3rem; overflow-x: auto;">
      <button type="button" class="tab-btn active" onclick="switchTab('orders', this)">
        My Orders (<?= count($orders) ?>)
      </button>
      <button type="button" class="tab-btn" onclick="switchTab('addresses', this)">
        Saved Addresses (<?= count($addresses) ?>)
      </button>
      <button type="button" class="tab-btn" onclick="switchTab('profile', this)">
        Profile Settings
      </button>
    </div>

    <!-- TAB 1: ORDERS -->
    <div id="tab-orders" class="tab-pane active">
      <?php if (empty($orders)): ?>
        <div class="checkout-card" style="text-align: center; padding: 5rem 2rem;">
          <div style="font-size: 4rem; margin-bottom: 1rem; color: #94a3b8;">🛍️</div>
          <h2 class="h3" style="margin-bottom: 0.8rem;">You haven't placed any orders yet</h2>
          <p style="color: var(--color-muted); margin-bottom: 2.4rem;">Browse our collections of oversized tees and hoodies.</p>
          <a href="<?= base_url('/collections/all') ?>" class="button" style="border-radius: 8px;">Explore Products</a>
        </div>
      <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 2rem;">
          <?php foreach ($orders as $order): ?>
            <div class="checkout-card" style="padding: 2.4rem; border-radius: 12px; border: 1px solid #e2e8f0;">
              
              <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.4rem; margin-bottom: 1.8rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.4rem;">
                <div>
                  <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.4rem;">
                    <strong style="font-size: 1.7rem; color: #000; font-family: monospace;">#<?= e($order['order_number']) ?></strong>
                    <?php
                      $statusColors = [
                        'pending'   => ['bg' => '#fef3c7', 'text' => '#92400e'],
                        'confirmed' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                        'printing'  => ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
                        'shipped'   => ['bg' => '#dcfce7', 'text' => '#166534'],
                        'delivered' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                        'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                      ];
                      $st = $statusColors[$order['order_status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                    ?>
                    <span style="background: <?= $st['bg'] ?>; color: <?= $st['text'] ?>; font-size: 1.2rem; font-weight: 700; padding: 0.3rem 0.8rem; border-radius: 6px; text-transform: uppercase;">
                      <?= e($order['order_status']) ?>
                    </span>
                  </div>
                  <div style="font-size: 1.35rem; color: var(--color-muted);">
                    Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?> · Payment: <strong><?= strtoupper(e($order['payment_method'])) ?></strong> (<?= e($order['payment_status']) ?>)
                  </div>
                </div>

                <div style="text-align: right;">
                  <div style="font-size: 1.8rem; font-weight: 700; color: #000;">
                    <?= e(money_short_inr($order['total'])) ?>
                  </div>
                  <div style="font-size: 1.25rem; color: var(--color-muted);">
                    <?= count($order['items']) ?> item(s)
                  </div>
                </div>
              </div>

              <!-- Order Items List -->
              <div style="display: flex; flex-direction: column; gap: 1.2rem; margin-bottom: 1.8rem;">
                <?php foreach ($order['items'] as $item): ?>
                  <div style="display: flex; justify-content: space-between; align-items: center; gap: 1.2rem;">
                    <div style="display: flex; align-items: center; gap: 1.2rem;">
                      <?php if (!empty($item['image_src'])): ?>
                        <img src="<?= base_url($item['image_src']) ?>" alt="<?= e($item['product_title']) ?>" style="width: 4.8rem; height: 4.8rem; object-fit: cover; border-radius: 6px; border: 1px solid #f1f5f9;">
                      <?php endif; ?>
                      <div>
                        <div style="font-size: 1.45rem; font-weight: 600; color: #000;"><?= e($item['product_title']) ?></div>
                        <div style="font-size: 1.3rem; color: var(--color-muted);">
                          <?= !empty($item['variant_title']) ? e($item['variant_title']) . ' · ' : '' ?>Qty: <?= (int)$item['quantity'] ?>
                        </div>
                      </div>
                    </div>
                    <div style="font-weight: 600; font-size: 1.45rem; color: #000;">
                      <?= e(money_short_inr($item['line_total'])) ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <!-- Shipping & Actions Footer -->
              <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.2rem; background: #fafafa; border-radius: 8px; padding: 1.2rem 1.6rem; border: 1px solid #f1f5f9; font-size: 1.35rem;">
                <div style="color: var(--color-muted);">
                  📍 <strong>Delivering to:</strong> <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?>
                  <?php if (!empty($order['tracking_number'])): ?>
                    <span style="margin-left: 1rem; color: #000; font-weight: 600;">AWB / Tracking: <?= e($order['tracking_number']) ?></span>
                  <?php endif; ?>
                </div>

                <div style="display: flex; gap: 0.8rem;">
                  <a href="<?= base_url('/account/orders/' . (int)$order['id']) ?>" class="button button--secondary" style="padding: 0.6rem 1.2rem; font-size: 1.3rem; border-radius: 6px;">
                    Order Details & Invoice
                  </a>
                  <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode('Hi Okina Craft! Update regarding my order #' . $order['order_number']) ?>" target="_blank" class="button" style="padding: 0.6rem 1.2rem; font-size: 1.3rem; border-radius: 6px; background: #059669;">
                    WhatsApp Support
                  </a>
                </div>
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- TAB 2: ADDRESSES -->
    <div id="tab-addresses" class="tab-pane" style="display: none;">
      
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 class="h3" style="margin: 0;">Saved Delivery Locations</h2>
        <button type="button" class="button" onclick="openAddressModal()" style="font-size: 1.35rem; padding: 0.8rem 1.6rem; border-radius: 8px;">
          + Add New Address
        </button>
      </div>

      <?php if (empty($addresses)): ?>
        <div class="checkout-card" style="text-align: center; padding: 4rem 2rem;">
          <p style="color: var(--color-muted); margin-bottom: 1.8rem;">No saved addresses yet. Save one now for rapid 1-click checkout!</p>
          <button type="button" class="button" onclick="openAddressModal()" style="border-radius: 8px;">+ Add Address</button>
        </div>
      <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(30rem, 1fr)); gap: 1.8rem;">
          <?php foreach ($addresses as $addr): ?>
            <div class="checkout-card" style="padding: 2.2rem; border-radius: 12px; border: 1px solid <?= !empty($addr['is_default']) ? '#000' : '#e2e8f0' ?>; position: relative;">
              <?php if (!empty($addr['is_default'])): ?>
                <span style="position: absolute; top: 1.6rem; right: 1.6rem; background: #000; color: #fff; font-size: 1.1rem; font-weight: 700; padding: 0.3rem 0.8rem; border-radius: 4px; text-transform: uppercase;">
                  Default
                </span>
              <?php endif; ?>

              <strong style="font-size: 1.6rem; color: #000; display: block; margin-bottom: 0.4rem;">
                <?= e($addr['full_name']) ?>
              </strong>
              <div style="color: var(--color-muted); font-size: 1.35rem; margin-bottom: 1rem;">
                📞 +91 <?= e($addr['phone']) ?>
              </div>
              <div style="font-size: 1.4rem; color: #334155; line-height: 1.5; margin-bottom: 1.8rem;">
                <?= nl2br(e($addr['address_line'])) ?><br>
                <?= e($addr['city']) ?>, <?= e($addr['state']) ?> - <strong><?= e($addr['pincode']) ?></strong>
              </div>

              <div style="display: flex; gap: 0.8rem; border-top: 1px solid #f1f5f9; padding-top: 1.2rem; font-size: 1.3rem;">
                <?php if (empty($addr['is_default'])): ?>
                  <form action="<?= base_url('/account/addresses/' . (int)$addr['id'] . '/default') ?>" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" style="background: none; border: none; padding: 0; color: #000; font-weight: 600; cursor: pointer; text-decoration: underline;">
                      Set as Default
                    </button>
                  </form>
                  <span style="color: #cbd5e1;">·</span>
                <?php endif; ?>

                <form action="<?= base_url('/account/addresses/' . (int)$addr['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Remove this address?');" style="display: inline;">
                  <?= csrf_field() ?>
                  <button type="submit" style="background: none; border: none; padding: 0; color: #dc2626; font-weight: 600; cursor: pointer; text-decoration: underline;">
                    Delete
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

    <!-- TAB 3: PROFILE -->
    <div id="tab-profile" class="tab-pane" style="display: none; max-width: 54rem;">
      <div class="checkout-card" style="padding: 3rem 2.8rem; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h2 class="h3" style="margin-bottom: 2rem;">Personal Details</h2>

        <form action="<?= base_url('/account/profile') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-row">
            <label class="form-label" for="prof-name" style="font-weight: 600;">Full Name</label>
            <input id="prof-name" name="name" type="text" class="field__input" required value="<?= e($customer['name']) ?>" style="border-radius: 8px;">
          </div>

          <div class="form-row">
            <label class="form-label" for="prof-email" style="font-weight: 600;">Email Address</label>
            <input id="prof-email" type="email" class="field__input" disabled value="<?= e($customer['email']) ?>" style="background: #f8fafc; border-radius: 8px; color: #64748b;">
            <small style="color: var(--color-muted); font-size: 1.2rem;">Email address cannot be changed directly.</small>
          </div>

          <div class="form-row">
            <label class="form-label" for="prof-phone" style="font-weight: 600;">Phone / WhatsApp</label>
            <input id="prof-phone" name="phone" type="tel" pattern="[0-9]{10}" class="field__input" value="<?= e($customer['phone'] ?? '') ?>" placeholder="10-digit mobile" style="border-radius: 8px;">
          </div>

          <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 2.4rem 0;">

          <h3 class="h4" style="margin-bottom: 1.4rem;">Change Password</h3>

          <?php if (!empty($customer['password_hash'])): ?>
            <div class="form-row">
              <label class="form-label" for="prof-cur-pw" style="font-weight: 600;">Current Password</label>
              <input id="prof-cur-pw" name="current_password" type="password" class="field__input" placeholder="••••••••" style="border-radius: 8px;">
            </div>
          <?php endif; ?>

          <div class="form-row">
            <label class="form-label" for="prof-new-pw" style="font-weight: 600;">New Password (Leave blank to keep current)</label>
            <input id="prof-new-pw" name="new_password" type="password" minlength="8" class="field__input" placeholder="••••••••" style="border-radius: 8px;">
          </div>

          <div style="margin-top: 2.6rem;">
            <button type="submit" class="button" style="border-radius: 8px; padding: 1.2rem 2.4rem;">
              Update Profile
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</section>

<!-- Add Address Modal Dialog -->
<dialog id="address-modal" style="border: none; border-radius: 16px; padding: 0; max-width: 50rem; width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
  <div style="padding: 2.8rem; background: #fff;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
      <h3 class="h3" style="margin: 0;">Add Delivery Address</h3>
      <button type="button" onclick="closeAddressModal()" style="background: none; border: none; font-size: 2.4rem; cursor: pointer; color: #94a3b8;">&times;</button>
    </div>

    <form action="<?= base_url('/account/addresses') ?>" method="post">
      <?= csrf_field() ?>

      <div class="form-row">
        <label class="form-label" style="font-weight: 600;">Receiver Full Name *</label>
        <input name="full_name" type="text" class="field__input" required value="<?= e($customer['name']) ?>" style="border-radius: 8px;">
      </div>

      <div class="form-row">
        <label class="form-label" style="font-weight: 600;">10-digit Phone Number *</label>
        <input name="phone" type="tel" pattern="[0-9]{10}" class="field__input" required value="<?= e($customer['phone'] ?? '') ?>" placeholder="9876543210" style="border-radius: 8px;">
      </div>

      <div class="form-row">
        <label class="form-label" style="font-weight: 600;">Full Shipping Address *</label>
        <textarea name="address_line" class="textarea__input" required placeholder="House/Flat No, Street, Landmark, Area" style="min-height: 8rem; border-radius: 8px;"></textarea>
      </div>

      <div class="form-grid-2">
        <div class="form-row">
          <label class="form-label" style="font-weight: 600;">City *</label>
          <input name="city" type="text" class="field__input" required placeholder="e.g. Pune" style="border-radius: 8px;">
        </div>
        <div class="form-row">
          <label class="form-label" style="font-weight: 600;">State *</label>
          <input name="state" type="text" class="field__input" required placeholder="e.g. Maharashtra" style="border-radius: 8px;">
        </div>
      </div>

      <div class="form-row">
        <label class="form-label" style="font-weight: 600;">PIN Code (6 digits) *</label>
        <input name="pincode" type="text" pattern="[0-9]{6}" maxlength="6" class="field__input" required placeholder="411001" style="border-radius: 8px;">
      </div>

      <label style="display: flex; align-items: center; gap: 0.8rem; margin: 1.6rem 0; cursor: pointer; font-size: 1.4rem;">
        <input type="checkbox" name="is_default" value="1" <?= empty($addresses) ? 'checked' : '' ?>>
        Set as default shipping address
      </label>

      <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="button" style="flex: 1; border-radius: 8px;">Save Address</button>
        <button type="button" onclick="closeAddressModal()" class="button button--secondary" style="border-radius: 8px;">Cancel</button>
      </div>
    </form>
  </div>
</dialog>

<style>
.tab-btn {
  background: none;
  border: none;
  font-size: 1.6rem;
  font-weight: 600;
  padding: 1.2rem 1.8rem;
  color: var(--color-muted);
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  white-space: nowrap;
}
.tab-btn.active {
  color: #000;
  border-bottom-color: #000;
}
</style>

<script>
function switchTab(tabName, btn) {
  document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
  
  const target = document.getElementById('tab-' + tabName);
  if (target) {
    target.style.display = 'block';
  }
  if (btn) {
    btn.classList.add('active');
  }
}

function openAddressModal() {
  const modal = document.getElementById('address-modal');
  if (modal.showModal) modal.showModal();
  else modal.style.display = 'block';
}

function closeAddressModal() {
  const modal = document.getElementById('address-modal');
  if (modal.close) modal.close();
  else modal.style.display = 'none';
}
</script>

<?= $this->endSection() ?>
