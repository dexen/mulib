#!/usr/bin/env php-acme
<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require 'src/Testcase.php';
require 'src/AssertionFailed.php';
require 'src/Uuid.php';
require 'src/Lz4.php';
require 'src/Lz4/Frame.php';
require 'src/Lz4/DataBlock.php';
require 'src/MalformedLz4DataException.php';
require 'src/Debug.php';

function processTC(string $name)
{
	$O = new $name;
	$R = new ReflectionObject($O);
	foreach ($R->getMethods() as $M)
		if ($M->isPublic() && (!$M->isConstructor()) && (!$M->isDestructor()))
			if (strncmp($M->name, 'test', 4) === 0)
				call_user_func_array([ $O, $M->name ], [])
					or print '.';
}

function tp(...$a) { array_map('var_dump', $a); }
function td(...$a) { array_map('var_dump', $a); die('td()'); }
function hd($v) { dexen\mulib\Debug::hd($v); }

$a = glob('test/*/*.php');	# yes yes i know
foreach ($a as $pn)
	require $pn;

$a = get_declared_classes();
$tc = dexen\mulib\Testcase::class;
foreach ($a as $className)
	if (is_subclass_of($className, $tc))
		processTC($className);

echo "\nALL DUN.";
