<?php
//引入 Composer 的自動載入檔
require 'vendor/autoload.php';

use Dotenv\Dotenv;

// 建立一個 Dotenv 類別的實例 使用 createImmutable 方法
$dotenv = Dotenv::createImmutable(__DIR__);
// 載入 .env 檔案
$dotenv->load();

//從 $_ENV 超全域數組讀取 .env 檔案中定義的環境變數值，並將它們賦值給對應的 PHP 變數
$servername = $_ENV['DB_SERVERNAME'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$databasename = $_ENV['DB_NAME'];

//建立連線(伺服器名稱、mysql帳號、mysql密碼、資料庫名稱)
$connect = new mysqli($servername, $username, $password, $databasename);

//檢查連線
if ($connect->connect_error) {
    die("連結失敗：" . $connect->connect_error);
}
