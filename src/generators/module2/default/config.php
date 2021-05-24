<?php
/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module2\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

return [
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'cookieValidationKey' => '<?=Yii::$app->security->generateRandomString() ?>',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'user' => [
            'identityClass' => '<?= $generator->getModelNamespace() ?>\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
    ],
    'params' => [
        'user.accessTokenExpire' => 10800, // 登录秘钥默认有效时间是3小时
    ],
];