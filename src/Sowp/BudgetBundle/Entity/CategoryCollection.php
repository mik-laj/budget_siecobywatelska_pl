<?php
/**
 * Created by PhpStorm.
 * User: andrzej
 * Date: 20.09.16
 * Time: 13:42
 */

namespace Sowp\BudgetBundle\Entity;


class CategoryCollection
{
    public $categories;
    public $count;
    public $total;

    /**
     * CategoryCollection constructor.
     * @param $categories
     * @param $count
     * @param $total
     */
    public function __construct($categories, $count, $total)
    {
        $this->categories = $categories;
        $this->count = $count;
        $this->total = $total;
    }

}