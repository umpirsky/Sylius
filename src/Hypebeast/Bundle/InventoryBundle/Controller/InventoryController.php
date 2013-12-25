<?php

namespace Hypebeast\Bundle\InventoryBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InventoryController extends ResourceController
{
    public function updateAction(Request $request)
    {
        $config = $this->getConfiguration();

        $form = $this->get('form.factory')->create('sylius_inventory_adjustment');

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {

            $this->get('sylius.form.handler.inventory')->update($form->getData());

            $this->setFlash('success', 'update');

            return $this->redirectToRoute('sylius_backend_inventory_index');
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('update.html'))
            ->setData(array(
                'form' => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }

    public function variantsAction(Request $request)
    {
        $view = $this
            ->view()
            ->setData($this->get('sylius.repository.variant')->findByKeywordForTypeahead($request->get('q')))
        ;

        return $this->handleView($view);
    }
}
