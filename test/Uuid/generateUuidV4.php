<?php

namespace Successup\Mulib;

class Test_Uuid_generateUuidV4 extends Testcase
{
	protected $str;
	protected $a;

	function __construct()
	{
			# since we want to test certain textual (or bitwise) properties of the contents
			# generated at random, the best i could think of is generate A BUNCH and test them all
		$cnt = 256;
		$this->a = [];
		foreach (range(1, $cnt) as $n)
			$this->a[] = Uuid::generateUuidV4();

		$this->str = $this->a[0];
	}

	function test_strlen()
	{
		foreach ($this->a as $v)
			$this->assertStrlen('uuid', $v, 36);
	}

	function test_hexadecimal_structure()
	{
		foreach ($this->a as $v)
			$this->assertPregMatch('uuid hexadecimal structure', $v,
				'/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
	}

	function test_v4()
	{
		foreach ($this->a as $v) {
				# the 3rd group carries version bits in the top 4 bits
			$vv = substr($v, 14, 4);
			$bb = hexdec($vv);
			$cc = $bb >> 12;
			$this->assertIntEquals('uuid version bits', $cc, 4); }
	}

	function test_variant1()
	{
		$pattern = 0b10;	# variant 1
;
		foreach ($this->a as $v) {
				# the 4th group carries variant bit sequence in uppermost bits - til the first 0 bit
			$vv = substr($v, 19, 4);
			$bb = hexdec($vv);
			$cc = $bb & 0x3fff;
			$dd = $bb >> 14;
			$this->assertIntEquals('uuid variant bits', $dd, $pattern); }
	}

	function test_v4_timing_old()
	{
		$cnt = 1000 * 1000;

		$startS = microtime($get_as_float = true);

		for ($n = 0; $n < $cnt; ++$n)
			$str = Uuid::generateUuidV4_old();

		$endS = microtime($get_as_float = true);

		$totalS = $endS - $startS;
		printf('seconds: %f; final uuid: "%s"' .PHP_EOL, $totalS, $str);
	}

	function test_v4_timing()
	{
		$cnt = 1000 * 1000;

		$startS = microtime($get_as_float = true);

		for ($n = 0; $n < $cnt; ++$n)
			$str = Uuid::generateUuidV4();

		$endS = microtime($get_as_float = true);

		$totalS = $endS - $startS;
		printf('seconds: %f; final uuid: "%s"' .PHP_EOL, $totalS, $str);
	}
}
