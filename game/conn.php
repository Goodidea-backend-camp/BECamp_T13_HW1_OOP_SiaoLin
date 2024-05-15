<?php
//使用mysqli連接資料庫
$servername = 'localhost';
$username = 'xiaolin';
$password = 'password';
$databasename = 'game';

//建立連線(伺服器名稱、mysql帳號、mysql密碼、資料庫名稱)
$conn = new mysqli($servername, $username, $password, $databasename);

//檢查連線
if ($conn->connect_error) {
    die("連結失敗：" . $conn->connect_error);
//} else {
//    echo "資料庫連結成功!";
}
