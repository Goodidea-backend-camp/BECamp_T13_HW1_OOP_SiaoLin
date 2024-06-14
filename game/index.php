<?php
require_once "connect.php";
require_once "Class/MainMenu.php";
require_once "Class/Character.php";
require_once "Class/Player.php";
require_once "Class/Game.php";
require_once "Class/GameRecord.php";

// 初始化遊戲
$mainMenu = new MainMenu();
$createCharacter = new Character();
$game = new Game($createCharacter, $connect);
$gameRecord = new GameRecord($connect);

// 顯示遊戲主選單
$mainMenu->showMainMenu();

// 遊戲迴圈
while (TRUE) {
    // 取得玩家輸入選擇
    $choice = $mainMenu->playerChoice();
    // 處理玩家的選擇
    if ($choice === '1' || $choice === '2') {
        // 創建角色
        $playerName = $createCharacter->createCharacter();
        $game->addPlayer($playerName);
        sleep(2);
        // 開始遊戲
        $game->start();
    } elseif ($choice === '3') {
        // 查看遊戲紀錄
        $gameRecord->displayGameRecord();
        // 再次顯示主選單
        $mainMenu->showMainMenu();
    } elseif ($choice ==='4') {
        echo "即將結束遊戲！";
        sleep(1);
        echo "感謝你的遊玩～再見\n";
        exit();
    }
}