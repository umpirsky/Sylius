<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frontend homepage controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class HomepageController extends Controller
{
    /**
     * Store front page.
     *
     * @return Response
     */
    public function mainAction()
    {
        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig');
    }

    public function sessionRace1Action(Request $request)
    {
        $request->getSession()->set('session_race', 'hello from page1.');
        sleep(10);

        return new Response($request->getSession()->get('session_race'));
    }

    public function sessionRace2Action(Request $request)
    {
        $request->getSession()->set('session_race', 'hello from page2.');

        return new Response($request->getSession()->get('session_race'));
    }
}
