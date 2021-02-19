<?php
echo ord('a') . PHP_EOL;
echo decbin(ord('a')). PHP_EOL;
echo chr(bindec(decbin(ord('a')))) . PHP_EOL;
var_dump(chr(0x1));
var_dump(decbin(ord(chr(0x1))));
var_dump(decbin(127));
var_dump(bindec('1111111'));

var_dump(sprintf("%064d",decbin(65536)));
var_dump(sprintf("%064b",65536));
$val = '1111111111111111111111111111111111111111111111111111111111111111';
var_dump($val);
var_dump( bindec($val));