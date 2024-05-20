<?php
require_once "connect.php";

class Player {
    private $connect;
    public $level;
    public $experience;

    // 建構函數：設置資料庫連接並初始化等級為1等、經驗值為0
    public function __construct($connect) {
        $this->connect = $connect;
        $this->level = 1;
        $this->experience = 0;
    }

    // 創建角色方法
    public function create($playerName, $jobChoice, $jobName, $healthPoints, $attackPoints, $defensePoints, $magicPoints, $magicAttackPoints, $magicDefensePoints, $luckPoints) {
        // SQL 查詢
        $sql = "INSERT INTO players (player_Name, job_id, job_name, player_level, player_experience) VALUES ('$playerName', '$jobChoice', '$jobName', '$this->level', '$this->experience');";
        $sql .= "INSERT INTO characters (health_points, attack, defense, magic_points, magic_attack, magic_defense, luck) VALUES ('$healthPoints', '$attackPoints', '$defensePoints', '$magicPoints', '$magicAttackPoints', '$magicDefensePoints', '$luckPoints');";

        // 執行 SQL 查詢
        if ($this->connect->multi_query($sql) === TRUE) {
            // 成功建立角色
            return true;
        } else {
            echo "Error: " . $sql . "<br>" . $this->connect->error;
            // 發生錯誤
            return false;
        }
    }
    
}
