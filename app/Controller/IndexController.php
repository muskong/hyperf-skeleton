<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;
use App\Services\LoggerService;

class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

		// LoggerService::info('test', ['test1']);

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
			'userVideoIcon' => 'https://images.69hypercar.com/mkt/video-icon.png',
			'userPoster'    => 'https://images.69hypercar.com/mkt/user_poster.png',
			'userSrc'       => 'https://images.69hypercar.com/mkt/home_vidoe.mp4',
			'dealerPoster'  => 'https://images.69hypercar.com/mkt/dealer_poster.png',
			'dealerSrc'     => 'https://images.69hypercar.com/mkt/home_vidoe.mp4',
        ];
    }
}
