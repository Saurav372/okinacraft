/* Okina Craft Storefront Interactions (Zero external JS dependencies) */
(function () {
  'use strict';

  const basePath = (window.OC_BASE || window.CS_BASE || '').replace(/\/$/, '');

  // 1. Sticky Header Reduce Logo Size on Scroll
  const stickyHeader = document.querySelector('.section-header');
  if (stickyHeader) {
    const onScroll = () => {
      if (window.scrollY > 40) {
        stickyHeader.classList.add('scrolled-past-header');
      } else {
        stickyHeader.classList.remove('scrolled-past-header');
      }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // 2. Mobile Menu Drawer
  const drawer = document.getElementById('mobile-menu-drawer');
  const drawerBackdrop = document.getElementById('mobile-drawer-backdrop');
  const openDrawerBtn = document.getElementById('open-mobile-menu');
  const closeDrawerBtn = document.getElementById('close-mobile-menu');

  function toggleDrawer(open) {
    if (!drawer || !drawerBackdrop) return;
    drawer.classList.toggle('open', open);
    drawerBackdrop.classList.toggle('open', open);
    document.body.style.overflow = open ? 'hidden' : '';
  }

  if (openDrawerBtn) openDrawerBtn.addEventListener('click', () => toggleDrawer(true));
  if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', () => toggleDrawer(false));
  if (drawerBackdrop) drawerBackdrop.addEventListener('click', () => toggleDrawer(false));

  // 3. Predictive Search Modal
  const searchModal = document.getElementById('search-modal');
  const searchInput = document.getElementById('predictive-search-input');
  const searchResults = document.getElementById('predictive-search-results');
  const openSearchBtns = document.querySelectorAll('[data-open-search]');
  const closeSearchBtns = document.querySelectorAll('[data-close-search]');
  let searchTimer = null;

  function toggleSearch(open) {
    if (!searchModal) return;
    searchModal.classList.toggle('open', open);
    if (open && searchInput) {
      setTimeout(() => searchInput.focus(), 60);
    }
  }

  openSearchBtns.forEach((b) => b.addEventListener('click', () => toggleSearch(true)));
  closeSearchBtns.forEach((b) => b.addEventListener('click', () => toggleSearch(false)));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      toggleSearch(false);
      toggleDrawer(false);
      hideCartNotification();
    }
  });

  if (searchInput && searchResults) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimer);
      const q = searchInput.value.trim();
      if (q.length < 1) {
        searchResults.classList.remove('has-results');
        searchResults.innerHTML = '';
        return;
      }
      searchTimer = setTimeout(async () => {
        try {
          const resp = await fetch(`${basePath}/api/search/suggest?q=${encodeURIComponent(q)}`, {
            headers: { Accept: 'application/json' },
          });
          const data = await resp.json();
          if (!data.ok) return;
          if (!data.products || data.products.length === 0) {
            searchResults.innerHTML = `<div class="predictive-results__heading">No results found for "${escapeHtml(q)}"</div>`;
            searchResults.classList.add('has-results');
            return;
          }
          const rows = data.products
            .map(
              (p) => `
              <a class="predictive-results__item" href="${p.url}">
                ${p.image ? `<img class="predictive-results__thumb" src="${p.image}" alt="${escapeHtml(p.title)}">` : ''}
                <div>
                  <div style="font-size:1.45rem;color:#000;line-height:1.3">${escapeHtml(p.title)}</div>
                  <div style="font-size:1.35rem;color:rgba(0,0,0,0.65);margin-top:0.2rem">${escapeHtml(p.price)} ${!p.available ? '· Sold out' : ''}</div>
                </div>
              </a>`
            )
            .join('');
          searchResults.innerHTML = `
            <div class="predictive-results__heading">Products</div>
            ${rows}
            <a class="predictive-results__footer" href="${basePath}/search?q=${encodeURIComponent(q)}">Search for "${escapeHtml(q)}" →</a>
          `;
          searchResults.classList.add('has-results');
        } catch (err) {
          console.error(err);
        }
      }, 180);
    });
  }

  // 4. Deferred Video Player ("How to Buy from Okina Craft")
  const videoBtn = document.getElementById('deferred-video-btn');
  if (videoBtn) {
    videoBtn.addEventListener('click', () => {
      const container = videoBtn.closest('.video-section__media');
      const src = videoBtn.getAttribute('data-video-src');
      const poster = videoBtn.getAttribute('data-video-poster');
      if (!container || !src) return;
      container.innerHTML = `
        <video class="video-section__player" controls autoplay playsinline poster="${poster || ''}">
          <source src="${src}" type="video/mp4">
          Your browser does not support video playback.
        </video>
      `;
    });
  }

  // 5. Cart Notification Popup & Bubble Sync
  const cartNotif = document.getElementById('cart-notification');
  const cartNotifProd = document.getElementById('cart-notification-product');
  const cartNotifClose = document.querySelectorAll('[data-close-cart-notif]');

  function hideCartNotification() {
    if (cartNotif) cartNotif.classList.remove('open');
  }
  cartNotifClose.forEach((b) => b.addEventListener('click', hideCartNotification));

  function updateCartBubble(count) {
    const bubble = document.getElementById('cart-count-bubble');
    if (!bubble) return;
    bubble.textContent = String(count);
    bubble.classList.toggle('hidden', count <= 0);
  }

  function showCartNotification(item, totalCount) {
    if (!cartNotif || !cartNotifProd || !item) return;
    cartNotifProd.innerHTML = `
      ${item.image_url ? `<img src="${item.image_url}" alt="${escapeHtml(item.title)}">` : ''}
      <div>
        <div style="font-size:1.45rem;color:#000;line-height:1.35">${escapeHtml(item.title)}</div>
        ${item.variant_title ? `<div style="font-size:1.3rem;color:rgba(0,0,0,0.65);margin-top:0.2rem">${escapeHtml(item.variant_title)}</div>` : ''}
        <div style="font-size:1.35rem;color:#000;margin-top:0.3rem">${escapeHtml(item.unit_price_formatted)} × ${item.quantity}</div>
      </div>
    `;
    const viewBtn = document.getElementById('cart-notification-view-btn');
    if (viewBtn) viewBtn.textContent = `View cart (${totalCount})`;
    cartNotif.classList.add('open');
  }

  // 6. Product Detail Page — Gallery, Variant Pills, Qty Stepper, Custom Upload Preview, AJAX Add to Cart
  const mainImg = document.getElementById('pdp-main-image');
  document.querySelectorAll('.product-gallery__thumb').forEach((btn) => {
    btn.addEventListener('click', () => {
      const src = btn.getAttribute('data-src');
      if (mainImg && src) mainImg.src = src;
      document.querySelectorAll('.product-gallery__thumb').forEach((t) => t.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  // Quantity stepper
  document.querySelectorAll('.qty-stepper, .pdp-qty-stepper').forEach((stepper) => {
    const input = stepper.querySelector('input');
    const dec = stepper.querySelector('[data-qty-dec]');
    const inc = stepper.querySelector('[data-qty-inc]');
    if (!input) return;
    if (dec) {
      dec.addEventListener('click', () => {
        const val = Math.max(1, parseInt(input.value || '1', 10) - 1);
        input.value = String(val);
        input.dispatchEvent(new Event('change'));
      });
    }
    if (inc) {
      inc.addEventListener('click', () => {
        const val = Math.min(10000, parseInt(input.value || '1', 10) + 1);
        input.value = String(val);
        input.dispatchEvent(new Event('change'));
      });
    }
  });

  // Variant Selector (Color Swatches + Size Selection)
  let variants = [];
  let selected = {};
  let syncVariant = () => {};
  let syncVariantOption = (pos, val) => {
    selected[pos] = val;
    syncVariant();
  };

  const variantsJsonEl = document.getElementById('pdp-variants-json');
  if (variantsJsonEl) {
    try {
      variants = JSON.parse(variantsJsonEl.textContent || '[]');
    } catch (e) {
      variants = [];
    }
    const variantIdInput = document.getElementById('pdp-variant-id');
    const priceDisplay = document.getElementById('pdp-price-display');
    const addBtn = document.getElementById('pdp-add-btn');
    const buyBtn = document.getElementById('pdp-buy-btn');
    const mainImg = document.getElementById('pdp-main-image');
    const galleryThumbs = document.querySelectorAll('.product-gallery__thumb');
    document.querySelectorAll('.variant-group').forEach((group) => {
      const pos = group.getAttribute('data-option-pos');
      const activeEl = group.querySelector('.variant-pill.active, .color-swatch.active');
      if (pos && activeEl) {
        selected[pos] = activeEl.getAttribute('data-value');
      }

      // Handle standard pills (e.g. Size, Material)
      group.querySelectorAll('.variant-pill').forEach((pill) => {
        pill.addEventListener('click', () => {
          group.querySelectorAll('.variant-pill').forEach((p) => p.classList.remove('active'));
          pill.classList.add('active');
          selected[pos] = pill.getAttribute('data-value');
          syncVariant();
        });
      });

      // Handle color swatches (e.g. Color)
      group.querySelectorAll('.color-swatch').forEach((swatch) => {
        swatch.addEventListener('click', () => {
          group.querySelectorAll('.color-swatch').forEach((s) => s.classList.remove('active'));
          swatch.classList.add('active');
          const val = swatch.getAttribute('data-value');
          selected[pos] = val;

          // Update header label text (e.g. Color: Maroon)
          const labelVal = document.getElementById('selected-opt-' + pos);
          if (labelVal) {
            labelVal.textContent = val;
          }

          // Switch gallery image and filter thumbnails for this color
          const directImg = swatch.getAttribute('data-image');
          switchGalleryColor(val, directImg);

          syncVariant();
        });
      });
    });

    function switchGalleryColor(colorName, directImg) {
      if (!colorName) return;
      const lower = colorName.toLowerCase();
      let firstVisibleThumb = null;

      if (galleryThumbs && galleryThumbs.length > 0) {
        let hasColorThumbs = false;
        galleryThumbs.forEach((thumb) => {
          const tColor = (thumb.getAttribute('data-color') || '').toLowerCase();
          if (tColor) {
            hasColorThumbs = true;
            if (tColor === lower) {
              thumb.style.display = '';
              if (!firstVisibleThumb) firstVisibleThumb = thumb;
            } else {
              thumb.style.display = 'none';
              thumb.classList.remove('active');
            }
          }
        });

        if (hasColorThumbs && firstVisibleThumb) {
          galleryThumbs.forEach((t) => t.classList.remove('active'));
          firstVisibleThumb.classList.add('active');
          const src = firstVisibleThumb.getAttribute('data-src');
          if (mainImg && src) {
            mainImg.src = src;
          }
          return;
        }
      }

      // Fallback: direct image on swatch
      if (mainImg && directImg) {
        mainImg.src = directImg;
      }
    }

    syncVariant = function() {
      const match = variants.find((v) => {
        if (selected['1'] && v.option1 !== selected['1']) return false;
        if (selected['2'] && v.option2 !== selected['2']) return false;
        if (selected['3'] && v.option3 !== selected['3']) return false;
        return true;
      });
      if (!match) return;
      if (variantIdInput) variantIdInput.value = String(match.id);

      // If bulk pricing is active on this product, keep bulk pricing in sync
      if (typeof recalculateBulkPricing === 'function' && bulkTiers && bulkTiers.length) {
        recalculateBulkPricing();
      } else if (priceDisplay) {
        priceDisplay.textContent = 'Rs. ' + Number(match.price).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' INR';
      }

      const isAvail = Boolean(Number(match.available));
      if (addBtn) {
        addBtn.disabled = !isAvail;
        addBtn.textContent = isAvail ? 'Add to cart' : 'Sold out';
      }
      const stickyAddBtn = document.getElementById('sticky-pdp-add-btn');
      if (stickyAddBtn) {
        stickyAddBtn.disabled = !isAvail;
        const sSpan = stickyAddBtn.querySelector('span');
        if (sSpan) sSpan.textContent = isAvail ? 'Add to cart' : 'Sold out';
      }
      const stickyBarImg = document.getElementById('sticky-bar-image');
      if (stickyBarImg && mainImg) {
        stickyBarImg.src = mainImg.src;
      }
      if (!bulkTiers || !bulkTiers.length) {
        const stickyBarPrice = document.getElementById('sticky-bar-price');
        if (stickyBarPrice) {
          stickyBarPrice.textContent = 'Rs. ' + Number(match.price).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
      }
      if (buyBtn) {
        buyBtn.disabled = !isAvail;
        buyBtn.style.display = isAvail ? '' : 'none';
      }
    }
  }

  // Custom Design Image Upload Preview & Accordion Integration
  const customFile = document.getElementById('custom-design-file');
  const customPreview = document.getElementById('custom-design-preview');
  const customPreviewImg = document.getElementById('custom-design-preview-img');
  const customPreviewName = document.getElementById('custom-design-preview-name');
  const customAccordion = document.getElementById('customizer-accordion');
  const customHint = document.getElementById('customizer-status-hint');

  if (customFile && customPreview) {
    customFile.addEventListener('change', () => {
      const f = customFile.files && customFile.files[0];
      if (!f) {
        customPreview.classList.remove('visible');
        if (customHint) {
          customHint.textContent = 'Click to add photo, artwork, or print instructions';
          customHint.style.color = '';
          customHint.style.fontWeight = '';
        }
        return;
      }
      if (customPreviewName) customPreviewName.textContent = `${f.name} (${(f.size / 1024).toFixed(0)} KB)`;
      if (customHint) {
        customHint.textContent = `✓ Attached: ${f.name} (${(f.size / 1024).toFixed(0)} KB)`;
        customHint.style.color = '#1b7f3b';
        customHint.style.fontWeight = '600';
      }
      if (customAccordion && !customAccordion.open) {
        customAccordion.open = true;
      }
      if (customPreviewImg) {
        const reader = new FileReader();
        reader.onload = (ev) => {
          customPreviewImg.src = ev.target.result;
          customPreview.classList.add('visible');
        };
        reader.readAsDataURL(f);
      }
    });
  }

  // AJAX Add to Cart on PDP (keeps Buy It Now as normal form submit to /checkout!)
  const pdpForm = document.getElementById('pdp-cart-form');
  if (pdpForm) {
    let clickedBuyNow = false;
    const buyBtn = document.getElementById('pdp-buy-btn');
    if (buyBtn) buyBtn.addEventListener('click', () => { clickedBuyNow = true; });

    pdpForm.addEventListener('submit', async (e) => {
      if (clickedBuyNow) {
        clickedBuyNow = false;
        return; // Let normal POST redirect straight to /checkout
      }
      e.preventDefault();
      const addBtn = document.getElementById('pdp-add-btn');
      const stickyAddBtn = document.getElementById('sticky-pdp-add-btn');
      const errBox = document.getElementById('pdp-form-error');
      if (errBox) { errBox.style.display = 'none'; errBox.textContent = ''; }
      if (addBtn) { addBtn.disabled = true; addBtn.textContent = 'Adding...'; }
      if (stickyAddBtn) { stickyAddBtn.disabled = true; const sSpan = stickyAddBtn.querySelector('span'); if (sSpan) sSpan.textContent = 'Adding...'; }

      try {
        const fd = new FormData(pdpForm);
        const resp = await fetch(`${basePath}/api/cart/add`, {
          method: 'POST',
          body: fd,
          headers: { Accept: 'application/json' },
        });
        const data = await resp.json();
        if (!data.ok) {
          if (errBox) {
            errBox.textContent = data.error || 'Could not add item to cart.';
            errBox.style.display = 'block';
          }
        } else {
          updateCartBubble(data.cart.count);
          showCartNotification(data.item, data.cart.count);
        }
      } catch (err) {
        pdpForm.submit();
      } finally {
        if (addBtn) { addBtn.disabled = false; addBtn.textContent = 'Add to cart'; }
        const stickyAddBtn = document.getElementById('sticky-pdp-add-btn');
        if (stickyAddBtn) { stickyAddBtn.disabled = false; const sSpan = stickyAddBtn.querySelector('span'); if (sSpan) sSpan.textContent = 'Add to cart'; }
      }
    });
  }

  // Tiered Bulk Pricing Calculator (Updates Main Pricing Directly - Vistaprint Style)
  const bulkTiersEl = document.getElementById('pdp-bulk-tiers-json');
  let bulkTiers = [];
  if (bulkTiersEl) {
    try {
      bulkTiers = JSON.parse(bulkTiersEl.textContent || '[]');
    } catch (e) {}
  }

  const pdpQty = document.getElementById('pdp-qty');
  const pdpQtyPill = document.getElementById('pdp-qty-pill');
  const pdpQtyUnitPrice = document.getElementById('pdp-qty-unit-price');
  const pdpQtyTotalPrice = document.getElementById('pdp-qty-total-price');
  const pdpQtyUpsellBanner = document.getElementById('pdp-qty-upsell-banner');
  const pdpQtyUpsellText = document.getElementById('pdp-qty-upsell-text');
  const pdpQtyUpsellBtn = document.getElementById('pdp-qty-upsell-btn');
  const tierChips = document.querySelectorAll('.pdp-tier-chip');
  const pdpPriceDisplay = document.getElementById('pdp-price-display');
  const bulkUnitBreakdown = document.getElementById('bulk-unit-breakdown');
  const bulkDiscountBadge = document.getElementById('bulk-discount-badge');
  const bulkDefaultNote = document.getElementById('bulk-default-note');
  const intervalChips = document.querySelectorAll('.interval-chip');

  function formatInr(num) {
    return '₹' + Number(num).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function getBasePrice() {
    const variantIdInput = document.getElementById('pdp-variant-id');
    const curVid = variantIdInput ? parseInt(variantIdInput.value, 10) : null;
    if (curVid && typeof variants !== 'undefined' && variants && variants.length) {
      const found = variants.find(v => parseInt(v.id, 10) === curVid);
      if (found && found.price) return parseFloat(found.price);
    }
    return 549.00;
  }

  function recalculateBulkPricing() {
    if (!pdpQty) return;
    const qty = Math.max(1, parseInt(pdpQty.value || '1', 10));
    const basePrice = getBasePrice();

    let matchedTier = null;
    let minPrice = basePrice;
    let maxPrice = basePrice;

    if (bulkTiers && bulkTiers.length) {
      const allPrices = bulkTiers.map(t => parseFloat(t.unit_price)).filter(p => !isNaN(p) && p > 0);
      minPrice = allPrices.length ? Math.min(...allPrices) : basePrice;
      maxPrice = allPrices.length ? Math.max(...allPrices) : basePrice;

      for (const t of bulkTiers) {
        const minQ = parseInt(t.min_qty, 10) || 1;
        const maxQ = t.max_qty !== null && t.max_qty !== undefined && String(t.max_qty).trim() !== '' ? parseInt(t.max_qty, 10) : Infinity;
        if (qty >= minQ && qty <= maxQ) {
          matchedTier = t;
          break;
        }
      }
    }

    const unitPrice = matchedTier ? parseFloat(matchedTier.unit_price) : basePrice;
    const discountPercent = matchedTier ? parseFloat(matchedTier.discount_percent || 0) : 0;
    const totalAmount = unitPrice * qty;
    const totalSavings = Math.max(0, (basePrice - unitPrice) * qty);

    // Update Quantity Pill
    if (pdpQtyPill) {
      pdpQtyPill.textContent = qty === 1 ? '1 pc' : `${qty} pcs`;
    }

    // Update Live Stepper Pricing (PrintMine style)
    if (pdpQtyUnitPrice) {
      pdpQtyUnitPrice.textContent = formatInr(unitPrice);
    }
    if (pdpQtyTotalPrice) {
      pdpQtyTotalPrice.textContent = `${formatInr(totalAmount)} total`;
    }

    // Keep Sticky PDP Bar in sync
    const stickyBarPrice = document.getElementById('sticky-bar-price');
    if (stickyBarPrice) {
      stickyBarPrice.textContent = formatInr(unitPrice);
    }
    const stickyQtyInput = document.getElementById('sticky-qty-input');
    if (stickyQtyInput && parseInt(stickyQtyInput.value, 10) !== qty) {
      stickyQtyInput.value = qty;
    }

    // Update Main Pricing in Place
    if (pdpPriceDisplay) {
      if (qty === 1 && bulkTiers && bulkTiers.length) {
        pdpPriceDisplay.innerHTML = `${formatInr(minPrice)} &ndash; ${formatInr(maxPrice)} <span class="bulk-price-each">each</span>`;
        if (bulkDefaultNote) bulkDefaultNote.style.display = 'inline-block';
        if (bulkUnitBreakdown) bulkUnitBreakdown.style.display = 'none';
        if (bulkDiscountBadge) bulkDiscountBadge.style.display = 'none';
      } else {
        pdpPriceDisplay.innerHTML = `${formatInr(totalAmount)}`;
        if (bulkDefaultNote) bulkDefaultNote.style.display = 'none';
        if (bulkUnitBreakdown) {
          bulkUnitBreakdown.style.display = 'inline-block';
          bulkUnitBreakdown.textContent = `${formatInr(unitPrice)} each / ${qty} units`;
        }
        if (bulkDiscountBadge) {
          if (discountPercent > 0) {
            bulkDiscountBadge.style.display = 'inline-flex';
            bulkDiscountBadge.textContent = `${discountPercent}% Volume Discount (Save ${formatInr(totalSavings)})`;
          } else {
            bulkDiscountBadge.style.display = 'none';
          }
        }
      }
    }

    // Update Tier Grid Active State
    tierChips.forEach(chip => {
      const minQ = parseInt(chip.getAttribute('data-min'), 10) || 1;
      const maxQ = parseInt(chip.getAttribute('data-max'), 10) || Infinity;
      if (qty >= minQ && qty <= maxQ) {
        chip.classList.add('active');
      } else {
        chip.classList.remove('active');
      }
    });

    // Update Dynamic Upsell Banner
    if (pdpQtyUpsellBanner && bulkTiers && bulkTiers.length) {
      const nextTier = bulkTiers.find(t => parseInt(t.min_qty, 10) > qty);
      if (nextTier) {
        const nextMin = parseInt(nextTier.min_qty, 10);
        const nextPrice = parseFloat(nextTier.unit_price);
        const diff = nextMin - qty;
        const savingPerPiece = Math.max(0, unitPrice - nextPrice);

        pdpQtyUpsellBanner.style.display = 'flex';
        pdpQtyUpsellText.innerHTML = `Add <strong>${diff} more piece${diff > 1 ? 's' : ''}</strong> &rarr; pay <strong>₹${Number(nextPrice).toFixed(0)}/pc</strong> and save <strong>₹${Number(savingPerPiece).toFixed(2)}</strong> on every piece.`;
        if (pdpQtyUpsellBtn) {
          pdpQtyUpsellBtn.style.display = 'inline-block';
          pdpQtyUpsellBtn.textContent = `Make it ${nextMin}`;
          pdpQtyUpsellBtn.setAttribute('data-target-qty', nextMin);
        }
      } else {
        pdpQtyUpsellBanner.style.display = 'flex';
        pdpQtyUpsellText.innerHTML = `🎉 <strong>Maximum wholesale tier unlocked!</strong> You are getting our best rate of <strong>₹${Number(unitPrice).toFixed(0)}/pc</strong>.`;
        if (pdpQtyUpsellBtn) {
          pdpQtyUpsellBtn.style.display = 'none';
        }
      }
    }

    // Update interval chip active state if exact match
    intervalChips.forEach(chip => {
      const chipQty = parseInt(chip.getAttribute('data-qty'), 10);
      if (chipQty === qty || (chipQty === 500 && qty >= 500)) {
        chip.classList.add('active');
      } else {
        chip.classList.remove('active');
      }
    });
  }

  if (pdpQty) {
    pdpQty.addEventListener('input', recalculateBulkPricing);
    pdpQty.addEventListener('change', recalculateBulkPricing);
  }

  tierChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const targetQty = parseInt(chip.getAttribute('data-min'), 10);
      if (pdpQty && targetQty) {
        pdpQty.value = targetQty;
        pdpQty.dispatchEvent(new Event('change'));
      }
    });
  });

  if (pdpQtyUpsellBtn) {
    pdpQtyUpsellBtn.addEventListener('click', () => {
      const targetQty = parseInt(pdpQtyUpsellBtn.getAttribute('data-target-qty'), 10);
      if (pdpQty && targetQty) {
        pdpQty.value = targetQty;
        pdpQty.dispatchEvent(new Event('change'));
      }
    });
  }

  intervalChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const targetQty = parseInt(chip.getAttribute('data-qty'), 10);
      if (pdpQty && targetQty) {
        pdpQty.value = targetQty;
        pdpQty.dispatchEvent(new Event('change'));
      }
    });
  });

  if (pdpQty) {
    recalculateBulkPricing();
  }

  // Mixed Size Breakdown Manager (PrintMine Style)
  // Flow: Select Quantity First, then Select Sizes Accordingly
  function initMixedSizeSelector() {
    const sizeCard = document.getElementById('mixed-size-card');
    if (!sizeCard) return;

    const sizeItems = sizeCard.querySelectorAll('.mixed-size-item');
    const badge = document.getElementById('size-selected-badge');
    const progressFill = document.getElementById('size-progress-fill');
    const statusText = document.getElementById('size-status-text');
    const breakdownInput = document.getElementById('pdp-size-breakdown');
    const pdpQty = document.getElementById('pdp-qty');
    const formError = document.getElementById('pdp-form-error');

    const sizeCounts = {};
    sizeItems.forEach(item => {
      const sizeName = item.getAttribute('data-size');
      const countEl = item.querySelector('.size-count-num');
      const count = parseInt(countEl ? countEl.textContent.trim() : '0', 10) || 0;
      sizeCounts[sizeName] = count;
    });

    function getTotalSelectedSizes() {
      return Object.values(sizeCounts).reduce((sum, val) => sum + val, 0);
    }

    // Trims sizes when user decreases Quantity in the quantity card
    function trimSizesToTarget(newTarget) {
      let total = getTotalSelectedSizes();
      if (total <= newTarget) return;

      let excess = total - newTarget;
      // Loop sizes from end to beginning
      const keys = Object.keys(sizeCounts).reverse();
      for (const key of keys) {
        if (excess <= 0) break;
        const cur = sizeCounts[key] || 0;
        if (cur > 0) {
          const reduceBy = Math.min(cur, excess);
          sizeCounts[key] -= reduceBy;
          excess -= reduceBy;

          const itemEl = sizeCard.querySelector(`.mixed-size-item[data-size="${key}"]`);
          const countEl = itemEl ? itemEl.querySelector('.size-count-num') : null;
          if (countEl) countEl.textContent = String(sizeCounts[key]);
          if (itemEl) {
            if (sizeCounts[key] > 0) itemEl.classList.add('has-count');
            else itemEl.classList.remove('has-count');
          }
        }
      }
    }

    function updateSizeUI() {
      const totalSelected = getTotalSelectedSizes();
      const targetQty = pdpQty ? Math.max(1, parseInt(pdpQty.value || '1', 10)) : 1;

      // Update badge: "X/Y Selected"
      if (badge) {
        badge.textContent = `${totalSelected}/${targetQty} Selected`;
        if (totalSelected === targetQty && totalSelected > 0) {
          badge.classList.add('all-selected');
        } else {
          badge.classList.remove('all-selected');
        }
      }

      // Update progress bar
      if (progressFill) {
        const pct = Math.min(100, Math.round((totalSelected / targetQty) * 100));
        progressFill.style.width = pct + '%';
        if (totalSelected === targetQty && totalSelected > 0) {
          progressFill.classList.add('is-complete');
        } else {
          progressFill.classList.remove('is-complete');
        }
      }

      // Update status text
      if (statusText) {
        if (totalSelected === targetQty) {
          statusText.innerHTML = `✓ <strong>All ${targetQty} sizes selected!</strong>`;
          statusText.classList.add('is-complete');
        } else if (totalSelected < targetQty) {
          const diff = targetQty - totalSelected;
          statusText.innerHTML = `Choose sizes for <strong>${diff} more</strong>`;
          statusText.classList.remove('is-complete');
        } else {
          statusText.innerHTML = `✓ <strong>All ${totalSelected} sizes selected!</strong>`;
          statusText.classList.add('is-complete');
        }
      }

      // Format size breakdown string (e.g. "6 × S, 7 × M, 4 × L, 8 × XL")
      const breakdownParts = [];
      let primarySize = null;
      for (const [sizeName, count] of Object.entries(sizeCounts)) {
        if (count > 0) {
          if (!primarySize) primarySize = sizeName;
          breakdownParts.push(`${count} × ${sizeName}`);
        }
      }

      const breakdownStr = breakdownParts.join(', ');
      if (breakdownInput) {
        breakdownInput.value = breakdownStr;
      }

      // Sync variant option for primary size if applicable
      const sizePos = sizeCard.closest('.variant-group--size') ? sizeCard.closest('.variant-group--size').getAttribute('data-option-pos') : null;
      if (sizePos && primarySize && typeof syncVariantOption === 'function') {
        syncVariantOption(sizePos, primarySize);
      }

      // Clear error state if filled
      if (totalSelected === targetQty) {
        sizeCard.classList.remove('has-error');
        if (formError && formError.textContent.includes('sizes')) {
          formError.style.display = 'none';
        }
      }
    }

    // Listen to changes in Quantity input from Quantity Card
    function handleQtyChange() {
      const targetQty = pdpQty ? Math.max(1, parseInt(pdpQty.value || '1', 10)) : 1;
      const totalSelected = getTotalSelectedSizes();

      // If user lowered quantity below current size selection, trim sizes down!
      if (targetQty < totalSelected) {
        trimSizesToTarget(targetQty);
      }

      updateSizeUI();
    }

    if (pdpQty) {
      pdpQty.addEventListener('input', handleQtyChange);
      pdpQty.addEventListener('change', handleQtyChange);
    }

    // Attach click listeners to + and - buttons for each size
    sizeItems.forEach(item => {
      const sizeName = item.getAttribute('data-size');
      const countEl = item.querySelector('.size-count-num');
      const decBtn = item.querySelector('[data-size-dec]');
      const incBtn = item.querySelector('[data-size-inc]');

      if (decBtn) {
        decBtn.addEventListener('click', (e) => {
          e.preventDefault();
          if (sizeCounts[sizeName] > 0) {
            sizeCounts[sizeName]--;
            if (countEl) countEl.textContent = String(sizeCounts[sizeName]);
            if (sizeCounts[sizeName] === 0) {
              item.classList.remove('has-count');
            }
            updateSizeUI();
          }
        });
      }

      if (incBtn) {
        incBtn.addEventListener('click', (e) => {
          e.preventDefault();
          sizeCounts[sizeName] = (sizeCounts[sizeName] || 0) + 1;
          if (countEl) countEl.textContent = String(sizeCounts[sizeName]);
          item.classList.add('has-count');

          // If adding this size makes total exceed target qty, automatically increment target qty and recalculate bulk pricing!
          const currentTotal = getTotalSelectedSizes();
          const targetQty = pdpQty ? parseInt(pdpQty.value || '1', 10) : 1;
          if (currentTotal > targetQty && pdpQty) {
            pdpQty.value = currentTotal;
            if (typeof recalculateBulkPricing === 'function') {
              recalculateBulkPricing();
            }
          }

          updateSizeUI();
        });
      }
    });

    // Form submit validation & size breakdown serialization
    const cartForm = document.getElementById('pdp-cart-form');
    if (cartForm) {
      cartForm.addEventListener('submit', (e) => {
        const totalSelected = getTotalSelectedSizes();
        const targetQty = pdpQty ? parseInt(pdpQty.value || '1', 10) : 1;

        if (totalSelected < targetQty) {
          e.preventDefault();
          const diff = targetQty - totalSelected;
          if (formError) {
            formError.textContent = `Please select sizes for the remaining ${diff} piece${diff > 1 ? 's' : ''} (${totalSelected}/${targetQty} selected).`;
            formError.style.display = 'block';
          }
          sizeCard.classList.add('has-error');
          sizeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return false;
        }

        const breakdownStr = breakdownInput ? breakdownInput.value : '';
        const customNoteInput = document.getElementById('custom-note');
        if (breakdownStr && customNoteInput) {
          const currentNote = customNoteInput.value.trim();
          if (currentNote) {
            if (!currentNote.includes('Sizes:')) {
              customNoteInput.value = `Sizes: ${breakdownStr} | Note: ${currentNote}`;
            }
          } else {
            customNoteInput.value = `Sizes: ${breakdownStr}`;
          }
        }
      });
    }

    updateSizeUI();
  }

  initMixedSizeSelector();

  // Sticky Floating Add to Cart Bar
  function initStickyAddToCart() {
    const stickyBar = document.getElementById('sticky-pdp-bar');
    if (!stickyBar) return;

    const pdpQty = document.getElementById('pdp-qty');
    const stickyQtyInput = document.getElementById('sticky-qty-input');
    const stickyQtyDec = document.getElementById('sticky-qty-dec');
    const stickyQtyInc = document.getElementById('sticky-qty-inc');
    const stickyAddBtn = document.getElementById('sticky-pdp-add-btn');
    const mainAddBtn = document.getElementById('pdp-add-btn');

    function syncStickyToMain(val) {
      if (pdpQty) {
        pdpQty.value = val;
        pdpQty.dispatchEvent(new Event('input', { bubbles: true }));
        pdpQty.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }

    if (stickyQtyDec && stickyQtyInput) {
      stickyQtyDec.addEventListener('click', () => {
        const cur = Math.max(1, parseInt(stickyQtyInput.value || '1', 10) - 1);
        stickyQtyInput.value = cur;
        syncStickyToMain(cur);
      });
    }

    if (stickyQtyInc && stickyQtyInput) {
      stickyQtyInc.addEventListener('click', () => {
        const cur = parseInt(stickyQtyInput.value || '1', 10) + 1;
        stickyQtyInput.value = cur;
        syncStickyToMain(cur);
      });
    }

    if (stickyQtyInput) {
      stickyQtyInput.addEventListener('input', () => {
        const cur = Math.max(1, parseInt(stickyQtyInput.value || '1', 10));
        syncStickyToMain(cur);
      });
    }

    if (stickyAddBtn && mainAddBtn) {
      stickyAddBtn.addEventListener('click', (e) => {
        e.preventDefault();
        mainAddBtn.click();
      });
    }

    // Scroll listener: appear after first scroll (~180px)
    let isVisible = false;
    let ticking = false;

    function handleScroll() {
      const scrollY = window.pageYOffset || document.documentElement.scrollTop;
      const shouldShow = scrollY > 180;

      if (shouldShow !== isVisible) {
        isVisible = shouldShow;
        if (isVisible) {
          stickyBar.classList.add('visible');
          stickyBar.setAttribute('aria-hidden', 'false');
        } else {
          stickyBar.classList.remove('visible');
          stickyBar.setAttribute('aria-hidden', 'true');
        }
      }
      ticking = false;
    }

    window.addEventListener('scroll', () => {
      if (!ticking) {
        window.requestAnimationFrame(handleScroll);
        ticking = true;
      }
    }, { passive: true });

    handleScroll();
  }

  initStickyAddToCart();

  // Size Chart Modal Handlers
  window.openSizeChartModal = function() {
    const m = document.getElementById('size-chart-modal');
    if (m) {
      m.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeSizeChartModal = function() {
    const m = document.getElementById('size-chart-modal');
    if (m) {
      m.classList.remove('active');
      document.body.style.overflow = '';
    }
  };

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      window.closeSizeChartModal();
    }
  });

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
})();
