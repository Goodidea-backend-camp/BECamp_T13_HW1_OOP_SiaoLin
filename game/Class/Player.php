<?php
require_once "connect.php";

class Player {
    private $connect;
    private $character; // 包含角色資訊的 Character 物件
    private $jobName;
    public $level;
    public $experience;
    public $healthPoints;
    public $maxHealthPoints;
    public $attackPoints;
    public $defensePoints;
    public $magicPoints;
    public $maxMagicPoints;
    public $magicAttackPoints;
    public $magicDefensePoints;
    public $luckPoints;
    public $isDefending = false; // 判斷是否防禦的屬性

    // 建構函數：設置資料庫連接並初始化等級為1等、經驗值為0
    public function __construct($connect, $character, $jobName, $healthPoints, $attackPoints, $defensePoints, $magicPoints, $magicAttackPoints, $magicDefensePoints, $luckPoints) {
        $this->connect = $connect;
        $this->character = $character;
        $this->jobName = $jobName;
        $this->level = 1;
        $this->experience = 0;
        $this->maxHealthPoints = $healthPoints; // 根據傳入的 healthPoints 設定最大生命值
        $this->maxMagicPoints = $magicPoints; // 根據傳入的 magicPoints 設定最大魔力值
        $this->healthPoints = $this->maxHealthPoints;
        $this->attackPoints = $attackPoints;
        $this->defensePoints = $defensePoints;
        $this->magicPoints = $this->maxMagicPoints;
        $this->magicAttackPoints = $magicAttackPoints;
        $this->magicDefensePoints = $magicDefensePoints;
        $this->luckPoints = $luckPoints;
    }

    // 角色資料保存
    public function savePlayerProfile($connect, $playerName, $jobName, $completedLevels, $totalKills, $gameStartTime, $gameEndTime) {
        // 插入資料庫
        $sql = "INSERT INTO players (player_Name, job_name, player_level, player_experience, completed_levels, total_kills, game_start_time, game_end_time) VALUES ('$playerName', '$jobName', '$this->level', '$this->experience', '$completedLevels', '$totalKills', '$gameStartTime', '$gameEndTime');";
        $sql .= "INSERT INTO characters (player_Name, health_points, attack, defense, magic_points, magic_attack, magic_defense, luck) VALUES ('$playerName', '$this->maxHealthPoints', '$this->attackPoints', '$this->defensePoints', '$this->magicPoints', '$this->magicAttackPoints', '$this->magicDefensePoints', '$this->luckPoints');";
    
        // 執行
        if ($connect->multi_query($sql) === TRUE) {
            return TRUE;
        } else {
            echo "Error: " . $sql . "\n" . $this->connect->error;
            // 發生錯誤
            return false;
        }
    }
    
    // 獲得經驗值並檢查是否升級
    public function gainExperience($experience) {
        $this->experience += $experience;
        
        // 每 30 經驗值即升級
        while ($this->experience >= 30) {
            $this->experience -= 30; // 減去升級所需的經驗值
            $this->level++;
            $this->levelUp();
        }
    }

    // 升級方法
    private function levelUp() {
        // 定義不同職業的屬性增加值
        $jobAttributes = [
            '劍士' => [
                'maxHealthPoints' => 10,
                'attackPoints' => 2,
                'defensePoints' => 2,
                'luckPoints' => 2
            ],
            '法師' => [
                'maxHealthPoints' => 10,
                'maxMagicPoints' => 10,
                'magicAttackPoints' => 2,
                'magicDefensePoints' => 2,
                'luckPoints' => 2
            ]
        ];
    
        // 檢查是否存在該職業的屬性，如果存在則執行
        if (array_key_exists($this->jobName, $jobAttributes)) {
            foreach ($jobAttributes[$this->jobName] as $attribute => $value) {
                $this->{$attribute} += $value;
            }
        }
    }

    // 恢復狀態的方法
    public function restoreState() {
        $this->healthPoints = $this->maxHealthPoints;
        $this->magicPoints = $this->maxMagicPoints;
        $this->isDefending = false; // 重置防禦狀態
    }

    // 攻擊行動
    public function attack($enemy) {
        // 判斷是否暴擊
        $isCritical = $this->isCriticalHit();

        // 基本傷害
        $damage = $this->attackPoints;

        // 如果產生暴擊則傷害翻倍
        if ($isCritical) {
            $damage *= 2;
            echo "暴擊！傷害加倍！";
        }
        // 計算最終傷害
        $finalDamage = $this->calculateDamage($damage, $enemy->getDefensePoints(), $enemy->isDefending, $isCritical);

        // 敵人受傷
        $enemy->takeDamage($finalDamage);
    }

    // 魔法攻擊行動
    public function magicAttack($enemy) {
        // 檢查魔力值
        if ($this->magicPoints <= 0) {
            echo "目前魔力值不足，無法使用魔法攻擊。\n";
            return;
        }
        // 判斷是否暴擊
        $isCritical = $this->isCriticalHit();

        // 基本傷害
        $damage = $this->magicAttackPoints;

        // 如果產生暴擊則傷害翻倍
        if ($isCritical) {
            $damage *= 2;
        }
        // 計算最終傷害
        $finalDamage = $this->calculateDamage($damage, $enemy->getDefensePoints(), $enemy->isDefending, $isCritical);
        
        // 敵人受傷
        $enemy->takeDamage($finalDamage);
        
        // 每次魔法攻擊消耗1點魔力值
        $this->magicPoints--;
    }

    // 防禦行動
    public function defend() {
        $this->isDefending = TRUE;
    }

    // 承受傷害
    public function takeDamage($damage) {
        $this->healthPoints -= $damage;
    }

    // 檢查是否存活
    public function isAlive() {
        return $this->healthPoints > 0;
    }

    // 計算傷害機制：敵人有無防禦情況下，如果產生暴擊，傷害翻倍
    private function calculateDamage($attackPoints, $defensePoints, $isDefending, $isCritical) {
        if ($isDefending) {
            // 在防禦狀態下傷害為攻擊力值-防禦力值
            $damage = $attackPoints - $defensePoints;
            $damage = $isCritical ? $damage * 2 : $damage;
            return $damage > 0 ? $damage : 1; // 確保最小傷害值為1
        } else {
            $damage = $isCritical ? $attackPoints * 2 : $attackPoints;
            return $damage;
        }
    }
    
    // 暴擊機制：判斷是否產生暴擊
    private function isCriticalHit() {
        // 根據幸運值計算暴擊機率，每點幸運值增加10%的暴擊機率
        $criticalChance = $this->luckPoints;
        $random = mt_rand(1, 10);
    
        return $random <= $criticalChance;
    }
}