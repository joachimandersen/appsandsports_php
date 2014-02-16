<?php

namespace Faucon\Bundle\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function signupAction($invitationtoken)
    {
        $token = $this->getInvitationToken($invitationtoken);
        if ($token == null) {
            throw new NotFoundHttpException('Invalid token');
        }
        
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();
            $this->createClubRelation($user, $token);

            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $this->authenticateUser($user);
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);

            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:signup.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
            'theme' => $this->container->getParameter('fos_user.template.theme'),
            'invitationtoken' => $invitationtoken
        ));
    }
    
    private function getInvitationToken($invitationtoken)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $invitationtokenrepository = $em->getRepository('FauconClubBundle:InvitationToken');
        return $invitationtokenrepository->getInvitationToken($invitationtoken);
    }

    private function createClubRelation(\Faucon\Bundle\ClubBundle\Entity\User $user, \Faucon\Bundle\ClubBundle\Entity\InvitationToken $invitationtoken)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $em->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $clubrelation = new \Faucon\Bundle\ClubBundle\Entity\ClubRelation();
            $clubrelation->setClub($invitationtoken->getClub());
            $clubrelation->setCreatedby($user);
            $clubrelation->setUser($user);
            $clubrelation->setIsAdmin(FALSE);
            $invitationtoken->setUsed(new \DateTime("now"));
            $em->persist($invitationtoken);
            $em->persist($clubrelation);
            $em->flush();
            $em->getConnection()->commit();
        }
        catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }
    }
}
