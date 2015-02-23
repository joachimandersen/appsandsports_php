<?php

namespace Faucon\Bundle\ClubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\ClubBundle\Entity\User;
use Faucon\Bundle\ClubBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/user/", name="user")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('FauconRankingBundle:Category')->findAll();
        $data = array();
        foreach ($categories as $category)
        {
            $rankings = $em->getRepository('FauconRankingBundle:Ranking')->getUsersByCategory($category);
            $data[] = array('category' => $category, 'rankings' => $rankings);
        }

        return array('data' => $data);
    }

    /**
     * Finds and displays a User entity and all his open challenges.
     *
     * @Route("/user/{id}/showall", name="user_show_open_challenges")
     * @Template()
     */
    public function showallAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FauconClubBundle:User')->find($id);
        $openchallenges = $em->getRepository('FauconRankingBundle:Challenge')->getChallengesByUser($entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return array(
            'entity' => $entity,
            'challenges' => $openchallenges,
            'any' => count($openchallenges),
            );
    }
    
    /**
     * Finds and displays a User entity.
     *
     * @Route("/user/{id}/{categoryid}/show", name="user_show")
     * @Template()
     */
    public function showAction($id, $categoryid)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FauconClubBundle:User')->find($id);
        $myself = $this->get('security.context')->getToken()->getUser() == $entity;
        $category = $em->getRepository('FauconRankingBundle:Category')->find($categoryid);
        $openchallenges = $em->getRepository('FauconRankingBundle:Challenge')->getChallengesByUserAndCategory($entity, $category);
        $ranking = $em->getRepository('FauconClubBundle:User')->getRankingByCategory($entity, $category);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return array(
            'entity' => $entity,
            'challenges' => $openchallenges,
            'ranking' => $ranking,
            'myself' => $myself,
            'category' => $category,        );
    }

    /**
     * Sends welcome mail to User
     *
     * @Route("/admin/user/{id}/welcome", name="user_welcome")
     */
    public function welcomeAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('FauconClubBundle:User')->find($id);

        $message = \Swift_Message::newInstance()
            ->setSubject('Velkommen til Slagelse Squash ranglistesystem')
            ->setFrom('ranking@slagelsesquash.dk')
            ->setTo($user->getEmail())
            ->setBody($this->render(
                        'FauconClubBundle:User:welcome.txt.twig',
                        array(
                            'name' => $user->__toString(), 
                            'login' => $user->getUsername())));
        try
        {
            $this->get('mailer')->send($message);
        }
        catch (Exception $ex)
        {
            $logger = $this->get('logger');
            $logger->err('Unable to send welcome mail - '.$ex);
        }
        
        return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
    }
    
    /**
     * Evaluates the availability of a username
     *
     * @Route("/user/username/valid", defaults={"_format"="json"}, name="user_valid_username")
     * @Template()
     */
    public function validUsernameAction()
    {
        $data = json_decode($this->getRequest()->getContent());
        $em = $this->getDoctrine()->getEntityManager();
        $ur = $em->getRepository('FauconClubBundle:User');
        return $this->render(
                'FauconClubBundle:User:availablity.json.twig', 
                array('data' => $ur->isUsernameAvailable($data->username))
                );
    }

    /**
     * Evaluates the availability of an email address
     *
     * @Route("/user/email/valid", defaults={"_format"="json"}, name="user_valid_email")
     * @Template()
     */
    public function validEmailAction()
    {
        $data = json_decode($this->getRequest()->getContent());
        $em = $this->getDoctrine()->getEntityManager();
        $ur = $em->getRepository('FauconClubBundle:User');
        return $this->render(
                'FauconClubBundle:User:availablity.json.twig', 
                array('data' => $ur->isEmailAddressAvailable($data->email) && filter_var($data->email, FILTER_VALIDATE_EMAIL))
                );
    }
}
