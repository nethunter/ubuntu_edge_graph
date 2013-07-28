<?php
namespace Edge\GraphBundle\Command;

use Doctrine\ORM\EntityManager;
use Edge\GraphBundle\EdgeGraphBundle;
use Edge\GraphBundle\Entity\GraphTick;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class EstimatePerkCommand extends ContainerAwareCommand
{
    const PERIOD = 5;

    protected function configure()
    {
        $this
            ->setName('edge:predict')
            ->setDescription('Given the current average growing rate, will the perk succeed?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $perk_end = new \DateTime();
        $perk_end->add(new \DateInterval('P25D'));

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $query = $em->createQuery('SELECT MAX(gt.graphFunding) max_fund, '
            .'MAX (gt.graphFunding) - MIN(gt.graphFunding)growth, DATE(gt.graphDatetime) dateonly '
            .'FROM Edge\GraphBundle\Entity\GraphTick gt GROUP BY dateonly '
            .' ORDER BY dateonly DESC');
        $query->setMaxResults(self::PERIOD);

        $results = $query->getResult();

        // Calculate the average growth per day
        $first = reset($results);
        $currentFund =  $first['max_fund'];
        $avg = $first['growth'];
        $pos = 0;

        while($result = next($results)) {
            $avg += $result['growth'] / ++$pos;
        }

        $result = ($avg * 25) + $currentFund;

        $nf = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);
        $resultFormatted = $nf->formatCurrency($result, "USD");

        $output->writeln('According to the average of the last ' . self::PERIOD . ' days,'
            .'by the end of the perk it will raise ' . $resultFormatted);
    }
}
