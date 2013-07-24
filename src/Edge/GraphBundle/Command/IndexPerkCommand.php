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

class IndexPerkCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('edge:index')
            ->setDescription('Add another tick to the current perks funding value.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $crawler = $client->request('GET', 'http://www.indiegogo.com/projects/ubuntu-edge/');

        try {
            $nodes = $crawler->filter('#big-goal span');
            $funds = $nodes->text();
            $clear_funds = preg_replace("/[^\d\.]/", "", $funds);
            $output->writeln('So far, funded ' . $clear_funds . '.');

            /** @var EntityManager $em */
            $em = $this->getContainer()->get('doctrine')->getEntityManager();

            $graphTick = new GraphTick();
            $graphTick->setGraphFunding($clear_funds);

            $em->persist($graphTick);
            $em->flush();
        } catch (InvalidArgumentException $e) {
            $output->writeln('Couldn\'t find the relevant response.');
        }
    }
}
