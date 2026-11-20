<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<section class="policy-page">
  <div class="page-width--narrow">
    <h1 class="h1" style="text-align:center; margin-bottom: 3rem;"><?= e($policy['title']) ?></h1>
    <div class="rte">
      <?= sanitize_rich_html($policy['body_html']) ?>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
