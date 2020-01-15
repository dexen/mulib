<?php

namespace dexen\mulib;

class Testcase
{
	function assertIntEquals(string $subject, $v, int $expected)
	{
		$this->assertDataType($subject, $v, 'integer');

		if ($v !== $expected)
			throw new AssertionFailed(
				sprintf('%s === %d, got %d',
					$subject, $expected, $v ) );
	}

	function assertStringEquals(string $subject, $v, string $expected)
	{
		$this->assertDataType($subject, $v, 'string');

		if ($v !== $expected)
			throw new AssertionFailed(
				sprintf('%s === %s, got %s',
					$subject, $expected, $v ) );
	}

		# functionally equivalent of ->assertStringEquals()
		# but geared towards position-wise comparision
	function assertStringEqualsPositionWise(string $subject, $v, string $expected)
	{
		$isTextual = function(string $str) : bool
		{
			if (strpos($str, '\0') !== false)
				return false;

			$lineLenCutoff = 120;
			$a = explode('\n', $str);
			if (max(array_map('strlen', $a)) > $lineLenCutoff)
				return false;
			return true;
		};

		if ($isTextual($v) && $isTextual($expected))
			return $this->assertTextualStringEqualsPositionWise($subject, $v, $expected);
		else
			return $this->assertBinaryStringEqualsPositionWise($subject, $v, $expected);
	}

		# helper for ->assertStringEqualsPositionWise()
	protected
	function assertBinaryStringEqualsPositionWise(string $subject, $v, string $expected)
	{
		$this->assertDataType($subject, $v, 'string');

		if ($v !== $expected) {
echo 'EXPECTED:', PHP_EOL;
echo $expected, PHP_EOL, '========', PHP_EOL;
echo 'ACTUAL:', PHP_EOL;
echo $v, PHP_EOL, '======', PHP_EOL;

			throw new AssertionFailed(
				sprintf('%s === %s, got %s',
					$subject, $expected, $v ) ); }
	}

	function assertDataType(string $subject, $v, string $expected)
	{
		if (gettype($v) === $expected)
			return;

		throw new AssertionFailed(
			sprintf('data type (%s) === "%s" got "%s", for "%s"',
				$subject, $expected, gettype($v), $v ) );
	}

	function assertPregMatch(string $subject, $v, string $re)
	{
		$this->assertDataType($subject, $v, 'string');

		if (!preg_match($re, $v))
			throw new AssertionFailed(
				sprintf('%s failed to preg_match() "%s": "%s"',
					$subject, $re, $v ) );
	}

	function assertStrlen(string $subject, $v, int $expected)
	{
		$this->assertDataType($subject, $v, 'string');

		$len = strlen($v);
		if ($len !== $expected)
			throw new AssertionFailed(
				sprintf('strlen(%s) === %d, got %d, for "%s"',
					$subject, $expected, $len, $v ) );
	}
}
