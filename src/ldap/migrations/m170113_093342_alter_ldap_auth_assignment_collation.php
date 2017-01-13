<?php

use yii\db\Migration;

class m170113_093342_alter_ldap_auth_assignment_collation extends Migration
{
    /**
     * @var string $tableName
     */
    private static $tableName = '{{%ldap_auth_assignment}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $query = sprintf("
            ALTER TABLE %s
            CONVERT TO CHARACTER SET utf8
            COLLATE utf8_unicode_ci
            ",
            self::$tableName
        );

        $this->db
            ->createCommand($query)
            ->query();
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $query = sprintf("
            ALTER TABLE %s
            CONVERT TO CHARACTER SET utf8
            COLLATE utf8_general_ci
            ",
            self::$tableName
        );

        $this->db
            ->createCommand($query)
            ->query();
    }
}
