<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$videoHeading = store_setting('video_heading', 'How to Buy from Okina Craft');
?>

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

<!-- Exact "How to Buy from Okina Craft" Deferred Video Section -->
<section class="video-section">
  <div class="page-width">
    <div class="collection__title">
      <h2 class="title h1"><?= e($videoHeading) ?></h2>
    </div>
    <div class="video-section__media">
      <button
        type="button"
        id="deferred-video-btn"
        class="video-section__poster"
        aria-label="Play video: <?= e($videoHeading) ?>"
        data-video-src="<?= base_url('assets/media/how-to-buy.mp4') ?>"
        data-video-poster="<?= base_url('assets/media/video-poster.jpg') ?>"
      >
        <img src="<?= base_url('assets/media/video-poster.jpg') ?>" alt="<?= e($videoHeading) ?>" loading="lazy">
        <span class="deferred-media__poster-button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14"><path fill="currentColor" fill-rule="evenodd" d="M1.482.815A1 1 0 0 0 0 1.69v10.517a1 1 0 0 0 1.525.851L10.54 7.5a1 1 0 0 0-.043-1.728z" clip-rule="evenodd"/></svg>
        </span>
      </button>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
