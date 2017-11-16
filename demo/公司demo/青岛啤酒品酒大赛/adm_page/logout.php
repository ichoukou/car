<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";

$_SESSION['admin_id'] = $_SESSION['username'] = $_SESSION['system_group'] = '';

exit(header('location:login.php'));





