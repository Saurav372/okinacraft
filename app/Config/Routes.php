<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ---- Storefront Routes ----
$routes->get('/', 'StoreController::home');
$routes->get('collections/all', 'StoreController::collection/all');
$routes->get('collections/(:segment)', 'StoreController::collection/$1');
$routes->get('products/(:segment)', 'StoreController::product/$1');
$routes->get('search', 'StoreController::search');

$routes->get('cart', 'StoreController::cart');
$routes->post('cart', 'StoreController::cartPost');

$routes->get('checkout', 'StoreController::checkout');
$routes->post('checkout', 'StoreController::checkoutPost');
$routes->get('order/(:segment)', 'StoreController::orderSuccess/$1');

$routes->get('pages/contact', 'StoreController::contact');
$routes->post('pages/contact', 'StoreController::contactPost');

$routes->get('policies/(:segment)', 'StoreController::policy/$1');
$routes->get('account', 'StoreController::account');
$routes->post('account', 'StoreController::account');

// ---- JSON API Routes ----
$routes->get('api/search/suggest', 'ApiController::searchSuggest');
$routes->get('api/cart', 'ApiController::cartGet');
$routes->post('api/cart/add', 'ApiController::cartAdd');
$routes->post('api/cart/update', 'ApiController::cartUpdate');
$routes->post('api/cart/remove', 'ApiController::cartRemove');

// ---- Admin Auth (Public) ----
$routes->get('admin/login', 'AdminController::loginForm');
$routes->post('admin/login', 'AdminController::loginPost');
$routes->post('admin/logout', 'AdminController::logout');

// ---- Admin Dashboard & Management (Protected) ----
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('products', 'AdminController::products');
    $routes->get('products/new', 'AdminController::productForm');
    $routes->post('products/new', 'AdminController::productSave');
    $routes->get('products/(:num)/edit', 'AdminController::productForm/$1');
    $routes->post('products/(:num)/edit', 'AdminController::productSave/$1');
    $routes->post('products/(:num)/delete', 'AdminController::productDelete/$1');

    $routes->get('collections', 'AdminController::collections');
    $routes->post('collections', 'AdminController::collectionSave');

    $routes->get('orders', 'AdminController::orders');
    $routes->get('orders/(:num)', 'AdminController::orderDetail/$1');
    $routes->post('orders/(:num)', 'AdminController::orderUpdate/$1');

    $routes->get('messages', 'AdminController::messages');
    $routes->post('messages', 'AdminController::messageAction');

    $routes->get('settings', 'AdminController::settings');
    $routes->post('settings', 'AdminController::settingsSave');
});
