<?php

namespace Faucon\Bundle\ClubBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\ClubBundle\Entity\Club;
use Faucon\Bundle\ClubBundle\Form\ClubType;
use Faucon\Bundle\ClubBundle\Entity\User;
use Faucon\Bundle\ClubBundle\Form\Type\RegistrateUserFormType;
use Faucon\Bundle\ClubBundle\Form\UserType;
use Faucon\Bundle\ClubBundle\Entity\ClubRelation;
use Faucon\Bundle\ClubBundle\Form\ClubRelationType;
use Faucon\DataProviders\DataTables;

/**
 * Club controller.
 *
 * @Route("/club")
 */
class ClubController extends Controller
{
    /**
     * Lists all Club entities.
     *
     * @Route("/", name="club")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Page all Club entities.
     *
     * @Route("/paging", defaults={"_format"="json"}, name="club_paging")
     * @Template()
     */
    public function pagingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $datatables = new DataTables($this->getRequest(), $em->getRepository('FauconClubBundle:Club'), $this->container);
        return $this->render('FauconClubBundle:Club:paging.json.twig', array('data' => $datatables->getJsonResult()));
    }

    /**
     * Finds and displays a Club entity.
     *
     * @Route("/{id}/show", name="club_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('FauconRankingBundle:Category');
        $entity = $em->getRepository('FauconClubBundle:Club')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Club entity.');
        }

        return array(
            'entity'      => $entity,
            'repo' => $repo
            );
    }
    
    public function lastfiveAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FauconClubBundle:Club')->findLastFive();

        $dateutil = $this->get('date_utility');
        return $this->render('FauconClubBundle:Club:lastfive.html.twig', array('entities' => $entities, 'dateutil' => $dateutil));
    }

    /**
     * Creates a wizard to generate a new club and an administrator
     * for the club
     * 
     * @Route("/wizard/", name="club_wizard")
     * @Template()
     */
    public function wizardAction()
    {
        $club = new Club();
        $usermanager = $this->container->get('fos_user.user_manager');
        $user = $usermanager->createUser();
        $clubrelation = new ClubRelation();
        $clubrelation->setClub($club);
        $clubrelation->setUser($user);
        $user->addClubRelation($clubrelation);
        $club->addClubRelation($clubrelation);
        $form = $this->createForm($this->container->get('club_relation_type'), $clubrelation);

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Club entity.
     *
     * @Route("/create", name="club_create")
     * @Method("post")
     * @Template("FauconClubBundle:Club:wizard.html.twig")
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $club = new Club();
        $usermanager = $this->container->get('fos_user.user_manager');
        $user = $usermanager->createUser();
        $clubrelation = new ClubRelation();
        $clubrelation->setClub($club);
        $clubrelation->setUser($user);
        $clubrelation->setIsAdmin(true);
        $clubrelation->setCreatedby($user);
        $user->addClubRelation($clubrelation);
        $club->addClubRelation($clubrelation);
        $club->setCreatedby($user);
        $form = $this->createForm($this->container->get('club_relation_type'), $clubrelation);
        $form->bindRequest($request);
        $user->addRole('ROLE_CLUB_ADMIN');  

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $em->persist($clubrelation);
                $em->persist($user);
                $em->persist($club);
                $em->flush();
                $em->getConnection()->commit();
            }
            catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                throw $e;
            }
            $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
            if ($confirmationEnabled) {
                return $this->sendConfirmationMail($user);
            } else {
                $this->authenticateUser($user);
                $route = 'fos_user_registration_confirmed';
            }

            $this->container->get('session')->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);

            return new RedirectResponse($url);

            //return $this->redirect($this->generateUrl('club_show', array('id' => $club->getId())));
            
        }

        return array(
            'user' => $user,
            'form'   => $form->createView()
        );
    }
    
    protected function sendConfirmationMail($user)
    {
        $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
        $this->container->get('fos_user.mailer')->sendConfirmationEmailMessage($user);
        return new RedirectResponse($this->container->get('router')->generate('fos_user_registration_check_email'));
    }

    /**
     * Authenticate a user with Symfony Security
     *
     * @param \Faucin\Bundle\ClubBundle\Entity\User $user
     */
    protected function authenticateUser(User $user)
    {
        try {
            $this->container->get('fos_user.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            // Don't authenticate locked, disabled or expired users
            return;
        }

        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }

}
