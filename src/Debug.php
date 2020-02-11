<?php

namespace dexen\mulib;

class Debug
{
	static
	function td(...$vars)
	{
		echo 'td()::';
		echo '<pre>';
		foreach ($vars as $v) {
			ob_start();
			var_dump($v);
			$str = ob_get_clean();
			echo H($str); }
		echo '::td()';
		echo PHP_EOL;
#debug_display_backtrace(1);
		static::displayBacktrace();
		die();
	}

	static
	function hd($v)
	{
		if (is_string($v))
			$a = unpack('C*', $v);
		elseif (is_array($v))
			$a = $v;
		else
			throw new Exception('unsupported type: ' .get_type($v));

		$aa = array_map('dechex', $a);
		td($aa);
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
