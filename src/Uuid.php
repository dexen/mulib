<?php

namespace Successup\Mulib;

class Uuid
{
	static
	function generateUuidV4() : string
	{
		return implode('-',
			[
				bin2hex(random_bytes(4)),
				bin2hex(random_bytes(2)),
				bin2hex((random_bytes(2) & "\x0f\xff") | "\x40\x00"),
				bin2hex((random_bytes(2) & "\x3f\xff") | "\x80\x00"),
				bin2hex(random_bytes(6)) ] );
	}
}
