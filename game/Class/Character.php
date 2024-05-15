<?php
require_once 'Player.php';
require_once 'conn.php';

class createCharacter {
    private $conn;

    // 建構函數：設置資料庫連接
    public function __construct($conn) {
        $this->conn = $conn;
    }
    public function create($conn) {
        // 新建角色
        echo "請輸入您的角色名稱: ";
        $playerName = readline();

        // 檢查角色名字不得為空
        if (empty($playerName)) {
            echo "角色名字不得為空！";
            return $this->create($conn);
        }
        // 顯示職業選項
        echo "請選擇您的職業: \n";
        echo "1. 劍士\n";
        echo "2. 法師\n";
        $jobChoice = readline();

        // 根據職業設定屬性
        if ($jobChoice === '1') {
            $jobName = '劍士';
            $healthPoints = 100;
            $magicPoints = 0;
        } elseif ($jobChoice === '2') {
            $jobName = '法師';
            $healthPoints = 50;
            $magicPoints = 50;
        } else {
            echo "無效的選擇！請重新輸入。\n";
            return $this->create($conn);
        }
        
        // 初始提供10點屬性點可自行分配
        echo "您有10點屬性點可自行分配!\n";
        $remainingPoints = 10; // 初始化剩餘點數為 10 點
        echo "是否進行分配? \n";
        echo "1. 是\n";
        echo "2. 否\n";
        $assignationChoice = readline();

        if ($assignationChoice === '1') {
            $attackPoints = intval(readline("請分配物理攻擊力點數: \n"));
            $remainingPoints -= $attackPoints;
            echo "剩餘可分配點數為: " . $remainingPoints . "點\n";
            
            $defensePoints = intval(readline("請分配物理防禦力點數: \n"));
            $remainingPoints -= $defensePoints;
            echo "剩餘可分配點數為: " . $remainingPoints . "點\n";
            
            $magicAttackPoints = intval(readline("請分配魔法攻擊力點數: \n"));
            $remainingPoints -= $magicAttackPoints;
            echo "剩餘可分配點數為: " . $remainingPoints . "點\n";
            
            $magicDefensePoints = intval(readline("請分配魔法防禦力點數: \n"));
            $remainingPoints -= $magicDefensePoints;
            echo "剩餘可分配點數為: " . $remainingPoints . "點\n";
            
            $luckPoints = intval(readline("請分配幸運值點數: \n"));
            $remainingPoints -= $luckPoints;
            echo "剩餘可分配點數為: " . $remainingPoints . "點\n";
        } elseif ($assignationChoice === '2') {
            // 設定能力點數初始值
            $remainingPoints = 10;
            // 初始化各属性點為0
            $attackPoints = 0;
            $defensePoints = 0;
            $magicAttackPoints = 0;
            $magicDefensePoints = 0;
            $luckPoints = 0;
        
            // 剩餘屬性點為0才停止迴圈
            while ($remainingPoints > 0) {
                // 隨機分配1到剩餘點數之間的點數到各屬性
                $randomPoints = rand(1, $remainingPoints);
                
                // 隨機選擇一個屬性並分配點數
                $randomAttribute = rand(1, 5); // 將各屬性先標號
                if ($randomAttribute === 1) {
                    $attackPoints += $randomPoints;
                } elseif ($randomAttribute === 2) {
                    $defensePoints += $randomPoints;
                } elseif ($randomAttribute === 3) {
                    $magicAttackPoints += $randomPoints;
                } elseif ($randomAttribute === 4) {
                    $magicDefensePoints += $randomPoints;
                } elseif ($randomAttribute === 5) {
                    $luckPoints += $randomPoints;
                }
                // 更新剩餘屬性點
                $remainingPoints -= $randomPoints;
            }
        } else {
            // 無效選擇
            echo "無效的選擇! 請重新進行選擇。\n";
            return $this->create($conn);
        }

        // 使用 Player 類的方法創建角色
        $player = new Player($conn);
        $player->createCharacter($playerName, $jobChoice, $jobName, $healthPoints, $attackPoints, $defensePoints, $magicPoints, $magicAttackPoints, $magicDefensePoints, $luckPoints);
        echo "===== 角色創建中! =====\n";
        sleep(3);
        if ($player) {
            echo "已成功建立角色! 您的角色資訊如下: \n";
            sleep(1);
            echo ".\n";
            echo ".\n";
            echo ".\n";
            echo "角色名稱: " . $playerName . "\n";
            echo "職業: " . $jobName . "\n";
            echo "生命值: " . $healthPoints . "\n";
            echo "物理攻擊力: " . $attackPoints . "\n";
            echo "物理防禦力: " . $defensePoints. "\n";
            echo "魔力值: " . $magicPoints . "\n";
            echo "魔法攻擊力: " . $magicAttackPoints . "\n";
            echo "魔法防禦力: " . $magicDefensePoints . "\n";
            echo "幸運值: " . $luckPoints. "\n";
            echo "=======================\n";
            echo ".\n";
            echo ".\n";
            echo ".\n";
            // 成功建立角色後回到主選單
            echo "請問是否以此角色直接開始遊戲？\n";
            echo "1. 返回主選單\n";
            echo "2. 開始遊戲\n";
            $menuChoice = readline();
            
            if ($menuChoice === '1') {
                $mainMenu = new MainMenu();
                $mainMenu->showMainMenu();
            } elseif ($menuChoice === '2') {
                // 繼續遊戲
                // 在此放置繼續遊戲的程式碼
            } else {
                // 無效選擇，重新詢問
                echo "無效的選擇! 請重新進行選擇。\n";
            }

        } else {
            echo "創建角色失敗! 請重新創建角色! \n";
            return $this->create($conn);
        }
    }
}

// // 属性分配器類別
// class AttributeAllocator {
//     // 隨機分配剩餘屬性點並返回分配結果的陣列
//     public static function randomAssign($remainingPoints) {
//         // 初始化各属性點為0
//         $attackPoints = 0;
//         $defensePoints = 0;
//         $magicAttackPoints = 0;
//         $magicDefensePoints = 0;
//         $luckPoints = 0;

//         // 剩餘屬性點為0才停止迴圈
//         while ($remainingPoints > 0) {
//             // 隨機分配1到剩餘點數之間的點數到各屬性
//             $randomPoints = rand(1, $remainingPoints);
            
//             // 隨機選擇一個屬性並分配點數
//             $randomAttribute = rand(1, 5); // 將各屬性先標號
//             if ($randomAttribute === 1) {
//                 $attackPoints += $randomPoints;
//             } elseif ($randomAttribute === 2) {
//                 $defensePoints += $randomPoints;
//             } elseif ($randomAttribute === 3) {
//                 $magicAttackPoints += $randomPoints;
//             } elseif ($randomAttribute === 4) {
//                 $magicDefensePoints += $randomPoints;
//             } elseif ($randomAttribute === 5) {
//                 $luckPoints += $randomPoints;
//             }
            
//             // 更新剩餘屬性點
//             $remainingPoints -= $randomPoints;
//         }

//         // 返回分配結果的陣列
//         return [
//             'attackPoints' => $attackPoints,
//             'defensePoints' => $defensePoints,
//             'magicAttackPoints' => $magicAttackPoints,
//             'magicDefensePoints' => $magicDefensePoints,
//             'luckPoints' => $luckPoints
//         ];
//     }
// }