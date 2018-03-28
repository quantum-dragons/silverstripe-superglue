<?php

namespace SilverStripe\SuperGlue;

use SilverStripe\ORM\DataList;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows as BaseGridFieldOrderableRows;

class GridFieldOrderableRows extends BaseGridFieldOrderableRows
{
    /**
     * @inheritdoc
     *
     * @param DataList $list
     * @param array $values
     * @param array $order
     */
    protected function reorderItems($list, array $values, array $order)
    {
        $min = min($values);
        $ordered = array();

        foreach ($values as $key => $value) {
            $ordered[$key] = $min++;
        }

        parent::reorderItems($list, $ordered, $order);
    }
}
