<?php

require_once "connect.php";
require_once "Class/MainMenu.php";
require_once "Class/Player.php";
require_once "Class/Character.php";
require_once "Class/Enemy.php";

class Game {
    private $name; //遊戲名稱
    private $players = []; //存放開始遊戲的玩家
    private $levels = 10; //總共10個關卡

    public function __construct($name) {
        $this->name = $name;
    }

    //加入創建的角色
    public function addPlayer($player) {
        $this->players[] = $player;
    }

    //開始遊戲
    public function start() {
        echo "===== 遊戲開始! =====\n";
        sleep(1);
        echo "請選擇一個已創建的角色進行遊戲! \n";

        $player = $this->selectPlayer();
        if ($player) {
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
            foreach ($this->players as $index => $player) {
                echo ($index + 1) . ". " . $player->getName() . "\n";
            }
            $choice = readline("請選擇角色(輸入編號):");
            // 檢查輸入是否是一個數字、是否大於 0 以及是否不超過可用角色的總數
            if (is_numeric($choice) && $choice > 0 && $choice <= count($this->players)) {
                return $this->players[$choice - 1];
            }
            echo "無效的選擇，請重新選擇。\n";
        }
    }

    // 開始遊戲關卡
    private function startLevel($player, $level) {
        if ($level <= $this->levels) {
            echo "===== 第 {$level} 關卡 =====\n";
            sleep(1);
            // 生成敵人
            $enemy = new Enemy($level);

            // 戰鬥
            $this->battle($player, $enemy, $level);

            // 更新角色生命值和獲得經驗值
            $this->updatePlayerAfterBattle($player, $enemy, $level);

            // 顯示是否進入下一關
            $this->promptNextLevel($player, $level);

        } else {
            $this->end();
        }
    }

    // 戰鬥邏輯
    private function battle($player, $enemy, $level) {
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
            sleep(1);
            // 玩家自行選擇攻擊方式
            $playerChoice = $this->getPlayerAction($actions);
            // 敵人隨機選擇攻擊方式
            $enemyActions = ['attack', 'defend'];
            $enemyChoice = $enemyActions[array_rand($enemyActions)];

            echo "玩家選擇了 {$actions[$playerChoice]}。\n";
            echo "敵人選擇了 {$enemyChoice}。\n";
            echo "你與對方進行交戰！\n";

            $this->analyzeActions($player, $enemy, $actions[$playerChoice], $enemyChoice);
            // 假設敵人沒有存活
            if (!$enemy->isAlive()) {
                echo "你打敗了敵人！\n";
                sleep(2);
                break;
            }
            // 假設玩家沒有存活
            if (!$player->isAlive()) {
                echo "你被打敗了...\n";
                $this->end();
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
        if ($enemyChoice === 'attack') { // 敵人攻擊
            if ($playerAction !== 'defend') {
                $player->$playerAction($enemy);
            }
            $enemy->attack($player);
        } else { // 敵人防禦
            $enemy->defend();
            if ($playerAction !== 'defend') {
                $player->$playerAction($enemy);
            }
        }
    }

    // 更新玩家在戰鬥後的狀態
    private function updatePlayerAfterBattle($player, $enemy) {
        $player->fullHealth();
        $experienceGained = $enemy->getExperience();
        // 如果是魔王，經驗值加倍
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
            $this->end();
        }
    }
    //結束遊戲
    public function end() {
        echo "請選擇是否退出遊戲或回到遊戲主選單: \n";
        echo "1. 退出遊戲\n";
        echo "2. 回到遊戲主選單\n";
        $endChoice = readline();
        if ($endChoice === '1') {
            echo "感謝您的遊玩～再見! \n";
            sleep(1);
            echo "===== 遊戲結束! =====\n";
            exit;
        } else {
            $mainMenu = new MainMenu();
            $mainMenu->showMainMenu();
        }
    }
}