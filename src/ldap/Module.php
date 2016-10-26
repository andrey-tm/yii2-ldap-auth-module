<?php

namespace templatemonster\ldapauth;

use Yii;
use yii\base\InvalidConfigException;
use templatemonster\ldapauth\services\LdapAuth;
use templatemonster\ldapauth\wrappers\LdapWrapper;

class Module extends \yii\base\Module implements \indigerd\migrationaware\MigrationAwareInterface
{
    /**
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * @var int
     */
    public $port = 389;

    /**
     * @var string
     */
    public $baseDN = '';

    /**
     * @var string
     */
    public $userDN = '';

    /**
     * @var string
     */
    public $groupDN = '';

    /**
     * @var LdapAuth
     */
    public $ldapAuth;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (is_null(Yii::$app->getAuthManager())) {
            throw new InvalidConfigException('You should configure "authManager" component for your application  to use Ldap-Auth module');
        }

        Yii::$classMap['backend\controllers\SignInController'] = __DIR__ . '/controllers/SignInController.php';
        Yii::$classMap['backend\controllers\UserController'] = __DIR__ . '/controllers/UserController.php';
        Yii::$app->setComponents(require(__DIR__ . '/config/base.php'));

        $this->ldapAuth = new LdapAuth([
            'ldap' => new LdapWrapper($this->host, $this->port),
            'baseDN' => $this->baseDN,
            'userDN' => $this->userDN,
            'groupDN' => $this->groupDN
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getMigrationPath()
    {
        return __DIR__.'/migrations';
    }
}
