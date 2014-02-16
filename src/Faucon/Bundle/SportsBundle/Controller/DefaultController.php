<?php

namespace Faucon\Bundle\SportsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Default controller.
 *
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="sports_index")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => 'name');
    }
}
