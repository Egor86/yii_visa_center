<?php

use yii\db\Migration;

/**
 * Handles the creation of table `application_form`.
 */
class m170416_134247_create_application_form_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('application_form', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255),
            'email' => $this->string(255)->notNull()->unique(),
            'passport' => $this->string(255),
            'citizenship' => $this->string(255),
            'password' => $this->text()->notNull(),
            'access_token' => $this->text(),
            'status' => $this->boolean()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('application_form');
    }
}
