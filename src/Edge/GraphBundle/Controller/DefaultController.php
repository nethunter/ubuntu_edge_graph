<?php

namespace Edge\GraphBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\NumberFormatter\NumberFormatter;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $graphTicksRepository = $this->getDoctrine()->getRepository("EdgeGraphBundle:GraphTick");
        $data = $graphTicksRepository->findAllAsDataSeries();

        // Prepare prediction
        $perk_end = new \DateTime('22.08.2013');
        $period = $this->getRequest()->get('period', 5);

        $prediction = $graphTicksRepository->predictAmountOverTimeAsDataSeries($perk_end, $period);
        array_unshift($prediction, end($data));

        // Chart
        $series = array(
            array('name' => 'Funding', 'data' => $data),
            array('name' => 'Prediction', 'data' => $prediction)
        );

        $ob = new Highchart();
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->chart->zoomType('x');
        $ob->chart->type('spline');
        $ob->chart->spacingRight(20);

        $ob->tooltip->shared(true);
        
        $ob->title->text('Ubuntu Edge Funding Over Time');

        $ob->xAxis->type('datetime');
        $ob->xAxis->dateTimeLabelFormats(array(
            'month' => '%e. %b',
            'year' => '%b'
        ));

        $ob->plotOptions->spline(array(
                'lineWidth' => 4,
                'stats' => array(
                    'hover' => array(
                        'lineWidth' => 5
                    )
                ),
                'marker' => array(
                    'enabled' => false
                )
        ));

        $ob->yAxis->title(array('text'  => "Funding amount in \$US"));
        $ob->series($series);

        return $this->render('EdgeGraphBundle:Default:index.html.twig', array(
            'chart' => $ob
        ));
    }

    public function lastAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        /** @var \Edge/GraphBundle/Entity/GraphTick $lastTickEntity */
        $lastTickEntity = $em->getRepository("EdgeGraphBundle:GraphTick")->findLastTick();

        if ($request->query->get('raw')) {
            $lastTick = $lastTickEntity->getGraphFunding();
            return new Response($lastTick);
        } else {
            $lastTick = $lastTickEntity->getGraphFundingFormatted();
        }

        return $this->render('EdgeGraphBundle:Default:last.html.twig', array(
            'last' => $lastTick
        ));
    }

    public function rssAction()
    {
        $tickRepo = $this->getDoctrine()->getRepository('EdgeGraphBundle:GraphTick');
        $ticks = $tickRepo->findBy(array(), array('graphDatetime' => 'DESC'), 5);

        $feed = $this->get('eko_feed.feed.manager')->get('funding_feed');
        $feed->addFromArray($ticks);

        return new Response($feed->render('rss'));
    }

    public function predictAction()
    {
        $perk_end = new \DateTime('22.08.2013');
        $period = $this->getRequest()->get('period', 5);

        $em = $this->getDoctrine()->getManager();
        $prediction = $em->getRepository('EdgeGraphBundle:GraphTick')->predictAmountByDaysLeft(
            $perk_end, $period
        );

        return $this->render('EdgeGraphBundle:Default:predict.html.twig', array(
            'period' => $period,
            'date' => $perk_end->format('d M Y'),
            'funding' => $prediction->getGraphFundingFormatted()
        ));
    }
}
