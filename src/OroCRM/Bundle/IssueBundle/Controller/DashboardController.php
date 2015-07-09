<?php

namespace OroCRM\Bundle\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/issue/dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route(
     *      "/by_status/{widget}",
     *      name="orocrm_issue_dashboard_by_status_chart",
     *      requirements={"widget"="[\w-]+"}
     * )
     * @Template
     */
    public function byStatusAction($widget)
    {
        $items = $this->getDoctrine()->getRepository('OroCRMIssueBundle:Issue')
            ->getCountByStatus();

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($items)
            ->setOptions([
                'name' => 'bar_chart',
                'data_schema' => [
                    'label' => ['field_name' => 'label'],
                    'value' => ['field_name' => 'issue_count'],
                ],
                'settings' => ['xNoTicks' => 2],
            ])
            ->getView();

        return $widgetAttr;
    }
}
