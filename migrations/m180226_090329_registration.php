<?php

use yii\db\Migration;

/**
 * Class m180226_090329_registration
 */
class m180226_090329_registration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('registration', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(),
            'last_name' => $this->string(),
            'email' => $this->string(),
            'date' => $this->date(),
            'time' => $this->integer(),
            'hints' => $this->integer(),
            'secrets' => $this->integer(),
            'score' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('registration');
    }

}
