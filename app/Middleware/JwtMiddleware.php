<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\TokenService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

class JwtMiddleware implements MiddlewareInterface
{
    protected HttpResponse $response;
	public function __construct(protected ContainerInterface $container, HttpResponse $response)
	{
		$this->response = $response;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		// if ($request->is('public/*')) {
		// 	return $handler->handle($request);
		// }
		$token = $request->getHeaderLine('Authorization');

		if (strrpos($token, 'Bearer ') !== false) {
			$token = ltrim($token, 'Bearer ');

			$member = TokenService::decode($token);
			if (!$member) {
				return $this->response->json('token fail');
			}

			$request = $request->withAttribute('member', $member);
		}


		return $handler->handle($request);
	}
}
