<?php
require_once "connect.php";

class Player {
    private $connect;
    private $name;
    public $level;
    public $experience;
    public $healthPoints;
    public $maxHealthPoints;
    public $attackPoints;
    public $defensePoints;
    public $magicPoints;
    public $magicAttackPoints;
    public $magicDefensePoints;
    public $luckPoints;
    public $isDefending = false; // 判斷是否防禦的屬性

    // 建構函數：設置資料庫連接並初始化等級為1等、經驗值為0
    public function __construct($connect, $name, $healthPoints, $attackPoints, $defensePoints, $magicPoints, $magicAttackPoints, $magicDefensePoints, $luckPoints) {
        $this->connect = $connect;
        $this->name = $name;
        $this->level = 1;
        $this->experience = 0;
        $this->maxHealthPoints = $healthPoints; // 根據傳入的 healthPoints 設定最大生命值
        $this->healthPoints = $this->maxHealthPoints;
        $this->attackPoints = $attackPoints;
        $this->defensePoints = $defensePoints;
        $this->magicPoints = $magicPoints;
        $this->magicAttackPoints = $magicAttackPoints;
        $this->magicDefensePoints = $magicDefensePoints;
        $this->luckPoints = $luckPoints;
    }

    // 角色資料保存
    public function savePlayerProfile($playerName, $jobChoice, $jobName) {
        // SQL 查詢
        $sql = "INSERT INTO players (player_Name, job_id, job_name, player_level, player_experience) VALUES ('$playerName', '$jobChoice', '$jobName', '$this->level', '$this->experience');";
        $sql .= "INSERT INTO characters (health_points, attack, defense, magic_points, magic_attack, magic_defense, luck) VALUES ('$this->maxHealthPoints', '$this->attackPoints', '$this->defensePoints', '$this->magicPoints', '$this->magicAttackPoints', '$this->magicDefensePoints', '$this->luckPoints');";

        // 執行 SQL 查詢
        if ($this->connect->multi_query($sql) === TRUE) {
            // 成功建立角色
            return TRUE;
        } else {
            echo "Error: " . $sql . "<br>" . $this->connect->error;
            // 發生錯誤
            return false;
        }
    }
    // 取得角色名稱
    public function getName() {
        return $this->name;
    }
    
    // 獲得經驗值並檢查是否升級
    public function gainExperience($experience) {
        $this->experience += $experience;
        
        // 每打倒一個小怪獲得 10 經驗值，每打倒一個魔王獲得 20 經驗值
        if ($experience >= 50) {
            $this->level++;
            $this->experience = 0; // 重置經驗值
        }
    }
    // 滿血恢復的方法
    public function fullHealth() {
        $this->healthPoints = $this->maxHealthPoints;
    }

    // 攻擊行動
    public function attack($enemy) {
        if ($enemy->isDefending) {
            // 敵人防禦時的傷害計算
            $enemyDefense = $enemy->getDefensePoints();
            $damage = $this->calculateDamage($this->attackPoints, $enemyDefense, $enemy->isDefending);
        } else {
            // 敵人未防禦時的傷害計算
            $damage = $this->attackPoints;
        }
        // 敵人受傷
        $enemy->takeDamage($damage);
    }

    // 魔法攻擊行動
    public function magicAttack($enemy) {
        // 檢查魔力值
        if ($this->magicPoints <= 0) {
            echo "目前魔力值不足，無法使用魔法攻擊。\n";
            return;
        }
        if ($enemy->isDefending) {
            // 敵人防禦時的傷害計算
            $damage = $this->calculateDamage($this->magicAttackPoints, $enemy->defensePoints, $enemy->isDefending);
        } else {
            // 敵人未防禦時的傷害計算
            $damage = $this->magicAttackPoints;
        }
        
        // 敵人受傷
        $enemy->takeDamage($damage);
        
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

    // 計算傷害
    private function calculateDamage($attackPoints, $defensePoints, $isDefending) {
        if ($isDefending) {
            // 在防禦狀態下傷害為攻擊力值-防禦力值
            $damage = $attackPoints - $defensePoints;
            return $damage > 0 ? $damage : 1; // 確保最小傷害值為1
        } else {
            // 傷害為攻擊力值
            return $attackPoints;
        }
    }
}
