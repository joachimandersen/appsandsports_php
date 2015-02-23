<?php

namespace Faucon\Bundle\ClubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\ClubBundle\Entity\User;
use Faucon\Bundle\ClubBundle\Entity\ClubRelation;
use Faucon\Bundle\RankingBundle\Entity\Ranking;
use Faucon\Bundle\ClubBundle\Form\UserType;
use Faucon\Bundle\RankingBundle\Form\RankingType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User controller.
 *
 * @Route("/admin/user")
 */
class UserAdminController extends Controller
{
        /**
     * Lists all User entities.
     *
     * @Route("/", name="admin_user")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $clubrelations = $em
                ->getRepository('FauconClubBundle:User')
                ->getByClubs($em->getRepository('FauconClubBundle:Club')->getClubsThatUserAdministrates($user));

        return array('clubrelations' => $clubrelations);
    }

    private function sendWelcomeMail($user, $password)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($this->container->get('translator')->trans('admin.user.welcome.mail.subject'))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($user->getEmail())
            ->setBody($this->container->get('templating')->render(
                        'FauconClubBundle:UserAdmin:welcomemail.txt.twig',
                        array(
                            'firstname' => $user->getFirstname(),
                            'lastname' => $user->getLastname(),
                            'login' => $user->getUsername(),
                            'password' => $password
                        )));
        try
        {
            $this->container->get('mailer')->send($message);
        }
        catch (Exception $ex)
        {
            // Log exception and message body
        }
    }
    
    private function createUser(User $entity)
    {
        $usermanager = $this->container->get('fos_user.user_manager');
        $user = $usermanager->createUser();
        $user->setUsername($entity->getUsername());
        $user->setEmail($entity->getEmail());
        $user->setFirstname($entity->getFirstname());
        $user->setLastname($entity->getLastname());
        $user->setPhone($entity->getPhone());
        $user->setEnabled(true);
        $password = \Faucon\Bundle\ClubBundle\Services\PasswordUtility::createRandomPassword();
        $user->setPlainPassword($password);
        return array('user' => $user, 'password' => $password);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FauconClubBundle:User')->find($id);
        if (!$this->canEdit($user)){
            throw new AccessDeniedException('Unauthorized access.');
        }
        $rankings = $em->getRepository('FauconRankingBundle:Category')
                ->findAllByUser($user);
        $clubrelations = $em->getRepository('FauconClubBundle:Club')->findByUser($user);
        $clubs = array();
        foreach($clubrelations as $clubrelation) {
            $clubs[] = $clubrelation->getClub();
        }
        $categories = $em->getRepository('FauconRankingBundle:Category')->findAllByClubs($clubs);
        $ids = array();
        foreach ($rankings as $ranking) {
            $ids[] = $ranking->getCategory()->getId();
        }

        return array(
            'user' => $user,
            'categories' => $categories,
            'ids' => $ids
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/update", defaults={"_format"="json"}, name="admin_user_update")
     * @Method("post")
     */
    public function updateAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FauconClubBundle:User')->find($id);
        $currentuser = $this->get('security.context')->getToken()->getUser();

        $request = $this->getRequest();
        $categoryjson = $request->getContent();
        $categoriesobject = json_decode($categoryjson, true);
        $categoryids = array();
        foreach ($categoriesobject['categories'] as $categoryid) {
            $categoryids[] = intval($categoryid);
        }
        try {
            $em->getRepository('FauconRankingBundle:Category')->updateCategoriesFor($user, $categoryids, $currentuser);
        }
        catch (Exception $ex) {
            return $this->render('FauconClubBundle:UserAdmin:response.json.twig', array('data' => array('status' => 'error')));
        }

        return $this->render('FauconClubBundle:UserAdmin:response.json.twig', array('data' => array('status' => 'ok')));
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", defaults={"_format"="json"}, name="admin_user_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new HttpException('Unauthorized access.', 401);
        }
        $em = $this->getDoctrine()->getManager();

        try {
            $user = $em->getRepository('FauconClubBundle:User')->find($id);
            $currentuser = $this->get('security.context')->getToken()->getUser();
            $clubrepository = $em->getRepository('FauconClubBundle:Club');
            if (!$clubrepository->isUserInMyClub($user, $currentuser)) {
                return $this->render('FauconClubBundle:UserAdmin:response.json.twig', array('data' => array('status' => 'denied')));
            }

            if (!$user) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            /*foreach ($user->getRankings() as $ranking) {
                $em->remove($ranking);
            }
            foreach ($user->getRankinghistory() as $rankinghistory) {
                $em->remove($rankinghistory);
            }
            foreach ($user->getClubRelations() as $clubrelation) {
                $em->remove($clubrelation);
            }
            $em->remove($user);
            $em->flush();*/
            $em->getRepository('FauconClubBundle:User')->deleteUser($user);
        }
        catch (Exception $ex) {
            return $this->render('FauconClubBundle:UserAdmin:response.json.twig', array('data' => array('status' => 'error')));
        }

        return $this->render('FauconClubBundle:UserAdmin:response.json.twig', array('data' => array('status' => 'ok')));
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function canEdit(User $user)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLUB_ADMIN')) {
            throw new AccessDeniedException('Unauthorized access.');
        }
        $currentuser = $this->get('security.context')->getToken()->getUser();
        return $this->getDoctrine()->getManager()->getRepository('FauconClubBundle:Club')
                ->isUserInMyClub($user, $currentuser);
    }
}
