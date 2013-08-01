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

    public function findAllAsDataSeries($skipDuplicate = true)
    {
        $ticks = $this->findAll();
        $data = array();
        $prevFunding = 0;

        /** @var \Edge\GraphBundle\Entity\GraphTick $tick */
        foreach($ticks as $tick) {
            $funding = (int)$tick->getGraphFunding();

            if ($funding != $prevFunding || false == $skipDuplicate) {
                $data[] = array($tick->getGraphDatetime()->getTimestamp() * 1000, $funding);
                $prevFunding = $funding;
            }
        }

        return $data;
    }

    private function predictionQuery(\DateTime $dateTime, $period = 5)
    {
        // Get the growth for each day in the dataset
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT MAX(gt.graphFunding) max_fund, '
        .'MAX (gt.graphFunding) - MIN(gt.graphFunding) growth, DATE(gt.graphDatetime) dateonly '
        .'FROM Edge\GraphBundle\Entity\GraphTick gt WHERE gt.graphDatetime < CURRENT_DATE() '
        .'GROUP BY dateonly ORDER BY dateonly DESC');
        $query->setMaxResults($period);
        $results = $query->getResult();

        return $results;
    }

    public function predictAmountOverTimeAsDataSeries(\DateTime $dateTime, $period)
    {
        $results = $this->predictionQuery($dateTime, $period);

        // Calculate the average growth per day
        $first = reset($results);
        $currentFund =  $first['max_fund'];
        $avg = $first['growth'];
        $pos = 1;

        // Calculate the average
        while($result = next($results)) {
            $avg += $result['growth'];
            $pos++;
        }
        $avg /= $pos;

        // Initialize the variables
        $now = new \DateTime();
        $daysLeft = (int) $now->diff( $dateTime )->format( '%a' );

        $funding = $currentFund;
        $date = $now;
        $interval = new \DateInterval('P1D');
        $ticks = array();

        // Calculate the values 'till perks end
        for($day=0; $day<=$daysLeft; $day++) {
            $funding += $avg;

            $ticks[] = array(
                $date->getTimestamp() * 1000, $funding
            );

            $date->add($interval);
        }

        return $ticks;

    }

    /**
     * Try to the predict the funding, by taking the average growth of
     * each day of the period, and multiplying it by the amount of days left
     * till the end of the perk.
     *
     * @param \DateTime $dateTime The date when the perk ends
     * @param int $period How many days back to analyze
     * @return GraphTick A GraphTick with the estimated funding, and the end date
     */
    public function predictAmountByDaysLeft(\DateTime $dateTime, $period)
    {
        $results = $this->predictionQuery($dateTime, $period);

        // Calculate the average growth per day
        $first = reset($results);
        $currentFund =  $first['max_fund'];
        $avg = $first['growth'];
        $pos = 1;

        while($result = next($results)) {
            $avg += $result['growth'];
            $pos++;
        }

        $avg /= $pos;

        $now = new \DateTime();
        $daysLeft = (int) $now->diff( $dateTime )->format( '%a' );

        $funding = ($avg * $daysLeft) + $currentFund;

        $graphTick = new GraphTick();
        $graphTick->setGraphFunding($funding);
        $graphTick->setGraphDatetime($dateTime);

        return $graphTick;
    }
}