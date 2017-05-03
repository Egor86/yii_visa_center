<?php

use yii\db\Migration;

/**
 * Handles the creation of table `application_form`.
 */
class m170416_134247_create_customer_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('customer', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255),
            'email' => $this->string(255)->notNull()->unique(),
            'passport' => $this->string(255),
            'citizenship' => $this->string(255),
            'password_hash' => $this->char(60)->notNull(),
            'access_token' => $this->text()->notNull(),
            'status' => $this->boolean()->defaultValue(2),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('customer');
    }
}
