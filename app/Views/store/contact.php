<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$storeEmail = store_setting('store_email', 'okinacraft@gmail.com');
$instagram = store_setting('instagram_handle', 'okina_craft');
?>
<section class="contact-page">
  <div class="page-width--narrow">
    <h1 class="h0" style="margin-bottom: 1.6rem;">Contact</h1>
  </div>

  <!-- Exact Okina Craft "LET'S TALK" Rich Text Section -->
  <div class="page-width">
    <div class="rich-text--center">
      <h2 class="h1" style="margin-bottom: 1.6rem;">LET'S TALK</h2>
      <p style="font-size: 1.6rem; color: rgba(0,0,0,0.75);">
        Questions about your order or a custom design? We're here to help. Whether you need help choosing a product, uploading your design, or tracking your order, our team is happy to assist.
      </p>
      <div class="rich-text__buttons">
        <a href="mailto:<?= e($storeEmail) ?>" class="button button--secondary">Email Us</a>
        <a href="https://www.instagram.com/<?= e($instagram) ?>/" target="_blank" rel="noopener noreferrer" class="button">Follow Us on Instagram</a>
      </div>
    </div>
  </div>

  <!-- Direct Inquiry Form (Saves into Admin -> Messages) -->
  <div class="page-width--narrow" style="margin-top: 2rem;">
    <div class="checkout-card">
      <h3 class="h2" style="margin-bottom: 1.6rem;">Send us a message</h3>
      <form action="<?= base_url('/pages/contact') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-grid-2">
          <div class="form-row">
            <label class="form-label" for="ct-name">Name *</label>
            <input id="ct-name" name="name" type="text" class="field__input" required placeholder="Your name">
          </div>
          <div class="form-row">
            <label class="form-label" for="ct-email">Email *</label>
            <input id="ct-email" name="email" type="email" class="field__input" required placeholder="you@example.com">
          </div>
        </div>
        <div class="form-grid-2">
          <div class="form-row">
            <label class="form-label" for="ct-phone">Phone / WhatsApp</label>
            <input id="ct-phone" name="phone" type="tel" class="field__input" placeholder="Optional">
          </div>
          <div class="form-row">
            <label class="form-label" for="ct-subj">Subject</label>
            <input id="ct-subj" name="subject" type="text" class="field__input" placeholder="Custom design / Order inquiry">
          </div>
        </div>
        <div class="form-row">
          <label class="form-label" for="ct-msg">Message *</label>
          <textarea id="ct-msg" name="message" class="textarea__input" required placeholder="Tell us about your order or custom design idea..."></textarea>
        </div>
        <button type="submit" class="button">Send Message</button>
      </form>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
