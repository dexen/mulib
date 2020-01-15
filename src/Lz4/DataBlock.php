<?php

namespace dexen\mulib\Lz4;

class DataBlock
{
	protected $isUncompressed;
	protected $content;
	protected $blockSize;
	protected $hasBlockChecksum;
	protected $blockChecksum;

	const HEADER_LEN = 4;
	const LZ4_BLOCK_HEADER_LEN = 4;
	const LZ4_BLOCK_CHECKSUM_LEN = 4;

	function __construct(string $data, bool $hasBlockChecksum)
	{
	}

	function blockSize() : int
	{
		return $this->blockSize;
	}

	function blockOuternSize() : int
	{
		return $this->blockSize + static::LZ4_BLOCK_HEADER_LEN + (static::LZ4_BLOCK_CHECKSUM_LEN * $this->hasBlockChecksum);
	}
}
