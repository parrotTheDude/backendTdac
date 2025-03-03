<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'accounts.thatdisabilityadventurecompany.com.au/webhook/subscription-change',
    ];
}