<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feed;
use AppBundle\Form\FeedType;
use Debril\RssAtomBundle\Protocol\Parser\Item;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Debril\RssAtomBundle\Protocol\FeedReader;


class FeedController extends Controller
{
    /**
     * @Route("/feed/new", name="ajax_new_feed")
     */
    public function editAction(Request $request)
    {
        $feed = new Feed();
        $formFeed = $this->createForm(FeedType::class, $feed, array(
            'action' => $this->generateUrl('ajax_new_feed'),
        ));

        $formFeed->handleRequest($request);

        if ($formFeed->isSubmitted() && $formFeed->isValid()) {
            $feed->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($feed);
            $em->flush();
            return new JsonResponse(array(
                'message'=> "Le flux a bien été enregistré.",
                'type' => 'success'
            ));
        }

        return $this->render('AppBundle:Feed:form.html.twig', array(
            'formFeed' => $formFeed->createView()
        ));
    }

    /**
     * @Route("/entry/refresh/{feed}", options={"expose"=true}, name="ajax_refresh_feed")
     */
    public function refreshAction(Feed $feed)
    {
        /** @var FeedReader $reader */
        $reader = $this->container->get('debril.reader');
        $feed->parse($reader);

        $twig = $this->get('twig');

        $tabItem = array();
        /** @var Item $item */
        foreach ($feed->getItems() as $item) {
            $tabItem[] = array(
                'title' => twig_truncate_filter($twig, $item->getTitle(), 70),
                'link' => $item->getLink(),
                'date' => $item->getUpdated()->format('H:i')
            );
        }

        return new JsonResponse($tabItem);
    }
}