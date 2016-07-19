<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feed;
use AppBundle\Entity\Weather;
use AppBundle\Entity\Twitter;
use AppBundle\Form\SearchOnGoogleType;
use Debril\RssAtomBundle\Protocol\FeedReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        /** @var FeedReader $reader */

        $components = $this->getDoctrine()->getRepository('AppBundle:Component')->findBy(array('user' => $user->getId()));
        foreach ($components as $component) {;
            switch ($component->getClassName()) {
                case "Feed":
                    $reader = $this->container->get('debril.reader');
                    /** @var Feed $component */
                    $component->parse($reader);
                    break;
                case "Gmail":
                    /** @var Twitter $component */
                    $component->parse($this->getUser()->getId(), $this->get('kernel')->getRootDir());
                    break;
                default:
                    $component->parse();
                    break;
            }
        }

        $formSearch = $this->createForm(SearchOnGoogleType::class, null, array(
            'action' => $this->generateUrl('homepage'),
        ));

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $data = $formSearch->getData();
            return $this->redirect('http://www.google.com/search?q='.urlencode($data['search']));
        }

        return $this->render('AppBundle::index.html.twig', array(
            'user' => $user,
            'formSearch' => $formSearch->createView(),
            'components' => $components
        ));
    }
}
