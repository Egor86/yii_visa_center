<?php

use yii\db\Migration;

/**
 * Handles the creation of table `customer_order`.
 */
class m170501_093054_create_customer_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('customer_order', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'order_time' => $this->dateTime()->notNull()
        ]);

        // add foreign key for table `customer`
        $this->addForeignKey(
            'fk-customer_order-customer_id',
            'customer_order',
            'customer_id',
            'customer',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `customer`
        $this->dropForeignKey(
            'fk-customer_order-customer_id',
            'customer_order'
        );

        $this->dropTable('customer_order');
    }
}
