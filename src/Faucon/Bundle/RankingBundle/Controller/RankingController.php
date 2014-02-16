<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\Ranking;
use Faucon\Bundle\RankingBundle\Form\RankingType;
use Faucon\Bundle\ClubBundle\Entity\User;
use Faucon\DataProviders\DataTables;

/**
 * Ranking controller.
 *
 */
class RankingController extends Controller
{
    /**
     * Lists all Categories with links to the ranking lists.
     *
     * @Route("/ranking", name="ranking")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Page all Game entities.
     *
     * @Route("/ranking/paging", defaults={"_format"="json"}, name="ranking_paging")
     * @Template()
     */
    public function pagingAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $datatables = new DataTables($this->getRequest(), $em->getRepository('FauconRankingBundle:Ranking'), $this->container);
        return $this->render('FauconRankingBundle:Ranking:paging.json.twig', array('data' => $datatables->getJsonResult()));
    }

    
    /**
     * Lists all Categories with links to the ranking lists.
     *
     * @Route("/ranking/{clubid}", name="ranking_club")
     * @Template()
     */
    public function categoryAction($clubid)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Category')->findAllInClub($clubid);

        return array('entities' => $entities);
    }
    
    /**
     * Lists all Rankings for a given Category.
     *
     * @Route("/ranking/{categoryid}/list", name="ranking_list")
     * @Template()
     */
    public function listAction($categoryid)
    {
        return $this->rankingAction($categoryid);
    }
    
    /**
     * Lists all Rankings for a given Category - to be used in ie. an iframe.
     *
     * @Route("/ranking/{categoryid}/isolated", name="ranking_isolated")
     * @Template()
     */
    public function isolatedAction($categoryid)
    {
        return $this->rankingAction($categoryid);
    }
    
    private function rankingAction($categoryid)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $category = $em->getRepository('FauconRankingBundle:Category')->find($categoryid);
        $rr = $em->getRepository('FauconRankingBundle:Ranking');
        $rr->setContainer($this->container);
        
        $authenticated = count($this->get('security.context')->getToken()->getRoles()) != 0;
        if (!$authenticated) {
            $data = $rr->getRankingArray(null, $category);
        }
        else {
            $data = $rr->getRankingArray($this->get('security.context')->getToken()->getUser(), $category);
        }

        return array(
            'data' => $data['data'], 
            'category' => $category, 
            'count' => $data['count'], 
            'layers' => $data['layers'],
            'authenticated' => $authenticated,
            'maxitemsperlayer' => $rr->getMaxItemsPerLayer());
    }

    /**
     * Displays a form to edit an existing Ranking entity.
     *
     * @Route("/admin/ranking/{id}/edit", name="ranking_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Ranking')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ranking entity.');
        }

        $editForm = $this->createForm(new RankingType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Ranking entity.
     *
     * @Route("/admin/ranking/{id}/update", name="ranking_update")
     * @Method("post")
     * @Template("FauconRankingBundle:Ranking:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Ranking')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ranking entity.');
        }

        $editForm   = $this->createForm(new RankingType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                //$em->getRepository('FauconRankingBundle:Ranking')
                //        ->updateRankings($entity);
                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();
            }
            catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                throw $e;
            }

            return $this->redirect($this->generateUrl('ranking_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Ranking entity.
     *
     * @Route("/admin/ranking/{id}/delete", name="ranking_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        throw $this->createNotFoundException('Not implemented');
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('FauconRankingBundle:Ranking')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ranking entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ranking'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
