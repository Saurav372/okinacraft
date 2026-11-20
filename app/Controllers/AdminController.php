<?php

namespace App\Controllers;

use App\Libraries\AuthService;
use App\Libraries\CatalogService;
use App\Models\ContactMessageModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PolicyModel;
use App\Models\SettingModel;
use Config\Database;

class AdminController extends BaseController
{
    protected AuthService $auth;
    protected CatalogService $catalog;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->catalog = new CatalogService();
    }

    public function loginForm()
    {
        if ($this->auth->check()) {
            return redirect()->to('/admin');
        }
        return view('admin/login', ['pageTitle' => 'Admin Sign In – Okina Craft']);
    }

    public function loginPost()
    {
        $email = trim((string)$this->request->getPost('email'));
        $password = (string)$this->request->getPost('password');

        $res = $this->auth->attempt($email, $password);
        if (!$res['ok']) {
            session()->setFlashdata('error', $res['error']);
            return redirect()->to('/admin/login');
        }

        session()->setFlashdata('success', 'Welcome back, ' . $res['admin']['name'] . '!');
        return redirect()->to('/admin');
    }

    public function logout()
    {
        $this->auth->logout();
        session()->setFlashdata('info', 'You have been signed out.');
        return redirect()->to('/admin/login');
    }

    public function dashboard()
    {
        $admin = $this->auth->admin();
        $db = Database::connect();

        $stats = [
            'total_orders'    => $db->table('orders')->countAllResults(),
            'pending_orders'  => $db->table('orders')->whereIn('order_status', ['pending', 'confirmed', 'printing'])->countAllResults(),
            'revenue'         => (float)($db->table('orders')->selectSum('total')->where('order_status !=', 'cancelled')->get()->getRowArray()['total'] ?? 0),
            'total_products'  => $db->table('products')->countAllResults(),
            'sold_out'        => $db->table('products')->where('available', 0)->countAllResults(),
            'unread_messages' => $db->table('contact_messages')->where('status', 'unread')->countAllResults(),
        ];

        $recentOrders = $db->table('orders')->orderBy('id', 'DESC')->limit(8)->get()->getResultArray();

        return view('admin/dashboard', [
            'pageTitle'    => 'Dashboard – Admin',
            'adminNav'     => 'dashboard',
            'admin'        => $admin,
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function products()
    {
        $admin = $this->auth->admin();
        $q = trim((string)$this->request->getGet('q'));
        $products = $this->catalog->getAllProducts('featured', null, null, null, true);

        if ($q !== '') {
            $ql = mb_strtolower($q);
            $products = array_values(array_filter($products, fn($p) =>
                str_contains(mb_strtolower($p['title']), $ql)
                || str_contains(mb_strtolower($p['product_type']), $ql)
                || str_contains(mb_strtolower($p['handle']), $ql)
            ));
        }

        return view('admin/products', [
            'pageTitle' => 'Products – Admin',
            'adminNav'  => 'products',
            'admin'     => $admin,
            'products'  => $products,
            'q'         => $q,
        ]);
    }

    public function productForm(?int $id = null)
    {
        $admin = $this->auth->admin();
        $product = $id ? $this->catalog->getProductById($id) : null;
        if ($id && !$product) {
            session()->setFlashdata('error', 'Product not found.');
            return redirect()->to('/admin/products');
        }

        return view('admin/product_form', [
            'pageTitle'   => ($product ? 'Edit Product' : 'Add Product') . ' – Admin',
            'adminNav'    => 'products',
            'admin'       => $admin,
            'product'     => $product,
            'collections' => $this->catalog->getAllCollections(),
        ]);
    }

    public function productSave(?int $id = null)
    {
        $request = $this->request;
        $opt1Name = trim((string)$request->getPost('option1_name')) ?: trim((string)$request->getPost('option_name')) ?: 'Color';
        $opt2Name = trim((string)$request->getPost('option2_name')) ?: 'Size';

        $vOpt1s   = $request->getPost('variant_opt1') ?? [];
        $vOpt2s   = $request->getPost('variant_opt2') ?? [];
        $vTitles  = $request->getPost('variant_title') ?? [];
        $vPrices  = $request->getPost('variant_price') ?? [];
        $vSkus    = $request->getPost('variant_sku') ?? [];
        $vQtys    = $request->getPost('variant_qty') ?? [];
        $vAvails  = $request->getPost('variant_available') ?? [];

        $variants = [];
        $opt1Vals = [];
        $opt2Vals = [];
        $basePrice = (float)($request->getPost('price') ?: 0);

        $rowCount = max(count($vOpt1s), count($vTitles));
        if ($rowCount > 0) {
            for ($idx = 0; $idx < $rowCount; $idx++) {
                $o1 = isset($vOpt1s[$idx]) ? trim((string)$vOpt1s[$idx]) : (isset($vTitles[$idx]) ? trim((string)$vTitles[$idx]) : '');
                $o2 = isset($vOpt2s[$idx]) ? trim((string)$vOpt2s[$idx]) : '';
                if ($o1 === '' && $o2 === '') continue;

                $title = ($o1 !== '' && $o2 !== '') ? "{$o1} / {$o2}" : ($o1 !== '' ? $o1 : $o2);
                $vp = isset($vPrices[$idx]) && $vPrices[$idx] !== '' ? (float)$vPrices[$idx] : $basePrice;

                $variants[] = [
                    'title'         => $title,
                    'option1'       => $o1 !== '' ? $o1 : null,
                    'option2'       => $o2 !== '' ? $o2 : null,
                    'price'         => $vp,
                    'sku'           => trim((string)($vSkus[$idx] ?? '')),
                    'inventory_qty' => max(0, (int)($vQtys[$idx] ?? 50)),
                    'available'     => !empty($vAvails[$idx]) ? 1 : 0,
                ];

                if ($o1 !== '' && $o1 !== 'Default Title') $opt1Vals[] = $o1;
                if ($o2 !== '') $opt2Vals[] = $o2;
            }
        }

        $options = [];
        if (!empty($opt1Vals)) {
            $options[] = [
                'name'     => $opt1Name,
                'position' => 1,
                'values'   => array_values(array_unique($opt1Vals)),
            ];
        }
        if (!empty($opt2Vals)) {
            $options[] = [
                'name'     => $opt2Name,
                'position' => 2,
                'values'   => array_values(array_unique($opt2Vals)),
            ];
        }

        $images = [];
        $existingImgs = $request->getPost('existing_images') ?? [];
        if (is_array($existingImgs)) {
            foreach ($existingImgs as $src) {
                $src = trim((string)$src);
                if ($src !== '') {
                    $images[] = ['src' => $src, 'alt' => (string)$request->getPost('title')];
                }
            }
        }

        $files = $request->getFileMultiple('new_images');
        if ($files) {
            foreach ($files as $f) {
                if ($f && $f->isValid() && !$f->hasMoved()) {
                    $up = handle_ci4_image_upload($f, 'products');
                    if ($up['ok']) {
                        $images[] = ['src' => $up['path'], 'alt' => (string)$request->getPost('title')];
                    }
                }
            }
        }

        $collectionIds = array_map('intval', (array)($request->getPost('collection_ids') ?? []));

        // Bulk / Tiered Pricing
        $enableBulk = !empty($request->getPost('enable_bulk_pricing')) ? 1 : 0;
        $tierMinQtys = $request->getPost('tier_min_qty') ?? [];
        $tierMaxQtys = $request->getPost('tier_max_qty') ?? [];
        $tierPrices  = $request->getPost('tier_unit_price') ?? [];
        $tierDiscounts = $request->getPost('tier_discount_percent') ?? [];

        $bulkTiers = [];
        if ($enableBulk && is_array($tierMinQtys) && count($tierMinQtys) > 0) {
            foreach ($tierMinQtys as $tIdx => $minQ) {
                $minQ = (int)$minQ;
                if ($minQ <= 0) continue;
                $rawMax = isset($tierMaxQtys[$tIdx]) ? trim((string)$tierMaxQtys[$tIdx]) : '';
                $maxQ = ($rawMax !== '' && $rawMax !== '+' && is_numeric($rawMax)) ? (int)$rawMax : null;
                $uPrice = isset($tierPrices[$tIdx]) && $tierPrices[$tIdx] !== '' ? (float)$tierPrices[$tIdx] : $basePrice;
                $disc = isset($tierDiscounts[$tIdx]) && $tierDiscounts[$tIdx] !== '' ? (float)$tierDiscounts[$tIdx] : 0.0;
                if ($disc <= 0 && $basePrice > 0 && $uPrice < $basePrice) {
                    $disc = round((($basePrice - $uPrice) / $basePrice) * 100, 1);
                }
                $bulkTiers[] = [
                    'min_qty'          => $minQ,
                    'max_qty'          => $maxQ,
                    'unit_price'       => $uPrice,
                    'discount_percent' => $disc,
                ];
            }
            usort($bulkTiers, fn($a, $b) => $a['min_qty'] <=> $b['min_qty']);
        }

        $savedId = $this->catalog->saveProduct($id, [
            'title'               => $request->getPost('title'),
            'handle'              => $request->getPost('handle'),
            'body_html'           => $request->getPost('body_html'),
            'vendor'              => $request->getPost('vendor') ?: 'Okina Craft',
            'product_type'        => $request->getPost('product_type'),
            'tags'                => $request->getPost('tags'),
            'price'               => $basePrice,
            'compare_at_price'    => $request->getPost('compare_at_price'),
            'available'           => !empty($request->getPost('available')) ? 1 : 0,
            'allow_custom_upload' => !empty($request->getPost('allow_custom_upload')) ? 1 : 0,
            'free_delivery'       => !empty($request->getPost('free_delivery')) ? 1 : 0,
            'enable_bulk_pricing' => $enableBulk,
            'bulk_tiers_json'     => !empty($bulkTiers) ? $bulkTiers : null,
            'status'              => in_array($request->getPost('status'), ['active', 'draft'], true) ? $request->getPost('status') : 'active',
            'options'             => $options,
        ], $variants, $images, $collectionIds);

        session()->setFlashdata('success', $id ? 'Product updated successfully.' : 'Product created successfully.');
        return redirect()->to('/admin/products/' . $savedId . '/edit');
    }

    public function productDelete(int $id)
    {
        $this->catalog->deleteProduct($id);
        session()->setFlashdata('success', 'Product deleted.');
        return redirect()->to('/admin/products');
    }

    public function collections()
    {
        $admin = $this->auth->admin();
        return view('admin/collections', [
            'pageTitle'   => 'Collections – Admin',
            'adminNav'    => 'collections',
            'admin'       => $admin,
            'collections' => $this->catalog->getAllCollections(),
        ]);
    }

    public function collectionSave()
    {
        $db = Database::connect();
        $action = (string)$this->request->getPost('action') ?: 'create';

        if ($action === 'delete') {
            $cid = (int)$this->request->getPost('id');
            if ($cid > 0) {
                $db->table('collections')->where('id', $cid)->delete();
                session()->setFlashdata('success', 'Collection removed.');
            }
            return redirect()->to('/admin/collections');
        }

        $cid = (int)$this->request->getPost('id');
        $title = trim((string)$this->request->getPost('title'));
        $handle = trim((string)$this->request->getPost('handle')) ?: slugify($title);
        $shape = in_array($this->request->getPost('card_shape'), ['standard', 'arch', 'blob'], true) ? $this->request->getPost('card_shape') : 'standard';
        $cols = in_array((int)$this->request->getPost('desktop_cols'), [2, 3, 4], true) ? (int)$this->request->getPost('desktop_cols') : 4;
        $showHome = !empty($this->request->getPost('show_on_home')) ? 1 : 0;
        $homeOrder = (int)($this->request->getPost('home_order') ?: 99);

        if ($title === '') {
            session()->setFlashdata('error', 'Collection title is required.');
            return redirect()->to('/admin/collections');
        }

        $colData = [
            'title'        => $title,
            'handle'       => $handle,
            'card_shape'   => $shape,
            'desktop_cols' => $cols,
            'show_on_home' => $showHome,
            'home_order'   => $homeOrder,
        ];

        if ($cid > 0) {
            $db->table('collections')->where('id', $cid)->update($colData);
            session()->setFlashdata('success', 'Collection updated.');
        } else {
            $colData['created_at'] = date('Y-m-d H:i:s');
            $db->table('collections')->insert($colData);
            session()->setFlashdata('success', 'Collection created.');
        }

        return redirect()->to('/admin/collections');
    }

    public function orders()
    {
        $admin = $this->auth->admin();
        $status = (string)$this->request->getGet('status') ?: 'all';
        $db = Database::connect();

        $builder = $db->table('orders')->orderBy('id', 'DESC');
        if ($status !== 'all') {
            $builder->where('order_status', $status);
        }
        $orders = $builder->get()->getResultArray();

        return view('admin/orders', [
            'pageTitle' => 'Orders – Admin',
            'adminNav'  => 'orders',
            'admin'     => $admin,
            'orders'    => $orders,
            'status'    => $status,
        ]);
    }

    public function orderDetail(int $id)
    {
        $admin = $this->auth->admin();
        $orderModel = new OrderModel();
        $order = $orderModel->find($id);
        if (!$order) {
            session()->setFlashdata('error', 'Order not found.');
            return redirect()->to('/admin/orders');
        }

        $itemModel = new OrderItemModel();
        $order['items'] = $itemModel->where('order_id', $id)->findAll();

        return view('admin/order_detail', [
            'pageTitle' => 'Order ' . $order['order_number'] . ' – Admin',
            'adminNav'  => 'orders',
            'admin'     => $admin,
            'order'     => $order,
        ]);
    }

    public function orderUpdate(int $id)
    {
        $allowedOrder = ['pending', 'confirmed', 'printing', 'shipped', 'delivered', 'cancelled'];
        $allowedPay = ['pending', 'awaiting_verification', 'paid', 'refunded'];

        $os = in_array($this->request->getPost('order_status'), $allowedOrder, true) ? $this->request->getPost('order_status') : 'pending';
        $ps = in_array($this->request->getPost('payment_status'), $allowedPay, true) ? $this->request->getPost('payment_status') : 'pending';
        $tracking = trim((string)$this->request->getPost('tracking_number'));

        $orderModel = new OrderModel();
        $orderModel->update($id, [
            'order_status'    => $os,
            'payment_status'  => $ps,
            'tracking_number' => $tracking,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Order status & tracking updated.');
        return redirect()->to('/admin/orders/' . $id);
    }

    public function messages()
    {
        $admin = $this->auth->admin();
        $msgModel = new ContactMessageModel();
        $messages = $msgModel->orderBy('id', 'DESC')->findAll();

        return view('admin/messages', [
            'pageTitle' => 'Messages – Admin',
            'adminNav'  => 'messages',
            'admin'     => $admin,
            'messages'  => $messages,
        ]);
    }

    public function messageAction()
    {
        $id = (int)$this->request->getPost('id');
        $action = (string)$this->request->getPost('action') ?: 'read';
        $model = new ContactMessageModel();

        if ($id > 0) {
            if ($action === 'delete') {
                $model->delete($id);
                session()->setFlashdata('success', 'Message deleted.');
            } else {
                $model->update($id, ['status' => 'read']);
                session()->setFlashdata('success', 'Marked as read.');
            }
        }
        return redirect()->to('/admin/messages');
    }

    public function settings()
    {
        $admin = $this->auth->admin();
        $settingModel = new SettingModel();
        $policyModel = new PolicyModel();

        return view('admin/settings', [
            'pageTitle' => 'Settings – Admin',
            'adminNav'  => 'settings',
            'admin'     => $admin,
            'settings'  => $settingModel->getAllAsMap(),
            'policies'  => $policyModel->orderBy('slug', 'ASC')->findAll(),
        ]);
    }

    public function settingsSave()
    {
        $admin = $this->auth->admin();
        $section = (string)$this->request->getPost('section') ?: 'general';
        $settingModel = new SettingModel();

        if ($section === 'password') {
            $newPass = (string)$this->request->getPost('new_password');
            $confirm = (string)$this->request->getPost('confirm_password');
            if (strlen($newPass) < 8) {
                session()->setFlashdata('error', 'New password must be at least 8 characters.');
                return redirect()->to('/admin/settings');
            }
            if ($newPass !== $confirm) {
                session()->setFlashdata('error', 'Passwords do not match.');
                return redirect()->to('/admin/settings');
            }
            $this->auth->updatePassword((int)$admin['id'], $newPass);
            session()->setFlashdata('success', 'Admin password updated successfully.');
            return redirect()->to('/admin/settings');
        }

        if ($section === 'policy') {
            $slug = trim((string)$this->request->getPost('policy_slug'));
            $title = trim((string)$this->request->getPost('policy_title'));
            $body = (string)$this->request->getPost('policy_body');
            if ($slug !== '' && $title !== '') {
                $policyModel = new PolicyModel();
                $policyModel->save([
                    'slug'       => $slug,
                    'title'      => $title,
                    'body_html'  => $body,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Policy "' . $title . '" saved.');
            }
            return redirect()->to('/admin/settings');
        }

        $fields = [
            'store_name'              => trim((string)$this->request->getPost('store_name') ?: 'Okina Craft'),
            'store_email'             => trim((string)$this->request->getPost('store_email') ?: 'support@okinacraft.com'),
            'whatsapp_number'         => preg_replace('/\D+/', '', (string)$this->request->getPost('whatsapp_number') ?: '918380808435'),
            'instagram_handle'        => trim((string)$this->request->getPost('instagram_handle') ?: 'okinacraft', '@ '),
            'business_address'        => trim((string)$this->request->getPost('business_address') ?: 'Hingoli, Maharashtra'),
            'business_hours'          => trim((string)$this->request->getPost('business_hours') ?: 'Monday–Saturday, 10:00 AM–6:00 PM'),
            'shipping_flat_rate'      => (string)max(0, (float)$this->request->getPost('shipping_flat_rate') ?: 60),
            'free_shipping_threshold' => (string)max(0, (float)$this->request->getPost('free_shipping_threshold') ?: 999),
            'upi_id'                  => trim((string)$this->request->getPost('upi_id')),
            'video_heading'           => trim((string)$this->request->getPost('video_heading') ?: 'How to Buy from Okina Craft'),
        ];

        foreach ($fields as $k => $v) {
            $settingModel->setSetting($k, $v);
        }

        session()->setFlashdata('success', 'Store settings updated.');
        return redirect()->to('/admin/settings');
    }
}
