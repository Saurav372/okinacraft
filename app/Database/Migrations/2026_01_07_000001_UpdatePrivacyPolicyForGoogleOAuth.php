<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePrivacyPolicyForGoogleOAuth extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('policies')) {
            $policyHtml = '<div class="policy-content">
  <p class="text-muted"><em>Last updated: November 2026</em></p>

  <p>At <strong>Okina Craft</strong> ("we", "our", or "us"), operating at <a href="https://okinacraft.com/">https://okinacraft.com</a>, we are committed to protecting your privacy and personal data. This Privacy Policy outlines how your personal information is collected, used, shared, and protected when you browse our website, create an account, purchase custom apparel and merchandise, or sign in using third-party services such as Google.</p>

  <h3>1. Information We Collect</h3>
  <p>When you visit Okina Craft, register an account, or place an order, we collect information necessary to deliver our personalized apparel and e-commerce services:</p>
  <ul>
    <li><strong>Account &amp; Contact Details:</strong> Your full name, email address, phone number, shipping address, and billing address.</li>
    <li><strong>Google User Data (Google Sign-In):</strong> When you choose to authenticate using Google Sign-In, we collect your name, email address, profile picture URL, and unique Google account identifier provided by Google Identity Services via OAuth 2.0.</li>
    <li><strong>Custom Artwork &amp; Design Uploads:</strong> Digital image files (PNG, JPG, SVG, PSD), vector graphics, custom text, logos, and printing instructions you upload for customizing t-shirts, hoodies, mouse pads, mugs, and other merchandise.</li>
    <li><strong>Payment &amp; Transaction Information:</strong> Payment reference IDs, UPI transaction identifiers, and payment status. We do not store complete credit card or debit card numbers on our servers; payments are processed securely by certified payment gateways.</li>
    <li><strong>Technical &amp; Device Information:</strong> IP address, browser type, operating system, device details, and essential session cookies required for cart persistence and secure checkout.</li>
  </ul>

  <h3>2. How We Use Your Information</h3>
  <p>We use your information exclusively for legitimate business and fulfillment purposes:</p>
  <ul>
    <li><strong>Order Processing &amp; Custom Printing:</strong> Fulfilling customized print-on-demand orders, garment preparation, DTF printing, and packing your products.</li>
    <li><strong>Shipping &amp; Delivery Tracking:</strong> Generating shipping labels and providing real-time tracking updates via email, SMS, or WhatsApp.</li>
    <li><strong>Account Management &amp; Authentication:</strong> Enabling seamless login, maintaining your order history, and saving your wishlist items.</li>
    <li><strong>Customer Support:</strong> Responding to order inquiries, artwork checks, returns, replacements, and sizing assistance.</li>
    <li><strong>Security &amp; Fraud Prevention:</strong> Protecting our website, verifying accounts, and preventing unauthorized or fraudulent transactions.</li>
  </ul>

  <h3>3. Google User Data Policy &amp; Limited Use Disclosure</h3>
  <p>Okina Craft offers Google Sign-In as a quick, secure authentication option for our customers. Regarding Google user data:</p>
  <ul>
    <li><strong>Data Accessed:</strong> We request only basic profile information (name, email address, and profile picture). We do not request access to your Google Drive, Gmail, Google Contacts, or other private Google services.</li>
    <li><strong>Purpose:</strong> Google user data is used solely to authenticate your identity, create your Okina Craft customer account, and link your custom apparel orders and wishlist to your account.</li>
    <li><strong>No Sale or Third-Party Transfer:</strong> We never sell, rent, trade, or transfer Google user data to data brokers, advertising networks, or third parties for marketing purposes.</li>
    <li><strong>No Advertising or Surveillance:</strong> We do not use Google user data to serve targeted advertisements or build advertising profiles.</li>
    <li><strong>No AI Model Training:</strong> We do not use data obtained through Google APIs to train generalized artificial intelligence or machine learning models.</li>
    <li><strong>Limited Use Commitment:</strong> <strong>Okina Craft\'s use and transfer to any other app of information received from Google APIs will adhere to the <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">Google API Services User Data Policy</a>, including the Limited Use requirements.</strong></li>
  </ul>

  <h3>4. Sharing Your Information</h3>
  <p>We never sell your personal data. We share information only with trusted third parties strictly necessary to operate our store and deliver your purchases:</p>
  <ul>
    <li><strong>Courier &amp; Logistics Partners:</strong> Delivery carriers (such as DTDC, Blue Dart, Delhivery, India Post) to transport and deliver orders to your shipping address.</li>
    <li><strong>Secure Payment Processors:</strong> RBI-compliant payment gateways and banking networks to securely authorize payments.</li>
    <li><strong>Legal Obligations:</strong> Law enforcement or regulatory authorities only when strictly required by applicable Indian laws or valid legal processes.</li>
  </ul>

  <h3>5. Custom Artwork, Content Standards &amp; Terms Compliance</h3>
  <p>All designs, artwork, and logos uploaded by customers remain the customer\'s intellectual property. We do not reuse, resell, or distribute your custom designs.</p>
  <p><strong>Content Standards &amp; Prohibited Use:</strong> In compliance with the Google APIs Terms of Service and acceptable use policies, our platform strictly prohibits the upload, generation, or printing of Non-Consensual Intimate Imagery (NCII), sexually explicit material, abusive or defamatory graphics, hateful content, or materials infringing on third-party copyrights or trademarks.</p>

  <h3>6. Data Retention &amp; Security</h3>
  <p>We implement industry-standard encryption, SSL/TLS protocols, and secure server environments to safeguard your personal data from unauthorized access, loss, or misuse.</p>
  <ul>
    <li>We retain your account details and order records only as long as your account remains active or as required by law for taxation, accounting, and warranty obligations.</li>
    <li>Google authentication tokens and session identifiers are stored securely and encrypted in transit.</li>
  </ul>

  <h3>7. Data Deletion &amp; Revocation of Google Access</h3>
  <p>You have full control over your personal data and account:</p>
  <ul>
    <li><strong>Account &amp; Data Deletion:</strong> You can request permanent deletion of your Okina Craft account and all associated personal data (including Google account data and order history) at any time by emailing us at <a href="mailto:okinacraft@gmail.com">okinacraft@gmail.com</a> or messaging WhatsApp at <a href="https://wa.me/917827747556" target="_blank">+91 7827747556</a> with the subject line <em>"Data Deletion Request"</em>. Requests are fulfilled within 30 days.</li>
    <li><strong>Revoking Google Access:</strong> You can revoke Okina Craft\'s access to your Google account at any time via your <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener">Google Account Third-Party Permissions Page</a>. Once revoked, Okina Craft will no longer be able to access your Google profile.</li>
  </ul>

  <h3>8. Your Legal Rights</h3>
  <p>Under applicable data privacy laws, you have the right to access the personal data we hold about you, request corrections to inaccurate information, or request data erasure. For any requests or inquiries, please contact our privacy officer.</p>

  <h3>9. Contact Information</h3>
  <p>If you have any questions, concerns, or requests regarding this Privacy Policy or our data handling practices, please contact us:</p>
  <ul>
    <li><strong>Brand:</strong> Okina Craft</li>
    <li><strong>Support Email:</strong> <a href="mailto:okinacraft@gmail.com">okinacraft@gmail.com</a></li>
    <li><strong>WhatsApp Support:</strong> <a href="https://wa.me/917827747556" target="_blank">+91 7827747556</a> (Mon–Sat, 10:00 AM – 6:00 PM IST)</li>
    <li><strong>Official Address:</strong> Ground Floor, Property No. 3, Khasra No. 31/2, Street No. 10, Surender Colony, Jharoda Majra Part-2, New Delhi, North Delhi, Delhi - 110084, India.</li>
    <li><strong>Website:</strong> <a href="https://okinacraft.com/">https://okinacraft.com</a></li>
  </ul>
</div>';

            $existing = $this->db->table('policies')->where('slug', 'privacy-policy')->get()->getRowArray();
            if ($existing) {
                $this->db->table('policies')
                    ->where('slug', 'privacy-policy')
                    ->update([
                        'title'      => 'Privacy Policy',
                        'body_html'  => $policyHtml,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                $this->db->table('policies')->insert([
                    'slug'       => 'privacy-policy',
                    'title'      => 'Privacy Policy',
                    'body_html'  => $policyHtml,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // Safe no-op on rollback
    }
}
