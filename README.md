# Yii2 Sortorder Behavior

Behavior for automatic filling sorting field for Yii2 AR Models

## Install via Composer:
```bash
composer require smukm/sortorder
```

## Configuring
```php
use smukm\sortorder;

class Sample extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'SortOrderBehavior' => [
                'class' => SortOrderBehavior::class,
                'sortAttribute' => 'order',
            ]
        ];
    }
}
```

## Options
- `sortAttribute = 'order'` - name of the sorting field
- `groupAttribute = 'some_group'` - sorting within a group
- `incrementValue = 10` - increment value
