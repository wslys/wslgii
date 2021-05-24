<?php
/* @var $this yii\web\View */
/* @var $generator wsl\gii\generators\module2\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getModelNamespace() ?>;

use Yii;
use yii\base\Model;

/**
 * Login form
 *
 * @property User|null $LoginUser 登录用户
 */
class LoginForm extends Model
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTER = 'register';

    public $username;
    public $password;
    public $rememberMe = true;
    public $email;
    public $access_token = '';

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password', 'email'], 'required'],
            // rememberMe must be a boolean value
            /*['rememberMe', 'boolean'],*/
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = ['username', 'password'];
        $scenarios[self::SCENARIO_REGISTER] = ['username', 'password', 'email'];
        return $scenarios;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码不正确。');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->access_token = $this->_user->generateAccessToken();
            if (!$this->access_token) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            // TODO 此处可以扩展，以支持多种登录方式【如何：用户名/电话/邮箱】
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * 登录用户
     *
     * @return mixed
     */
    public function getLoginUser() {
        return $this->_user;
    }

    /**
     * 格式化错误信息为字符串
     *
     * @return mixed|string
     */
    public function getErrorMessage() {
        $errors = $this->getFirstErrors();
        if(!$errors || !is_array($errors)) return '';
        return array_shift($errors);
    }
}
