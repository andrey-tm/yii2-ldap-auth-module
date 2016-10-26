<?php

use yii\db\Migration;

class m161007_145213_create_ldap_group_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%ldap_group}}', [
            'id' => $this->primaryKey(),
            'ldap_group' => $this->string()->notNull(),
            'rbac_roles' => $this->string(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%ldap_group}}');
    }
}
