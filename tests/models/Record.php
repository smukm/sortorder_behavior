<?php

declare(strict_types=1);

namespace smukm\sortorder\tests\models;

use smukm\sortorder\SortOrderBehavior;

use yii\db\ActiveRecord;

class Record extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'records';
    }

    public function behaviors(): array
    {
        return [
            'SortOrderBehavior' => [
                'class' => SortOrderBehavior::class,
                'sortAttribute' => 'sort_order',
                'incrementValue' => 10,
            ]
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'group_id'], 'string'],
            [['sort_order'], 'integer'],
        ];
    }
}
