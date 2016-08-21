<?php
namespace Sowp\BudgetBundle\Repository;

trait findAllQueryTrait
{

    public function findAllQueryBuilder()
    {
        return $this->createQueryBuilder('a');
    }
    
    public function findAllQuery()
    {
        return $this->findAllQueryBuilder()->getQuery();
    }
}
