<?php

use yii\db\Migration;

/**
 * Class m211014_030420_alter_table_orders_remove_created_at_foreign_key
 */
class m211014_030420_alter_table_orders_remove_created_at_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-orders-created_at}}',
            '{{%orders}}'
        );

        //drops index for column `created_at`
        $this->dropIndex(
            '{{%idx-orders-created_at}}',
            '{{%orders}}'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
        
    }
    // drops foreign key for table `{{%user}}`
        // $this->dropForeignKey(
        //     '{{%fk-orders-created_at}}',
        //     '{{%orders}}'
        // );

        // drops index for column `created_at`
        // $this->dropIndex(
        //     '{{%idx-orders-created_at}}',
        //     '{{%orders}}'
        // );

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211014_030420_alter_table_orders_remove_created_at_foreign_key cannot be reverted.\n";

        return false;
    }
    */
}
