<?php

namespace App\Services;

use Exception;
use Hyperf\Support\Filesystem\Filesystem;

class UploadService
{
	static function upload($uploadedFile)
	{
		try {
			$filename =  md5_file($uploadedFile->getRealPath());
			$fullname = sprintf("%s.%s", md5_file($uploadedFile->getRealPath()), $uploadedFile->guessExtension());

			$path = sprintf("public/%s", $filename[0]);
			$pathfile = sprintf("%s/%s", $path, $fullname);

			if (!Filesystem::exists($pathfile)) {
				Filesystem::put($path, $uploadedFile);
			}

			return $pathfile;
		} catch (Exception $e) {
			LoggerService::exception($e, __METHOD__ . __LINE__);
			return '';
		}
	}
}
