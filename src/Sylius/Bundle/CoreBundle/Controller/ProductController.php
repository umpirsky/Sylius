<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Product controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductController extends ResourceController
{
    public function showAction()
    {
        $request = $this->getRequest();
        $config = $this->getConfiguration();
        $form = $this->createForm('sylius_gift_card_front');
        $product = $this->findOr404();

        if (
            (!$product->isGiftCard() && $request->isMethod('POST')) ||
            ($product->isGiftCard() && $request->isMethod('POST') && $form->bind($request)->isValid())
        ) {
            return $this->forward('sylius.controller.cart_item:addAction', array('id' => $product->getId()));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('show.html'))
            ->setTemplateVar($config->getResourceName())
            ->setData([
                'product'      => $product,
                'giftCardForm' => $form->createView(),
            ])
        ;

        return $this->handleView($view);
    }

    public function indexAction(Request $request)
    {
        $config = $this->getConfiguration();

        $criteria = $config->getCriteria();
        $sorting = $this->getSortBy($request);

        $pluralName = $config->getPluralResourceName();
        $repository = $this->getRepository();

        if ($config->isPaginated()) {
            $resources = $this
                ->getResourceResolver()
                ->getResource($repository, $config, 'createPaginator', array($criteria, $sorting))
            ;

            $resources
                ->setMaxPerPage($this->getMaxPerPage($request))
                ->setCurrentPage($request->get('page', 1), true, true)
            ;
        } else {
            $resources = $this
                ->getResourceResolver()
                ->getResource($repository, $config, 'findBy', array($criteria, $sorting, $config->getLimit()))
            ;
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('index.html'))
            ->setData(array(
                $pluralName => $resources,
                'form'      => $this->get('form.factory')->create('sylius_product_price'),
            ))
        ;

        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        $config = $this->getConfiguration();

        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->get('sylius.file_uploader')->syncFiles(array(
                'from_folder' => $form->get('uploadId')->getData(),
                'resource'    => $resource->getMasterVariant(),
            ));

            $event = $this->create($resource);

            $this->get('sylius.file_uploader_file_manager')->cleanupFiles($form->get('uploadId')->getData());

            if (!$event->isStopped()) {
                $this->setFlash('success', 'create');

                return $this->redirectTo($resource);
            }

            $this->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParams());
        }

        if ($config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('create.html'))
            ->setData(array(
                $config->getResourceName() => $resource,
                'form'                     => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }

    public function updateAction(Request $request)
    {
        $config = $this->getConfiguration();

        $resource = $this->findOr404();
        $form = $this->getForm($resource);

        if (($request->isMethod('PUT') || $request->isMethod('POST')) && $form->bind($request)->isValid()) {
            $this->get('sylius.file_uploader')->syncFiles(array(
                'from_folder' => $form->get('uploadId')->getData(),
                'resource'    => $resource->getMasterVariant(),
            ));

            $event = $this->update($resource);

            $this->get('sylius.file_uploader_file_manager')->cleanupFiles($form->get('uploadId')->getData());

            if (!$event->isStopped()) {
                $this->setFlash('success', 'update');

                return $this->redirectTo($resource);
            }

            $this->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParams());
        }

        if ($config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('update.html'))
            ->setData(array(
                $config->getResourceName() => $resource,
                'form'                     => $form->createView(),
                'upload_id'                => $form->get('uploadId')->getData(),
            ))
        ;

        return $this->handleView($view);
    }

    public function updatePriceAction(Request $request)
    {
        $config = $this->getConfiguration();

        $resource = $this->findOr404();
        $form = $this->get('form.factory')->create('sylius_product_price', $resource);

        if ($request->isMethod('POST') && $form->bind($request->request->get('value'))->isValid()) {
            $event = $this->update($resource);

            if (!$event->isStopped()) {
                return new Response();
            }
        }

        return new Response(
            $this->get('translator')->trans('sylius.form.product_price.invalid'),
            400
        );
    }

    public function uploadAction($id)
    {
        $this->get('sylius.file_uploader')->handleFileUpload(array('folder' => $id));
    }

    /**
     * List products categorized under given taxon.
     *
     * @param Request $request
     * @param string  $permalink
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByTaxonAction(Request $request, $permalink)
    {
        $taxon = $this->get('sylius.repository.taxon')
            ->findOneByPermalink($permalink);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        /** @var $paginator Pagerfanta */
        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon, null, $this->getSortBy($request));
        ;

        $paginator->setMaxPerPage($this->getMaxPerPage($request));
        $paginator->setCurrentPage($request->query->get('page', 1));

        return $this->renderResponse('SyliusWebBundle:Frontend/Product:indexByTaxon.html.twig', array(
            'taxon'    => $taxon,
            'products' => $paginator,
        ));
    }

    public function indexByTaxonIdAction(Request $request, $id)
    {
        $taxon = $this->get('sylius.repository.taxon')->find($id);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $config = $this->getConfiguration();

        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon)
        ;

        $paginator->setMaxPerPage($config->getPaginationMaxPerPage());
        $paginator->setCurrentPage($request->query->get('page', 1));

        return $this->renderResponse($config->getTemplate('productIndex.html'), array(
            'taxon'    => $taxon,
            'products' => $paginator,
        ));
    }

    private function getSortBy(Request $request)
    {
        $sort = strtolower($request->query->get('sort'));

        if ($sort && in_array($sort, ['desc', 'asc'])) {
            return ['variant.price' => $sort];
        }

        return null;
    }

    private function getMaxPerPage(Request $request)
    {
        $limit = $request->query->get('limit');
        $session = $request->getSession();

        if ($limit && in_array($limit, [30,60,90])) {
            $session->set('_hypebeast_product_per_page', $limit);
        }

        return (int) $session->get('_hypebeast_product_per_page', 12);
    }

    /**
     * Get product history changes.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $config = $this->getConfiguration();
        $logEntryRepository = $this->getManager()->getRepository('Gedmo\Loggable\Entity\LogEntry');

        $product = $this->findOr404();

        $data = array(
            $config->getResourceName() => $product,
            'logs'                     => $logEntryRepository->getLogEntries($product)
        );

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('history.html'))
            ->setData($data)
        ;

        return $this->handleView($view);
    }

    /**
     * Render product filter form.
     *
     * @param Request $request
     */
    public function filterFormAction(Request $request)
    {
        return $this->renderResponse('SyliusWebBundle:Backend/Product:filterForm.html.twig', array(
            'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_product_filter', $request->query->get('criteria'))->createView()
        ));
    }
}
