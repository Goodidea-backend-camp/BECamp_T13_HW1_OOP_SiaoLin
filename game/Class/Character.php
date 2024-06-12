<?php
require_once "connect.php";
require_once "Class/Player.php";

class Character {
    public $name;
    public $job;
    public $jobName;
    public $attributes;

    public function createCharacter() {
        // 新建角色
        echo "===== 創建角色 =====\n";
        echo "\n";
        echo "請輸入您的角色名稱: ";
        $playerName = readline();
        // 檢查角色名字不得為空
        if (empty($playerName)) {
            echo "角色名字不得為空！";
            return $this->createCharacter();
        }
        $this->name = $playerName;

        // 顯示職業選項並限制職業屬性
        $jobs = [
            '1' => ['name' => '劍士', 'healthPoints' => 100, 'magicPoints' => 0, 'allowedAttributes' => ['attackPoints', 'defensePoints', 'luckPoints']],
            '2' => ['name' => '法師', 'healthPoints' => 80, 'magicPoints' => 80, 'allowedAttributes' => ['magicAttackPoints', 'magicDefensePoints', 'luckPoints']]
        ];
    
        $jobChoice = readline("請選擇您的職業: \n1. 劍士\n2. 法師\n");
        $job = $jobs[$jobChoice];
        $this->job = $job;
        $this->jobName = $job['name'];

        echo "===== 角色屬性點 =====\n";
        echo "\n";

        // 提供10點屬性點可自行分配
        $remainingPoints = 10;
        $attributes = [
            'attackPoints' => 0,
            'defensePoints' => 0,
            'magicAttackPoints' => 0,
            'magicDefensePoints' => 0,
            'luckPoints' => 0
        ];
    
        $assignationChoice = readline("您有10點屬性點可自行分配!\n是否進行分配? \n1. 是\n2. 否\n");
    
        if ($assignationChoice === '1') {
            $attributes = $this->manualAttributeAssignment($attributes, $remainingPoints, $job['allowedAttributes']);
        } else {
            $attributes = $this->randomAttributeAssignment($attributes, $remainingPoints, $job['allowedAttributes']);
        }
        $this->attributes = $attributes;

        echo "===== 角色創建中! =====\n";
        sleep(1);
        echo ".\n";
        echo ".\n";
        echo ".\n";
        sleep(1);

        // 顯示角色屬性
        $this->displayCharacter($playerName, $job, $attributes);
        echo ".\n";
        echo ".\n";
        echo ".\n";

        return $playerName;
    }

    // 取得玩家名稱的方法
    public function getPlayerName() {
        return $this->name;
    }

    // 取得職業選項的方法
    public function getJob() {
        return $this->job;
    }

    // 取得職業名稱的方法
    public function getJobName() {
        return $this->jobName;
    }

    // 取得屬性的方法
    public function getAttributes() {
        return $this->attributes;
    }
    
    // 手動分配屬性點的方法
    private function manualAttributeAssignment(array $attributes, int $remainingPoints, array $allowedAttributes): array {
        foreach ($allowedAttributes as $attribute) {
            if ($remainingPoints <= 0) {
                break;
            }
    
            echo "剩餘可分配點數為: " . $remainingPoints . "點\n";
            $points = readline("請分配{$this->translateAttribute($attribute)}點數: \n");
            
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