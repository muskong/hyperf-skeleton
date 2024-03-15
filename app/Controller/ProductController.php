<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Work\Product;
use App\Services\LoggerService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

#[Controller]
class ProductController
{

	#[RequestMapping(path: 'check', methods: ['POST'])]
	public function checkMobile(RequestInterface $request, ResponseInterface $response): PsrResponseInterface
	{
		$member1 = $request->getAttribute('member.memberId');
		$member = $request->getHeaderLine('Authorization');

		$mobile = $request->input('mobile');
		// $isp = NumberService::mobile($mobile);
		$isp = [
			'area'       => "上海-上海", // 手机号归属地
			'isp'        => '电信', //  手机号转网运营商
			'oldisp'     => '联通', // 手机号原运营商
			'transfer'   => 0,
			'virtualIsp' => 0
		];

		$product       = Product::queryTags($isp)
			->with([
				'prices' => function ($query) {
					$query->select('id', 'product_id', 'tag_id', 'sale', 'price')
						->queryMerchant();
				}
			])
			->first(['id', 'udid', 'title', 'note']);
		$product->area = $isp['area'];
		$product->member1 = $member1;
		$product->member = $member;

		return $response->json($product);
	}
}
