<?php
var_dump("video--------------------------------\r\n");
var_dump($_FILES['video']);
var_dump("video--------------------------------\r\n");

var_dump("audio--------------------------------\r\n");
var_dump($_FILES['audio']);
var_dump("audio--------------------------------\r\n");

#file_put_contents('/data/wwwroot/qx.maizhao.net/'.$_FILES['video']['name'], $_FILES['video']['tmp_name']);
#file_put_contents('/data/wwwroot/qx.maizhao.net/'.$_FILES['audio']['name'], $_FILES['audio']['tmp_name']);

move_uploaded_file($_FILES['video']['tmp_name'], "/data/wwwroot/qx.maizhao.net/".$_FILES['video']['name']);
move_uploaded_file($_FILES['audio']['tmp_name'], "/data/wwwroot/qx.maizhao.net/".$_FILES['audio']['name']);