<?php

namespace templatemonster\ldapauth\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class AclForm extends Model
{
    /**
     * @var string
     */
    public $ldap_group;

    /**
     * @var string
     */
    public $rbac_roles;

    /**
     * @var mixed
     */
    public $roles;

    /**
     * @var Acl
     */
    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['ldap_group', 'required'],
            ['ldap_group', 'unique', 'targetClass' => Acl::className(), 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                }
            }],
            [['roles'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
                    Yii::$app->getAuthManager()->getRoles(),
                    'name'
                )]
            ],
        ];
    }

    /**
     * @return Acl
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new Acl();
        }
        return $this->model;
    }

    /**
     * @param Acl $model
     * @return Acl
     */
    public function setModel($model)
    {
        $this->ldap_group = $model->ldap_group;
        $this->model = $model;
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->ldapGroupsManager->getRolesByUser($model->getId()),
            'name'
        );
        return $this->model;
    }

    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();

            $model->ldap_group = $this->ldap_group;
            $model->rbac_roles = (is_array($this->roles)) ? join(', ', $this->roles) : '';
            if (!$model->save()) {
                throw new Exception('Model not saved');
            }

            /** @var \yii\rbac\DbManager $auth */
            $auth = Yii::$app->ldapGroupsManager;
            $auth->revokeAll($model->getId());

            if ($this->roles && is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $auth->assign($auth->getRole($role), $model->getId());
                }
            }
            return !$model->hasErrors();
        }

        return null;
    }
}