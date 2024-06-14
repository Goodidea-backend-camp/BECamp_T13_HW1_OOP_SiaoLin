<?php

require_once "connect.php";

class GameRecord {
    private $connect;

    // 建構函數：設置資料庫連接並初始化遊戲時間
    public function __construct($connect) {
        $this->connect = $connect;
    }

    // 顯示遊戲紀錄
    public function displayGameRecord() {
        $sql = "SELECT player_id, player_name, job_name, player_level, completed_levels, total_kills, game_start_time, game_end_time FROM players";
        $result = $this->connect->query($sql);

        // 檢查結果是否有紀錄
        if (mysqli_num_rows($result) > 0) {
            // 顯示紀錄
            echo "遊戲紀錄：\n";
            while ($row = mysqli_fetch_assoc($result)) {
                echo $row['player_id'] . "\n" . "玩家名稱：" .  $row['player_name'] . " | " . "職業：" . $row['job_name'] . " | " . "等級：" . $row['player_level'] . "\n" . "目前完成關卡數：" . $row['completed_levels'] . "\n" . "擊殺敵人總數量：" . $row['total_kills'] . "\n" . "遊戲開始時間：" . $row['game_start_time'] . "\n" . "遊戲結束時間：" . $row['game_end_time'] . "\n" . "\n";
            }
        } else {
            echo "目前沒有遊戲紀錄。\n";
        }
    }       
}