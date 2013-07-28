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
        $perk_end = new \DateTime('22.08.2013');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $result = $em->getRepository('EdgeGraphBundle:GraphTick')->predictAmountByDaysLeft(
            $perk_end, self::PERIOD
        );

        $output->writeln('According to the average of the last ' . self::PERIOD . ' days,'
            .'by the end of the perk it will raise ' . $result->getGraphFundingFormatted());
    }
}
