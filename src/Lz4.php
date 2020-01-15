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
		if ($DB->isUncompressed())
			return $DB->payload();

		$ret = '';

		$payload = $DB->payload();
		$pos = 0;
		$blockSize = $DB->blockSize();
		while ($pos < $blockSize) {
			$token = unpack('C', substr($payload, $pos++, 1))[1];

			$fieldA = ($token >> 4) & 0xf;
			$fieldB = ($token >> 0) & 0xf;

			$numBytes = 0;
			switch ($fieldA) {
			case 15: # add some more bytes to indicate the full length
				do {
					$numBytes += ($nn = unpack('C', substr($payload, $pos++, 1))[1]);
				} while ($nn === 255);
			default:	# a literal number of bytes
			case 0:	# no literal
				$numBytes += $fieldA; }
			$literals = substr($payload, $pos, $numBytes);
			$pos += $numBytes;

			$ret .= $literals;

			if ($pos < $blockSize) {
					# the matchCopyOperation
				$offset = unpack('v', substr($payload, $pos, 2))[1];
				$pos += 2;
				if ($offset === 0)
					throw new MalformedLz4DataException('offset 0');

				$minmatch = 4;
				$matchlength = $minmatch;
				switch ($fieldB) {
				case 15:
					do {
						$matchlength += ($nn = unpack('C', substr($payload, $pos++, 1))[1]);
					} while ($nn === 255);
				default:
				case 0:
					$matchlength += $fieldB; }

				$to_repeat = substr($ret, -$offset);
				$repeated = str_repeat($to_repeat, (int)ceil(1.0 * $matchlength / $offset));
				$to_append = substr($repeated, 0, $matchlength);

				$ret .= $to_append; } }

		return $ret;
	}
}
