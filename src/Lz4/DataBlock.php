<?php

namespace dexen\mulib\Lz4;

class DataBlock
{
	protected $isUncompressed;
	protected $payload;
	protected $blockSize;
	protected $hasBlockChecksum;
	protected $blockChecksum;

	const HEADER_LEN = 4;
	const LZ4_BLOCK_HEADER_LEN = 4;
	const LZ4_BLOCK_CHECKSUM_LEN = 4;

	function __construct(string $data, bool $hasBlockChecksum)
	{
		$blockSize = unpack('V', $data)[1];
		$this->isUncompressed = $blockSize & (~0x7fffffff);
		$this->blockSize = $blockSize & 0x7fffffff;

		$this->payload = substr($data, static::LZ4_BLOCK_HEADER_LEN, $this->blockSize);

		$this->hasBlockChecksum = $hasBlockChecksum;
		if ($this->hasBlockChecksum)
			$this->blockChecksum = substr($data, static::LZ4_BLOCK_HEADER_LEN + $this->blockSize, static::LZ4_BLOCK_CHECKSUM_LEN);
			# FIXME - do check the block checksum
	}

	function payload() : string
	{
		return $this->payload;
	}

	function isUncompressed() : bool
	{
		return $this->isUncompressed;
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
