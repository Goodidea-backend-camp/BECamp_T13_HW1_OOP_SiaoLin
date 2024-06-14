<?php
class Enemy {
    private $name; // 敵人名稱
    private $type; // 敵人類型
    private $healthPoints; // 敵人生命值
    private $attackPoints; // 敵人攻擊力
    private $defensePoints; // 敵人防禦力
    private $magicDefensePoints; // 敵人魔法防禦力
    private $isBoss; // 判斷是否為魔王的屬性
    public $isDefending = false; // 判斷是否防禦的屬性

    // 初始化敵人
    public function __construct($level) {
        $bossInitialHealthPoint = 100; // 魔王初始生命值
        $bossInitialAttackPoint = 20; // 魔王初始攻擊力
        $bossDefensePoints = 10; // 魔王初始防禦力
        $bossMagicDefensePoints = 10; // 魔王初始魔法防禦力
        $bossHealthPointIncrement = 20; // 增加生命值
        $bossAttackPointIncrement = 10; // 增加攻擊力

        $minionInitialHealthPoint = 30; // 小怪初始生命值
        $minionInitialAttackPoint = 5; // 小怪初始攻擊力
        $minionDefensePoints = 5; // 小怪初始防禦力
        $minionMagicDefensePoints = 5; // 小怪初始魔法防禦力
        $minionHealthPointIncrement = 5; // 增加生命值
        $minionAttackPointIncrement = 3; // 增加攻擊力
        

        // 如果當前的關卡數能被5整除，則認為這關是魔王關卡並生成魔王，反之是小怪
        if ($level % 5 === 0) {
            $this->name = "安茲烏爾恭";
            $this->type = '魔王';
            $this->healthPoints = $bossInitialHealthPoint + ($level / 5 - 1) * $bossHealthPointIncrement;
            $this->attackPoints = $bossInitialAttackPoint + ($level / 5 - 1) * $bossAttackPointIncrement;
            $this->defensePoints = $bossDefensePoints;
            $this->magicDefensePoints = $bossMagicDefensePoints;
            $this->isBoss = TRUE;
        } else {
            $this->name = "小怪獸";
            $this->type = '小怪';
            $this->healthPoints = $minionInitialHealthPoint + ($level - 1) * $minionHealthPointIncrement;
            $this->attackPoints = $minionInitialAttackPoint + ($level - 1) * $minionAttackPointIncrement;
            $this->defensePoints = $minionDefensePoints;
            $this->magicDefensePoints = $minionMagicDefensePoints;
            $this->isBoss = false;
        }
    }
    //取得敵人名稱的值
    public function getName() {
        return $this->name;
    }

    // 取得敵人類型的值
    public function getType() {
        return $this->type;
    }

    // 取得敵人生命值的值
    public function getHealthPoints() {
        return $this->healthPoints;
    }

    // 取得敵人攻擊力的值
    public function getAttackPoints() {
        return $this->attackPoints;
    }

    // 取得敵人防禦力的值
    public function getDefensePoints() {
        return $this->defensePoints;
    }

    // 取得敵人魔法防禦力的值
    public function getMagicDefensePoints() {
        return $this->magicDefensePoints;
    }

    // 承受傷害
    public function takeDamage($damage) {
        $this->healthPoints -= $damage;
    }

    // 攻擊行動
    public function attack($player) {
        if ($player->isDefending) {
            // 玩家防禦時的傷害計算
            $damage = $this->calculateDamage($this->attackPoints, $player->defensePoints, $player->isDefending);
        } else {
            // 玩家未防禦時的傷害計算
            $damage = $this->attackPoints;
        }
        // 玩家受傷
        $player->takeDamage($damage);
    }

    // 防禦行動
    public function defend() {
        $this->isDefending = TRUE;
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

    // 取得敵人經驗值
    public function getExperience() {
        return $this->isBoss ? 20 : 10; // 每打倒一個小怪獲得10經驗值，魔王獲得20經驗值
    }
}
