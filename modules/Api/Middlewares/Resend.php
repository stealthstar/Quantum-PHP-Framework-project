<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.8.0
 */

namespace Modules\Api\Middlewares;

use Quantum\Middleware\QtMiddleware;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Closure;

/**
 * Class Resend
 * @package Modules\Web\Middlewares
 */
class Resend extends QtMiddleware
{

    /**
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     * @param \Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        if (!route_param('code')) {
            $response->json([
                'status' => 'error',
                'message' => t('validation.required', 'code')
            ]);

            stop();
        }

        return $next($request, $response);
    }

}
