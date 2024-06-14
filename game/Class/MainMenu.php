<?php
class MainMenu {
    //顯示主選單
    public function showMainMenu() {
        echo "===== 遊戲主選單 =====\n";
        echo "1. 開始遊戲\n";
        echo "2. 創建角色\n";
        echo "3. 查看遊戲紀錄\n";
        echo "4. 退出遊戲\n";
        echo "======================\n";
    }
    //取得玩家選擇
    public function playerChoice() {
        return readline("請選擇: ");
    }

}