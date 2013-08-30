<?php

require __DIR__ . '/../../vendor/autoload.php';

Tester\Helpers::setup();

class_alias('Tester\Assert', 'Assert');

function test(\Closure $function)
{
	$function();
}
