<?php
/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

use <?= $generator->getBaseControllerNamespace() ?>\RestApiBaseController;

/**
 * Default controller for the `<?= $generator->moduleID ?>` module
 */
class DefaultController extends RestApiBaseController
{
    /**
     * Lists all <?= $generator->moduleID ?> models.
     * @OA\Get(
     *     path="/<?= $generator->moduleID ?>/default",
     *     tags={"默认模块控制器"},
     *     summary="列表",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=403,
     *         description="无权访问"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="响应结果"
     *     )
     * )
     */
    public function actionIndex()
    {
        return $this->success([
            'controller' => 'default',
            'action' => 'index',
            'doc' => '测试接口'
        ]);
    }
}
