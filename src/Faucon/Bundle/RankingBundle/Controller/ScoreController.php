<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\Score;
use Faucon\Bundle\RankingBundle\Form\ScoreType;

/**
 * Score controller.
 *
 * @Route("/score")
 */
class ScoreController extends Controller
{
    /**
     * Lists all Score entities.
     *
     * @Route("/", name="score")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Score')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Score entity.
     *
     * @Route("/{id}/show", name="score_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Score')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Score entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Score entity.
     *
     * @Route("/new", name="score_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Score();
        $form   = $this->createForm(new ScoreType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Score entity.
     *
     * @Route("/create", name="score_create")
     * @Method("post")
     * @Template("FauconRankingBundle:Score:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Score();
        $request = $this->getRequest();
        $form    = $this->createForm(new ScoreType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('score_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Score entity.
     *
     * @Route("/{id}/edit", name="score_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Score')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Score entity.');
        }

        $editForm = $this->createForm(new ScoreType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Score entity.
     *
     * @Route("/{id}/update", name="score_update")
     * @Method("post")
     * @Template("FauconRankingBundle:Score:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Score')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Score entity.');
        }

        $editForm   = $this->createForm(new ScoreType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('score_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Score entity.
     *
     * @Route("/{id}/delete", name="score_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('FauconRankingBundle:Score')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Score entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('score'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
