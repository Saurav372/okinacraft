<?php

namespace App\Libraries;

use Config\Database;

class CartService
{
    private const SESSION_KEY = '_cs_cart';

    private function session()
    {
        return \Config\Services::session();
    }

    public function rawItems(): array
    {
        return $this->session()->get(self::SESSION_KEY) ?? [];
    }

    public function count(): int
    {
        $total = 0;
        foreach ($this->rawItems() as $item) {
            $total += max(0, (int)($item['quantity'] ?? 0));
        }
        return $total;
    }

    public function add(
        int $productId,
        ?int $variantId = null,
        int $quantity = 1,
        string $customDesignPath = '',
        string $customNote = '',
        string $variantImage = ''
    ): array {
        $catalog = new CatalogService();
        $product = $catalog->getProductById($productId);
        if (!$product || $product['status'] !== 'active') {
            return ['ok' => false, 'error' => 'Product not found.'];
        }
        if (!(int)$product['available']) {
            return ['ok' => false, 'error' => 'This product is currently sold out.'];
        }

        $variant = null;
        if ($variantId) {
            foreach ($product['variants'] as $v) {
                if ((int)$v['id'] === $variantId) {
                    $variant = $v;
                    break;
                }
            }
        }
        if (!$variant && !empty($product['variants'])) {
            foreach ($product['variants'] as $v) {
                if ((int)$v['available']) {
                    $variant = $v;
                    break;
                }
            }
            $variant = $variant ?? $product['variants'][0];
        }

        if ($variant && !(int)$variant['available']) {
            return ['ok' => false, 'error' => 'Selected variant is sold out.'];
        }

        $qty = max(1, min(10000, $quantity));
        $vid = $variant ? (int)$variant['id'] : 0;
        $key = md5($productId . '|' . $vid . '|' . $customDesignPath . '|' . trim($customNote));

        $items = $this->rawItems();
        if (isset($items[$key])) {
            $items[$key]['quantity'] = min(10000, (int)$items[$key]['quantity'] + $qty);
            if (!empty($variantImage)) {
                $items[$key]['variant_image'] = $variantImage;
            }
        } else {
            $items[$key] = [
                'key'                => $key,
                'product_id'         => $productId,
                'variant_id'         => $vid,
                'quantity'           => $qty,
                'custom_design_path' => $customDesignPath,
                'custom_note'        => trim($customNote),
                'variant_image'      => $variantImage,
            ];
        }
        $this->session()->set(self::SESSION_KEY, $items);

        $summary = $this->summary();
        $addedItem = null;
        foreach ($summary['items'] as $it) {
            if ($it['key'] === $key) {
                $addedItem = $it;
                break;
            }
        }

        return [
            'ok'   => true,
            'item' => $addedItem,
            'cart' => $summary,
        ];
    }

    public function updateQuantity(string $key, int $quantity): array
    {
        $items = $this->rawItems();
        if (!isset($items[$key])) {
            return ['ok' => false, 'error' => 'Item not found in cart.'];
        }
        if ($quantity <= 0) {
            unset($items[$key]);
        } else {
            $items[$key]['quantity'] = min(10000, $quantity);
        }
        $this->session()->set(self::SESSION_KEY, $items);
        return ['ok' => true, 'cart' => $this->summary()];
    }

    public function remove(string $key): array
    {
        $items = $this->rawItems();
        unset($items[$key]);
        $this->session()->set(self::SESSION_KEY, $items);
        return ['ok' => true, 'cart' => $this->summary()];
    }

    public function clear(): void
    {
        $this->session()->remove(self::SESSION_KEY);
    }

    public function summary(): array
    {
        $catalog = new CatalogService();
        $raw = $this->rawItems();
        $hydrated = [];
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $count = 0;
        $allFreeDelivery = true;

        foreach ($raw as $key => $row) {
            $product = $catalog->getProductById((int)$row['product_id']);
            if (!$product) {
                continue;
            }
            $variant = null;
            foreach ($product['variants'] as $v) {
                if ((int)$v['id'] === (int)$row['variant_id']) {
                    $variant = $v;
                    break;
                }
            }
            if (!$variant && !empty($product['variants'])) {
                $variant = $product['variants'][0];
            }

            $baseUnitPrice = $variant ? (float)$variant['price'] : (float)$product['price'];
            $qty = max(1, (int)$row['quantity']);
            $unitPrice = $baseUnitPrice;
            $tierDiscountPercent = 0.0;
            $appliedTier = null;

            // Check Bulk Tier Pricing
            if (!empty($product['enable_bulk_pricing']) && !empty($product['bulk_tiers'])) {
                foreach ($product['bulk_tiers'] as $tier) {
                    $minQ = (int)($tier['min_qty'] ?? 1);
                    $maxQ = isset($tier['max_qty']) && $tier['max_qty'] !== null ? (int)$tier['max_qty'] : PHP_INT_MAX;
                    if ($qty >= $minQ && $qty <= $maxQ) {
                        $unitPrice = (float)($tier['unit_price'] ?? $baseUnitPrice);
                        $tierDiscountPercent = (float)($tier['discount_percent'] ?? 0.0);
                        $appliedTier = $tier;
                        break;
                    }
                }
            }

            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;
            $count += $qty;

            $gstRate = isset($product['gst_rate']) && $product['gst_rate'] !== null && $product['gst_rate'] !== '' ? (float)$product['gst_rate'] : 5.00;
            $hsnCode = !empty($product['hsn_code']) ? (string)$product['hsn_code'] : '6109';
            $lineTax = round($lineTotal * ($gstRate / 100), 2);
            $taxTotal += $lineTax;

            if (!(int)$product['free_delivery']) {
                $allFreeDelivery = false;
            }

            $variantTitle = $variant ? (string)$variant['title'] : '';
            if ($variantTitle === 'Default Title') {
                $variantTitle = '';
            }

            $savings = max(0, ($baseUnitPrice - $unitPrice) * $qty);

            $hydrated[] = [
                'key'                   => $key,
                'product_id'            => (int)$product['id'],
                'variant_id'            => $variant ? (int)$variant['id'] : null,
                'handle'                => $product['handle'],
                'title'                 => $product['title'],
                'variant_title'         => $variantTitle,
                'image'                 => $this->resolveVariantImage($product, $variant, (string)($row['variant_image'] ?? '')),
                'base_unit_price'       => $baseUnitPrice,
                'unit_price'            => $unitPrice,
                'tier_discount_percent' => $tierDiscountPercent,
                'is_bulk_discounted'    => ($tierDiscountPercent > 0 || $unitPrice < $baseUnitPrice),
                'savings'               => $savings,
                'quantity'              => $qty,
                'line_total'            => $lineTotal,
                'gst_rate'              => $gstRate,
                'hsn_code'              => $hsnCode,
                'line_tax'              => $lineTax,
                'tax_amount'            => $lineTax,
                'free_delivery'         => (int)$product['free_delivery'],
                'custom_design_path'    => (string)($row['custom_design_path'] ?? ''),
                'custom_note'           => (string)($row['custom_note'] ?? ''),
            ];
        }

        $flatShipping = (float)store_setting('shipping_flat_rate', '60');
        $freeThreshold = (float)store_setting('free_shipping_threshold', '999');

        if ($count === 0) {
            $shipping = 0.0;
        } elseif ($allFreeDelivery || ($freeThreshold > 0 && $subtotal >= $freeThreshold)) {
            $shipping = 0.0;
        } else {
            $shipping = $flatShipping;
        }

        return [
            'items'             => $hydrated,
            'count'             => $count,
            'subtotal'          => $subtotal,
            'tax_total'         => $taxTotal,
            'shipping'          => $shipping,
            'total'             => $subtotal + $taxTotal + $shipping,
            'all_free_delivery' => $allFreeDelivery,
            'free_threshold'    => $freeThreshold,
        ];
    }

    public function resolveVariantImage(array $product, ?array $variant, string $preferredImage = ''): string
    {
        if ($preferredImage !== '') {
            $parsed = parse_url($preferredImage, PHP_URL_PATH) ?? $preferredImage;
            $clean = '/' . ltrim($parsed, '/');
            if (file_exists(FCPATH . ltrim($clean, '/'))) {
                return $clean;
            }
        }

        if (!$variant) {
            return (string)($product['featured_image'] ?? ($product['images'][0]['src'] ?? ''));
        }

        $images = $product['images'] ?? [];

        // 1. Detect color name from product options or variant fields
        $color = '';
        $options = $product['options'] ?? [];
        foreach ($options as $idx => $opt) {
            $name = strtolower((string)($opt['name'] ?? ''));
            if (str_contains($name, 'color') || str_contains($name, 'colour')) {
                $pos = (int)($opt['position'] ?? ($idx + 1));
                $val = trim((string)($variant['option' . $pos] ?? ''));
                if ($val !== '') {
                    $color = $val;
                    break;
                }
            }
        }

        if ($color === '') {
            $knownColors = [
                'beige', 'black', 'maroon', 'white', 'off-white', 'blue', 'navy',
                'green', 'red', 'grey', 'gray', 'yellow', 'orange', 'pink', 'purple',
                'lavender', 'olive', 'charcoal'
            ];
            $candidates = [
                (string)($variant['option1'] ?? ''),
                (string)($variant['option2'] ?? ''),
                (string)($variant['option3'] ?? ''),
                (string)($variant['title'] ?? ''),
            ];
            foreach ($candidates as $cand) {
                $lowerCand = strtolower(trim($cand));
                foreach ($knownColors as $kc) {
                    if ($lowerCand === $kc || str_contains($lowerCand, $kc)) {
                        $color = $cand;
                        break 2;
                    }
                }
            }
        }

        if ($color !== '') {
            $lowerColor = strtolower(trim($color));

            // A. Check product images by 'alt' tag
            foreach ($images as $img) {
                if (!empty($img['alt']) && stripos($img['alt'], $color) !== false) {
                    return (string)$img['src'];
                }
            }

            // B. Check product images by known color hashes/keywords in filename
            $colorHashes = [
                'maroon'    => ['74d844', 'maroon'],
                'beige'     => ['02cfe9', 'beige'],
                'black'     => ['c6ed92', 'fa716d', 'black'],
                'white'     => ['1a6c77', 'white'],
                'off-white' => ['fe889d', '1a6c77', 'off-white'],
            ];

            foreach ($colorHashes as $cKey => $patterns) {
                if (str_contains($lowerColor, $cKey)) {
                    foreach ($images as $img) {
                        $src = (string)($img['src'] ?? '');
                        foreach ($patterns as $pattern) {
                            if (stripos($src, $pattern) !== false) {
                                return $src;
                            }
                        }
                    }
                }
            }

            // C. Check if any image in $images has color name in src
            foreach ($images as $img) {
                if (stripos((string)($img['src'] ?? ''), $lowerColor) !== false) {
                    return (string)$img['src'];
                }
            }

            // D. Global fallback across known apparel assets in public/assets/media/products
            $isHoodie = str_contains(strtolower((string)($product['title'] ?? '')), 'hoodie');
            $globalColorMap = [
                'maroon'    => '/assets/media/products/custom-design-240-gsm-premium-74d844-1.png',
                'beige'     => '/assets/media/products/custom-design-240-gsm-premium-02cfe9-1.png',
                'black'     => $isHoodie
                    ? '/assets/media/products/custom-design-350-gsm-premium-fa716d-1.png'
                    : '/assets/media/products/premium-240-gsm-oversized-t-sh-c6ed92-1.png',
                'white'     => $isHoodie
                    ? '/assets/media/products/custom-design-350-gsm-premium-fe889d-1.png'
                    : '/assets/media/products/premium-240-gsm-oversized-t-sh-1a6c77-1.png',
                'off-white' => '/assets/media/products/custom-design-350-gsm-premium-fe889d-1.png',
            ];
            foreach ($globalColorMap as $cKey => $path) {
                if (str_contains($lowerColor, $cKey) && file_exists(FCPATH . ltrim($path, '/'))) {
                    return $path;
                }
            }
        }

        return (string)($product['featured_image'] ?? ($images[0]['src'] ?? ''));
    }
}
