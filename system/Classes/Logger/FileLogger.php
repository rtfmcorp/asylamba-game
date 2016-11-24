<?php

namespace Asylamba\Classes\Logger;

use Asylamba\Classes\Worker\Logger;

class FileLogger extends Logger {
	/**
	 * {@inheritdoc}
	 */
	public function log($message, $level = self::LOG_LEVEL_DEBUG, $type = self::LOG_TYPE_PHP) {
		$datetime = new \DateTime();
		file_put_contents(
			"{$this->rootPath}/public/log/$type/{$datetime->format('Y-m-d')}.log",
			$this->formatMessage($message, $level, $datetime),
			FILE_APPEND
		);
	}
}