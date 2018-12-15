#!/usr/bin/env php-acme
<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require 'src/Testcase.php';
require 'src/AssertionFailed.php';
require 'src/Uuid.php';

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

$a = glob('test/*/*.php');	# yes yes i know
foreach ($a as $pn)
	require $pn;

$a = get_declared_classes();
$tc = Successup\Mulib\Testcase::class;
foreach ($a as $className)
	if (is_subclass_of($className, $tc))
		processTC($className);

echo "\nALL DUN.";
