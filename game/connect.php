<?php

$servername = 'localhost';
$username = 'xiaolin';
$password = 'password';
$databasename = 'game';

//建立連線(伺服器名稱、mysql帳號、mysql密碼、資料庫名稱)
$connect = new mysqli($servername, $username, $password, $databasename);

//檢查連線
if ($connect->connect_error) {
    die("連結失敗：" . $connect->connect_error);
}
