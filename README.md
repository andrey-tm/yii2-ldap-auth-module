# yii2-ldap-auth-module
Yii2 extension which allows to authenticate user via LDAP. Based on Yii2 Boilerplate. Also provides you LDAP groups to RBAC roles mapper.

##Installation

The preferred way to install this extension is through composer.
First add

```
{
   "type":"git",
   "url":"https://github.com/andrey-tm/yii2-ldap-auth-module"
}
```

to the repositories section of your application's composer.json file.
Than add 

```
"templatemonster/ldap-auth" : "*"
```

to the require section.

##Usage

Add the following in your web config file:

```php
...
'bootstrap' => ['ldap'],
...
'modules' => [
    ...
    'ldap' => [,
        'class' => 'templatemonster\ldapauth\Module',
        'host' => 'localhost',
        'port' => 389,
        'baseDN' => 'dc=example,dc=com',
        'userDN' => 'uid={$username},ou=Users,dc=example,dc=com',
        'groupDN' => 'ou=Groups,dc=example,dc=com',
    ],
    ...
],
...
'as globalAccess'=>[
    ...
    'rules'=>[
        ...
        [
            'controllers'=>['ldap/acl'],
            'allow' => true,
            'roles' => ['administrator'],
        ],
        [
            'controllers'=>['ldap/acl'],
            'allow' => false,
        ],
        ...
    ],
    ...
],
...
```


In your console config file in migration section replace class property and add configFiles array property.
configFiles is array of configs where component will scan for migration aware modules.

```php
...
'controllerMap' => [
    ...
    'migrate'=>[
        'class' => 'indigerd\migrationaware\controllers\MigrateController',
        'configFiles' => [
            '@backend/config/web.php',
        ],
        'migrationPath'=>'@common/migrations/db', //leave as it was before
        'migrationTable'=>'{{%system_db_migration}}' //leave as it was before
    ],
    ...
],
...
```

Create migration for initial RBAC roles assignment with ldap groups.
For example:

```php
<?php

use yii\db\Migration;
use common\models\User;

class m161011_115434_assign_ldap_roles extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        /** @var \yii\rbac\DbManager $auth */
        $auth = \Yii::$app->ldapGroupsManager;

        $this->insert('{{%ldap_group}}', [
            'id' => 1,
            'ldap_group' => 'Administrators',
            'rbac_roles' => User::ROLE_ADMINISTRATOR
        ]);
        $auth->assign($auth->getRole(User::ROLE_ADMINISTRATOR), 1);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%ldap_group}}', ['id' => [1]]);
        \Yii::$app->ldapGroupsManager->revokeAll(1);
    }
}
```

Run migrations

```
php console/yii migrate
```

Add the following in layout file (for example in /backend/views/layouts/common.php in Menu section)

```php
...
[
    'label'=>Yii::t('backend', 'ACL Roles'),
    'icon'=>'<i class="fa fa-user-plus"></i>',
    'url'=>['/ldap/acl/index'],
    'visible'=>Yii::$app->user->can('administrator')
],
...
```

##License

yii2-ldap-auth-module is released under the MIT License. See the bundled LICENSE file for details.

