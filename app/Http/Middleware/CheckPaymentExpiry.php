<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expiryDate = Carbon::createFromDate(2024, 11, 1);

        $vnwa_key = env('VNWA_KEY');

        if (now()->greaterThanOrEqualTo($expiryDate) && is_null($vnwa_key)) {
            abort(403, 'Demo has expired, please activate the website!');
        }
        if (!empty($vnwa_key) && $vnwa_key == 'vnwassg@gh!38abaysa') {
            return $next($request);

        }else{
            if (is_null($vnwa_key) || $vnwa_key !== 'vnwassg@gh!38abaysa') {
                abort(403, 'Demo has expired, please activate !');
            }
        }
        return $next($request);


    }
}
