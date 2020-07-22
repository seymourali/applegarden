<?php

use yii\db\Migration;

/**
 * Class m200721_202252_apples
 */
class m200721_202252_apples extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('apples', [
        	'id' => $this->primaryKey()->unsigned(),
	        'color' => $this->string('10'),
	        'size' => $this->float()->unsigned(),
	        'create_datetime' => $this->dateTime(),
	        'fall_datetime' => $this->dateTime()->null(),
	        'status' => $this->tinyInteger()->defaultValue(0)->comment('0 - on the tree | 1 - fell')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('apples');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200721_202252_apples cannot be reverted.\n";

        return false;
    }
    */
}
