<?php

namespace App\Controllers;

use App\Libraries\CatalogService;
use App\Libraries\CustomerAuthService;
use App\Models\CustomerWishlistModel;
use App\Models\ProductModel;
use Config\Database;

class WishlistController extends BaseController
{
    private CustomerAuthService $auth;

    public function __construct()
    {
        CatalogService::ensureBootstrapped();
        $this->auth = new CustomerAuthService();
    }

    /**
     * Dedicated Wishlist Page (/wishlist)
     */
    public function index()
    {
        $customer = $this->auth->customer();
        $products = [];
        $wishlistIds = [];

        if ($customer) {
            $wishlistModel = new CustomerWishlistModel();
            $wishlistIds = $wishlistModel->getProductIds((int)$customer['id']);

            if (!empty($wishlistIds)) {
                $db = Database::connect();
                $products = $db->table('products')
                    ->select('products.*, product_images.src as image_src')
                    ->join('product_images', 'product_images.product_id = products.id AND product_images.position = 1', 'left')
                    ->whereIn('products.id', $wishlistIds)
                    ->where('products.status', 'active')
                    ->get()
                    ->getResultArray();
            }
        }

        return view('store/wishlist', [
            'pageTitle'    => 'My Wishlist – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav'    => 'wishlist',
            'customer'     => $customer,
            'products'     => $products,
            'wishlistIds'  => $wishlistIds,
        ]);
    }

    /**
     * API: Toggle Wishlist Item (Add or Remove)
     */
    public function apiToggle()
    {
        $productId = (int)$this->request->getPost('product_id');
        if ($productId <= 0) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Invalid product.']);
        }

        $customer = $this->auth->customer();
        if (!$customer) {
            // Guest mode: UI handles local storage
            return $this->response->setJSON([
                'ok'       => true,
                'is_guest' => true,
            ]);
        }

        $customerId = (int)$customer['id'];
        $wishlistModel = new CustomerWishlistModel();
        $added = $wishlistModel->toggle($customerId, $productId);
        $allIds = $wishlistModel->getProductIds($customerId);

        return $this->response->setJSON([
            'ok'          => true,
            'is_guest'    => false,
            'in_wishlist' => $added,
            'count'       => count($allIds),
            'wishlist_ids'=> $allIds,
        ]);
    }

    /**
     * API: Sync Guest LocalStorage Items into Customer Database
     */
    public function apiSync()
    {
        $customer = $this->auth->customer();
        if (!$customer) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Not authenticated.']);
        }

        $raw = $this->request->getJSON(true) ?: $this->request->getPost('product_ids');
        $productIds = is_array($raw) ? ($raw['product_ids'] ?? $raw) : [];

        $wishlistModel = new CustomerWishlistModel();
        $syncedIds = $wishlistModel->syncGuestItems((int)$customer['id'], (array)$productIds);

        return $this->response->setJSON([
            'ok'           => true,
            'count'        => count($syncedIds),
            'wishlist_ids' => $syncedIds,
        ]);
    }

    /**
     * API: Fetch product details for guest wishlist rendering
     */
    public function apiData()
    {
        $idsParam = (string)$this->request->getGet('ids');
        $ids = array_filter(array_map('intval', explode(',', $idsParam)), fn($id) => $id > 0);

        if (empty($ids)) {
            return $this->response->setJSON(['ok' => true, 'products' => []]);
        }

        $db = Database::connect();
        $products = $db->table('products')
            ->select('products.id, products.title, products.handle, products.price, products.compare_at_price, products.available, product_images.src as image_src')
            ->join('product_images', 'product_images.product_id = products.id AND product_images.position = 1', 'left')
            ->whereIn('products.id', $ids)
            ->where('products.status', 'active')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['ok' => true, 'products' => $products]);
    }
}
