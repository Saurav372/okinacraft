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

$routes->get('checkout', 'StoreController::checkout', ['filter' => 'customerauth']);
$routes->post('checkout', 'StoreController::checkoutPost', ['filter' => 'customerauth']);
$routes->get('order/(:segment)', 'StoreController::orderSuccess/$1');

$routes->get('pages/contact', 'StoreController::contact');
$routes->post('pages/contact', 'StoreController::contactPost');

$routes->get('policies/(:segment)', 'StoreController::policy/$1');

// ---- Customer Auth (Public) ----
$routes->get('account/login', 'CustomerAuthController::login');
$routes->post('account/login', 'CustomerAuthController::loginPost');
$routes->get('account/register', 'CustomerAuthController::register');
$routes->post('account/register', 'CustomerAuthController::registerPost');
$routes->post('account/google', 'CustomerAuthController::googleCallback');
$routes->get('account/forgot-password', 'CustomerAuthController::forgotPassword');
$routes->post('account/forgot-password', 'CustomerAuthController::forgotPasswordPost');
$routes->get('account/reset-password/(:segment)', 'CustomerAuthController::resetPassword/$1');
$routes->post('account/reset-password/(:segment)', 'CustomerAuthController::resetPasswordPost/$1');
$routes->get('account/logout', 'CustomerAuthController::logout');
$routes->post('account/logout', 'CustomerAuthController::logout');
$routes->get('account/track', 'StoreController::trackGuestOrder');
$routes->post('account/track', 'StoreController::trackGuestOrder');

// ---- Customer Portal (Protected by customerauth filter) ----
$routes->group('account', ['filter' => 'customerauth'], static function ($routes) {
    $routes->get('/', 'CustomerAccountController::dashboard');
    $routes->get('orders/(:num)', 'CustomerAccountController::orderDetail/$1');
    $routes->post('addresses', 'CustomerAccountController::addressSave');
    $routes->post('addresses/(:num)/delete', 'CustomerAccountController::addressDelete/$1');
    $routes->post('addresses/(:num)/default', 'CustomerAccountController::addressSetDefault/$1');
    $routes->post('profile', 'CustomerAccountController::profileUpdate');
});

// ---- Wishlist Routes ----
$routes->get('wishlist', 'WishlistController::index');
$routes->post('api/wishlist/toggle', 'WishlistController::apiToggle');
$routes->post('api/wishlist/sync', 'WishlistController::apiSync');
$routes->get('api/wishlist/data', 'WishlistController::apiData');

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
