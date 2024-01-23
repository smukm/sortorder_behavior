<?php

declare(strict_types=1);

namespace smukm\sortorder;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;


/**
 *
 * @property-read ActiveRecord|null $prevElement
 * @property-read ActiveRecord|null $lastElement
 * @property-read ActiveRecord|null $firstElement
 * @property-read ActiveRecord|null $nextElement
 */
class SortOrderBehavior extends Behavior
{
    public string $sortAttribute = 'sort_order';
    public string $groupAttribute = 'group_id';
    public int $incrementValue = 1;

    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert'
        ];
    }


    public function beforeInsert(): void
    {
        $last = $this->getLastElement();

        $value = ($last) ? $last->{$this->sortAttribute} + $this->incrementValue : $this->incrementValue;

        $this->owner->{$this->sortAttribute} = $value;
    }

    public function getLastElement(): ActiveRecord|null
    {
        $sortAttribute = $this->sortAttribute;
        $q =  $this->owner::find()
            ->orderBy([$sortAttribute => SORT_DESC])
            ->limit(1);
        if(!empty($this->groupAttribute)) {
            $q->andWhere(['=', $this->groupAttribute, $this->owner->{$this->groupAttribute}]);
        }

        return $q->one();
    }

    public function getFirstElement(): ActiveRecord|null
    {
        $sortAttribute = $this->sortAttribute;
        $q = $this->owner::find()
            ->orderBy([$sortAttribute => SORT_ASC])
            ->limit(1);

        if(!empty($this->groupAttribute)) {
            $q->andWhere(['=', $this->groupAttribute, $this->owner->{$this->groupAttribute}]);
        }
        return $q->one();
    }

    public function getPrevElement(): ActiveRecord|null
    {
        $sortAttribute = $this->sortAttribute;
        $q = $this->owner::find()
            ->where(['<', $this->sortAttribute, $this->owner->{$this->sortAttribute}])
            ->orderBy([$sortAttribute => SORT_DESC]);
        if(!empty($this->groupAttribute)) {
            $q->andWhere(['=', $this->groupAttribute, $this->owner->{$this->groupAttribute}]);
        }
        return $q->one();

    }

    public function getNextElement(): ActiveRecord|null
    {
        $sortAttribute = $this->sortAttribute;
        $q = $this->owner::find()
            ->where(['>', $this->sortAttribute, $this->owner->{$this->sortAttribute}])
            ->orderBy([$sortAttribute => SORT_ASC]);
        if(!empty($this->groupAttribute)) {
            $q->andWhere(['=', $this->groupAttribute, $this->owner->{$this->groupAttribute}]);
        }
        return $q->one();
    }

    public function moveUp(): void
    {
        $prevElement = $this->getPrevElement();
        if($prevElement) {
            $ownerSort = $this->owner->{$this->sortAttribute};
            $this->owner->{$this->sortAttribute} = $prevElement->{$this->sortAttribute};
            $prevElement->{$this->sortAttribute} = $ownerSort;

            $this->owner->save();
            $prevElement->save();
        }
    }

    public function moveDown(): void
    {
        $nextElement = $this->getNextElement();
        if($nextElement) {
            $ownerSort = $this->owner->{$this->sortAttribute};
            $this->owner->{$this->sortAttribute} = $nextElement->{$this->sortAttribute};
            $nextElement->{$this->sortAttribute} = $ownerSort;

            $this->owner->save();
            $nextElement->save();
        }
    }

    public function moveFirst(): void
    {
        $firstElement = $this->getFirstElement();
        if($firstElement) {
            $ownerSort = $this->owner->{$this->sortAttribute};
            $this->owner->{$this->sortAttribute} = $firstElement->{$this->sortAttribute};
            $firstElement->{$this->sortAttribute} = $ownerSort;

            $this->owner->save();
            $firstElement->save();
        }

    }

    public function moveLast(): void
    {
        $lastElement = $this->getLastElement();
        if($lastElement) {
            $ownerSort = $this->owner->{$this->sortAttribute};
            $this->owner->{$this->sortAttribute} = $lastElement->{$this->sortAttribute};
            $lastElement->{$this->sortAttribute} = $ownerSort;

            $this->owner->save();
            $lastElement->save();
        }

    }
}