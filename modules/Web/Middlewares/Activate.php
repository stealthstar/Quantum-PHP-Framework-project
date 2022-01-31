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
 * @since 2.6.0
 */

namespace Modules\Web\Middlewares;

use Quantum\Middleware\QtMiddleware;
use Quantum\Factory\ModelFactory;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Shared\Models\User;
use Quantum\Di\Di;
use Closure;

/**
 * Class Activate
 * @package Modules\Web\Middlewares
 */
class Activate extends QtMiddleware
{

    /**
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     * @param \Closure $next
     * @return mixed
     */
    public function apply(Request $request, Response $response, Closure $next)
    {
        list($lang, $token) = route_args();

        if (!$this->checkToken($token)) {
            hook('errorPage');
            stop();
        }

        $request->set('activation_token', $token);

        return $next($request, $response);
    }

    /**
     * Check token
     * @param string $token
     * @return bool
     */
    private function checkToken(string $token): bool
    {
        $modelFactory = Di::get(ModelFactory::class);
        $userModel = $modelFactory->get(User::class);

        return !empty($userModel->findOneBy('activation_token', $token)->asArray());
    }

}
