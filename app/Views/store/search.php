<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="collection-hero">
  <div class="page-width">
    <div style="max-width: 60rem; margin: 0 auto 3.6rem; text-align: center;">
      <h1 class="h1" style="margin-bottom: 2rem;">Search results</h1>
      <form action="<?= base_url('/search') ?>" method="get" style="display:flex; gap:1rem;">
        <input type="search" name="q" value="<?= e($query) ?>" class="field__input" placeholder="Search products...">
        <button type="submit" class="button">Search</button>
      </form>
    </div>

    <?php if ($query !== ''): ?>
      <p style="margin-bottom: 2.4rem; color: var(--color-muted);">
        <?= count($products) ?> <?= count($products) === 1 ? 'result' : 'results' ?> found for "<strong><?= e($query) ?></strong>"
      </p>

      <?php if (!empty($products)): ?>
        <ul class="product-grid product-grid--4" role="list" style="margin-bottom: 4rem;">
          <?php $cardShape = 'standard'; ?>
          <?php foreach ($products as $product): ?>
            <?= view('store/partials/product_card', ['product' => $product, 'cardShape' => $cardShape]) ?>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div style="text-align:center; padding: 4rem 0;">
          <p style="margin-bottom: 1.8rem;">Check the spelling or try a different keyword.</p>
          <a href="<?= base_url('/collections/all') ?>" class="button">Browse Products</a>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?= $this->endSection() ?>
