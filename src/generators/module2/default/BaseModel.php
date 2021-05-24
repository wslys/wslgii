<?php
/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module2\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getBaseModelNamespace() ?>;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Application;

/**
 * @property-read \yii\web\IdentityInterface|null $loginUser
 */
class BaseModel extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db;
    }

    /**
     * @var \yii\web\IdentityInterface|null
     */
    public static $loginUser;

    /*
     * 初始化模型基础数据
     */
    public function init()
    {
        parent::init();
        if (Yii::$app instanceof Application) {
            if (!Yii::$app->user->isGuest) {
                self::$loginUser = Yii::$app->user->identity;
            }
        }
    }

    // 登录用户
    public function getLoginUser() {
        if (Yii::$app instanceof Application) {
            if (!Yii::$app->user->isGuest) {
                return Yii::$app->user->identity;
            }
        }
        return null;
    }

    public static function loginUser() {
        if (Yii::$app instanceof Application) {
            if (!Yii::$app->user->isGuest) {
                return Yii::$app->user->identity;
            }
        }
        return null;
    }

    public function getErrorMessage() {
        $errors = $this->getFirstErrors(); // 得到第一条的错误信息
        if(!$errors || !is_array($errors)) return '';
        return array_shift($errors);
    }

    public function formatSearch($dataProvider) {
        if (!$dataProvider instanceof ActiveDataProvider) {
            return [
                'list' => [],
                'count' => 0
            ];
        }

        return [
            'list' => $dataProvider->getModels(),
            'count' => $dataProvider->totalCount
        ];
    }
}
