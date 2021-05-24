<?php
/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module\Generator */

echo "<?php\n";
?>
namespace <?= $generator->getSwaggerNamespace() ?>;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="<?= $generator->moduleProjectDoc ?>",
 *     termsOfService= "http://example.com/terms/",
 *     @OA\Contact(
 *         name="API支持",
 *         url="https://gitee.com/wslys/yii2-swageer-gii",
 *         email="wsl_ys@163.com"
 *     ),
 *     @OA\License(
 *         name="Nginx 1.18",
 *         url="http://nginx.org/"
 *     )
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="Authorization",
 *     type="http",
 *     scheme="Bearer",
 *     bearerFormat="JWT",
 * ),
 */

