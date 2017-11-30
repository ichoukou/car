<?php
//var_dump("video--------------------------------\r\n");
//var_dump($_FILES['video']);
//var_dump("video--------------------------------\r\n");

//var_dump("audio--------------------------------\r\n");
var_dump($_FILES['audio']);
//var_dump("audio--------------------------------\r\n");

#file_put_contents('/data/wwwroot/qx.maizhao.net/'.$_FILES['video']['name'], $_FILES['video']['tmp_name']);
#file_put_contents('/data/wwwroot/qx.maizhao.net/'.$_FILES['audio']['name'], $_FILES['audio']['tmp_name']);

//move_uploaded_file($_FILES['video']['tmp_name'], "/data/wwwroot/qx.maizhao.net/".$_FILES['video']['name']);
$ext = explode('/' ,$_FILES['audio']['type']);
var_dump('本身格式'.$_FILES['audio']['type']);
if ($ext[1] == 'mp3') {
    $file_path = "/data/wwwroot/qx.maizhao.net/demo/video_and_audio/不需要转换.".$ext[1];
    move_uploaded_file($_FILES['audio']['tmp_name'], $file_path);
} else {
    $file_path = "/data/wwwroot/qx.maizhao.net/demo/video_and_audio/别的格式.".$ext[1];
    move_uploaded_file($_FILES['audio']['tmp_name'], $file_path);
    $amr = $file_path;
    $mp3 = '/data/wwwroot/qx.maizhao.net/demo/video_and_audio/新的.mp3';

    $command = "/usr/local/bin/ffmpeg -i $amr $mp3";
    exec($command,$error);
}

