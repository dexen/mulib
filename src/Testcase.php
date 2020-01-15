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
