<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace wsl\gii\generators\module2;

use wsl\gii\CodeFile;
use yii\helpers\Html;
use Yii;
use yii\helpers\StringHelper;

/**
 * This generator will generate the skeleton code needed by a module.
 *
 * @property string $controllerNamespace The controller namespace of the module. This property is read-only.
 * @property bool $modulePath The directory that contains the module class. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \wsl\gii\Generator
{
    public $moduleClass;
    public $moduleID;
    public $moduleProjectDoc;
    public $hasLogin = true; // 是否初始化登录退出接口


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Module2 Generator';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '生成RESTFUL风格的API【Yii模块】，其中集成了 swagger在线api文档、授权登录、基于access_token方式访问接口';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleID', 'moduleClass'], 'filter', 'filter' => 'trim'],
            [['moduleID', 'moduleClass'], 'required'],
            ['moduleProjectDoc', 'string'],
            [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['moduleClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['moduleClass'], 'validateModuleClass'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'moduleID' => 'Module ID',
            'moduleClass' => 'Module Class',
            'moduleProjectDoc' => '模块说明',
            'hasLogin' => '是否初始化好登录接口',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return [
            'moduleID' => 'This refers to the ID of the module, e.g., <code>admin</code>.',
            'moduleClass' => 'This is the fully qualified class name of the module, e.g., <code>app\modules\admin\Module</code>.',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function successMessage()
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);

            return "已成功生成模块。你可以 $link.";
        }

        $output = <<<EOD
<p>已成功生成模块。</p>
<p>要访问该模块，您需要将其添加到应用程序配置中，Swagger在线API文档访问配置：</p>
EOD;
        $code = <<<EOD
<?php
    模块配置 ...
    'modules' => [
        '{$this->moduleID}' => [
            'class' => '{$this->moduleClass}',
        ],
    ],
    ......

    请在控制器 SiteController.php 中配置swagger接口文档的访问配置
    1 ...
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['doc', 'api'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }
    
    2 ...
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => yii\helpers\Url::to(['/site/api'], true),
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => [
                    Yii::getAlias('@backend/modules/{$this->moduleID}/swagger'),
                    Yii::getAlias('@backend/modules/{$this->moduleID}/controllers'),
                    Yii::getAlias('@backend/modules/{$this->moduleID}/models'),
                ],
                'api_key' => 'api',
            ],
        ];
    }
    ......
EOD;

        return $output . '<pre>' . highlight_string($code, true) . '</pre>';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['module.php', 'controller.php', 'view.php'];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath . '/' . StringHelper::basename($this->moduleClass) . '.php',
            $this->render("module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config.php',
            $this->render("config.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/DefaultController.php',
            $this->render("controller.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/base/RestApiBaseController.php',
            $this->render("RestApiBaseController.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/models/base/BaseModel.php',
            $this->render("BaseModel.php")
        );

        // hasLogin
        if ($this->hasLogin) {
            $files[] = new CodeFile($modulePath . '/models/LoginForm.php', $this->render("LoginForm.php"));
            // TODO 自动从数据库读取用户表来初始化User模型 + 加检查是否基本字段是否满足
            $files[] = new CodeFile($modulePath . '/models/User.php', $this->render("User.php"));
            $files[] = new CodeFile($modulePath . '/controllers/AuthController.php', $this->render("AuthController.php"));
        }

        // swagger
        $files[] = new CodeFile(
            $modulePath . '/swagger/definitions.php',
            $this->render("swagger/definitions.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/swagger/security.php',
            $this->render("swagger/security.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/swagger/swagger.php',
            $this->render("swagger/swagger.php")
        );

        return $files;
    }

    /**
     * Validates [[moduleClass]] to make sure it is a fully qualified class name.
     */
    public function validateModuleClass()
    {
        if (strpos($this->moduleClass, '\\') === false || Yii::getAlias('@' . str_replace('\\', '/', $this->moduleClass), false) === false) {
            $this->addError('moduleClass', 'Module class must be properly namespaced.');
        }
        if (empty($this->moduleClass) || substr_compare($this->moduleClass, '\\', -1, 1) === 0) {
            $this->addError('moduleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\\modules\\admin\\Module".');
        }
    }

    /**
     * @return bool the directory that contains the module class
     */
    public function getModulePath()
    {
        return Yii::getAlias('@' . str_replace('\\', '/', substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\'))));
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\controllers';
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getBaseControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\base';
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getSwaggerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\swagger';
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getModelNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\models';
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getBaseModelNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\models\base';
    }
}
