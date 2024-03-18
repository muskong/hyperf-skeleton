<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Services\LoggerService;
use App\Services\RespondService;
use App\Services\Wechat\MiniService;
use Exception;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class LoginController
{

	#[RequestMapping(path: 'wechat', methods: ['POST'])]
	public function WechatLogin(RequestInterface $request): ResponseInterface
	{
		try {
			$code   = $request->input('code');
			$invite = $request->input('invite', 999);

			$openId = MiniService::codeExchangeOpenid($code);
			if (!$openId) {
				throw new Exception('openId 错误');
			}

			$user = User::firstOrCreate([
				'openid' => $openId
			], [
				'nickname' => '我最棒',
				'password' => 'iloveyou',
				'invite_id' => $invite ?: 999,
				'level' => 0,
				'status' => 1,
			]);
			if (!$user->status) {
				throw new Exception('账号错误');
			}
			// if ($user->blacklist) {
			// 	throw new DataException('账号错误', $request->all(), Error::MemberExists);
			// }

			$data = $user->generateToken();

			return RespondService::Success($data);
		} catch (Exception $e) {
			LoggerService::exception($e, __METHOD__ . __LINE__);
			return RespondService::Error($e->getMessage());
		}
	}
}
