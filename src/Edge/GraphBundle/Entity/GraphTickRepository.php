<?php
namespace Edge\GraphBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class GraphTickRepository extends EntityRepository
{
    public function findLastTick()
    {
        $qb = $this->getEntityManager()
            ->createQuery('SELECT gt.graphFunding FROM EdgeGraphBundle:GraphTick gt ORDER BY gt.graphDatetime DESC')
            ->setMaxResults(1);

        return $qb->getSingleScalarResult();
    }

    public function findAllAsDataSeries()
    {
        $ticks = $this->findAll();

        $data = array();
        /** @var \Edge\GraphBundle\Entity\GraphTick $tick */
        foreach($ticks as $tick) {
            $data[] = array($tick->getGraphDatetime()->getTimestamp() * 1000, (int)$tick->getGraphFunding());
        }
    }
}