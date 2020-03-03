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

	static
	function basicHtmlErrorHandler(int $errno, string $errstr, string $errfile, int $errline)
	{
		$H = fn(string $str) => print(htmlspecialchars($str));
		$S = fn(...$a) => $H(sprintf(...$a));
		$L = fn($fmt, ...$a) => $S($fmt .PHP_EOL, ...$a);

		$E = function(int $errno) : string
		{
			return [
				E_ERROR => 'ERROR',
				E_WARNING => 'WARNING',
				E_PARSE => 'PARSE ERROR',
				E_NOTICE  => 'NOTICE',
				E_CORE_ERROR => 'CORE ERROR',
				E_CORE_WARNING => 'CORE WARNING',
				E_COMPILE_ERROR => 'COMPILE ERROR',
				E_COMPILE_WARNING => 'COMPILE WARNING',
				E_USER_ERROR => 'USER ERROR',
				E_USER_WARNING => 'USER WARNING',
				E_USER_NOTICE => 'USER NOTICE',
				E_STRICT => 'STRICT NOTICE',
				E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
				E_DEPRECATED => 'DEPRECIATION WARNING',
				E_USER_DEPRECATED => 'USER DEPRECIATION WARNING',
			][$errno] ?? 'OTHER ERROR';
		};

		$P = [ __CLASS__, 'nicePhpScriptPathname' ];

		echo '<pre>', PHP_EOL;

		$L('%s: %s', $E($errno), $errstr);
		$L('%s:%s', $P($errfile), $errline);

		$a = debug_backtrace();
		if ($a)
			echo '--', PHP_EOL;
		foreach ($a as $frame)
			$L('	%s:%s', $P($frame['file']??null), $frame['line']??null);

		echo '--', PHP_EOL;
		die('Uncaught error, quitting.');
	}

	static
	function basicHtmlExceptionHandler(\Throwable $T)
	{
		$H = fn(string $str) => print(htmlspecialchars($str));
		$S = fn(...$a) => $H(sprintf(...$a));
		$L = fn($fmt, ...$a) => $S($fmt .PHP_EOL, ...$a);

		$P = [ __CLASS__, 'nicePhpScriptPathname' ];

		echo '<pre>', PHP_EOL;

		$L('%s: %s', get_class($T), $T->getMessage());
		$L('%s:%s', $P($T->getFile()), $T->getLine());

		if ($T->getTrace())
			echo '--', PHP_EOL;
		foreach ($T->getTrace() as $frame)
			$L('	%s:%s', $P($frame['file']??null), $frame['line']??null);

		echo '--', PHP_EOL;
		die('Uncaught exception, quitting.');
	}
}
