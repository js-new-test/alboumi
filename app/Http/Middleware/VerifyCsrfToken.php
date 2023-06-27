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
        '/api/v1/*','/getLangSpecificData','/benefits/success','/benefits/cancel','/eventorders/benefits/success','/eventorders/benefits/cancel','/eventEnq/benefits/success','/eventEnq/benefits/cancel'
    ];
}
