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

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    public const SERVER_ERROR = 500;

	public const DuplicateRequest = 100001;
	public const Empty    = 100002;
	public const PasswordDB       = 100003;
	public const PasswordVerify   = 100004;
	public const PasswordCode     = 100005;
	public const InputString      = 100006;
	public const MemberExists     = 100007;
	public const MemberRegister   = 100008;
	public const SendSMS          = 100009;

	// 同程
	public const TcArgs             = 1000;  // 参数异常; 参数异常
	public const TcUser           = 1001;    // 无效商户; 无效商户
	public const TcUserDisable    = 1002;    // 商品未开通; 商品未开通
	public const TcSign             = 1003;  // 验签失败; 验签失败
	public const TcRequestExpire    = 1004;  // 请求时间过期; 时间戳过期
	public const TcDuplicateOrderNo = 1005;  // 订单号重复; 订单号重复
	public const TcBalance          = 1006;  // 余额不足; 余额不足
	public const TcRechargeAccount  = 1007;  // 充值账号格式有误; 充值账号格式有误
	public const TcNotOrderNo       = 1008;  // 订单不存在; 订单不存在
	public const TcException        = 1999;  // 异常错误; 异常错误，建议人工处理或查询订单状态
}
