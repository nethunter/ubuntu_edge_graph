<?php
namespace Edge\GraphBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class GraphTickRepository extends EntityRepository
{
    public function findLastTick()
    {
        return $this->findOneBy(array(), array('graphDatetime' => 'DESC'), 1);
    }

    public function findAllAsDataSeries()
    {
        $ticks = $this->findAll();
        $data = array();

        /** @var \Edge\GraphBundle\Entity\GraphTick $tick */
        foreach($ticks as $tick) {
            $data[] = array($tick->getGraphDatetime()->getTimestamp() * 1000, (int)$tick->getGraphFunding());
        }

        return $data;
    }

    public function predictAmountByDaysLeft(\DateTime $dateTime, $period = 5)
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT MAX(gt.graphFunding) max_fund, '
            .'MAX (gt.graphFunding) - MIN(gt.graphFunding)growth, DATE(gt.graphDatetime) dateonly '
            .'FROM Edge\GraphBundle\Entity\GraphTick gt WHERE gt.graphDatetime < CURRENT_DATE() '
            . 'GROUP BY dateonly ORDER BY dateonly DESC');
        $query->setMaxResults($period);

        $results = $query->getResult();

        // Calculate the average growth per day
        $first = reset($results);
        $currentFund =  $first['max_fund'];
        $avg = $first['growth'];
        $pos = 0;

        while($result = next($results)) {
            $avg += $result['growth'] / ++$pos;
        }

        $now = new \DateTime();
        $daysLeft = (int) $now->diff( $dateTime )->format( '%a' );

        $funding = ($avg * $daysLeft) + $currentFund;

        $graphTick = new GraphTick();
        $graphTick->setGraphFunding($funding);
        $graphTick->setGraphDatetime($dateTime);

        return $graphTick;
    }
}