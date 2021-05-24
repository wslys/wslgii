<?php
/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module2\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

use <?= $generator->getModelNamespace() ?>\LoginForm;
use <?= $generator->getModelNamespace() ?>\User;
use Yii;
use <?= $generator->getBaseControllerNamespace() ?>\RestApiBaseController;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * @OA\Tag(
 *   name="登录管理",
 *   description="包含login / logout 接口",
 *   @OA\ExternalDocumentation(
 *     description="更多相关",
 *     url="TODO"
 *   )
 * )
 */
class AuthController extends RestApiBaseController
{
    /**
     * @OA\Post(
     *     path="/<?= $generator->moduleID ?>/auth/login",
     *     tags={"登录管理"},
     *     operationId="login",
     *     summary="登录",
     *     description="用户名+密码登录接口",
     *    @OA\RequestBody(
     *        required=true,
     *        description="登录账号信息",
     *        @OA\JsonContent(ref="#/components/schemas/LoginParams")
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/LoginResult")
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="无效的输入",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="失败",
     *     )
     * )
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        // 设置登录场景
        $model->setScenario(LoginForm::SCENARIO_LOGIN);
        if ($model->load($this->PostData(), '') && $model->login()) {
            return $this->success([
                'token' => $model->access_token,
                'authority' => $model->loginUser->authority,
                'userinfo' => $model->loginUser,
            ]);
        }

        return $this->fail($model->getErrorMessage() || '登录失败', $model);
    }

    /**
     * @OA\Post(
     *     path="/<?= $generator->moduleID ?>/auth/logout",
     *     tags={"登录管理"},
     *     summary="退出登录",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function actionLogout() {
        $loginUser = $this->loginUser;

        if (!$loginUser || !($loginUser instanceof User)) {
            Yii::$app->user->logout();
            return $this->fail('您已退出');
        }

        if (!$loginUser->logoutByAccessToken()) {
            return $this->fail($loginUser->getErrorMessage() || '退出登录失败');
        }

        return $this->success($loginUser, 0, '已退出登录');
    }
}

/** ==================== 【swagger接口注释】为了不污染别的地方，将登录数据描述写在此处 ========================== **/
/* LoginParams */
/**
 * @OA\Schema(
 *      schema="LoginParams",
 *     @OA\Property(
 *        property="username",
 *        description="用户名/电话/邮箱",
 *        type="string",
 *        maxLength=100,
 *        example="admin"
 *    ),
 *     @OA\Property(
 *        property="password",
 *        description="密码",
 *        type="string",
 *        maxLength=32,
 *        example="123456"
 *    )
 * )
 */

/* LoginResult */
/**
 * @OA\Schema(
 *      schema="LoginResult",
 *     @OA\Property(
 *        property="token",
 *        description="登录成功秘钥",
 *        type="string"
 *    ),
 *     @OA\Property(
 *        property="authority",
 *        description="权限【具体的角色名称】",
 *        type="string",
 *        maxLength=32,
 *    ),
 *     @OA\Property(
 *        property="userinfo",
 *        description="登录用户信息",
 *        type="object",
 *        ref="#/components/schemas/LoginUserInfo"
 *    )
 * )
 */

/**
 * @OA\Schema(
 *     schema="LoginUserInfo",
 *     @OA\Property(
 *        property="id",
 *        description="",
 *        type="integer",
 *        format="int64",
 *    ),
 *     @OA\Property(
 *        property="username",
 *        description="用户名",
 *        type="string",
 *        maxLength=255,
 *    ),
 *     @OA\Property(
 *        property="phone",
 *        description="电话",
 *        type="string",
 *        maxLength=20,
 *    ),
 *     @OA\Property(
 *        property="email",
 *        description="邮箱",
 *        type="string",
 *        maxLength=255,
 *    ),
 *     @OA\Property(
 *        property="status",
 *        description="用户状态 0禁用户 9未激活 10已激活",
 *        type="integer",
 *        format="int64",
 *        default="10",
 *    )
 * )
 */