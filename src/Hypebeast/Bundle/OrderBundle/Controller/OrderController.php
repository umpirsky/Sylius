<?php

namespace Hypebeast\Bundle\OrderBundle\Controller;

use Sylius\Bundle\CoreBundle\Controller\OrderController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Hypebeast\Bundle\OrderBundle\Status\States\OrderState;

class OrderController extends BaseController
{
    public function tabsAction(Request $request)
    {
        $config = $this->getConfiguration();
        $repository = $this->getRepository();
        $data = $repository->findAllOrderStates();

        $state = $this->getCurrentState($request);

        $tabs = $repository->findAllOrderStates();
        $labels = array_map(function ($e) { return $e['orderState']; }, $tabs);

        $view = $this
            ->view()
            ->setTemplate('HypebeastWebBundle:Backend/Order:tabs.html.twig')
            ->setData([
                'tabs'   => $tabs,
                'state'  => $state,
                'states' => OrderState::getStates(),
            ])
        ;

        return $this->handleView($view);
    }

    public function indexAction(Request $request)
    {
        $config = $this->getConfiguration();

        $criteria = $config->getCriteria();
        $sorting = $config->getSorting();
        $state = $this->getCurrentState($request);

        $criteria['orderState'] = $state;

        foreach ($criteria as $key => $value) {
            if (empty($value)) {
                unset($criteria[$key]);
            }
        }

        $pluralName = $config->getPluralResourceName();
        $repository = $this->getRepository();


        $resources = $repository->findWithStateBy($criteria, $sorting);

        $template = sprintf('HypebeastWebBundle:Backend/Order/Index:%s.html.twig', str_replace(' ', '', strtolower($state)));

        if (!$this->getTemplating()->exists($template)) {
            $template = $config->getTemplate('index.html');
        }

        $shipmentForms = [];

        foreach ($resources as $order) {
            if (null !== $order->getShipment()) {
                $shipmentForms[$order->getId()] = $this->createForm('sylius_shipment', $order->getShipment())->createView();
            }
        }

        $view = $this
            ->view()
            ->setTemplate($template)
            ->setData([
                $pluralName     => $resources,
                'shipmentForms' => $shipmentForms,
                'state'         => $state,
            ])
        ;

        return $this->handleView($view);
    }

    public function updateShipmentAction(Request $request)
    {
        $config = $this->getConfiguration();

        $criteria = $config->getCriteria();

        $order    = $this->findOr404();
        $shipment = $order->getShipment();

        $form = $this->createForm('sylius_shipment', $shipment);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $processor = $this->get('sylius.hypebeast.order.state.processor');
            $processor->applyState($order, 'order', 'STATE_SHIPPED');

            $this->persistAndFlush($shipment, 'update');
        }

        return $this->redirectToRoute(
            $config->getRedirectRoute('index'),
            $request->query->all()
        );
    }

    public function printPackingSlipsAction(Request $request)
    {
        $config = $this->getConfiguration();

        $orders = $this->getRepository()->findAll(
            explode(',', $request->query->get('orders', ''))
        );

        // Change order status to in process
        $processor = $this->get('sylius.hypebeast.order.state.processor');
        foreach ($orders as $order) {
            $processor->applyState($order, 'order', 'STATE_IN_PROCESS');
        }

        $this->getManager()->flush();

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('index.html'))
            ->setTemplateVar($config->getPluralResourceName())
            ->setData($orders)
        ;

        return $this->handleView($view);
    }

    protected function getCurrentState(Request $request)
    {
        $criteria = $request->query->get('criteria', []);
        $criteria = array_merge(
            ['orderState' => OrderState::STATE_NEW],
            $criteria
        );

        $state = $criteria['orderState'];
        $state = in_array($state, OrderState::getStates()) ? $state : null;

        return $state;
    }

    protected function getTemplating()
    {
        return $this->get('templating');
    }
}
