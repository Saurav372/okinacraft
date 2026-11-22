<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="wishlist-page" style="padding: 3.5rem 1.6rem 6rem;">
  <div class="page-width">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.8rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.6rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h1 class="h1" style="margin: 0; font-weight: 700;">My Wishlist</h1>
        <p style="color: var(--color-muted); font-size: 1.45rem; margin-top: 0.4rem; margin-bottom: 0;">
          Your saved apparel and custom merchandise.
        </p>
      </div>

      <div style="font-size: 1.4rem; color: var(--color-muted);">
        <span id="wishlist-total-count"><?= !empty($products) ? count($products) : 0 ?></span> item(s) saved
      </div>
    </div>

    <!-- Wishlist Grid Container -->
    <div id="wishlist-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(24rem, 1fr)); gap: 2.4rem;">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $prod): ?>
          <div class="wishlist-item-card" data-product-id="<?= (int)$prod['id'] ?>" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s;">
            
            <div style="position: relative; aspect-ratio: 1; background: #f8fafc; overflow: hidden;">
              <a href="<?= base_url('/products/' . e($prod['handle'])) ?>">
                <?php if (!empty($prod['image_src'])): ?>
                  <img src="<?= base_url($prod['image_src']) ?>" alt="<?= e($prod['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                  <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;">No Image</div>
                <?php endif; ?>
              </a>

              <!-- Remove Heart Button -->
              <button type="button" onclick="removeWishlistItem(<?= (int)$prod['id'] ?>, this)" title="Remove from wishlist" style="position: absolute; top: 1.2rem; right: 1.2rem; width: 3.6rem; height: 3.6rem; border-radius: 50%; background: #fff; border: 1px solid rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; cursor: pointer; color: #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              </button>
            </div>

            <div style="padding: 1.8rem; display: flex; flex-direction: column; flex: 1;">
              <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0 0 0.8rem; line-height: 1.4;">
                <a href="<?= base_url('/products/' . e($prod['handle'])) ?>" style="color: #000; text-decoration: none;">
                  <?= e($prod['title']) ?>
                </a>
              </h2>

              <div style="display: flex; align-items: baseline; gap: 0.8rem; margin-bottom: 1.6rem; margin-top: auto;">
                <span style="font-size: 1.7rem; font-weight: 700; color: #000;">
                  <?= e(money_short_inr($prod['price'])) ?>
                </span>
                <?php if (!empty($prod['compare_at_price']) && (float)$prod['compare_at_price'] > (float)$prod['price']): ?>
                  <span style="font-size: 1.35rem; color: var(--color-muted); text-decoration: line-through;">
                    <?= e(money_short_inr($prod['compare_at_price'])) ?>
                  </span>
                <?php endif; ?>
              </div>

              <a href="<?= base_url('/products/' . e($prod['handle'])) ?>" class="button button--full-width" style="border-radius: 8px; font-size: 1.35rem; padding: 1rem;">
                Select Options / Add to Cart
              </a>
            </div>

          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Empty State -->
    <div id="wishlist-empty-state" style="<?= !empty($products) ? 'display: none;' : '' ?> text-align: center; padding: 6rem 2rem;">
      <div style="width: 8rem; height: 8rem; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 3.4rem; color: #94a3b8; margin-bottom: 1.8rem;">
        ♥
      </div>
      <h2 class="h2" style="margin-bottom: 0.8rem;">Your wishlist is empty</h2>
      <p style="color: var(--color-muted); font-size: 1.5rem; max-width: 42rem; margin: 0 auto 2.4rem;">
        Explore our custom 240 GSM oversized tees, 350 GSM fleece hoodies, and click the heart icon on any product to save it here.
      </p>
      <a href="<?= base_url('/collections/all') ?>" class="button" style="border-radius: 8px; padding: 1.4rem 2.8rem;">
        Explore Collections
      </a>
    </div>

  </div>
</section>

<script>
// If guest, load items from localStorage via API
document.addEventListener('DOMContentLoaded', function() {
  const isCustomerLoggedIn = <?= !empty($customer) ? 'true' : 'false' ?>;
  
  if (!isCustomerLoggedIn) {
    let localIds = [];
    try {
      localIds = JSON.parse(localStorage.getItem('okina_wishlist') || '[]');
    } catch(e) {}

    const countEl = document.getElementById('wishlist-total-count');
    if (countEl) countEl.textContent = localIds.length;

    if (localIds.length === 0) {
      document.getElementById('wishlist-empty-state').style.display = 'block';
      document.getElementById('wishlist-grid').innerHTML = '';
      return;
    }

    fetch('<?= base_url('/api/wishlist/data?ids=') ?>' + localIds.join(','))
      .then(res => res.json())
      .then(res => {
        if (!res.ok || !res.products || res.products.length === 0) {
          document.getElementById('wishlist-empty-state').style.display = 'block';
          document.getElementById('wishlist-grid').innerHTML = '';
          return;
        }

        document.getElementById('wishlist-empty-state').style.display = 'none';
        let html = '';
        res.products.forEach(prod => {
          html += `
            <div class="wishlist-item-card" data-product-id="${prod.id}" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
              <div style="position: relative; aspect-ratio: 1; background: #f8fafc; overflow: hidden;">
                <a href="<?= base_url('/products/') ?>${prod.handle}">
                  <img src="${prod.image_src ? '<?= base_url() ?>/' + prod.image_src : ''}" alt="${prod.title}" style="width: 100%; height: 100%; object-fit: cover;">
                </a>
                <button type="button" onclick="removeWishlistItem(${prod.id}, this)" title="Remove" style="position: absolute; top: 1.2rem; right: 1.2rem; width: 3.6rem; height: 3.6rem; border-radius: 50%; background: #fff; border: 1px solid rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; cursor: pointer; color: #dc2626;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </button>
              </div>
              <div style="padding: 1.8rem; display: flex; flex-direction: column; flex: 1;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0 0 0.8rem;">
                  <a href="<?= base_url('/products/') ?>${prod.handle}" style="color: #000; text-decoration: none;">${prod.title}</a>
                </h2>
                <div style="font-size: 1.7rem; font-weight: 700; color: #000; margin-bottom: 1.6rem; margin-top: auto;">
                  ₹${parseFloat(prod.price).toFixed(2)}
                </div>
                <a href="<?= base_url('/products/') ?>${prod.handle}" class="button button--full-width" style="border-radius: 8px; font-size: 1.35rem; padding: 1rem;">
                  Select Options / Add to Cart
                </a>
              </div>
            </div>
          `;
        });
        document.getElementById('wishlist-grid').innerHTML = html;
      });
  }
});

function removeWishlistItem(productId, btn) {
  const card = btn.closest('.wishlist-item-card');
  if (card) {
    card.style.opacity = '0.4';
    card.style.pointerEvents = 'none';
  }

  // Toggle API
  const formData = new FormData();
  formData.append('product_id', productId);
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  fetch('<?= base_url('/api/wishlist/toggle') ?>', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    // Also remove from localStorage
    try {
      let ids = JSON.parse(localStorage.getItem('okina_wishlist') || '[]');
      ids = ids.filter(id => id !== productId);
      localStorage.setItem('okina_wishlist', JSON.stringify(ids));
      updateHeaderWishlistCount(ids.length);
    } catch(e) {}

    if (card) card.remove();

    const remaining = document.querySelectorAll('.wishlist-item-card').length;
    const countEl = document.getElementById('wishlist-total-count');
    if (countEl) countEl.textContent = remaining;

    if (remaining === 0) {
      document.getElementById('wishlist-empty-state').style.display = 'block';
    }
  });
}
</script>

<?= $this->endSection() ?>
