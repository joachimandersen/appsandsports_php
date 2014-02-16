<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\Category;
use Faucon\Bundle\RankingBundle\Form\CategoryType;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{
    /**
     * Lists all Category entities.
     *
     * @Route("/category", name="category")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Category')->findAll();

        return array('entities' => $entities);
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Category')->findAll();

        return $this->render('FauconRankingBundle:Category:list.html.twig', array('entities' => $entities));
    }
    
    /**
     * Finds and displays a Category entity.
     *
     * @Route("/category/{id}/show", name="category_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @Route("/admin/category/new", name="category_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createForm(new CategoryType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/admin/category/{id}/delete", name="category_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('FauconRankingBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
