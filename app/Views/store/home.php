<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="store-hero-purpose page-width" style="padding: 2.2rem 1.5rem 1rem; text-align: center;">
  <h1 style="font-size: 2.1rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.6rem; color: #111;">
    Okina Craft <span style="font-weight: 400; color: #555; font-size: 1.35rem;">– Custom Apparel & Personalized Gifts</span>
  </h1>
  <p style="font-size: 1.05rem; color: #666; max-width: 680px; margin: 0 auto 1.2rem; line-height: 1.6;">
    Discover handcrafted premium apparel, oversized t-shirts, custom hoodies, and personalized merchandise. Powered by professional DTF printing on 100% French Terry Cotton.
  </p>
</section>

<?php foreach ($collections as $col): ?>
  <?php if (empty($col['products'])) continue; ?>
  <?php
    $colsClass = ((int)$col['desktop_cols'] === 2) ? 'product-grid--2' : (((int)$col['desktop_cols'] === 3) ? 'product-grid--3' : 'product-grid--4');
    $cardShape = $col['card_shape'] ?? 'standard';
  ?>
  <section class="collection-section">
    <div class="page-width">
      <div class="collection__title">
        <h2 class="title h1"><?= e($col['title']) ?></h2>
      </div>
      <ul class="product-grid <?= e($colsClass) ?>" role="list">
        <?php foreach ($col['products'] as $product): ?>
          <?= view('store/partials/product_card', ['product' => $product, 'cardShape' => $cardShape]) ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endforeach; ?>

<?= $this->endSection() ?>
