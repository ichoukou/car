<?php
$amr = '1.amr';
$mp3 = 'test1'.'.mp3';

$command = "/usr/local/bin/ffmpeg -i $amr $mp3 2>&1";
echo exec('whoami');
exec($command, $output, $return_val);
print_r($error);
print_r($return_val);
print_r('aaaaa');