<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$cardShape = $collection['card_shape'] ?? 'standard';
$currentHandle = $collection['handle'] ?? 'all';
?>
<section class="collection-hero">
  <div class="page-width">
    <h1 class="h1" style="margin-bottom: 1.4rem;"><?= e($collection['title']) ?></h1>
    <?php if (!empty($collection['description'])): ?>
      <p style="max-width: 68rem; margin: 0 0 2rem; color: var(--color-muted);"><?= e($collection['description']) ?></p>
    <?php endif; ?>

    <!-- Quick Collection Pills -->
    <div class="collection-pills">
      <a href="<?= base_url('/collections/all') ?>" class="collection-pill <?= $currentHandle === 'all' ? 'active' : '' ?>">All Products</a>
      <?php foreach ($allCollections as $c): ?>
        <?php if ((int)$c['product_count'] <= 0) continue; ?>
        <a href="<?= base_url('/collections/' . $c['handle']) ?>" class="collection-pill <?= $currentHandle === $c['handle'] ? 'active' : '' ?>">
          <?= e($c['title']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Dawn Filter & Sort Facets Bar -->
    <form method="get" class="facets-bar">
      <div class="facets-group">
        <span style="font-size:1.4rem;color:var(--color-muted);">Filter:</span>
        <select name="availability" class="facets-select" onchange="this.form.submit()" aria-label="Availability">
          <option value="">Availability (All)</option>
          <option value="in_stock" <?= $availability === 'in_stock' ? 'selected' : '' ?>>In stock</option>
          <option value="out_of_stock" <?= $availability === 'out_of_stock' ? 'selected' : '' ?>>Out of stock</option>
        </select>

        <div class="facets-price">
          <input type="number" name="price_min" min="0" step="1" placeholder="Min Rs." value="<?= $priceMin !== null ? e((string)$priceMin) : '' ?>" aria-label="Minimum price">
          <span>–</span>
          <input type="number" name="price_max" min="0" step="1" placeholder="Max Rs." value="<?= $priceMax !== null ? e((string)$priceMax) : '' ?>" aria-label="Maximum price">
          <button type="submit" class="button button--secondary button--small">Apply</button>
        </div>

        <?php if ($availability || $priceMin !== null || $priceMax !== null): ?>
          <a href="<?= base_url('/collections/' . $currentHandle) ?>" class="link--underline" style="font-size:1.35rem;">Remove all</a>
        <?php endif; ?>
      </div>

      <div class="facets-group">
        <label for="sort-by" style="font-size:1.4rem;color:var(--color-muted);">Sort by:</label>
        <select id="sort-by" name="sort_by" class="facets-select" onchange="this.form.submit()">
          <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
          <option value="best-selling" <?= $sort === 'best-selling' ? 'selected' : '' ?>>Best selling</option>
          <option value="title-ascending" <?= $sort === 'title-ascending' ? 'selected' : '' ?>>Alphabetically, A-Z</option>
          <option value="title-descending" <?= $sort === 'title-descending' ? 'selected' : '' ?>>Alphabetically, Z-A</option>
          <option value="price-ascending" <?= $sort === 'price-ascending' ? 'selected' : '' ?>>Price, low to high</option>
          <option value="price-descending" <?= $sort === 'price-descending' ? 'selected' : '' ?>>Price, high to low</option>
          <option value="created-descending" <?= $sort === 'created-descending' ? 'selected' : '' ?>>Date, new to old</option>
          <option value="created-ascending" <?= $sort === 'created-ascending' ? 'selected' : '' ?>>Date, old to new</option>
        </select>
        <span class="facets-count"><?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?></span>
      </div>
    </form>

    <?php if (empty($products)): ?>
      <div style="text-align:center; padding: 5rem 0;">
        <p style="font-size:1.8rem; margin-bottom:1.6rem;">No products found matching your filters.</p>
        <a href="<?= base_url('/collections/' . $currentHandle) ?>" class="button">Clear filters</a>
      </div>
    <?php else: ?>
      <ul class="product-grid product-grid--4" role="list" style="margin-bottom: 4rem;">
        <?php foreach ($products as $product): ?>
          <?= view('store/partials/product_card', ['product' => $product, 'cardShape' => $cardShape]) ?>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>

<?= $this->endSection() ?>
