<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\Challenge;
use Faucon\Bundle\RankingBundle\Entity\Category;
use Faucon\Bundle\RankingBundle\Form\ChallengeType;
use Faucon\DataProviders\DataTables;

/**
 * Challenge controller.
 *
 * @Route("/challenge")
 */
class ChallengeController extends Controller
{
    /**
     * Lists all Challenge entities.
     *
     * @Route("/", name="challenge")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * Page all Game entities.
     *
     * @Route("/paging", defaults={"_format"="json"}, name="challenge_paging")
     * @Template()
     */
    public function pagingAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $datatables = new DataTables($this->getRequest(), $em->getRepository('FauconRankingBundle:Challenge'), $this->container);
        return $this->render('FauconRankingBundle:Challenge:paging.json.twig', array('data' => $datatables->getJsonResult()));
    }

    public function lastfiveAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Challenge')->findLastFive();

        $dateutil = $this->get('date_utility');
        return $this->render('FauconRankingBundle:Challenge:lastfive.html.twig', array('entities' => $entities, 'dateutil' => $dateutil));
    }
    
    public function lastfiveincategoryAction($categoryid)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Challenge')->findLastFiveInCategory($categoryid);

        $dateutil = $this->get('date_utility');
        return $this->render('FauconRankingBundle:Challenge:lastfiveincategory.html.twig', array('entities' => $entities, 'dateutil' => $dateutil));
    }

    /**
     * Finds and displays a Challenge entity.
     *
     * @Route("/{id}/show", name="challenge_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Challenge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Challenge entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }
    
    /**
     * Displays a form to create a new Challenge entity.
     *
     * @Route("/{id}/test", defaults={"_format"="txt"}, name="challenge_test")
     * @Template()
     */
    public function testAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $challenge = $em->getRepository('FauconRankingBundle:Challenge')->find($id);

        return array(
            'name' => $challenge->getChallenged(), 
            'by' => $challenge->getChallenger(),
            'deadline' => new \DateTime(date("Y-m-d", strtotime("14 days"))));
    }    

    /**
     * Displays a form to create a new Challenge entity.
     *
     * @Route("/{id}/{categoryid}/new", name="challenge_new")
     * @Template()
     */
    public function newAction($id, $categoryid)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository('FauconRankingBundle:Category')->find($categoryid);
        $user = $em->getRepository('FauconClubBundle:User')->find($id);
        $canIchallenge = !$em->getRepository('FauconRankingBundle:Challenge')
                ->hasOpenChallengeInCategory($this->get('security.context')->getToken()->getUser(), $category);
        if (!$canIchallenge) {
            return $this->redirect($this->generateUrl('challenge_denied', array('me' => 1, 'categoryid' => $categoryid)));
        }
        $canbechallenged = !$em->getRepository('FauconRankingBundle:Challenge')
                ->hasOpenChallengeInCategory($user, $category);
        if (!$canbechallenged) {
            return $this->redirect($this->generateUrl('challenge_denied', array('me' => 0, 'categoryid' => $categoryid)));
        }
        $entity = new Challenge();
        $form   = $this->createForm($this->container->get('challenge_type'), $entity)
                ->remove('created')
                ->remove('createdby')
                ->remove('challenger')
                ->remove('challenged')
                ->remove('challengerrank')
                ->remove('challengedrank')
                ->remove('category')
                ->remove('game');
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'challenged' => $user,
            'category' => $category
        );
    }

    /**
     * Creates a new Challenge entity.
     *
     * @Route("/{id}/{categoryid}/create", name="challenge_create")
     * @Method("post")
     * @Template("FauconRankingBundle:Challenge:new.html.twig")
     */
    public function createAction($id, $categoryid)
    {
        $entity  = new Challenge();
        $request = $this->getRequest();
        $form    = $this->createForm($this->container->get('challenge_type'), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $ur = $em->getRepository('FauconClubBundle:User');
            $currentuser = $this->get('security.context')->getToken()->getUser();
            $entity->setCategory($em->getRepository('FauconRankingBundle:Category')->find($categoryid));
            $entity->setCreatedby($currentuser);
            $entity->setChallenger($currentuser);
            $entity->setChallenged($em->getRepository('FauconClubBundle:User')->find($id));
            $entity->setChallengedrank($ur->getRankingByCategory($entity->getChallenged(), $entity->getCategory()));
            $entity->setChallengerrank($ur->getRankingByCategory($currentuser, $entity->getCategory()));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show_open_challenges', array('id' => $currentuser->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Challenge entity.
     *
     * @Route("/{id}/edit", name="challenge_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Challenge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Challenge entity.');
        }

        $editForm = $this->createForm($this->container->get('challenge_type'), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Challenge entity.
     *
     * @Route("/{id}/update", name="challenge_update")
     * @Method("post")
     * @Template("FauconRankingBundle:Challenge:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Challenge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Challenge entity.');
        }

        $editForm   = $this->createForm($this->container->get('challenge_type'), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ranking_challenge_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Challenge entity.
     *
     * @Route("/{id}/delete", name="challenge_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('FauconRankingBundle:Challenge')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Challenge entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ranking_challenge'));
    }
    
    /**
     * Displays a form to create a new Challenge entity.
     *
     * @Route("/{me}/{categoryid}/denied", name="challenge_denied")
     * @Template()
     */
    public function deniedAction($me, $categoryid)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $category = $em->getRepository('FauconRankingBundle:Category')->find($categoryid);
        $challengeid = -1;
        if ($me) {
            $challenge = $em->getRepository('FauconRankingBundle:Challenge')
                    ->getOpenChallengeByCategory($user, $category);
            $challengeid = $challenge->getId();
        }
        //$user = $em->getRepository('FauconClubBundle:User')->find($id);
        return array(
            'category' => $category,
            'challengeid' => $challengeid,
            'user' => $user,
            'me' => $me,
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
