<?php

namespace smukm\sortorder\tests;

use smukm\sortorder\SortOrderBehavior;
use PHPUnit\Framework\TestCase;
use tests\common\models\Record;
use Yii;
use yii\db\Migration;

class SortorderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $db = Yii::$app->getDb();
        $migration = new Migration();

        $db->createCommand()->createTable(Record::tableName(), [
            'id'         => $migration->primaryKey(),
            'name' => $migration->string(255)->notNull(),
            'group_id' => $migration->string(255),
            'sort_order'   => $migration->integer()->defaultValue(0)
        ])->execute();
    }

    protected function tearDown(): void
    {
        $db = Yii::$app->getDb();
        $db->createCommand()->dropTable('records')->execute();
    }

    public function testInsertRecords()
    {
        $record = new Record();
        $record->name = Yii::$app->security->generateRandomString();
        $record->save();
        $this->assertEquals(10, $record->sort_order);

        $record = new Record();
        $record->name = Yii::$app->security->generateRandomString();
        $record->save();
        $this->assertEquals(20, $record->sort_order);
    }

    public function testInsertGroupRecords()
    {
        $record = new Record();
        $this->attachBehavior($record);
        $record->name = Yii::$app->security->generateRandomString();
        $record->group_id = 'group_one';
        $record->save();
        $this->assertEquals(10, $record->sort_order);

        $record = new Record();
        $this->attachBehavior($record);
        $record->name = Yii::$app->security->generateRandomString();
        $record->group_id = 'group_two';
        $record->save();
        $this->assertEquals(10, $record->sort_order);

        $record = new Record();
        $this->attachBehavior($record);
        $record->name = Yii::$app->security->generateRandomString();
        $record->group_id = 'group_one';
        $record->save();
        $this->assertEquals(20, $record->sort_order);

        $record = new Record();
        $this->attachBehavior($record);
        $record->name = Yii::$app->security->generateRandomString();
        $record->group_id = 'group_two';
        $record->save();
        $this->assertEquals(20, $record->sort_order);

    }

    public function testGetters()
    {
        $record1 = new Record();
        $record1->name = 'record 1';
        $record1->save();

        $record2 = new Record();
        $record2->name = 'record 2';
        $record2->save();

        $record3 = new Record();
        $record3->name = 'record 3';
        $record3->save();

        $record4 = new Record();
        $record4->name = 'record 4';
        $record4->save();

        $el = $record1->getLastElement();
        $this->assertEquals('record 4', $el->name);

        $el = $record1->getFirstElement();
        $this->assertEquals('record 1', $el->name);

        $el = $record1->getPrevElement();
        $this->assertNull($el);

        $el = $record1->getNextElement();
        $this->assertEquals('record 2', $el->name);

        $el = $record4->getNextElement();
        $this->assertNull($el);

        $el = $record4->getPrevElement();
        $this->assertEquals('record 3', $el->name);
    }


    public function testMoveRecords()
    {
        $record1 = new Record();
        $record1->name = 'record 1';
        $record1->save();

        $record2 = new Record();
        $record2->name = 'record 2';
        $record2->save();

        $record3 = new Record();
        $record3->name = 'record 3';
        $record3->save();

        $record1->moveDown();
        $this->assertEquals(20, $record1->sort_order);

        $record3->moveUp();
        $this->assertEquals(20, $record3->sort_order);

    }

    public function testMoveFirstLastRecords()
    {
        $record1 = new Record();
        $record1->name = 'record 1';
        $record1->save();

        $record2 = new Record();
        $record2->name = 'record 2';
        $record2->save();

        $record3 = new Record();
        $record3->name = 'record 3';
        $record3->save();

        $record1->moveLast();
        $this->assertEquals(30, $record1->sort_order);

        $record1->moveFirst();
        $this->assertEquals(10, $record1->sort_order);
    }


    private function attachBehavior(Record $record)
    {
        $record->detachBehaviors();
        $record->attachBehavior('SortOrderBehavior',[
            'class' => SortOrderBehavior::class,
            'sortAttribute' => 'sort_order',
            'groupAttribute' => 'group_id',
            'incrementValue' => 10,
        ]);
    }
}
