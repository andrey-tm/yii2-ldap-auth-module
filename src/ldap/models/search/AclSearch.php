<?php

namespace templatemonster\ldapauth\models\search;

use yii\data\ActiveDataProvider;
use templatemonster\ldapauth\models\Acl;

class AclSearch extends Acl
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['ldap_group', 'rbac_roles'],
                'string'
            ],
            [
                ['ldap_group', 'rbac_roles'],
                'safe'
            ],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Acl::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'ldap_group', $this->ldap_group])
              ->andFilterWhere(['like', 'rbac_roles', $this->rbac_roles]);

        return $dataProvider;
    }
}
