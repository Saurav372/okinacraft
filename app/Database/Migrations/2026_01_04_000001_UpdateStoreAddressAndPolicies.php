<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateStoreAddressAndPolicies extends Migration
{
    public function up()
    {
        $newAddress = 'Ground Floor, Property No. 3, Khasra No. 31/2, Street No. 10, Surender Colony, Jharoda Majra Part-2, New Delhi, North Delhi, Delhi - 110084, India.';

        // 1. Update business_address in settings table
        if ($this->db->tableExists('settings')) {
            $existing = $this->db->table('settings')->where('setting_key', 'business_address')->get()->getRowArray();
            if ($existing) {
                $this->db->table('settings')
                    ->where('setting_key', 'business_address')
                    ->update(['setting_value' => $newAddress]);
            } else {
                $this->db->table('settings')->insert([
                    'setting_key'   => 'business_address',
                    'setting_value' => $newAddress,
                ]);
            }
        }

        // 2. Update contact-information policy in policies table
        if ($this->db->tableExists('policies')) {
            $contactBody = '<div class="policy-content">
  <p class="text-muted"><em>We are here to assist you with custom orders, sizing, delivery, and inquiries.</em></p>

  <div class="card p-4 border rounded my-3" style="background:#fdfdfd;">
    <h3 class="mb-3 text-dark">Okina Craft — Official Helpdesk</h3>
    <ul class="list-unstyled" style="line-height: 2;">
      <li><strong>Brand / Business Name:</strong> Okina Craft</li>
      <li><strong>Customer Support Email:</strong> <a href="mailto:okinacraft@gmail.com">okinacraft@gmail.com</a></li>
      <li><strong>WhatsApp Support &amp; Orders:</strong> <a href="https://wa.me/917827747556" target="_blank">+91 7827747556</a></li>
      <li><strong>Instagram Direct:</strong> <a href="https://www.instagram.com/okina_craft" target="_blank">@okina_craft</a></li>
      <li><strong>Registered Business Address:</strong> ' . htmlspecialchars($newAddress) . '</li>
      <li><strong>Operating Support Hours:</strong> Monday through Saturday, 10:00 AM – 6:00 PM IST (Closed Sundays &amp; National Holidays)</li>
    </ul>
  </div>

  <h3>How to Reach Us for Order Support</h3>
  <p>For inquiries regarding an existing purchase, order tracking, or custom artwork adjustments, please include your <strong>Order Number</strong> (e.g. <code>#CS-1001</code>) and registered phone number. Our team typically replies within <strong>2 to 4 business hours</strong>.</p>
</div>';

            $this->db->table('policies')
                ->where('slug', 'contact-information')
                ->update([
                    'body_html'  => $contactBody,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // Also update any legacy addresses in privacy-policy & terms-of-service
            $privacy = $this->db->table('policies')->where('slug', 'privacy-policy')->get()->getRowArray();
            if ($privacy && !empty($privacy['body_html'])) {
                $updatedBody = str_replace(
                    ['Hingoli, Maharashtra, India', 'Burari, Delhi, India', 'Burari, Delhi'],
                    $newAddress,
                    $privacy['body_html']
                );
                $this->db->table('policies')->where('slug', 'privacy-policy')->update([
                    'body_html'  => $updatedBody,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $terms = $this->db->table('policies')->where('slug', 'terms-of-service')->get()->getRowArray();
            if ($terms && !empty($terms['body_html'])) {
                $updatedBody = str_replace(
                    ['Hingoli, Maharashtra, India', 'Burari, Delhi, India', 'Burari, Delhi'],
                    $newAddress,
                    $terms['body_html']
                );
                $updatedBody = str_replace('courts in Maharashtra', 'courts in Delhi', $updatedBody);
                $this->db->table('policies')->where('slug', 'terms-of-service')->update([
                    'body_html'  => $updatedBody,
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
