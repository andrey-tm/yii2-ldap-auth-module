<?php

namespace templatemonster\ldapauth\models;

use yii\db\ActiveRecord;

/**
 * Acl model
 *
 * @property integer $id
 * @property string $ldap_group
 * @property string $rbac_roles
 */
class Acl extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ldap_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['ldap_group'],
                'required'
            ],
            [
                ['ldap_group', 'rbac_roles'],
                'string'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
}
