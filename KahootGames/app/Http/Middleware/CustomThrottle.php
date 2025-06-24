<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class CustomThrottle extends ThrottleRequests
{
    public function __construct(RateLimiter $limiter)
    {
        parent::__construct($limiter);
    }

    protected function handleRequest($request, Closure $next, array $limits)
    {
        foreach ($limits as $limit) {
            $key = $limit->key;
            $maxAttempts = $limit->maxAttempts;
            $decaySeconds = $limit->decaySeconds ?? ($limit->decayMinutes * 60 ?? 60);

            if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
                $retryAfter = $this->limiter->availableIn($key);

                abort(response()->json([
                    'message' => 'Too many requests. Try again later.',
                ], Response::HTTP_TOO_MANY_REQUESTS)->withHeaders([
                    'Retry-After' => $retryAfter,
                ]));
            }

            $this->limiter->hit($key, $decaySeconds);
        }

        return $next($request);
    }
}
