<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Administration controller.
 *
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Lists all options in the administration area
     *
     * @Route("/", name="admin")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

}
