<?= $this->extend('admin/layout') ?>
<?= $this->section('admin_content') ?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle text-secondary">Communication & Inquiries</div>
        <h2 class="page-title fw-bold">
          Customer Messages & Quotes
          <span class="badge bg-secondary-lt ms-2 font-monospace fs-4"><?= count($messages) ?></span>
        </h2>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <?php if (empty($messages)): ?>
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <div class="text-secondary mb-3">
            <i class="ti ti-mail-opened fs-1 text-muted" style="font-size: 3rem;"></i>
          </div>
          <h4 class="text-secondary fw-semibold">No Inquiries Found</h4>
          <p class="text-muted small mb-0">Customer contact inquiries, custom merchandise quotes, and messages will appear here.</p>
        </div>
      </div>
    <?php else: ?>
      <div class="d-flex flex-column gap-3">
        <?php foreach ($messages as $m): ?>
          <div class="card border-0 shadow-sm <?= $m['status'] === 'unread' ? 'border-start border-primary border-3' : '' ?>">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent py-3">
              <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-sm bg-dark text-white rounded-circle">
                  <i class="ti ti-user"></i>
                </span>
                <div>
                  <div class="fw-bold text-dark">
                    <?= e($m['name']) ?>
                    <?php if ($m['status'] === 'unread'): ?>
                      <span class="badge bg-blue text-white ms-1">New Message</span>
                    <?php endif; ?>
                  </div>
                  <div class="text-secondary small">
                    <a href="mailto:<?= e($m['email']) ?>" class="text-reset"><?= e($m['email']) ?></a>
                    <?php if (!empty($m['phone'])): ?>
                      · <a href="tel:<?= e($m['phone']) ?>" class="text-reset"><?= e($m['phone']) ?></a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="text-secondary small">
                <i class="ti ti-clock me-1"></i> <?= date('d M Y, H:i', strtotime($m['created_at'])) ?>
              </div>
            </div>

            <div class="card-body">
              <div class="fw-bold text-dark mb-2 fs-3">
                <?= e($m['subject'] ?: 'Customer Inquiry') ?>
              </div>
              <div class="text-secondary bg-light p-3 rounded" style="white-space: pre-line; line-height: 1.6;">
                <?= e($m['message']) ?>
              </div>
            </div>

            <div class="card-footer bg-transparent py-2 d-flex justify-content-between align-items-center">
              <div class="btn-group btn-group-sm">
                <a href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . ($m['subject'] ?: 'Inquiry at Okina Craft')) ?>" class="btn btn-outline-secondary">
                  <i class="ti ti-mail-forward me-1"></i> Reply via Email
                </a>
                <?php if (!empty($m['phone'])):
                  $pClean = preg_replace('/\D+/', '', (string)$m['phone']);
                  if (strlen($pClean) === 10) $pClean = '91' . $pClean;
                ?>
                  <a href="https://wa.me/<?= e($pClean) ?>?text=<?= rawurlencode('Hi ' . $m['name'] . ', thank you for contacting Okina Craft:') ?>" target="_blank" class="btn btn-outline-success">
                    <i class="ti ti-brand-whatsapp me-1"></i> WhatsApp
                  </a>
                <?php endif; ?>
              </div>

              <form action="<?= base_url('/admin/messages') ?>" method="post" class="d-inline-flex gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                <?php if ($m['status'] === 'unread'): ?>
                  <button type="submit" name="action" value="read" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-check me-1"></i> Mark as Read
                  </button>
                <?php endif; ?>
                <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message permanently?');">
                  <i class="ti ti-trash"></i>
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?= $this->endSection() ?>
