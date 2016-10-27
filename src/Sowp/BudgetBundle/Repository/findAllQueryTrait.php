<?php
namespace Sowp\BudgetBundle\Repository;


trait findAllQueryTrait
{

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllQueryBuilder()
    {
        return $this->createQueryBuilder('a');
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        return $this->findAllQueryBuilder()->getQuery();
    }
}
