<?php
declare(strict_types=1);

namespace App\Services;

use Exception;
use Hyperf\Context\ApplicationContext;
use Hyperf\Logger\LoggerFactory;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

class LoggerService
{
	protected LoggerInterface $logger;

	public function __construct(string $name = 'app')
	{
		$this->logger = ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name);
	}

	static function exception(Exception $e, string $message, bool $hasTrace = false)
	{
		$data = [
			$e->getMessage(),
			$e->getCode(),
			$e->getLine(),
			$e->getFile(),
			$e->getCode(),
		];
		if ($hasTrace) {
			$data[] = $e->getTrace();
		}
		return (new LoggerService)->logger->error($message, $data);
	}

	public static function __callStatic($method, $args){
		$log = (new LoggerService)->logger;

		return $log->$method($args[0],$args[1]);
	}

	static function info($message, $context = [])
	{
		return (new LoggerService)->logger->info($message, $context);
	}
	// static function error($udid, $message, $context = [])
	// {
	// 	return (new LoggerService)->logger->error($message, $context);
	// }
}
