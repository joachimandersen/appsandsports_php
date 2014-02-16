<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\Category;
use Faucon\Bundle\RankingBundle\Form\CategoryType;

/**
 * Challenge admin controller.
 *
 * @Route("/admin/category")
 */
class CategoryAdminController extends Controller
{

    /**
     * Finds and displays a Club entity.
     *
     * @Route("/{id}/add", name="category_admin_add")
     * @Template()
     */
    public function addAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $entity = new Category();
        $form   = $this->createForm($this->get('category_type'), $entity);
        
        return array(
            'entity' => $entity,
            'clubid' => $id,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/{id}/create", name="admin_category_create")
     * @Method("post")
     * @Template("FauconRankingBundle:CategoryAdmin:add.html.twig")
     */
    public function createAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $entity  = new Category();
        $request = $this->getRequest();
        $form    = $this->createForm($this->get('category_type'), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $user = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getEntityManager();
            $entity->setCreatedby($user);
            $club = $em->getRepository('FauconClubBundle:Club')->find($id);
            $entity->setClub($club);
            $club->addCategory($entity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('club_admin_show', array('id' => $entity->getClub()->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }
    
    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_admin_edit")
     * @Template()
     */
    public function editAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createForm($this->get('category_type'), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Category entity.
     *
     * @Route("/admin/category/{id}/update", name="category_admin_update")
     * @Method("post")
     * @Template("FauconRankingBundle:CategoryAdmin:edit.html.twig")
     */
    public function updateAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm   = $this->createForm($this->get('category_type'), $entity);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('club_admin_show', array('id' => $entity->getClub()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/rankingsedit", name="category_admin_edit_rankings")
     * @Template()
     */
    public function rankingseditAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        
        $rankings = $em->getRepository('FauconRankingBundle:Ranking')->getByCategory($entity);

        return array(
            'entity'      => $entity,
            'rankings' => $rankings,
        );
    }
    
    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/rankingsupdate", defaults={"_format"="json"}, name="category_admin_update_rankings")
     * @Method("post")
     */
    public function rankingsupdateAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('FauconRankingBundle:Category')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $request = $this->getRequest();
        // Disable the automatic ranking update!
        $request->attributes->set('skipevent', 'true');
        $rankings = $request->getContent();
        try {
            $em->getRepository('FauconRankingBundle:Ranking')->updateRankingsInCategory($entity, $rankings);
        } catch (Exception $exc) {
            return $this->render('FauconRankingBundle:CategoryAdmin:response.json.twig', array('data' => array('status' => 'error')));
        }
        return $this->render('FauconRankingBundle:CategoryAdmin:response.json.twig', array('data' => array('status' => 'ok')));
    }
}
