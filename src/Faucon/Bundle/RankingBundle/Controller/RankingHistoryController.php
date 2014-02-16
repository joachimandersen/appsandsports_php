<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\RankingHistory;
use Faucon\Bundle\RankingBundle\Form\RankingHistoryType;

/**
 * RankingHistory controller.
 *
 * @Route("/rankinghistory")
 */
class RankingHistoryController extends Controller
{
    /**
     * Lists all RankingHistory entities.
     *
     * @Route("/", name="rankinghistory")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:RankingHistory')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Page all Game entities.
     *
     * @Route("/{id}/graph", defaults={"_format"="json"}, name="rankinghistory_graph")
     * @Template()
     */
    public function graphAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('FauconClubBundle:User')->find($id);
        $rankinghistories = $em->getRepository('FauconRankingBundle:RankingHistory')
                ->getByUser($user);
        $rankings = array();
        foreach($rankinghistories as $ranking)
        {
            $rankings[] = array(
                'date' => $ranking->getCreated()->format('Y, m, d'),
                'no' => $ranking->getRanking()
            );
        }
        $data = array(
            'name' => $user->__toString(),
            'rankings' => $rankings
        );
        return $this->render('FauconRankingBundle:RankingHistory:graph.json.twig', array('data' => $data));
    }

    /**
     * Finds and displays a RankingHistory entity.
     *
     * @Route("/{id}/show", name="rankinghistory_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:RankingHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RankingHistory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new RankingHistory entity.
     *
     * @Route("/new", name="rankinghistory_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new RankingHistory();
        $form   = $this->createForm(new RankingHistoryType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new RankingHistory entity.
     *
     * @Route("/create", name="rankinghistory_create")
     * @Method("post")
     * @Template("FauconRankingBundle:RankingHistory:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new RankingHistory();
        $request = $this->getRequest();
        $form    = $this->createForm(new RankingHistoryType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rankinghistory_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing RankingHistory entity.
     *
     * @Route("/{id}/edit", name="rankinghistory_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:RankingHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RankingHistory entity.');
        }

        $editForm = $this->createForm(new RankingHistoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing RankingHistory entity.
     *
     * @Route("/{id}/update", name="rankinghistory_update")
     * @Method("post")
     * @Template("FauconRankingBundle:RankingHistory:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:RankingHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RankingHistory entity.');
        }

        $editForm   = $this->createForm(new RankingHistoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rankinghistory_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a RankingHistory entity.
     *
     * @Route("/{id}/delete", name="rankinghistory_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('FauconRankingBundle:RankingHistory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RankingHistory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('rankinghistory'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
