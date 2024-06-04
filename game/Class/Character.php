<?php
require_once 'connect.php';
require_once 'Class/Player.php';

class Character {
    private $connect;

    // 建構函數：設置資料庫連接
    public function __construct($connect) {
        $this->connect = $connect;
    }
    public function createCharacter($connect) {
        // 新建角色
        echo "請輸入您的角色名稱: ";
        $playerName = readline();
        // 檢查角色名字不得為空
        if (empty($playerName)) {
            echo "角色名字不得為空！";
            return $this->createCharacter($connect);
        }
        // 顯示職業選項並限制職業屬性
        $jobs = [
            '1' => ['name' => '劍士', 'healthPoints' => 100, 'magicPoints' => 0, 'allowedAttributes' => ['attackPoints', 'defensePoints', 'luckPoints']],
            '2' => ['name' => '法師', 'healthPoints' => 80, 'magicPoints' => 80, 'allowedAttributes' => ['magicAttackPoints', 'magicDefensePoints', 'luckPoints']]
        ];
    
        $jobChoice = $this->inputSelection("請選擇您的職業: \n1. 劍士\n2. 法師\n", array_map('strval', array_keys($jobs)));
        $job = $jobs[$jobChoice];

        // 提供10點屬性點可自行分配
        $remainingPoints = 10;
        $attributes = [
            'attackPoints' => 0,
            'defensePoints' => 0,
            'magicAttackPoints' => 0,
            'magicDefensePoints' => 0,
            'luckPoints' => 0
        ];
    
        $assignationChoice = $this->inputSelection("您有10點屬性點可自行分配!\n是否進行分配? \n1. 是\n2. 否\n", ['1', '2']);
    
        if ($assignationChoice === '1') {
            $attributes = $this->manualAttributeAssignment($attributes, $remainingPoints, $job['allowedAttributes']);
        } else {
            $attributes = $this->randomAttributeAssignment($attributes, $remainingPoints, $job['allowedAttributes']);
        }
    
        // 創建 Player 物件並呼叫 savePlayerProfile 方法來保存資料
        $player = new Player($connect, $job['healthPoints'], $attributes['attackPoints'], $attributes['defensePoints'], $job['magicPoints'], $attributes['magicAttackPoints'], $attributes['magicDefensePoints'], $attributes['luckPoints']);
        $player->savePlayerProfile($playerName, $jobChoice, $job['name']);
        if ($player) {
            echo "===== 角色創建中! =====\n";
            sleep(3);
        } else {
            echo "創建角色失敗! 請重新創建角色! \n";
            return $this->createCharacter($connect);
        }
        // 顯示角色屬性
        $this->displayCharacter($playerName, $job, $attributes);
    }

    // 玩家輸入選擇處理邏輯:確保輸入有效
    private function inputSelection(string $prompt, array $validChoices): string {
        while (true) {
            echo $prompt;
            $choice = readline();
            if (in_array($choice, $validChoices, true)) {
                return $choice;
            }
            echo "無效的選擇！請重新輸入。\n";
        }
    }
    // 手動分配屬性點的方法
    private function manualAttributeAssignment(array $attributes, int $remainingPoints, array $allowedAttributes): array {
        foreach ($allowedAttributes as $attribute) {
            if ($remainingPoints <= 0) {
                break;
            }
    
            echo "剩餘可分配點數為: " . $remainingPoints . "點\n";
            $points = intval(readline("請分配{$this->translateAttribute($attribute)}點數: \n"));
            
            // 檢查輸入的點數是否有效
            if ($points > $remainingPoints) {
                echo "您不能分配超過剩餘點數的屬性點！\n";
                continue;
            }
            
            // 更新屬性點數和剩餘點數
            $attributes[$attribute] += $points;
            $remainingPoints -= $points;
        }
    
        // 如果還有剩餘點數，提醒用戶並將剩餘點數隨機分配至可分配的屬性上
        if ($remainingPoints > 0) {
            echo "還有" . $remainingPoints . "點未分配的點數，這些點數將隨機分配。\n";
            $attributes = $this->randomAttributeAssignment($attributes, $remainingPoints, $allowedAttributes);
        }
    
        return $attributes;
    }
    // 隨機分配屬性點的方法
    private function randomAttributeAssignment(array $attributes, int $remainingPoints, array $allowedAttributes): array {
        while ($remainingPoints > 0) {
            $randomPoints = rand(1, $remainingPoints);
            $randomAttribute = $allowedAttributes[array_rand($allowedAttributes)];
            $attributes[$randomAttribute] += $randomPoints;
            $remainingPoints -= $randomPoints;
        }
        return $attributes;
    }
    // 翻譯每個屬性點的名稱方法
    private function translateAttribute(string $attribute): string {
        $translations = [
            'attackPoints' => '物理攻擊力',
            'defensePoints' => '物理防禦力',
            'magicAttackPoints' => '魔法攻擊力',
            'magicDefensePoints' => '魔法防禦力',
            'luckPoints' => '幸運值'
        ];
        return $translations[$attribute] ?? $attribute;
    }
    // 顯示角色資訊方法
    private function displayCharacter(string $playerName, array $job, array $attributes): void {
        echo "角色名稱: " . $playerName . "\n";
        echo "職業: " . $job['name'] . "\n";
        echo "生命值: " . $job['healthPoints'] . "\n";
        echo "魔法值: " . $job['magicPoints'] . "\n";
        echo "屬性:\n";
        foreach ($attributes as $attribute => $value) {
            echo $this->translateAttribute($attribute) . ": " . $value . "\n";
        }
    }
}