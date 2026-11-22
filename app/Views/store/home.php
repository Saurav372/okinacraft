<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

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
