<?php
class Game {
    private $name;
    private $players = [];

    //加入創建角色
    public function addPlayer($player) {
        $this->players[] = $player;
    }

    public function start() {
        echo "===== 遊戲開始! =====\n";
        echo "===== 第一關卡 =====\n";

        foreach ($this->players as $player) {
            // 遊戲初始化
            $completedLevels = 1;

            // 開始遊戲
            $this->startLevel($player, 1);
        }
    }
}