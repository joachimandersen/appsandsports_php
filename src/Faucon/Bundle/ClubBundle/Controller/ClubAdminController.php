<?php

namespace Faucon\Bundle\ClubBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\ClubBundle\Entity\Club;
use Faucon\Bundle\ClubBundle\Form\ClubType;
use Faucon\Bundle\ClubBundle\Entity\User;
use Faucon\Bundle\ClubBundle\Form\Type\RegistrateUserFormType;
use Faucon\Bundle\ClubBundle\Form\UserType;

/**
 * Club controller.
 *
 */
class ClubAdminController extends Controller
{
    /**
     * Lists all Club entities.
     *
     * @Route("/admin/club/", name="club_admin")
     * @Template()
     */
    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconClubBundle:Club')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Club entity.
     *
     * @Route("/admin/club/{id}/edit", name="club_admin_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->checkCredentialsAndReturnClub($id);

        $editForm = $this->createForm(new ClubType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Edits an existing Club entity.
     *
     * @Route("/admin/club/{id}/update", name="club_admin_update")
     * @Method("post")
     * @Template("FauconClubBundle:ClubAdmin:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $this->checkCredentialsAndReturnClub($id);

        $editForm   = $this->createForm(new ClubType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('club_admin_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays Club entites.
     *
     * @Route("/admin/club/{id}/show", name="club_admin_show")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->checkCredentialsAndReturnClub($id);

        $deleteForm = $this->createDeleteForm($id);

        $sport = $this->get('sport');
        return array(
            'entity'      => $entity,
            'sport' => $sport,
            'delete_form' => $deleteForm->createView(),        );
    }
    
    /**
     * Finds and displays Club entites.
     *
     * @Route("/admin/club/list", name="club_admin_list")
     * @Template()
     */
    public function listAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_CLUB_ADMIN') === false) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }
        $user = $this->get('security.context')->getToken()->getUser();
        //$em = $this->getDoctrine()->getEntityManager();
        //$clubs = $em->getRepository('FauconClubBundle:Club')->getClubsThatUserAdministrates($user);
        
        $clubs = array();
        foreach($user->getClubRelations() as $clubrelation) {
            if ($clubrelation->getIsAdmin()) {
                $clubs[] = $clubrelation->getClub();
            }
        }

        return array(
            'clubs'      => $clubs
            );
    }
    
    /**
     * Finds and displays Club entites.
     *
     * @Route("/admin/club/{id}/invite", name="club_admin_invite")
     * @Template()
     */
    public function inviteAction($id)
    {
        if ($this->get('security.context')->isGranted('ROLE_CLUB_ADMIN') === false) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }
        //$user = $this->get('security.context')->getToken()->getUser();
        $club = $this->checkCredentialsAndReturnClub($id);

        return array(
            'club' => $club
            );
    }
    
    /**
     * Send invitations to join a club
     *
     * @Route("/admin/club/{id}/sendinvites", defaults={"_format"="json"}, name="club_admin_send_invite")
     * @Method("post")
     */
    public function sendinvitesAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $em = $this->getDoctrine()->getEntityManager();

        //$user = $this->get('security.context')->getToken()->getUser();
        $club = $this->checkCredentialsAndReturnClub($id);
        $request = $this->getRequest();
        $emailsobject = json_decode($request->getContent());
        $invitationrepository = $em->getRepository('FauconClubBundle:InvitationToken');
        try {
            $invitationrepository->createTokensForClub($club, $emailsobject->emails);
        } catch (Exception $exc) {
            return $this->render('FauconClubBundle:ClubAdmin:response.json.twig', array('data' => array('status' => 'error')));
        }
        return $this->render('FauconClubBundle:ClubAdmin:response.json.twig', array('data' => array('status' => 'ok')));
    }

    /**
     * Trigger the sending of unsent invites
     *
     * @Route("/club/sendunsentinvites", defaults={"_format"="json"}, name="club_admin_trigger_send_invite")
     * @Method("get")
     */
    public function sendUnsentInvitesAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $itr = $em->getRepository('FauconClubBundle:InvitationToken');
        $itr->setContainer($this->container);
        $itr->sendInvites();
        return $this->redirect($this->generateUrl('club_admin_list'));
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function checkCredentialsAndReturnClub($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $clubrepository = $em->getRepository('FauconClubBundle:Club');
        $entity = $clubrepository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }
        if (!$clubrepository->isClubAdministrator($user, $entity)) {
            throw new AccessDeniedHttpException('Unauthorized access.');
        }
        return $entity;
    }
}
