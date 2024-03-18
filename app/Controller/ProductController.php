<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Work\Product;
use App\Model\Work\ProductTag;
use App\Services\LoggerService;
use App\Services\RespondService;
use App\Services\Wechat\NumberService;
use Exception;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix:'mobile')]
class ProductController
{
	#[RequestMapping(path: 'products', methods: ['POST'])]
	public function list(RequestInterface $request)
	{
		$list = ProductTag::queryMerchant()
			->whereHas('product',function($query){
				$query->where('status', Product::Status1);
			})
			->with('product')
			->get(['id', 'product_id', 'tag_id', 'sale', 'price']);

		return RespondService::Success($list);
	}


	#[RequestMapping(path: 'check', methods: ['POST'])]
	public function checkMobile(RequestInterface $request, ): ResponseInterface
	{
		try {
		// $member = $request->getAttribute('member');

		$mobile = $request->input('mobile');
		$isp = NumberService::mobile($mobile);
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

		return RespondService::Success($product);
		} catch (Exception $e) {
			LoggerService::exception($e, __METHOD__ . __LINE__);
			return RespondService::Error($e->getMessage());
		}
	}
}
