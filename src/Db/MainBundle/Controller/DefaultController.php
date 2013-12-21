<?php

namespace Db\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Db\CreatorBundle\Form\PatentType;
use Db\CreatorBundle\Entity\Patent;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('DbMainBundle:Default:index.html.twig',array(
            'patent'=>'main'
        ));
    }

    public function collabInventorsAction() {
        $data = array();
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository("DbCreatorBundle:Inventor")->getInventorsCollabs();
        
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

        $n = null;
        if (isset($_GET['n'])) {
            $n = (int) $_GET['n'];
        }

        $data = $this->get('db.extractor')->getKeywordsFromPatents($patents, $n);

        $response = new JsonResponse($data);
        return $response;
    }

    public function inventorsAction() {
        $em = $this->getDoctrine()->getManager();

        if (isset($_GET['c'])) {
            $c = $_GET['c'];
            $inventors = $em->getRepository('DbCreatorBundle:Inventor')->findBy(array('country' => $c));
        } else {
            $inventors = $em->getRepository('DbCreatorBundle:Inventor')->findAll();
        }


        $data = array();

        foreach ($inventors as $inventor) {
            $data[$inventor->getFullName() . ' (' . strtoupper($inventor->getCountry()->getCode()) . ')'] = $inventor->getPatents()->count();
        }

        arsort($data);

        $break = false;
        if (isset($_GET['n'])) {
            $n = $_GET['n'];
            $break = true;
        }

        $d = array();
        $c = 1;
        foreach ($data as $key => $count) {
            $d[] = array('name' => $key, 'count' => $count);
            if ($break) {
                if ($c == $n) {
                    break;
                }
            }
            $c++;
        }

        $response = new JsonResponse($d);
        return $response;
    }

    public function evolutionAction() {
        $em = $this->getDoctrine()->getManager();
        $patents = $em->getRepository('DbCreatorBundle:Patent')->countPatentsByPubDate();
        
        $data = array();

        foreach ($patents as $key => $value) {
            $date = $value["publicationDate"]->format('d/m/Y');
            $data[$date] = intval($value[1]);
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
