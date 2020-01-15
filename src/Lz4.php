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

		# https://github.com/lz4/lz4/blob/dev/doc/lz4_Block_format.md
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
			$token = ord($payload[$pos++]);

			$fieldA = ($token >> 4) & 0xf;
			$fieldB = ($token >> 0) & 0xf;

			$numBytes = 0;
			switch ($fieldA) {
			case 15: # add some more bytes to indicate the full length
				do {
					$numBytes += ($nn = ord($payload[$pos++]));
				} while ($nn === 255);
			default:	# a literal number of bytes
			case 0:	# no literal
				$numBytes += $fieldA; }
			$literals = substr($payload, $pos, $numBytes);
			$pos += $numBytes;

			$ret .= $literals;

				# https://github.com/lz4/lz4/blob/dev/doc/lz4_Block_format.md#end-of-block-restrictions
			if ($pos < $blockSize) {
					# the matchCopyOperation
				$offset = $P = ord($payload[$pos++]);
				$offset += $Q = 256 * ord($payload[$pos++]);
				if ($offset === 0)
					throw new MalformedLz4DataException('offset 0');

				$minmatch = 4;
				$matchlength = $minmatch;
				switch ($fieldB) {
				case 15:
					do {
						$matchlength += ($nn = ord($payload[$pos++]));
					} while ($nn === 255);
				default:
				case 0:
					$matchlength += $fieldB; }

					# h/t to http://ticki.github.io/blog/how-lz4-works/
				$to_repeat = substr($ret, -$offset, min($matchlength, $offset));
				$num = (int)ceil(1.0 * $matchlength / $offset);
				if ($num > 1)
					$repeated = str_repeat($to_repeat, $num);
				else
					$repeated = $to_repeat;
				$to_append = substr($repeated, 0, $matchlength);

				$ret .= $to_append; } }

		return $ret;
	}
}
