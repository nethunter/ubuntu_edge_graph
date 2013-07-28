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
        $data= $this->getDoctrine()->getRepository("EdgeGraphBundle:GraphTick")->findAllAsDataSeries();

        // Chart
        $series = array(
            array("name" => "Funding", "data" => $data)
        );

        $ob = new Highchart();
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->chart->zoomType('x');
        $ob->chart->spacingRight(20);

        $ob->tooltip->shared(true);
        
        $ob->title->text('Ubuntu Edge Funding Over Time');

        $ob->xAxis->type('datetime');
        $ob->xAxis->dateTimeLabelFormats(array(
            'month' => '%e. %b',
            'year' => '%b'
        ));

        $ob->plotOptions->area(array(
            'fillColor' => array(
                'linearGradient' => array(
                    'x1' => 0,
                    'y1' => 0,
                    'x2' => 0,
                    'y2' => 1
                ),
            ),
            'lineWidth' => 1,
            'marker' => array(
                'enabled' => false,
            ),
            'shadow' => false,
            'states' => array(
                'hover' => array(
                    'lineWidth' => 1,
                )
            ),
            'threshold' => null
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
        } else {
            $lastTick = $lastTickEntity->getGraphFundingFormatted();
        }

        return new Response($lastTick);
    }

    public function rssAction()
    {
        $tickRepo = $this->getDoctrine()->getRepository('EdgeGraphBundle:GraphTick');
        $ticks = $tickRepo->findBy(array(), array('graphDatetime' => 'DESC'), 5);

        $feed = $this->get('eko_feed.feed.manager')->get('funding_feed');
        $feed->addFromArray($ticks);

        return new Response($feed->render('rss'));
    }
}
