<?php

require __DIR__ . '/vendor/autoload.php';

$wm = [];

$wm[0] = 1;
$wm[1] = 2;

dump(end($wm));
dd(current());
unset($wm[array_key_last($wm)]);
dump(end($wm));
