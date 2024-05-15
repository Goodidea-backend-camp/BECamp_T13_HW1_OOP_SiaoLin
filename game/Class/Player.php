<?php
require_once "conn.php";

class Player {
    private $conn;
    public $level;
    public $experience;

    // 建構函數：設置資料庫連接並初始化等級為1等、經驗值為0
    public function __construct($conn) {
        $this->conn = $conn;
        $this->level = 1;
        $this->experience = 0;
    }

    // 創建角色方法
    public function createCharacter($playerName, $jobChoice, $jobName, $healthPoints, $attackPoints, $defensePoints, $magicPoints, $magicAttackPoints, $magicDefensePoints, $luckPoints) {
        // SQL 查詢
        $sql = "INSERT INTO players (player_Name, job_id, job_name, player_level, player_experience) VALUES ('$playerName', '$jobChoice', '$jobName', '$this->level', '$this->experience');";
        $sql .= "INSERT INTO characters (health_points, attack, defense, magic_points, magic_attack, magic_defense, luck) VALUES ('$healthPoints', '$attackPoints', '$defensePoints', '$magicPoints', '$magicAttackPoints', '$magicDefensePoints', '$luckPoints');";

        // 打印 SQL 查询语句
        //echo "SQL 查询语句：" . $sql;

        // 執行 SQL 查詢
        if ($this->conn->multi_query($sql) === TRUE) {
            // 成功建立角色
            return true;
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
            // 發生錯誤
            return false;
        }
    }
    // //玩家隨機攻擊8-20傷害
    // public function attack() {
    //     return rand(8, 20);
    // }

    // //升級方法
    // public function levelUp($completedLevels) {
    //     //完成當前關卡即升一級
    //     if ($completedLevels > $this->level) {
    //         $this->level = $completedLevels;
    //         echo "恭喜您升等了！目前等級為: " . $this->level . "\n";
    //     }
    // }
}
