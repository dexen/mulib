<?php

namespace dexen\mulib;

class Test_Lz4_decompress extends Testcase
{
		# [ TUPLE [ , TUPLE [ , ... ] ] ]
		# TUPLE ::= [ original, compressed, decompression_result ]
	protected $testContent = [];

	function __construct()
	{
		$this->testContent =
			$this->_globTestData(
				$this->_globTestContent(
					$this->_globTestFiles() ) );
	}

	protected
	function _globTestFiles() : array
	{
		return array_map(
			function(string $compressedPN) {
				return [ basename($this->_uncopressedPN($compressedPN)), $this->_uncopressedPN($compressedPN), $compressedPN ];
			},
			glob('test/Lz4/test-data/*.lz4') );
	}

	protected
	function _uncopressedPN(string $compressedPN) : string
	{
		return preg_replace('/[.]lz4$/', '', $compressedPN);
	}

	protected
	function _globTestContent(array $a) : array
	{
		return array_map(
			function($tuple) {
				return [ $tuple[0], file_get_contents($tuple[1]), file_get_contents($tuple[2]) ];
			},
			$a );
	}

	protected
	function _globTestData(array $a) : array
	{
		return array_map(
			function($tuple) {
				return [ $tuple[0], $tuple[1], $tuple[2], Lz4::decompress($tuple[2]) ];
			},
			$a );
	}

	function test_ecompression()
	{
		foreach ($this->testContent as $tuple)
			$this->assertStringEqualsPositionWise(
				sprintf('decompressed data matches original data of file "%s"', $tuple[0]),
				$tuple[3], $tuple[1]);
	}
}
