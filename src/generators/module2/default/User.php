<?php
/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module2\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getModelNamespace() ?>;

use Yii;
use <?= $generator->getModelNamespace() ?>\base\BaseModel;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * @OA\Schema(
 *     schema="User",
 *      required={"username","auth_key","password_hash","access_token","email","created_at","updated_at"},
 *     @OA\Property(
 *        property="id",
 *        description="",
 *        type="integer",
 *        format="int64",
 *    ),
 *     @OA\Property(
 *        property="username",
 *        description="",
 *        type="string",
 *        maxLength=255,
 *    ),
 *     @OA\Property(
 *        property="auth_key",
 *        description="",
 *        type="string",
 *        maxLength=32,
 *    ),
 *     @OA\Property(
 *        property="password_hash",
 *        description="",
 *        type="string",
 *        maxLength=255,
 *    ),
 *     @OA\Property(
 *        property="access_token",
 *        description="",
 *        type="string",
 *        maxLength=32,
 *    ),
 *     @OA\Property(
 *        property="expire",
 *        description="",
 *        type="integer",
 *        format="int64",
 *        default="0",
 *    ),
 *     @OA\Property(
 *        property="password_reset_token",
 *        description="",
 *        type="string",
 *        maxLength=255,
 *    ),
 *     @OA\Property(
 *        property="email",
 *        description="",
 *        type="string",
 *        maxLength=255,
 *    ),
 *     @OA\Property(
 *        property="status",
 *        description="",
 *        type="integer",
 *        format="int64",
 *        default="10",
 *    ),
 *     @OA\Property(
 *        property="verification_token",
 *        description="",
 *        type="string",
 *        maxLength=255,
 *    ),
 * )
 */

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $access_token
 * @property int $expire
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $verification_token
 */
class User extends BaseModel implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'access_token', 'email', 'created_at', 'updated_at'], 'required'],
            [['expire', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key', 'access_token'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'access_token' => '登录TOKEN',
            'expire' => '登录过期时间',
            'password_reset_token' => '重置密码Token',
            'email' => '邮箱',
            'status' => '状态',
            'created_at' => '创建时间',
            'verification_token' => 'Verification Token',
            'updated_at' => '更新时间',
        ];
    }


    public function fields()
    {
        $fields = parent::fields();
        $customFields = [
            'created_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at,'php:c');
            },
            'updated_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->updated_at,'php:c');
            },
        ];
        unset($fields['deleted_at']);

        return ArrayHelper::merge($fields, $customFields);
    }

    // fields() 方法主要是对数据库字段进行过滤、重写等操作，而 extraFields() 方法主要是对数据库数据进行字段拓展，比如上面的例子新增了一个avatar_url
    public function extraFields()
    {
        $fields = parent::extraFields();

        // $fields['avatar_url'] = function () {
        // 不存在此属性，测试
        //     return empty($this->avatar_path) ? '可以设置一个默认的头像地址' : 'http://static.domian.com/' . $this->avatar_path;
        // };

        return $fields;
    }

    /**
     * 用户所属角色
     *
     * TODO 需要自己实现【或者使用yii框架自带的rbac来实现】
     * @return string
     */
    public function getAuthority() {
        return 'admin';
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
        // throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 获取Access-token
     * @return string|null
     * @throws \yii\base\Exception
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();

        // 登录成功， 设置过期时间
        $this->expire = time() + Yii::$app->params['user.accessTokenExpire'];

        if ($this->save()) {
            return $this->access_token;
        }
        return '';
    }

    /**
     * 清空登录时间以及TOKEN
     */
    public function logoutByAccessToken() {
        $this->access_token = Yii::$app->security->generateRandomString();
        $this->expire = time();
        return $this->save();
    }

    /**
     * 更新登录过期时间
     */
    public function resetAccessTokenExpire()
    {
        $this->expire = time() + Yii::$app->params['user.accessTokenExpire'];
        $this->save();
    }

    /**
     * @return bool
     */
    public function validateAccessTokenExpire() {
        return $this->expire > time();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
