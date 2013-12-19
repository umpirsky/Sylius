<?php

namespace Hypebeast\Bundle\InventoryBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class MovementHistoryController extends ResourceController
{
    public function filterFormAction(Request $request)
    {
        $config = $this->getConfiguration();

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('filterForm.html'))
            ->setData(array(
                'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_inventory_movement_history_filter', $request->query->get('criteria'))->createView()
            ))
        ;

        return $this->handleView($view);
    }
}
