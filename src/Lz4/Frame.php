<?php

namespace dexen\mulib\Lz4;

use dexen\mulib\MalformedLz4DataException;

class Frame
{
	const LZ4_MAGIC_NUMBER = 0x184D2204;
	const LZ4_FRAME_VERSION = 0x01;

	protected $FLG = [];
	protected $BD = [];

	protected $ContentSize;
	protected $DictID;

	protected $HC;

	protected $input;
	protected $pos;
	
	function __construct(string $input)
	{
		$pos = 0;

		$expected_magic_number = static::LZ4_MAGIC_NUMBER;
		$expected_magic_bytes = pack('V', $expected_magic_number);

		$magic_number_bytes = substr($input, $pos, strlen($expected_magic_bytes));
		if ($magic_number_bytes !== $expected_magic_bytes)
			throw new MalformedLz4DataException('magic number not found');
		$pos += strlen($expected_magic_bytes);

			# the const part
		$frame_descriptor_len = 2;

		$frame_descriptor_bytes = substr($input, $pos, $frame_descriptor_len);
		if (strlen($frame_descriptor_bytes) !== $frame_descriptor_len)
			throw new MalformedLz4DataException('short header: FLG/BD');
		$pos += $frame_descriptor_len;

				# why does unpack('C*', ...) return a 1-based array...?
		[ $FLG,  $BD ] = array_values(unpack('C*', $frame_descriptor_bytes));

		$this->FLG = [
			'DictID' => ($FLG >> 0) & 0x1,
			'Reserved1' => ($FLG >> 1) & 0x1,
			'CChecksum' => ($FLG >> 2) & 0x1,
			'CSize' => ($FLG >> 3) & 0x1,
			'BChecksum' => ($FLG >> 4) & 0x1,
			'BIndep' => ($FLG >> 5) & 0x1,
			'Version' => ($FLG >> 6) & 0x3,
		];

		$this->BD = [
			'Reserved2' => ($BD >> 0) & 0xf,
			'BlockMaxSize' => ($BD >> 4) & 0x7,
			'Reserved3' => ($BD >> 7) & 0x1,
		];

		if ($this->FLG['Version'] !== static::LZ4_FRAME_VERSION)
			throw new MalformedLz4DataException(sprintf('version mismatch; got "%s", expected "%s"',
				$this->FLG['Version'], static::LZ4_FRAME_VERSION ));

		if ($this->FLG['Reserved1'] !== 0)
			throw new MalformedLz4DataException('unsupported value in Reserved1');
		if ($this->BD['Reserved2'] !== 0)
			throw new MalformedLz4DataException('unsupported value in Reserved2');
		if ($this->BD['Reserved3'] !== 0)
			throw new MalformedLz4DataException('unsupported value in Reserved3');

		if ($this->FLG['CSize']) {
				# FIXME: check the content size
			$ContentSize_len = 8;
			$bytes = substr($input, $pos, $ContentSize_len);
			if (strlen($bytes) !== $ContentSize_len)
				throw new MalformedLz4DataException('short header: ContentSize');
			$this->ContentSize = unpack('P', $bytes);
			$frame_descriptor_len += $ContentSize_len;
			$pos += $ContentSize_len; }
		if ($this->FLG['DictID']) {
			$DictID_len = 4;
			$bytes = substr($input, $pos, $DictID_len);
			if (strlen($bytes) !== $DictID_len)
				throw new MalformedLz4DataException('short header: DictID');
			$this->DictID = unpack('V', $bytes);
			$frame_descriptor_len += $DictID_len;
			$pos += $DictID_len; }
		if (true) {
				# FIXME: check the header checksum
			$HC_len = 1;
			$bytes = substr($input, $pos, $HC_len);
			if (strlen($bytes) !== $HC_len)
				throw new MalformedLz4DataException('short header: HC');
			$this->HC = unpack('C', $bytes);
			$frame_descriptor_len += $HC_len;
			$pos += $HC_len; }

		$this->input = $input;
		$this->pos = $pos;
	}

	function dataBlocks() : \Generator /* of DataBlock */
	{
		$pos = $this->pos;
		while (true) {
			$DB = new DataBlock($this->input, $pos, $this->FLG['BChecksum']);
			if ($DB->blockSize() === 0)
				return;
			yield $DB;
			$pos += $DB->blockOuternSize();
		}
	}
}
