<?php

namespace App\Controllers;

use App\Libraries\CatalogService;
use App\Libraries\CustomerAuthService;
use App\Models\CustomerAddressModel;
use App\Models\CustomerModel;
use App\Models\CustomerWishlistModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use Config\Database;

class CustomerAccountController extends BaseController
{
    private CustomerAuthService $auth;

    public function __construct()
    {
        CatalogService::ensureBootstrapped();
        $this->auth = new CustomerAuthService();
    }

    /**
     * Customer Account Dashboard
     */
    public function dashboard()
    {
        $customer = $this->auth->customer();
        if (!$customer) {
            return redirect()->to('/account/login');
        }

        $customerId = (int)$customer['id'];
        $db = Database::connect();

        // Get recent orders
        $orders = $db->table('orders')
            ->where('customer_id', $customerId)
            ->orWhere('LOWER(customer_email)', strtolower($customer['email']))
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $orderItemModel = new OrderItemModel();
        foreach ($orders as &$o) {
            $o['items'] = $orderItemModel->where('order_id', (int)$o['id'])->findAll();
        }

        // Get saved addresses
        $addrModel = new CustomerAddressModel();
        $addresses = $addrModel->getByCustomer($customerId);

        // Get wishlist count
        $wishlistModel = new CustomerWishlistModel();
        $wishlistCount = count($wishlistModel->getProductIds($customerId));

        return view('store/account/dashboard', [
            'pageTitle'     => 'My Account – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav'     => 'account',
            'customer'      => $customer,
            'orders'        => $orders,
            'addresses'     => $addresses,
            'wishlistCount' => $wishlistCount,
            'error'         => session()->getFlashdata('error'),
            'success'       => session()->getFlashdata('success'),
        ]);
    }

    /**
     * View Order Details
     */
    public function orderDetail(int $id)
    {
        $customer = $this->auth->customer();
        if (!$customer) {
            return redirect()->to('/account/login');
        }

        $orderModel = new OrderModel();
        $order = $orderModel->find($id);

        // Ownership check
        if (!$order || ((int)($order['customer_id'] ?? 0) !== (int)$customer['id'] && strtolower($order['customer_email']) !== strtolower($customer['email']))) {
            session()->setFlashdata('error', 'Order not found or access denied.');
            return redirect()->to('/account');
        }

        $orderItemModel = new OrderItemModel();
        $order['items'] = $orderItemModel->where('order_id', (int)$order['id'])->findAll();

        return view('store/account/order_detail', [
            'pageTitle' => "Order #{$order['order_number']} – " . store_setting('store_name', 'Okina Craft'),
            'activeNav' => 'account',
            'customer'  => $customer,
            'order'     => $order,
        ]);
    }

    /**
     * Save / Add Address
     */
    public function addressSave()
    {
        $customer = $this->auth->customer();
        if (!$customer) {
            return redirect()->to('/account/login');
        }

        $addrId = (int)$this->request->getPost('id');
        $fullName = trim((string)$this->request->getPost('full_name'));
        $phone = trim((string)$this->request->getPost('phone'));
        $addressLine = trim((string)$this->request->getPost('address_line'));
        $city = trim((string)$this->request->getPost('city'));
        $state = trim((string)$this->request->getPost('state'));
        $pincode = trim((string)$this->request->getPost('pincode'));
        $isDefault = $this->request->getPost('is_default') ? 1 : 0;

        if ($fullName === '' || $addressLine === '' || $city === '' || $state === '' || !preg_match('/^\d{6}$/', $pincode)) {
            session()->setFlashdata('error', 'Please fill in all address fields with a valid 6-digit PIN code.');
            return redirect()->to('/account');
        }

        $addrModel = new CustomerAddressModel();

        if ($addrId > 0) {
            $existing = $addrModel->where('id', $addrId)->where('customer_id', (int)$customer['id'])->first();
            if (!$existing) {
                session()->setFlashdata('error', 'Address not found.');
                return redirect()->to('/account');
            }
            $addrModel->update($addrId, [
                'full_name'    => $fullName,
                'phone'        => $phone,
                'address_line' => $addressLine,
                'city'         => $city,
                'state'        => $state,
                'pincode'      => $pincode,
                'is_default'   => $isDefault,
            ]);
        } else {
            // If first address, make it default automatically
            $existingCount = $addrModel->where('customer_id', (int)$customer['id'])->countAllResults();
            if ($existingCount === 0) {
                $isDefault = 1;
            }

            $addrId = (int)$addrModel->insert([
                'customer_id'  => (int)$customer['id'],
                'full_name'    => $fullName,
                'phone'        => $phone,
                'address_line' => $addressLine,
                'city'         => $city,
                'state'        => $state,
                'pincode'      => $pincode,
                'is_default'   => $isDefault,
            ]);
        }

        if ($isDefault && $addrId > 0) {
            $addrModel->setDefault((int)$customer['id'], $addrId);
        }

        session()->setFlashdata('success', 'Address saved successfully.');
        return redirect()->to('/account');
    }

    /**
     * Delete Address
     */
    public function addressDelete(int $id)
    {
        $customer = $this->auth->customer();
        if (!$customer) {
            return redirect()->to('/account/login');
        }

        $addrModel = new CustomerAddressModel();
        $addrModel->where('id', $id)->where('customer_id', (int)$customer['id'])->delete();

        session()->setFlashdata('success', 'Address removed.');
        return redirect()->to('/account');
    }

    /**
     * Set Address as Default
     */
    public function addressSetDefault(int $id)
    {
        $customer = $this->auth->customer();
        if (!$customer) {
            return redirect()->to('/account/login');
        }

        $addrModel = new CustomerAddressModel();
        $addrModel->setDefault((int)$customer['id'], $id);

        session()->setFlashdata('success', 'Default address updated.');
        return redirect()->to('/account');
    }

    /**
     * Update Profile & Password
     */
    public function profileUpdate()
    {
        $customer = $this->auth->customer();
        if (!$customer) {
            return redirect()->to('/account/login');
        }

        $name = trim((string)$this->request->getPost('name'));
        $phone = trim((string)$this->request->getPost('phone'));
        $currentPass = (string)$this->request->getPost('current_password');
        $newPass = (string)$this->request->getPost('new_password');

        if ($name === '') {
            session()->setFlashdata('error', 'Full name cannot be empty.');
            return redirect()->to('/account');
        }

        $customerModel = new CustomerModel();
        $updates = [
            'name'  => $name,
            'phone' => preg_replace('/\D+/', '', $phone),
        ];

        // If user wants to change password
        if ($newPass !== '') {
            if (strlen($newPass) < 8) {
                session()->setFlashdata('error', 'New password must be at least 8 characters long.');
                return redirect()->to('/account');
            }

            // If account has an existing password, verify current
            if (!empty($customer['password_hash'])) {
                if (!password_verify($currentPass, $customer['password_hash'])) {
                    session()->setFlashdata('error', 'Current password is incorrect.');
                    return redirect()->to('/account');
                }
            }

            $updates['password_hash'] = password_hash($newPass, PASSWORD_DEFAULT);
        }

        $customerModel->update((int)$customer['id'], $updates);

        session()->setFlashdata('success', 'Profile details updated.');
        return redirect()->to('/account');
    }
}
