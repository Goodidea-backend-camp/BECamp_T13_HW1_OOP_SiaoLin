<?php
// 引入文件
require_once "conn.php";
require_once "Class/MainMenu.php";
require_once "Class/Character.php";
require_once "Class/Player.php";

// 初始化遊戲
$mainMenu = new MainMenu();
$createCharacter = new createCharacter($conn);

// 顯示遊戲主選單
$mainMenu->showMainMenu();

// 遊戲迴圈
while (TRUE) {
    // 取得玩家輸入選擇
    $choice = $mainMenu->playerChoice();
    // 處理玩家的選擇
    if ($choice === '1') {
        // 開始遊戲
    } elseif ($choice ==='2') {
        // 創建角色
        $createCharacter->create($conn);
    }
}