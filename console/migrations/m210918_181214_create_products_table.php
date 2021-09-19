<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m210918_181214_create_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => 'LONGTEXT',
            'image' => $this->string(2000),
            'price' => $this->decimal(10, 2)->notNull(),
            'status' => $this->integer(2)->notNull(),
            'created_at' => $this->integer(11),
            'updatet_at' => $this->integer(11),
        ]);

        // creates index for column `created_at`
        $this->createIndex(
            '{{%idx-products-created_at}}',
            '{{%products}}',
            'created_at'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-products-created_at}}',
            '{{%products}}',
            'created_at',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `updatet_at`
        $this->createIndex(
            '{{%idx-products-updatet_at}}',
            '{{%products}}',
            'updatet_at'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-products-updatet_at}}',
            '{{%products}}',
            'updatet_at',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-products-created_at}}',
            '{{%products}}'
        );

        // drops index for column `created_at`
        $this->dropIndex(
            '{{%idx-products-created_at}}',
            '{{%products}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-products-updatet_at}}',
            '{{%products}}'
        );

        // drops index for column `updatet_at`
        $this->dropIndex(
            '{{%idx-products-updatet_at}}',
            '{{%products}}'
        );

        $this->dropTable('{{%products}}');
    }
}
