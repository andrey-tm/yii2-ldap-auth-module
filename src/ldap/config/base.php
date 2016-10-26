<?php
return [
    'ldapGroupsManager' => [
        'class' => 'yii\rbac\DbManager',
        'itemTable' => '{{%rbac_auth_item}}',
        'itemChildTable' => '{{%rbac_auth_item_child}}',
        'assignmentTable' => '{{%ldap_auth_assignment}}',
        'ruleTable' => '{{%rbac_auth_rule}}'
    ],
];