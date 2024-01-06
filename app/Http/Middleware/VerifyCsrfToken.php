<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{

    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/payment/sslcommerz/pay-via-ajax',
        '/payment/sslcommerz/success',
        '/payment/sslcommerz/cancel',
        '/payment/sslcommerz/fail',

        '/app/payment/sslcommerz/success',
        '/app/payment/sslcommerz/cancel',
        '/app/payment/sslcommerz/fail',

        '/payment/sslcommerz/ipn',
        '/paytm/payment/status',
        '/paytm/deposit/status',
        '/paytm/subscription/status',
        '/install/*',
        'mobilpay/confirm/deposit',
        'mobilpay/confirm/payment',
        '/xapi/*',
        '/scorm/*',
        '/razerms/*',
        '/page-builder/new-upload',
        '/flutterwave/webhook/*',
        '/flutterwave/callback/*',
        'callback/*',
        "mollie/*"

    ];
}
