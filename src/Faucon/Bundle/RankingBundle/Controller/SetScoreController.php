<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\SetScore;
use Faucon\Bundle\RankingBundle\Form\SetScoreType;

/**
 * SetScore controller.
 *
 * @Route("/setscore")
 */
class SetScoreController extends Controller
{
    /**
     * Lists all SetScore entities.
     *
     * @Route("/", name="setscore")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:SetScore')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a SetScore entity.
     *
     * @Route("/{id}/show", name="setscore_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:SetScore')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SetScore entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new SetScore entity.
     *
     * @Route("/new", name="setscore_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new SetScore();
        $form   = $this->createForm(new SetScoreType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new SetScore entity.
     *
     * @Route("/create", name="setscore_create")
     * @Method("post")
     * @Template("FauconRankingBundle:SetScore:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new SetScore();
        $request = $this->getRequest();
        $form    = $this->createForm(new SetScoreType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('setscore_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing SetScore entity.
     *
     * @Route("/{id}/edit", name="setscore_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:SetScore')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SetScore entity.');
        }

        $editForm = $this->createForm(new SetScoreType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing SetScore entity.
     *
     * @Route("/{id}/update", name="setscore_update")
     * @Method("post")
     * @Template("FauconRankingBundle:SetScore:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:SetScore')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SetScore entity.');
        }

        $editForm   = $this->createForm(new SetScoreType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('setscore_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a SetScore entity.
     *
     * @Route("/{id}/delete", name="setscore_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('FauconRankingBundle:SetScore')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SetScore entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('setscore'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
