<?php

namespace App\Controllers;

use App\Libraries\CartService;
use App\Libraries\CatalogService;

class ApiController extends BaseController
{
    protected CatalogService $catalog;
    protected CartService $cart;

    public function __construct()
    {
        $this->catalog = new CatalogService();
        $this->cart = new CartService();
    }

    public function searchSuggest()
    {
        $q = trim((string)$this->request->getGet('q'));
        if (mb_strlen($q) < 1) {
            return $this->response->setJSON(['ok' => true, 'products' => []]);
        }

        $matches = $this->catalog->searchProducts($q, 6);
        $out = [];
        foreach ($matches as $p) {
            $out[] = [
                'id'        => (int)$p['id'],
                'handle'    => $p['handle'],
                'title'     => $p['title'],
                'price'     => money_format_inr($p['price']),
                'available' => (bool)$p['available'],
                'image'     => $p['featured_image'] ? base_url($p['featured_image']) : '',
                'url'       => base_url('/products/' . $p['handle']),
            ];
        }
        return $this->response->setJSON(['ok' => true, 'products' => $out, 'query' => $q]);
    }

    public function cartGet()
    {
        return $this->response->setJSON(['ok' => true, 'cart' => $this->cart->summary()]);
    }

    public function cartAdd()
    {
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
                return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => $up['error']]);
            }
            $customDesignPath = $up['path'];
        }

        $res = $this->cart->add($productId, $variantId, $qty, $customDesignPath, $customNote, $variantImage);
        if (!$res['ok']) {
            return $this->response->setStatusCode(422)->setJSON($res);
        }

        if (!empty($res['item']['image'])) {
            $res['item']['image_url'] = base_url($res['item']['image']);
        }
        $res['item']['unit_price_formatted'] = money_format_inr($res['item']['unit_price'] ?? 0);
        return $this->response->setJSON($res);
    }

    public function cartUpdate()
    {
        $key = (string)$this->request->getPost('key');
        $qty = (int)$this->request->getPost('quantity');
        $res = $this->cart->updateQuantity($key, $qty);
        return $this->response->setStatusCode($res['ok'] ? 200 : 404)->setJSON($res);
    }

    public function cartRemove()
    {
        $key = (string)$this->request->getPost('key');
        $res = $this->cart->remove($key);
        return $this->response->setJSON($res);
    }
}
