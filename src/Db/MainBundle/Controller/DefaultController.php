<?php

namespace Db\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Db\CreatorBundle\Form\PatentType;
use Db\CreatorBundle\Entity\Patent;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('DbMainBundle:Default:index.html.twig');
    }
    
    public function countryInventorsAction(){
        $em = $this->getDoctrine()->getManager();
        
        $countryCode= $_GET['countryCode'];
        $country = $em->getRepository('DbCreatorBundle:Country')->find($countryCode);
        
        if(!$country){
            $error = array('error'=>'Ce pays n\'a pas d\'inventeurs!');
            return new JsonResponse($error);
        }
        
        $inventors = $country->getInventors();
        $data =array();
        
        foreach($inventors as $inventor){
            $data[]=array('name'=>$inventor->getFullName());
        }
        
        $response = new JsonResponse($data);
        return $response;
    }

    public function inventorsCountryAction() {
        $em = $this->getDoctrine()->getManager();
        $countries = $em->getRepository('DbCreatorBundle:Country')->findAll();

        $data = array();

        foreach ($countries as $country) {
            $data[strtolower($country->getCode())] = $country->getInventors()->count();
        }

        $response = new JsonResponse($data);
        return $response;
    }

    public function keywordsAction() {
        $em = $this->getDoctrine()->getManager();
        $patents = $em->getRepository('DbCreatorBundle:Patent')->findAll();

        $data = $this->get('db.extractor')->getKeywordsFromPatents($patents);

        $response = new JsonResponse($data);
        return $response;
    }

    public function evolutionAction() {
        $em = $this->getDoctrine()->getManager();
        $patents = $em->getRepository('DbCreatorBundle:Patent')->findAllByPubDate();

        $data = array();

        foreach ($patents as $patent) {
            $pubDate = $patent->getPublicationDate()->format('d/m/Y');

            if (isset($data[$pubDate])) {
                $data[$pubDate] ++;
            } else {
                $data[$pubDate] = 1;
            }
        }

        foreach ($data as $key => $d) {
            $key = str_replace('/', '-', $key);
            $year = date('Y', strtotime($key));
            $month = date('m', strtotime($key));
            $day = date('d', strtotime($key));
            $final [] = array('year' => $year, 'month' => $month, 'day' => $day, 'count' => $d);
        }

        $response = new JsonResponse($final);
        return $response;
    }

}
