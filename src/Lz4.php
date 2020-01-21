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

				# operation A: a fragment of input $payload to be appended to output $ret
				# NOP if $fieldA === 0
			$len = ($token >> 4) & 0xf;
			switch ($len) {
			case 15: # add some more bytes to indicate the full length
				do {
					$len += ($nn = ord($payload[$pos++]));
				} while ($nn === 255);
			default:	# a literal number of bytes
				$ret .= substr($payload, $pos, $len);
				$pos += $len;
			case 0:	# no literal
				; }

				# https://github.com/lz4/lz4/blob/dev/doc/lz4_Block_format.md#end-of-block-restrictions
			if ($pos < $blockSize) {
					# operation B: a fragment of output $ret to be appended to output $ret
					# the encoding allows repeats via overlapping copy
				$len = ($token >> 0) & 0xf;

					# the matchCopyOperation
					# offset is encoded as little-endian uint16
				$offset = ord($payload[$pos++]);
				$offset += 256 * ord($payload[$pos++]);
				if ($offset === 0)
					throw new MalformedLz4DataException('offset 0');

				switch ($len) {
				case 15:
					do {
						$len += ($nn = ord($payload[$pos++]));
					} while ($nn === 255);
				default:
						# minimum match len
					$len += 4; }

					# h/t to http://ticki.github.io/blog/how-lz4-works/
				if ($len <= $offset)
					$ret .= substr($ret, -$offset, $len);
				else {
						# >which means that later bytes to copy are not yet decoded.
						# >This is called an "overlap match", and must be handled with special care
					$to_repeat = substr($ret, -$offset);
					$num = (int)ceil(1.0 * $len / $offset);

					$repeated = str_repeat($to_repeat, $num);

					if (($num * $offset) === $len)
						$ret .= $repeated;
					else
						$ret .= substr($repeated, 0, $len); } } }

		return $ret;
	}
}
