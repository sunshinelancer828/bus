<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/checkout/mercadopago/notify',
        '/paytm-callback',
        '/razorpay-callback',
        '/flutter/notify',
        '/ssl/notify',
        '/ssl/cancle',
        '/user/mercadopago/notify',
        '/user/paytm/notify',
        '/user/razorpay/notify',
        '/uflutter/notify',
        '/user/ssl/notify',
        '/user/ssl/cancle',
        '/user/deposit/mercadopago/notify',
        '/user/deposit/paytm/notify',
        '/user/deposit/razorpay/notify',
        '/dflutter/notify',
        '/user/deposit/ssl/notify',
        '/user/deposit/ssl/cancle',
    ];
}
