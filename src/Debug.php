<?php

namespace Successup\Mulib;

class Debug
{
	static
	function td(...$vars)
	{
		echo 'td()::';
		echo '<pre>';
		foreach ($vars as $v)
			var_dump($v);
		echo '::td()';
		echo PHP_EOL;
#debug_display_backtrace(1);
		static::displayBacktrace();
		die();
	}

	static
	function nicePhpScriptPathname(string $filePN = null) : ?string
	{
		if ($filePN === null)
			$filePN;
		$strip = INSTALL_DIR .'/';
		return str_replace($strip, '', $filePN);
	}

	static
	function displayBacktrace(int $skip = 0)
	{
		$a = debug_backtrace();
		$a = array_slice($a, $skip);

		foreach ($a as $frame) {
			if (empty($frame['file']))
				echo '??';
			else
				echo H(static::nicePhpScriptPathname($frame['file']));
			echo ':';
			if (empty($frame['line']))
				echo '??';
			else
				echo H($frame['line']);
			echo PHP_EOL; }
	}
}
