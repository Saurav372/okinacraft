<?php

namespace App\Filters;

use App\Libraries\CustomerAuthService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new CustomerAuthService();
        if (!$auth->check()) {
            $uri = (string)$request->getUri();
            // Relative path for redirect
            $path = '/' . ltrim($request->getUri()->getPath(), '/');
            $query = $request->getUri()->getQuery();
            $fullUrl = $query !== '' ? $path . '?' . $query : $path;

            session()->setFlashdata('error', 'Please log in or create an account to continue.');
            return redirect()->to('/account/login?redirect=' . rawurlencode($fullUrl));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
