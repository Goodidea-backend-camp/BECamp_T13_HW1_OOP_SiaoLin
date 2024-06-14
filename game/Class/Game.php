<?php

require_once "connect.php";
require_once "MainMenu.php";
require_once "Class/Player.php";
require_once "Class/Character.php";
require_once "Class/Enemy.php";
require_once "Class/GameRecord.php";

class Game {
    private $connect;
    private $character;
    private $players;
    private $levels = 10; // 總共10個關卡
    private $totalKills;
    private $completedLevels;
    private $gameStartTime;
    private $gameEndTime;

    public function __construct(Character $character, $connect) {
        $this->character = $character;
        $this->connect = $connect;
        $this->players = []; // 存放開始遊戲的玩家
        $this->totalKills = 0;
        $this->completedLevels = 0;
        $this->gameStartTime = null;
        $this->gameEndTime = null;
    }

    // 加入創建的角色
    public function addPlayer($playerName) {
        $this->players[] = $playerName;
    }

    // 開始遊戲
    public function start() {
        $this->gameStartTime = date("Y-m-d H:i:s");
        echo "===== 遊戲開始! =====\n";
        sleep(1);
        echo "請選擇一個已創建的角色進行遊戲! \n";

        $playerName = $this->selectPlayer();
        if ($playerName) {
            $job = $this->character->getJob();
            $attributes = $this->character->getAttributes();
        
            // 轉換格式
            $healthPoints = $job['healthPoints'];
            $attackPoints = $attributes['attackPoints'];
            $defensePoints = $attributes['defensePoints'];
            $magicPoints = $job['magicPoints'];
            $magicAttackPoints = $attributes['magicAttackPoints'];
            $magicDefensePoints = $attributes['magicDefensePoints'];
            $luckPoints = $attributes['luckPoints'];
        
            // 創建玩家物件並將屬性傳遞給構造函數
            $player = new Player(
                $playerName,
                $this->connect,
                $job,
                $healthPoints,
                $attackPoints,
                $defensePoints,
                $magicPoints,
                $magicAttackPoints,
                $magicDefensePoints,
                $luckPoints
            );
        
            $this->startLevel($player, 1);
        } else {
            echo "無有效的角色可供選擇。\n";
        }        
    }

    // 選擇角色
    private function selectPlayer() {
        if (empty($this->players)) {
            echo "沒有已創建的角色。\n";
            return null;
        }
        while (true) {
            echo "角色列表: \n";
            $playerName = $this->character->getPlayerName();
            foreach ($this->players as $index => $player) {
                echo ($index + 1) . ". " . $playerName . "\n";
            }
            $choice = readline("請選擇角色(輸入編號):");
            if (is_numeric($choice) && $choice > 0 && $choice <= count($this->players)) {
                return $this->players[$choice - 1];
            }
            echo "無效的選擇，請重新選擇。\n";
        }
    }

    // 開始遊戲關卡
    private function startLevel($player, $level) {
        $level = 1;
        if ($level <= $this->levels) {
            echo "===== 第 {$level} 關卡 =====\n";
            echo "\n";
            sleep(1);
            // 生成敵人
            $enemy = new Enemy($level);
            echo "出現敵人：{$enemy->getName()}\n";
            echo "敵人類型：{$enemy->getType()}\n";
            echo "\n";
            sleep(1);
            echo "===== 進入戰鬥！=====\n";
            echo ".\n";
            echo ".\n";
            echo ".\n";

            // 戰鬥
            $this->battle($player, $enemy);

            // 更新角色生命值和獲得經驗值
            $this->updatePlayerAfterBattle($player, $enemy);

            // 更新已完成的關卡數
            $this->completedLevels++;

            // 顯示是否進入下一關
            $this->promptNextLevel($player, $level);
        } else {
            $this->end($player);
        }
    }

    // 戰鬥邏輯
    private function battle($player, $enemy) {
        $actions = [
            '1' => 'attack',
            '2' => 'magicAttack',
            '3' => 'defend'
        ];

        // 玩家及敵人條件都成立
        while ($player->isAlive() && $enemy->isAlive()) {
            echo "玩家生命值：{$player->healthPoints}\n";
            echo "玩家魔力值：{$player->magicPoints}\n";
            echo "敵人生命值：{$enemy->getHealthPoints()}\n";
            echo "\n";
            sleep(1);
            // 玩家自行選擇攻擊方式
            $playerChoice = $this->getPlayerAction($actions);
            // 敵人隨機選擇攻擊方式
            $enemyActions = ['attack', 'defend'];
            $enemyChoice = $enemyActions[array_rand($enemyActions)];

            echo "玩家選擇了 {$actions[$playerChoice]}。\n";
            echo "敵人選擇了 {$enemyChoice}。\n";
            echo "\n";
            echo "你與對方進行交戰！\n";
            echo ".\n";
            echo ".\n";
            echo ".\n";

            $this->analyzeActions($player, $enemy, $actions[$playerChoice], $enemyChoice);

            if (!$enemy->isAlive()) {
                echo "你打敗了敵人！\n";
                $this->totalKills++; // 更新總擊殺數
                sleep(2);
                break;
            }

            if (!$player->isAlive()) {
                echo "你被打敗了...\n";
                $this->end($player);
                return;
            }
        }
    }

    // 取得玩家行動
    private function getPlayerAction($actions) {
        while (true) {
            echo "請選擇下一步：\n";
            echo "1. 物理攻擊\n";
            echo "2. 魔法攻擊\n";
            echo "3. 防禦\n";
            $playerChoice = readline();
            if (array_key_exists($playerChoice, $actions)) {
                return $playerChoice;
            }
            echo "無效的選擇，請重新選擇。\n";
        }
    }

    // 解析每回合的行動
    private function analyzeActions($player, $enemy, $playerAction, $enemyChoice) {
        if ($enemyChoice === 'attack') {
            if ($playerAction !== 'defend') {
                $player->$playerAction($enemy);
            }
            $enemy->attack($player);
        } else {
            $enemy->defend();
            if ($playerAction !== 'defend') {
                $player->$playerAction($enemy);
            }
        }
    }

    // 更新玩家在戰鬥後的狀態
    private function updatePlayerAfterBattle($player, $enemy) {
        $player->restoreState();
        $experienceGained = $enemy->getExperience();
        if ($enemy->getType() === 'boss') {
            $experienceGained *= 2;
        }
        $player->gainExperience($experienceGained);
    }

    // 顯示是否進入下一關
    private function promptNextLevel($player, $level) {
        echo "是否進入下一關？\n";
        echo "1. 是\n";
        echo "2. 否\n";
        $choice = readline();
        if ($choice === '1') {
            $this->startLevel($player, $level + 1);
        } else {
            $this->end($player);
        }
    }

    // 取得擊殺敵人數量
    public function getTotalKills() {
        return $this->totalKills;
    }

    // 取得已完成關卡數
    public function getCompletedLevels() {
        return $this->completedLevels;
    }

    public function setTotalKills($totalKills) {
        $this->totalKills = $totalKills;
    }

    public function setCompletedLevels($completedLevels) {
        $this->completedLevels = $completedLevels;
    }

    // 結束遊戲
    public function end($player) {
        $this->gameEndTime = date("Y-m-d H:i:s");
        $playerName = $this->character->getPlayerName();
        $jobName = $this->character->getJob()['name'];
        $completedLevels = $this->completedLevels;
        $totalKills = $this->totalKills;
        $gameStartTime = $this->gameStartTime;
        $gameEndTime = $this->gameEndTime;

        $player->savePlayerProfile($this->connect, $playerName, $jobName, $completedLevels, $totalKills, $gameStartTime, $gameEndTime);

        echo "===== 遊戲結束！ =====\n";
        echo "玩家名稱：{$playerName}\n";
        echo "職業：{$jobName}\n";
        echo "完成關卡數：{$completedLevels}\n";
        echo "總擊殺數：{$totalKills}\n";
        echo "遊戲開始時間：{$gameStartTime}\n";
        echo "遊戲結束時間：{$gameEndTime}\n";

        $mainMenu = new MainMenu();
        $mainMenu->showMainMenu();
    }
}

