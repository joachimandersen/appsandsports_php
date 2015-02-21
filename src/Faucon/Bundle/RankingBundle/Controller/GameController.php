<?php

namespace Faucon\Bundle\RankingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Faucon\Bundle\RankingBundle\Entity\Game;
use Faucon\Bundle\RankingBundle\Form\GameType;
use Faucon\DataProviders\DataTables;


/**
 * Game controller.
 *
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * Lists all Game entities.
     *
     * @Route("/", name="game")
     * @Template()
     */
    public function indexAction()
    {
        return array();
        /*$em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('FauconRankingBundle:Game')->findAll();

        return array('entities' => $entities);*/
    }
    
    /**
     * Page all Game entities.
     *
     * @Route("/paging", defaults={"_format"="json"}, name="game_paging")
     * @Template()
     */
    public function pagingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $datatables = new DataTables($this->getRequest(), $em->getRepository('FauconRankingBundle:Game'), $this->container);
        return $this->render('FauconRankingBundle:Game:paging.json.twig', array('data' => $datatables->getJsonResult()));
    }

    public function lastfiveAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FauconRankingBundle:Game')->findLastFive();

        $dateutil = $this->get('date_utility');
        return $this->render('FauconRankingBundle:Game:lastfive.html.twig', array('entities' => $entities, 'dateutil' => $dateutil));
    }
    
    public function lastfiveincategoryAction($categoryid)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FauconRankingBundle:Game')->findLastFiveInCategory($categoryid);

        $dateutil = $this->get('date_utility');
        return $this->render('FauconRankingBundle:Game:lastfive.html.twig', array('entities' => $entities, 'dateutil' => $dateutil));
    }

    /**
     * Finds and displays a Game entity.
     *
     * @Route("/{id}/show", name="game_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('FauconRankingBundle:Game');
        $entity = $repo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Game entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $dateutil = $this->get('date_utility');
        return array(
            'entity'      => $entity,
            'home' => $this->getHomeScore($entity->getScore()),
            'away' => $this->getAwayScore($entity->getScore()),
            'setwinner' => $this->getSetWinner($entity->getScore()),
            'winner' => $entity->getWinner(),
            'dateutil' => $dateutil,
            'delete_form' => $deleteForm->createView(),        );
    }

    private function getHomeScore(\Faucon\Bundle\RankingBundle\Entity\Score $score)
    {
        $home = array();
        foreach($score->getSetscore() as $set) {
            $home[] = $set->getHomegames();
        }
        return $home;
    }

    private function getAwayScore(\Faucon\Bundle\RankingBundle\Entity\Score $score)
    {
        $away = array();
        foreach($score->getSetscore() as $set) {
            $away[] = $set->getAwaygames();
        }
        return $away;
    }


    public function getSetWinner(\Faucon\Bundle\RankingBundle\Entity\Score $score)
    {
        $winner = array();
        foreach($score->getSetscore() as $set) {
            $winner[] = $set->getSetWinner();
        }
        return $winner;
    }

    /**
     * Displays a form to create a new Game entity.
     *
     * @Route("/{userid}/{challengeid}/new", name="game_new")
     * @Template()
     */
    public function newAction($userid, $challengeid)
    {
        return $this->getNewActionData($userid, $challengeid);
    }

    private function getNewActionData($userid, $challengeid, $score = null)
    {
        $entity = new Game();
        $form   = $this->createForm(new GameType(), $entity)
                ->remove('created')
                ->remove('createdby')
                ->remove('challenge')
                ->remove('winner')
                ->remove('notfinished')
                ->remove('score');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('FauconClubBundle:User')->find($userid);
        $challenge = $em->getRepository('FauconRankingBundle:Challenge')->find($challengeid);
        $statuses = $this->get('match_status')->getMatchStatuses();
        $sportsection = $this->container->getParameter('sport');
        $sport = $this->get('sport')->getSportName($challenge->getCategory()->getSport());
        $numberofsetspermatch = $sportsection[$sport]['maxsetsinmatch'];
        $setstowin = $sportsection[$sport]['setstowin'];
        $numberofpointstowinset = $sportsection[$sport]['numberofpointstowinset'];
        return array(
            'entity' => $entity,
            'user' => $user,
            'challenge' => $challenge,
            'statuses' => $statuses,
            'form'   => $form->createView(),
            'numberofsetspermatch' => $numberofsetspermatch,
            'numberofpointstowinset' => $numberofpointstowinset,
	    'setstowin' => $setstowin,
	    'initialscore' => $score
        );
    }

    /**
     * Creates a new Game entity.
     *
     * @Route("/{userid}/{challengeid}/create", name="game_create")
     * @Method("post")
     * @Template("FauconRankingBundle:Game:new.html.twig")
     */
    public function createAction($userid, $challengeid)
    {
        $logger = $this->get('logger');
        $entity  = new Game();
        $request = $this->getRequest();
        $form    = $this->createForm(new GameType(), $entity);
        $form->bindRequest($request);
        $score = json_decode($request->get('faucon_bundle_rankingbundle_gametype_score'));
        $matchstatus = $request->get('faucon_bundle_rankingbundle_gametype_notfinished');
        $em = $this->getDoctrine()->getManager();
        $scoreisvalid = $em->getRepository('FauconRankingBundle:Score')->isScoreValid($score);

        $challenge = $em->getRepository('FauconRankingBundle:Challenge')->find($challengeid);
        if ($form->isValid() && $challenge->getChallenger()->getId() == $userid && $scoreisvalid && $entity->getDescription() != null && $entity->getPlayed() != null) {
            $currentuser = $this->get('security.context')->getToken()->getUser();
            $entity->setCreatedby($currentuser);
            $entity->setChallenge($challenge);
            $entity->setNotfinished($matchstatus);
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $s = $em->getRepository('FauconRankingBundle:Score')->saveScore($score, $challenge, $currentuser);
                $entity->setScore($s);
                $entity->setWinner($em->getRepository('FauconRankingBundle:Score')->getWinner($score, $challenge));
                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();
            }
            catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                throw $e;
            }

            return $this->redirect($this->generateUrl('game_show', array('id' => $entity->getId())));
            
	}

	return $this->getNewActionData($userid, $challengeid, json_decode($request->get('faucon_bundle_rankingbundle_gametype_score')));
    }

    private function sendChallengeMail(\Faucon\Bundle\ClubBundle\Entity\User $challenged, \Faucon\Bundle\ClubBundle\Entity\User $challenger)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('joachimfanderen@gmail.com')
            ->setTo($challenged->getEmail())
            ->setBody($this->renderView('FauconRankingBundle:Game:challengeemail.txt.twig', array('name' => $challenged, 'by' => $challenger)));
        try
        {
            //$this->get('mailer')->send($message);
        }
        catch (Exception $ex)
        {
            // Log exception and message body
        }
    }
    
    /**
     * Displays a form to edit an existing Game entity.
     *
     * @Route("/{id}/edit", name="game_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FauconRankingBundle:Game')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Game entity.');
        }

        $editForm = $this->createForm(new GameType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Game entity.
     *
     * @Route("/{id}/update", name="game_update")
     * @Method("post")
     * @Template("FauconRankingBundle:Game:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FauconRankingBundle:Game')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Game entity.');
        }

        $editForm   = $this->createForm(new GameType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('game_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Game entity.
     *
     * @Route("/{id}/delete", name="game_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FauconRankingBundle:Game')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Game entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('game'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
