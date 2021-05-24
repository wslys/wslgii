<?php
/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

namespace <?= $ns ?>;

use Yii;
/**
 * <?= $generator->moduleID ?> module definition class
 */
class <?= $className ?> extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // 模块配置
        $config = require(__DIR__.'/config.php');
        // 获取应用程序的组件
        $components = Yii::$app->getComponents();

        // 遍历子模块独立配置的组件部分，并继承应用程序的组件配置
        foreach( $config['components'] AS $k=>$component ){
            if( isset($component['class']) && isset($components[$k]) == false ) continue;
            $config['components'][$k] = array_merge($components[$k], $component);
        }

        // params
        Yii::$app->params = yii\helpers\ArrayHelper::merge(Yii::$app->params, $config['params']);

        // 将新的配置设置到应用程序
        // 很多都是写 Yii::configure($this, $config)，但是并不适用子模块，必须写 Yii::$app
        Yii::configure(Yii::$app, $config);
        // custom initialization code goes here
    }
}
