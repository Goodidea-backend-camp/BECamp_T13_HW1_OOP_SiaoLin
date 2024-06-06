<?php
// 引入文件
require_once "connect.php";
require_once "Class/MainMenu.php";
require_once "Class/Character.php";
require_once "Class/Player.php";
require_once "Class/Game.php";

// 初始化遊戲
$mainMenu = new MainMenu();
$createCharacter = new Character($connect);
$game = new Game('凜凜仔的遊戲');

// 顯示遊戲主選單
$mainMenu->showMainMenu();

// 遊戲迴圈
while (TRUE) {
    // 取得玩家輸入選擇
    $choice = $mainMenu->playerChoice();
    // 處理玩家的選擇
    if ($choice === '1' || $choice === '2') {
        // 創建角色
        $playerName = $createCharacter->createCharacter($connect);
        $game->addPlayer($playerName);
        sleep(2);
        // 開始遊戲
        $game->start();
        // 開始遊戲後即退出迴圈
        break;
    } elseif ($choice ==='4') {
        echo "感謝你的遊玩～再見\n";
        exit();
    }
}