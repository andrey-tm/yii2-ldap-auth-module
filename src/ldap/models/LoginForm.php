<?php
namespace templatemonster\ldapauth\models;

use Yii;
use yii\base\Model;
use yii\web\ForbiddenHttpException;
use cheatsheet\Time;
use common\models\User;
use backend\models\UserForm;
use templatemonster\ldapauth\Module;
use templatemonster\ldapauth\services\LdapAuth;

class LoginForm extends Model
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var bool
     */
    public $rememberMe = true;

    /**
     * @var User
     */
    private $user;

    /**
     * @var LdapAuth
     */
    private $ldapAuth;

    public function init()
    {
        parent::init();
        $this->ldapAuth = Module::getInstance()->ldapAuth;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('backend', 'Username'),
            'password' => Yii::t('backend', 'Password'),
            'rememberMe' => Yii::t('backend', 'Remember Me')
        ];
    }

    /**
     * Validates the user's password via LDAP.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            if (!$this->ldapAuth->authenticate($this->username, $this->password)) {
                $this->addError('password', Yii::t('backend', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     * @throws ForbiddenHttpException
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->getUser()) {
            $this->registerUser();
        }
        $duration = $this->rememberMe ? Time::SECONDS_IN_AN_HOUR * 4 : 0;
        if (Yii::$app->user->login($this->getUser(), $duration)) {
            $this->ldapAuth->syncRbacRoles($this->getUser(), new AclForm(), Yii::$app->getAuthManager());
            if (!Yii::$app->user->can('loginToBackend')) {
                Yii::$app->user->logout();
                throw new ForbiddenHttpException;
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
    public function getUser()
    {
        if (is_null($this->user)) {
            $this->user = User::find()
                ->andWhere(['or', ['username'=>$this->username], ['email'=>$this->username]])
                ->one();
        }

        return $this->user;
    }

    /**
     * Register new user in DB
     *
     * @return mixed
     */
    private function registerUser()
    {
        $userForm = new UserForm();
        $userForm->setScenario('create');
        $userForm->load($this->toArray(), '');
        $userForm->email = $this->ldapAuth->getUserEmail($this->username);
        $userForm->status = User::STATUS_ACTIVE;
        return $userForm->save();
    }
}
