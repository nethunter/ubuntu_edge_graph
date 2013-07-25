<?php

namespace Edge\GraphBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $fundingValue = $this->getDoctrine()->getRepository("EdgeGraphBundle:GraphTick")->findAll();

        $data = array();

        /** @var \Edge\GraphBundle\Entity\GraphTick $value */
        foreach($fundingValue as $value) {
            $data[] = array($value->getGraphDatetime()->getTimestamp() * 1000, (int)$value->getGraphFunding());
        }

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

        $ob->yAxis->title(array('text'  => "Funding amount in \$US"));
        $ob->series($series);

        return $this->render('EdgeGraphBundle:Default:index.html.twig', array(
            'chart' => $ob
        ));
    }

    public function lastAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastTick = $em->getRepository("EdgeGraphBundle:GraphTick")->findLastTick();

        return new Response($lastTick);
    }
}
