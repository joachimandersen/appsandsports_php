<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\Challenge;
use Faucon\Bundle\RankingBundle\Form\ChallengeType;
use Faucon\DataProviders\DataTables;

/**
 * Challenge admin controller.
 *
 * @Route("/admin/challenge")
 */
class ChallengeAdminController extends Controller
{
    /**
     * Lists all open Challenge entities.
     *
     * @Route("/", name="admin_challenge")
     * @Template()
     */
    public function indexAction()
    {
        return array();
        /*$em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Challenge')->findAllOpen();

        return array('entities' => $entities);*/
    }
    
    /**
     * Page all Game entities.
     *
     * @Route("/paging", defaults={"_format"="json"}, name="challenge_admin_paging")
     * @Template()
     */
    public function pagingAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $datatables = new DataTables($this->getRequest(), $em->getRepository('FauconRankingBundle:Challenge'), $this->container, array('filter' => 'onlyopen'));
        return $this->render('FauconRankingBundle:ChallengeAdmin:paging.json.twig', array('data' => $datatables->getJsonResult()));
    }

    /**
     * Displays a form to create a new Challenge entity.
     *
     * @Route("/new", name="admin_challenge_new")
     * @Template()
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = new Challenge();
        $form   = $this->createForm($this->container->get('challenge_type'), $entity)
                ->remove('created')
                ->remove('createdby')
                ->remove('challengerrank')
                ->remove('challengedrank')
                ->remove('game');
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Challenge entity.
     *
     * @Route("/create", name="admin_challenge_create")
     * @Method("post")
     * @Template("FauconRankingBundle:ChallengeAdmin:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Challenge();
        $request = $this->getRequest();
        $form    = $this->createForm($this->container->get('challenge_type'), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $ur = $em->getRepository('FauconClubBundle:User');
            $currentuser = $this->get('security.context')->getToken()->getUser();
            $entity->setCreatedby($currentuser);
            $entity->setChallengedrank($ur->getRankingByCategory($entity->getChallenged(), $entity->getCategory()));
            $entity->setChallengerrank($ur->getRankingByCategory($currentuser, $entity->getCategory()));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_challenge'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }
}

?>
