<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncColorAndSizeOptions extends Migration
{
    public function up()
    {
        $db = $this->db;
        if (!$db->tableExists('products') || !$db->tableExists('product_variants') || !$db->tableExists('product_images')) {
            return;
        }

        // 1. Sync 240 GSM Oversized T-Shirt (Handle: custom-design-240-gsm-premium-oversized-t-shirt-100-french-terry-cotton)
        $tshirtHandle = 'custom-design-240-gsm-premium-oversized-t-shirt-100-french-terry-cotton';
        $tshirt = $db->table('products')->where('handle', $tshirtHandle)->get()->getRowArray();
        
        // If not found by exact handle, find first 240 GSM product
        if (!$tshirt) {
            $tshirt = $db->table('products')->like('title', '240 GSM')->get()->getRowArray();
        }

        if ($tshirt) {
            $pid = (int)$tshirt['id'];

            // Set clean Color + Size options
            $tshirtOptions = [
                ['name' => 'Color', 'position' => 1, 'values' => ['Beige', 'Maroon', 'Black', 'White']],
                ['name' => 'Size', 'position' => 2, 'values' => ['S', 'M', 'L', 'XL', '2XL']],
            ];
            $db->table('products')->where('id', $pid)->update([
                'options_json' => json_encode($tshirtOptions, JSON_UNESCAPED_UNICODE),
            ]);

            // Re-sync 20 color x size variants
            $colors = ['Beige', 'Maroon', 'Black', 'White'];
            $colorCodes = ['Beige' => 'BEI', 'Maroon' => 'MRN', 'Black' => 'BLK', 'White' => 'WHT'];
            $sizes = ['S', 'M', 'L', 'XL', '2XL'];

            // Check if 20 variants already exist
            $existingVars = $db->table('product_variants')->where('product_id', $pid)->get()->getResultArray();
            $needsRebuild = count($existingVars) < 20;
            if (!$needsRebuild) {
                // Check if any variant has empty option2 or option1 is a size
                foreach ($existingVars as $ev) {
                    if (empty($ev['option2']) || in_array($ev['option1'], $sizes, true) || $ev['option1'] === 'Pink') {
                        $needsRebuild = true;
                        break;
                    }
                }
            }

            if ($needsRebuild) {
                $db->table('product_variants')->where('product_id', $pid)->delete();
                $pos = 1;
                foreach ($colors as $c) {
                    foreach ($sizes as $s) {
                        $code = $colorCodes[$c] ?? strtoupper(substr($c, 0, 3));
                        $db->table('product_variants')->insert([
                            'product_id'       => $pid,
                            'title'            => "{$c} / {$s}",
                            'option1'          => $c,
                            'option2'          => $s,
                            'option3'          => null,
                            'sku'              => "OKN-240T-{$code}-{$s}",
                            'price'            => (float)($tshirt['price'] ?: 549.00),
                            'compare_at_price' => isset($tshirt['compare_at_price']) ? (float)$tshirt['compare_at_price'] : null,
                            'inventory_qty'    => 50,
                            'available'        => 1,
                            'position'         => $pos++,
                        ]);
                    }
                }
            }

            // Sync all 16 images for the 4 colors if not all present
            $existingImgCount = $db->table('product_images')->where('product_id', $pid)->countAllResults();
            if ($existingImgCount < 16) {
                $db->table('product_images')->where('product_id', $pid)->delete();
                $tshirtImages = [
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-02cfe9-1.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Beige'],
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-02cfe9-2.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Beige - Side View'],
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-02cfe9-3.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Beige - Back View'],
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-02cfe9-4.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Beige - Fabric Details'],
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-74d844-1.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Maroon'],
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-74d844-2.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Maroon - Side View'],
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-74d844-3.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Maroon - Back View'],
                    ['src' => '/assets/media/products/custom-design-240-gsm-premium-74d844-4.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Maroon - Fabric Details'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-c6ed92-1.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Black'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-c6ed92-2.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Black - Side View'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-c6ed92-3.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Black - Back View'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-c6ed92-4.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - Black - Fabric Details'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-1a6c77-1.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - White'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-1a6c77-2.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - White - Side View'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-1a6c77-3.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - White - Back View'],
                    ['src' => '/assets/media/products/premium-240-gsm-oversized-t-sh-1a6c77-4.png', 'alt' => 'Custom Design 240 GSM Oversized T-Shirt - White - Fabric Details'],
                ];
                $ipos = 1;
                foreach ($tshirtImages as $img) {
                    $db->table('product_images')->insert([
                        'product_id' => $pid,
                        'src'        => $img['src'],
                        'alt'        => $img['alt'],
                        'width'      => 1000,
                        'height'     => 1000,
                        'position'   => $ipos++,
                    ]);
                }
            }
        }

        // 2. Sync Hoodie (Black and Off-White)
        $hoodie = $db->table('products')->like('title', 'Hoodie')->get()->getRowArray();
        if ($hoodie) {
            $hPid = (int)$hoodie['id'];
            $hoodieOptions = [
                ['name' => 'Color', 'position' => 1, 'values' => ['Black', 'Off-White']],
                ['name' => 'Size', 'position' => 2, 'values' => ['S', 'M', 'L', 'XL', '2XL']],
            ];
            $db->table('products')->where('id', $hPid)->update([
                'options_json' => json_encode($hoodieOptions, JSON_UNESCAPED_UNICODE),
            ]);

            $hVars = $db->table('product_variants')->where('product_id', $hPid)->get()->getResultArray();
            $hNeedsRebuild = count($hVars) < 10;
            if (!$hNeedsRebuild) {
                foreach ($hVars as $hv) {
                    if (empty($hv['option2']) || in_array($hv['option1'], ['S', 'M', 'L', 'XL', '2XL'], true)) {
                        $hNeedsRebuild = true;
                        break;
                    }
                }
            }

            if ($hNeedsRebuild) {
                $db->table('product_variants')->where('product_id', $hPid)->delete();
                $hColors = ['Black', 'Off-White'];
                $hSizes = ['S', 'M', 'L', 'XL', '2XL'];
                $hPos = 1;
                foreach ($hColors as $hc) {
                    foreach ($hSizes as $hs) {
                        $cCode = ($hc === 'Black') ? 'BLK' : 'OFW';
                        $db->table('product_variants')->insert([
                            'product_id'       => $hPid,
                            'title'            => "{$hc} / {$hs}",
                            'option1'          => $hc,
                            'option2'          => $hs,
                            'option3'          => null,
                            'sku'              => "OKN-350H-{$cCode}-{$hs}",
                            'price'            => (float)($hoodie['price'] ?: 799.00),
                            'compare_at_price' => isset($hoodie['compare_at_price']) ? (float)$hoodie['compare_at_price'] : null,
                            'inventory_qty'    => 50,
                            'available'        => 1,
                            'position'         => $hPos++,
                        ]);
                    }
                }
            }

            $hImgCount = $db->table('product_images')->where('product_id', $hPid)->countAllResults();
            if ($hImgCount < 8) {
                $db->table('product_images')->where('product_id', $hPid)->delete();
                $hoodieImages = [
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fa716d-1.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Black'],
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fa716d-2.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Black - Side View'],
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fa716d-3.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Black - Back View'],
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fa716d-4.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Black - Details'],
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fe889d-1.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Off-White'],
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fe889d-2.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Off-White - Side View'],
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fe889d-3.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Off-White - Back View'],
                    ['src' => '/assets/media/products/custom-design-350-gsm-premium-fe889d-4.png', 'alt' => 'Custom Design 350 GSM Premium Hoodie - Off-White - Details'],
                ];
                $hIpos = 1;
                foreach ($hoodieImages as $hImg) {
                    $db->table('product_images')->insert([
                        'product_id' => $hPid,
                        'src'        => $hImg['src'],
                        'alt'        => $hImg['alt'],
                        'width'      => 1000,
                        'height'     => 1000,
                        'position'   => $hIpos++,
                    ]);
                }
            }
        }

        // 3. Clean up any other products where "Pink" was inserted into "Size" option values
        $allProducts = $db->table('products')->get()->getResultArray();
        foreach ($allProducts as $p) {
            $opts = json_decode((string)($p['options_json'] ?? '[]'), true) ?: [];
            $modified = false;
            foreach ($opts as &$opt) {
                if (stripos($opt['name'], 'size') !== false && !empty($opt['values'])) {
                    $newVals = [];
                    foreach ($opt['values'] as $v) {
                        if (strtolower(trim($v)) !== 'pink') {
                            $newVals[] = $v;
                        } else {
                            $modified = true;
                        }
                    }
                    $opt['values'] = array_values($newVals);
                }
            }
            if ($modified) {
                $db->table('products')->where('id', (int)$p['id'])->update([
                    'options_json' => json_encode($opts, JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
    }

    public function down()
    {
        // Safe no-op
    }
}
