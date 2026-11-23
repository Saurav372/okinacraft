<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$storeName = store_setting('store_name', 'Okina Craft');
$storeEmail = store_setting('store_email', 'okinacraft@gmail.com');
$instagram = store_setting('instagram_handle', 'okina_craft');
$whatsappNo = store_setting('whatsapp_number', '7827747556');
$whatsappPrefill = store_setting('whatsapp_prefill', 'Hi Okina Craft, I need help with a product or order.');
$businessAddress = store_setting('business_address', 'Ground Floor, Property No. 3, Khasra No. 31/2, Street No. 10, Surender Colony, Jharoda Majra Part-2, New Delhi, North Delhi, Delhi - 110084, India.');
$businessHours = store_setting('business_hours', 'Monday–Saturday, 10:00 AM–6:00 PM IST (Closed Sundays & National Holidays)');
?>
<section class="contact-page">
  <div class="page-width">
    <!-- Breadcrumbs -->
    <nav aria-label="Breadcrumb" style="margin-bottom: 2rem;">
      <ol style="display:flex;align-items:center;gap:0.8rem;list-style:none;padding:0;margin:0;font-size:1.35rem;color:var(--color-muted);">
        <li><a href="<?= base_url('/') ?>" class="link--underline" style="color:inherit;">Home</a></li>
        <li><span>/</span></li>
        <li style="color:rgb(var(--color-foreground));font-weight:600;">Contact Us</li>
      </ol>
    </nav>

    <!-- Page Header -->
    <div style="text-align: center; max-width: 680px; margin: 0 auto 3rem;">
      <h1 class="h1" style="font-size: 3.2rem; margin-bottom: 1.2rem; font-weight: 800;">Get in Touch</h1>
      <p style="font-size: 1.6rem; color: rgba(0,0,0,0.7); line-height: 1.55;">
        Have questions about custom t-shirt printing, sizing, bulk orders, or tracking an existing order? Our team is here to help!
      </p>
    </div>

    <div class="contact-layout">
      <!-- Left Column: Contact Cards -->
      <div class="contact-info-col">
        <!-- Direct Channels Card -->
        <div class="contact-card">
          <div class="contact-card__title">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <span>Direct Support Channels</span>
          </div>

          <div class="contact-channel">
            <div class="contact-channel__icon contact-channel__icon--wa">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                <path d="M20.5 3.5A11.8 11.8 0 0 0 12.1 0C5.6 0 .3 5.3.3 11.8c0 2.1.5 4.1 1.6 5.9L0 24l6.5-1.7a11.8 11.8 0 0 0 5.6 1.4h.1c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.5-8.4ZM12.1 21.7a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.9 1 1-3.8-.3-.4a9.8 9.8 0 1 1 8.6 4.8Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.5-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-1-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.8 4.3.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.1-1.3-.1-.2-.3-.3-.6-.4Z"/>
              </svg>
            </div>
            <div class="contact-channel__body">
              <div class="contact-channel__label">WhatsApp Support</div>
              <div class="contact-channel__val">
                <a href="https://wa.me/<?= e($whatsappNo) ?>?text=<?= rawurlencode($whatsappPrefill) ?>" target="_blank" rel="noopener noreferrer">+91 <?= e($whatsappNo) ?></a>
              </div>
              <div class="contact-channel__sub">Fastest response for design &amp; order inquiries</div>
            </div>
          </div>

          <div class="contact-channel">
            <div class="contact-channel__icon contact-channel__icon--mail">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
            </div>
            <div class="contact-channel__body">
              <div class="contact-channel__label">Official Email</div>
              <div class="contact-channel__val">
                <a href="mailto:<?= e($storeEmail) ?>"><?= e($storeEmail) ?></a>
              </div>
              <div class="contact-channel__sub">For corporate gifting, bulk quotes &amp; official matters</div>
            </div>
          </div>

          <div class="contact-channel">
            <div class="contact-channel__icon contact-channel__icon--ig">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
              </svg>
            </div>
            <div class="contact-channel__body">
              <div class="contact-channel__label">Instagram Community</div>
              <div class="contact-channel__val">
                <a href="https://www.instagram.com/<?= e($instagram) ?>/" target="_blank" rel="noopener noreferrer">@<?= e($instagram) ?></a>
              </div>
              <div class="contact-channel__sub">DM us for drops, tags &amp; community perks</div>
            </div>
          </div>
        </div>

        <!-- Official Address & Operating Hours Card -->
        <div class="contact-card">
          <div class="contact-card__title">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>Registered Business Address</span>
          </div>
          <p class="contact-address-text" style="margin-bottom: 1.6rem;">
            <?= nl2br(e($businessAddress)) ?>
          </p>

          <div style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 1.4rem;">
            <div style="display:flex;align-items:center;gap:0.8rem;font-size:1.35rem;font-weight:600;color:rgb(var(--color-foreground));margin-bottom:0.4rem;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--color-muted);">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              <span>Operating Support Hours</span>
            </div>
            <p style="font-size: 1.35rem; color: var(--color-muted); margin: 0; line-height: 1.5;">
              <?= e($businessHours) ?>
            </p>
          </div>
        </div>

        <!-- Order Tracking Shortcut -->
        <div class="contact-card" style="background: #fdfdfd; border-color: rgba(0,0,0,0.12);">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:1.2rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:1rem;">
              <span style="display:inline-flex;align-items:center;justify-content:center;width:3.6rem;height:3.6rem;border-radius:8px;background:rgba(0,0,0,0.05);">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                </svg>
              </span>
              <div>
                <strong style="font-size:1.45rem;display:block;">Looking to Track Your Order?</strong>
                <span style="font-size:1.3rem;color:var(--color-muted);">No login required with Order # &amp; Phone</span>
              </div>
            </div>
            <a href="<?= base_url('/account/track') ?>" class="button button--secondary" style="padding:0.8rem 1.6rem;font-size:1.35rem;">Track Order &rarr;</a>
          </div>
        </div>
      </div>

      <!-- Right Column: Message Form -->
      <div class="checkout-card" style="padding: 3rem;">
        <h2 class="h2" style="font-size: 2.2rem; margin-bottom: 0.8rem; font-weight: 700;">Send Us a Message</h2>
        <p style="font-size: 1.45rem; color: var(--color-muted); margin-bottom: 2.4rem;">
          Fill out the form below and our team will get back to you within 2 to 4 business hours.
        </p>

        <form action="<?= base_url('/contact') ?>" method="post">
          <?= csrf_field() ?>

          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label" for="ct-name">Full Name <span style="color:#dc2626;">*</span></label>
              <input id="ct-name" name="name" type="text" class="field__input" required placeholder="e.g. Rahul Sharma" autocomplete="name">
            </div>

            <div class="form-row">
              <label class="form-label" for="ct-email">Email Address <span style="color:#dc2626;">*</span></label>
              <input id="ct-email" name="email" type="email" class="field__input" required placeholder="name@example.com" autocomplete="email">
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-row">
              <label class="form-label" for="ct-phone">Phone / WhatsApp Number</label>
              <input id="ct-phone" name="phone" type="tel" class="field__input" placeholder="e.g. 9876543210" autocomplete="tel">
            </div>

            <div class="form-row">
              <label class="form-label" for="ct-subj">Subject</label>
              <input id="ct-subj" name="subject" type="text" class="field__input" placeholder="e.g. Custom Bulk Order / Sizing Help">
            </div>
          </div>

          <div class="form-row">
            <label class="form-label" for="ct-msg">Message / Inquiry <span style="color:#dc2626;">*</span></label>
            <textarea id="ct-msg" name="message" class="textarea__input" rows="5" required placeholder="Tell us how we can help you with your order, design, or sizing..."></textarea>
          </div>

          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.2rem;margin-top:2rem;">
            <button type="submit" class="button" style="min-width:180px;padding:1.3rem 2.6rem;font-size:1.5rem;">
              Send Message
            </button>
            <span style="font-size:1.25rem;color:var(--color-muted);">
              🔒 Your contact information is never shared.
            </span>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
