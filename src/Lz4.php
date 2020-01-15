<?php

namespace dexen\mulib;

use dexen\mulib\Lz4\Frame;
use dexen\mulib\Lz4\DataBlock;

class Lz4
{
	static
	function decompress(string $input) : string
	{
		return implode(
			array_map(
				[__CLASS__, 'decompressBlock',],
				(new Lz4Frame($input))->dataBlocks() ) );
	}

	static
	function decompressBlock(Lz4\DataBlock $DB) : string
	{
return 'foobar';
	}
}
