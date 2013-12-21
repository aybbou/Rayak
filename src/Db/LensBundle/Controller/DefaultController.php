<?php

namespace Db\LensBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Db\LensBundle\Form\PatentType;
use Db\LensBundle\Entity\Patent;

class DefaultController extends Controller {

    public function indexAction() {
        return $this->render('DbMainBundle:Default:index.html.twig', array(
                    'patent' => 'lens'
        ));
    }

    public function collabInventorsAction() {
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository("DbLensBundle:Inventor")->getInventorsCollabs();

        $response = new JsonResponse($data);
        return $response;
    }

    public function inventorsCountryAction() {
        $em = $this->getDoctrine()->getManager();
        $countries = $em->getRepository('DbLensBundle:Country')->findAll();

        $data = array();

        foreach ($countries as $country) {
            $data[strtolower($country->getCode())] = $country->getInventors()->count();
        }

        $response = new JsonResponse($data);
        return $response;
    }

    public function keywordsAction() {
        $em = $this->getDoctrine()->getManager();
        $patents = $em->getRepository('DbLensBundle:Patent')->findAll();

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
            $inventors = $em->getRepository('DbLensBundle:Inventor')->findBy(array('country' => $c));
        } else {
            $inventors = $em->getRepository('DbLensBundle:Inventor')->findAll();
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
        $patents = $em->getRepository('DbLensBundle:Patent')->findAllByPubDate();

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
