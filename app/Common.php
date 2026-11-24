<?php

/**
 * Okina Craft — Global Common Helpers (CodeIgniter 4)
 */

if (!function_exists('e')) {
    function e(?string $val): string
    {
        return esc((string)$val);
    }
}

if (!function_exists('money_format_inr')) {
    function money_format_inr(float|int|string $amount): string
    {
        $num = number_format((float)$amount, 2, '.', ',');
        return 'Rs. ' . $num . ' INR';
    }
}

if (!function_exists('money_short_inr')) {
    function money_short_inr(float|int|string $amount): string
    {
        return 'Rs. ' . number_format((float)$amount, 2, '.', ',');
    }
}

if (!function_exists('money_clean_inr')) {
    function money_clean_inr(float|int|string $amount): string
    {
        return '₹' . number_format(round((float)$amount), 0, '.', ',');
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-') ?: 'item-' . substr(bin2hex(random_bytes(4)), 0, 8);
    }
}

if (!function_exists('sanitize_rich_html')) {
    function sanitize_rich_html(?string $html): string
    {
        if (!$html) {
            return '';
        }
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input)[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = preg_replace('/\son[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/javascript\s*:/i', '', $html) ?? $html;
        return $html;
    }
}

if (!function_exists('store_setting')) {
    function store_setting(string $key, string $default = ''): string
    {
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            $db = \Config\Database::connect();
            try {
                $rows = $db->table('settings')->get()->getResultArray();
                foreach ($rows as $r) {
                    $cache[$r['setting_key']] = (string)$r['setting_value'];
                }
            } catch (\Throwable) {
                // Table might not exist yet during migration
            }
        }
        return isset($cache[$key]) && $cache[$key] !== '' ? $cache[$key] : $default;
    }
}

if (!function_exists('handle_ci4_image_upload')) {
    function handle_ci4_image_upload(?\CodeIgniter\HTTP\Files\UploadedFile $file, string $subDir = 'designs'): array
    {
        if (!$file || !$file->isValid()) {
            return ['ok' => false, 'error' => 'No file uploaded or upload failed.'];
        }

        if ($file->getSizeByUnit('mb') > 10) {
            return ['ok' => false, 'error' => 'Image size must be smaller than 10 MB.'];
        }

        $mime = $file->getMimeType();
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mime])) {
            return ['ok' => false, 'error' => 'Only JPG, PNG, and WebP images are allowed.'];
        }

        $safeSub = preg_replace('/[^a-z0-9_-]/i', '', $subDir) ?: 'designs';
        $targetDir = ROOTPATH . 'public/uploads/' . $safeSub;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $file->move($targetDir, $newName);

        return [
            'ok'   => true,
            'path' => '/uploads/' . $safeSub . '/' . $newName,
            'name' => $newName,
        ];
    }
}

if (!function_exists('color_hex_by_name')) {
    function color_hex_by_name(string $name): array
    {
        $clean = strtolower(trim($name));
        $colorMap = [
            'maroon'       => ['hex' => '#6B0F1E', 'light' => false],
            'burgundy'     => ['hex' => '#722F37', 'light' => false],
            'wine'         => ['hex' => '#722F37', 'light' => false],
            'beige'        => ['hex' => '#DDB68B', 'light' => true],
            'sand'         => ['hex' => '#D4B996', 'light' => true],
            'tan'          => ['hex' => '#D2B48C', 'light' => true],
            'khaki'        => ['hex' => '#C3B091', 'light' => true],
            'cream'        => ['hex' => '#FBF7EE', 'light' => true],
            'off-white'    => ['hex' => '#F4F2EC', 'light' => true],
            'off white'    => ['hex' => '#F4F2EC', 'light' => true],
            'white'        => ['hex' => '#FFFFFF', 'light' => true],
            'black'        => ['hex' => '#1F1F1E', 'light' => false],
            'grey'         => ['hex' => '#8E8E93', 'light' => false],
            'gray'         => ['hex' => '#8E8E93', 'light' => false],
            'dark grey'    => ['hex' => '#4A4A4A', 'light' => false],
            'dark gray'    => ['hex' => '#4A4A4A', 'light' => false],
            'light grey'   => ['hex' => '#D1D1D6', 'light' => true],
            'light gray'   => ['hex' => '#D1D1D6', 'light' => true],
            'charcoal'     => ['hex' => '#36454F', 'light' => false],
            'navy'         => ['hex' => '#1B2A4A', 'light' => false],
            'navy blue'    => ['hex' => '#1B2A4A', 'light' => false],
            'royal blue'   => ['hex' => '#1E40AF', 'light' => false],
            'blue'         => ['hex' => '#2563EB', 'light' => false],
            'sky blue'     => ['hex' => '#60A5FA', 'light' => true],
            'light blue'   => ['hex' => '#93C5FD', 'light' => true],
            'teal'         => ['hex' => '#0D9488', 'light' => false],
            'cyan'         => ['hex' => '#06B6D4', 'light' => false],
            'red'          => ['hex' => '#E83535', 'light' => false],
            'crimson'      => ['hex' => '#DC2626', 'light' => false],
            'coral'        => ['hex' => '#F87171', 'light' => false],
            'green'        => ['hex' => '#16A34A', 'light' => false],
            'dark green'   => ['hex' => '#14532D', 'light' => false],
            'forest green' => ['hex' => '#15803D', 'light' => false],
            'olive'        => ['hex' => '#65A30D', 'light' => false],
            'sage'         => ['hex' => '#9CA986', 'light' => true],
            'mint'         => ['hex' => '#A7F3D0', 'light' => true],
            'yellow'       => ['hex' => '#FACC15', 'light' => true],
            'mustard'      => ['hex' => '#EAB308', 'light' => false],
            'orange'       => ['hex' => '#F97316', 'light' => false],
            'saffron'      => ['hex' => '#FF9933', 'light' => false],
            'pink'         => ['hex' => '#EC4899', 'light' => false],
            'rose'         => ['hex' => '#FB7185', 'light' => false],
            'purple'       => ['hex' => '#9333EA', 'light' => false],
            'lavender'     => ['hex' => '#C084FC', 'light' => true],
            'brown'        => ['hex' => '#78350F', 'light' => false],
            'chocolate'    => ['hex' => '#5C4033', 'light' => false],
            'multicolor'   => ['hex' => 'linear-gradient(135deg, #f87171, #fbbf24, #34d399, #60a5fa, #a78bfa)', 'light' => false],
            'transparent'  => ['hex' => 'repeating-conic-gradient(#e5e7eb 0% 25%, #ffffff 0% 50%) 50% / 10px 10px', 'light' => true],
        ];

        if (isset($colorMap[$clean])) {
            return $colorMap[$clean];
        }

        foreach ($colorMap as $k => $val) {
            if (strpos($clean, $k) !== false) {
                return $val;
            }
        }

        return ['hex' => '#E2E8F0', 'light' => true];
    }
}

if (!function_exists('send_store_email')) {
    function send_store_email(string $to, string $subject, string $body, bool $isHtml = false): array
    {
        $smtpHost = store_setting('smtp_host', '');
        $smtpUser = store_setting('smtp_user', '');
        $smtpPass = store_setting('smtp_pass', '');
        $smtpPort = (int)store_setting('smtp_port', '465');
        $smtpCrypto = store_setting('smtp_crypto', 'ssl');
        $storeName = store_setting('store_name', 'Okina Craft');
        $fromEmail = !empty($smtpUser) ? $smtpUser : store_setting('store_email', 'support@okinacraft.com');

        $email = \Config\Services::email();

        if ($smtpHost !== '' && $smtpUser !== '') {
            $config = [
                'protocol'       => 'smtp',
                'SMTPHost'       => $smtpHost,
                'SMTPUser'       => $smtpUser,
                'SMTPPass'       => $smtpPass,
                'SMTPPort'       => $smtpPort,
                'SMTPCrypto'     => $smtpCrypto,
                'mailType'       => $isHtml ? 'html' : 'text',
                'charset'        => 'utf-8',
                'newline'        => "\r\n",
                'CRLF'           => "\r\n",
                'SMTPTimeout'    => 10,
            ];
            $email->initialize($config);
        } else {
            $email->initialize([
                'mailType' => $isHtml ? 'html' : 'text',
                'charset'  => 'utf-8',
                'newline'  => "\r\n",
                'CRLF'     => "\r\n",
            ]);
        }

        $email->setFrom($fromEmail, $storeName);
        $replyTo = store_setting('store_email', '');
        if (!empty($replyTo) && strtolower($replyTo) !== strtolower($fromEmail)) {
            $email->setReplyTo($replyTo, $storeName);
        }
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($body);

        try {
            $sent = $email->send();
            if ($sent) {
                return ['ok' => true];
            }
            return ['ok' => false, 'error' => $email->printDebugger(['headers'])];
        } catch (\Throwable $e) {
            log_message('error', 'send_store_email error: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}

