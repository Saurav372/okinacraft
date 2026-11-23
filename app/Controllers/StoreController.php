<?php

namespace App\Controllers;

use App\Libraries\CartService;
use App\Libraries\CatalogService;
use App\Models\ContactMessageModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PolicyModel;
use Config\Database;

class StoreController extends BaseController
{
    protected CatalogService $catalog;
    protected CartService $cart;

    public function __construct()
    {
        $this->catalog = new CatalogService();
        $this->cart = new CartService();
    }

    public function home()
    {
        $collections = $this->catalog->getHomeCollections();

        return view('store/home', [
            'pageTitle'   => store_setting('store_name', 'Okina Craft'),
            'activeNav'   => 'home',
            'collections' => $collections,
        ]);
    }

    public function collection(string $handle = 'all')
    {
        $request = $this->request;
        $sort = (string)$request->getGet('sort_by') ?: 'featured';
        $avail = $request->getGet('availability') !== null && $request->getGet('availability') !== '' ? (string)$request->getGet('availability') : null;
        $minP = $request->getGet('price_min') !== null && $request->getGet('price_min') !== '' ? (float)$request->getGet('price_min') : null;
        $maxP = $request->getGet('price_max') !== null && $request->getGet('price_max') !== '' ? (float)$request->getGet('price_max') : null;

        if ($handle === 'all') {
            $collection = [
                'handle'       => 'all',
                'title'        => 'Products',
                'description'  => '',
                'card_shape'   => 'standard',
                'desktop_cols' => 4,
            ];
            $products = $this->catalog->getAllProducts($sort, $avail, $minP, $maxP);
        } else {
            $collection = $this->catalog->getCollectionByHandle($handle);
            if (!$collection) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            $products = $this->catalog->getProductsByCollection((int)$collection['id'], $sort, $avail, $minP, $maxP);
        }

        return view('store/collection', [
            'pageTitle'      => $collection['title'] . ' – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav'      => 'catalog',
            'collection'     => $collection,
            'products'       => $products,
            'allCollections' => $this->catalog->getAllCollections(),
            'sort'           => $sort,
            'availability'   => $avail,
            'priceMin'       => $minP,
            'priceMax'       => $maxP,
            'priceBounds'    => $this->catalog->getPriceBounds(),
        ]);
    }

    public function product(string $handle)
    {
        $product = $this->catalog->getProductByHandle($handle);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $related = $this->catalog->getRelatedProducts($product, 4);

        return view('store/product', [
            'pageTitle' => $product['title'] . ' – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => 'catalog',
            'product'   => $product,
            'related'   => $related,
        ]);
    }

    public function search()
    {
        $q = trim((string)$this->request->getGet('q'));
        $products = $q !== '' ? $this->catalog->searchProducts($q, 60) : [];

        return view('store/search', [
            'pageTitle' => ($q !== '' ? "Search: {$q} – " : 'Search – ') . store_setting('store_name', 'Okina Craft'),
            'activeNav' => '',
            'query'     => $q,
            'products'  => $products,
        ]);
    }

    public function cart()
    {
        return view('store/cart', [
            'pageTitle' => 'Your Shopping Cart – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => '',
            'cart'      => $this->cart->summary(),
        ]);
    }

    public function cartPost()
    {
        $action = (string)$this->request->getPost('action') ?: 'add';

        if ($action === 'update') {
            $key = (string)$this->request->getPost('key');
            $qty = (int)$this->request->getPost('quantity');
            $this->cart->updateQuantity($key, $qty);
            return redirect()->to('/cart');
        }

        if ($action === 'remove') {
            $key = (string)$this->request->getPost('key');
            $this->cart->remove($key);
            return redirect()->to('/cart');
        }

        // Add
        $productId = (int)$this->request->getPost('product_id');
        $variantId = $this->request->getPost('variant_id') ? (int)$this->request->getPost('variant_id') : null;
        $qty = max(1, (int)($this->request->getPost('quantity') ?: 1));
        $customNote = trim((string)$this->request->getPost('custom_note'));
        $variantImage = trim((string)$this->request->getPost('variant_image'));
        $customDesignPath = '';

        $designFile = $this->request->getFile('custom_design');
        if ($designFile && $designFile->isValid() && !$designFile->hasMoved()) {
            $up = handle_ci4_image_upload($designFile, 'designs');
            if (!$up['ok']) {
                session()->setFlashdata('error', $up['error']);
                return redirect()->back();
            }
            $customDesignPath = $up['path'];
        }

        $res = $this->cart->add($productId, $variantId, $qty, $customDesignPath, $customNote, $variantImage);
        if (!$res['ok']) {
            session()->setFlashdata('error', $res['error']);
            return redirect()->back();
        }

        if ($this->request->getPost('buy_now')) {
            return redirect()->to('/checkout');
        }

        session()->setFlashdata('success', 'Item added to your cart.');
        return redirect()->to('/cart');
    }

    public function checkout()
    {
        $cartSummary = $this->cart->summary();
        if (empty($cartSummary['items'])) {
            session()->setFlashdata('info', 'Your cart is empty. Add items before checking out.');
            return redirect()->to('/collections/all');
        }

        $customerAuth = new \App\Libraries\CustomerAuthService();
        if (!$customerAuth->check()) {
            session()->setFlashdata('info', 'Please sign in or create an account to proceed to checkout.');
            return redirect()->to('/account/login?redirect=' . rawurlencode('/checkout'));
        }

        $customer = $customerAuth->customer();
        $addrModel = new \App\Models\CustomerAddressModel();
        $defaultAddr = $addrModel->getDefault((int)$customer['id']);

        $old = session()->getFlashdata('checkout_old') ?? [];
        if (empty($old)) {
            $old = [
                'name'    => $customer['name'] ?? '',
                'email'   => $customer['email'] ?? '',
                'phone'   => $customer['phone'] ?? '',
                'address' => $defaultAddr['address_line'] ?? '',
                'city'    => $defaultAddr['city'] ?? '',
                'state'   => $defaultAddr['state'] ?? '',
                'pincode' => $defaultAddr['pincode'] ?? '',
            ];
        }

        return view('store/checkout', [
            'pageTitle' => 'Checkout – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => '',
            'cart'      => $cartSummary,
            'customer'  => $customer,
            'old'       => $old,
        ]);
    }

    public function checkoutPost()
    {
        $cartSummary = $this->cart->summary();
        if (empty($cartSummary['items'])) {
            return redirect()->to('/cart');
        }

        $codEnabled = store_setting('cod_enabled', '0') === '1';
        $allowedPaymentMethods = $codEnabled ? ['upi', 'cod'] : ['upi'];
        $selectedPaymentMethod = (string)$this->request->getPost('payment_method');
        $paymentMethod = in_array($selectedPaymentMethod, $allowedPaymentMethods, true) ? $selectedPaymentMethod : 'upi';

        $customerAuth = new \App\Libraries\CustomerAuthService();
        $customerId = $customerAuth->id();

        $data = [
            'name'           => trim((string)$this->request->getPost('name')),
            'email'          => trim((string)$this->request->getPost('email')),
            'phone'          => trim((string)$this->request->getPost('phone')),
            'address'        => trim((string)$this->request->getPost('address')),
            'city'           => trim((string)$this->request->getPost('city')),
            'state'          => trim((string)$this->request->getPost('state')),
            'pincode'        => trim((string)$this->request->getPost('pincode')),
            'payment_method' => $paymentMethod,
            'note'           => trim((string)$this->request->getPost('note')),
        ];

        $errors = [];
        if ($data['name'] === '') $errors[] = 'Full name is required.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email address is required.';
        if (strlen(preg_replace('/\D+/', '', $data['phone'])) < 10) $errors[] = 'Valid 10-digit mobile number is required.';
        if ($data['address'] === '') $errors[] = 'Shipping address is required.';
        if ($data['city'] === '') $errors[] = 'City is required.';
        if ($data['state'] === '') $errors[] = 'State is required.';
        if (!preg_match('/^\d{6}$/', $data['pincode'])) $errors[] = 'Valid 6-digit PIN code is required.';

        if ($errors) {
            session()->setFlashdata('checkout_old', $data);
            session()->setFlashdata('error', implode(' ', $errors));
            return redirect()->to('/checkout');
        }

        $db = Database::connect();
        $db->transStart();
        try {
            $nextSeq = (int)($db->table('orders')->selectMax('id')->get()->getRowArray()['id'] ?? 0) + 1001;
            $yearSpan = date('Y') . '/' . substr((string)((int)date('Y') + 1), 2);
            $orderNumber = 'OC-' . $yearSpan . '-' . $nextSeq;

            $orderModel = new OrderModel();
            $orderModel->insert([
                'customer_id'      => $customerId,
                'order_number'     => $orderNumber,
                'customer_name'    => $data['name'],
                'customer_email'   => $data['email'],
                'customer_phone'   => $data['phone'],
                'shipping_address' => $data['address'],
                'shipping_city'    => $data['city'],
                'shipping_state'   => $data['state'],
                'shipping_pincode' => $data['pincode'],
                'payment_method'   => $data['payment_method'],
                'payment_status'   => $data['payment_method'] === 'upi' ? 'awaiting_verification' : 'pending',
                'order_status'     => 'pending',
                'subtotal'         => (float)$cartSummary['subtotal'],
                'tax_amount'       => (float)($cartSummary['tax_total'] ?? 0.0),
                'shipping_cost'    => (float)$cartSummary['shipping'],
                'total'            => (float)$cartSummary['total'],
                'customer_note'    => $data['note'],
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $orderId = (int)$db->insertID();

            $itemModel = new OrderItemModel();
            foreach ($cartSummary['items'] as $it) {
                $itemModel->insert([
                    'order_id'           => $orderId,
                    'product_id'         => $it['product_id'],
                    'variant_id'         => $it['variant_id'],
                    'product_title'      => $it['title'],
                    'variant_title'      => $it['variant_title'],
                    'product_handle'     => $it['handle'],
                    'image_src'          => $it['image'],
                    'unit_price'         => (float)$it['unit_price'],
                    'quantity'           => (int)$it['quantity'],
                    'gst_rate'           => (float)($it['gst_rate'] ?? 5.0),
                    'tax_amount'         => (float)($it['line_tax'] ?? 0.0),
                    'line_total'         => (float)$it['line_total'],
                    'custom_design_path' => $it['custom_design_path'],
                    'custom_note'        => $it['custom_note'],
                ]);
            }

            $db->transComplete();
            $this->cart->clear();

            $recent = session()->get('_recent_orders') ?? [];
            $recent[] = $orderNumber;
            session()->set('_recent_orders', $recent);

            return redirect()->to('/order/' . urlencode($orderNumber));
        } catch (\Throwable $e) {
            $db->transRollback();
            session()->setFlashdata('checkout_old', $data);
            session()->setFlashdata('error', 'Failed to create order: ' . $e->getMessage());
            return redirect()->to('/checkout');
        }
    }

    public function orderSuccess(string $orderNumber)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->where('order_number', $orderNumber)->first();
        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $itemModel = new OrderItemModel();
        $order['items'] = $itemModel->where('order_id', (int)$order['id'])->findAll();

        $recent = session()->get('_recent_orders') ?? [];
        $isAdmin = (new \App\Libraries\AuthService())->check();
        if (!in_array($orderNumber, $recent, true) && !$isAdmin) {
            session()->setFlashdata('info', 'Please verify your Order Number and Email/Phone to view this order.');
            return redirect()->to('/account?order=' . urlencode($orderNumber));
        }

        return view('store/order_success', [
            'pageTitle' => 'Order Confirmed #' . $order['order_number'] . ' – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => '',
            'order'     => $order,
        ]);
    }

    public function contact()
    {
        return view('store/contact', [
            'pageTitle' => 'Contact – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => 'contact',
        ]);
    }

    public function contactPost()
    {
        $name = trim((string)$this->request->getPost('name'));
        $email = trim((string)$this->request->getPost('email'));
        $phone = trim((string)$this->request->getPost('phone'));
        $subject = trim((string)$this->request->getPost('subject')) ?: 'General Enquiry';
        $message = trim((string)$this->request->getPost('message'));

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
            session()->setFlashdata('error', 'Please provide your name, valid email, and message.');
            return redirect()->to('/contact');
        }

        $model = new ContactMessageModel();
        $model->insert([
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'subject'    => $subject,
            'message'    => $message,
            'status'     => 'unread',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Thank you! Your message has been received. Our team will get back to you shortly.');
        return redirect()->to('/contact');
    }

    public function policy(string $slug)
    {
        $model = new PolicyModel();
        $policy = $model->find($slug);
        if (!$policy) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('store/policy', [
            'pageTitle' => $policy['title'] . ' – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => '',
            'policy'    => $policy,
        ]);
    }

    public function trackGuestOrder()
    {
        $order = null;
        $searched = false;
        $orderNo = trim((string)($this->request->getPost('order_number') ?: $this->request->getGet('order')));
        $contact = trim((string)$this->request->getPost('contact'));

        if ($this->request->getMethod() === 'POST') {
            $searched = true;
            if ($orderNo !== '' && $contact !== '') {
                $orderModel = new OrderModel();
                $found = $orderModel->where('order_number', $orderNo)->first();
                if ($found) {
                    $c = strtolower(trim($contact));
                    $emailMatch = strtolower((string)$found['customer_email']) === $c;
                    $phoneDigits = preg_replace('/\D+/', '', (string)$found['customer_phone']);
                    $inputDigits = preg_replace('/\D+/', '', $c);
                    $phoneMatch = $inputDigits !== '' && strlen($inputDigits) >= 6 && str_ends_with($phoneDigits, $inputDigits);

                    if ($emailMatch || $phoneMatch) {
                        $itemModel = new OrderItemModel();
                        $found['items'] = $itemModel->where('order_id', (int)$found['id'])->findAll();
                        $order = $found;
                    }
                }
            }
        }

        return view('store/account/track', [
            'pageTitle' => 'Track Order – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => '',
            'order'     => $order,
            'searched'  => $searched,
            'orderNo'   => $orderNo,
            'contact'   => $contact,
        ]);
    }
}
