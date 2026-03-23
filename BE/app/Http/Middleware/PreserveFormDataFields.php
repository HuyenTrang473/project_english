<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreserveFormDataFields extends Closure
{
    public function handle(Request $request, Closure $next)
    {
        // For FormData requests with files, don't let ConvertEmptyStringsToNull middleware
        // convert actual content to null just because LaravelMiddleware might interpret it as empty

        // If this is a multipart/form-data request for API, preserve exact values
        if ($request->isMethod('POST') || $request->isMethod('PUT')) {
            if ($request->is('api/*') && $request->hasAny(['tieu_de', 'mo_ta', 'noi_dung'])) {
                // Log the raw input to avoid it being modified
                $input = $request->all();

                // Ensure fields are not accidentally nullified
                if (isset($input['noi_dung']) && !$input['noi_dung']) {
                    // If noi_dung is falsy but was provided, it might've been stripped
                    \Log::warning('noi_dung was falsy after middleware:', [
                        'original' => $request->getContent(),
                        'current' => $input['noi_dung'] ?? 'NOT SET',
                    ]);
                }
            }
        }

        return $next($request);
    }
}
