<?php

namespace Edge\GraphBundle\Navbar;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;

class MenuBuilder extends AbstractNavbarMenuBuilder
{
    public function createMainMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();
        /* $dropdown = $this->createDropdownMenuItem($menu, "Chart", false, array('caret' => true));
        $dropdown->addChild('Minutes', array('route' => 'edge_graph_homepage', 'time' => '1min'));
        $dropdown->addChild('Hours', array('route' => 'edge_graph_homepage'));
        $dropdown->addChild('Days', array('route' => 'edge_graph_homepage')); */
        $menu->addChild('Last', array('route' => 'edge_graph_homepage_last'));
        $menu->addChild('Predict', array('route' => 'edge_graph_homepage_predict'));
        $menu->addChild('RSS', array('route' => 'edge_graph_homepage_rss'));
        return $menu;
    }

}