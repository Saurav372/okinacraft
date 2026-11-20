<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$storeEmail = store_setting('store_email', 'okinacraft@gmail.com');
$instagram = store_setting('instagram_handle', 'okina_craft');
$whatsappNo = store_setting('whatsapp_number', '7827747556');
$whatsappPrefill = store_setting('whatsapp_prefill', 'Hi Okina Craft, I need help with a product or order.');
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
      <div class="rich-text__buttons" style="display:flex;justify-content:center;gap:1.2rem;flex-wrap:wrap;">
        <a href="mailto:<?= e($storeEmail) ?>" class="button button--secondary">Email Us</a>
        <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode($whatsappPrefill) ?>" target="_blank" rel="noopener noreferrer" class="button button--secondary" style="display:inline-flex;align-items:center;gap:0.7rem;">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="color:#25d366;"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L0 24l6.5-1.7a11.8 11.8 0 0 0 5.6 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4ZM12.1 21.7a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.9 1 1-3.8-.3-.4a9.8 9.8 0 1 1 8.6 4.8Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-1-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.8 4.3.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.1-1.3-.1-.2-.3-.3-.6-.4Z"/></svg>
          <span>WhatsApp</span>
        </a>
        <a href="https://www.instagram.com/<?= e($instagram) ?>/" target="_blank" rel="noopener noreferrer" class="button" style="display:inline-flex;align-items:center;gap:0.7rem;">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#fff;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
          <span>Instagram (@<?= e($instagram) ?>)</span>
        </a>
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
