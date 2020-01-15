<?php

namespace dexen\mulib;

class Lz4
{
	static
	function decompress(string $input) : string
	{
		return implode(
			array_map(
				[__CLASS__, 'decompressBlock',],
				iterator_to_array((new Lz4\Frame($input))->dataBlocks()) ) );
	}

	static
	function decompressBlock(Lz4\DataBlock $DB) : string
	{
return 'foobar';
	}
}
