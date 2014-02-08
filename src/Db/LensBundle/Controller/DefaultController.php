<?php

namespace Db\LensBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('DbMainBundle:Default:index.html.twig', array(
                    'patent' => 'lens'
        ));
    }

    public function inventorKeywordsAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository("DbLensBundle:Inventor");
        $result = $repo->getTopXInventors(1);
        $inventor = $repo->findOneByFullName($result[0]["fullName"]);
        $keywords = $this->get('db.extractor')->getKeywordsOfInventor($inventor);
        return new JsonResponse($keywords);
    }

    public function collabInventorsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository("DbLensBundle:Inventor")->getInventorsCollabs();

        $response = new JsonResponse(array("links" => $data));
        return $response;
    }

    public function inventorsCountryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $countries = $em->getRepository('DbLensBundle:Country')->findAll();

        $data = array();

        foreach ($countries as $country) {
            $data[strtolower($country->getCode())] = $country->getInventors()->count();
        }

        $response = new JsonResponse($data);
        return $response;
    }

    public function keywordsAction()
    {
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

    public function inventorsAction()
    {
        $em = $this->getDoctrine()->getManager();

        if (isset($_GET['c'])) {
            $c = $_GET['c'];
            $inventors = $em->getRepository('DbLensBundle:Inventor')->getTopXInventors(10, $c);
        } else {
            $inventors = $em->getRepository('DbLensBundle:Inventor')->getTopXInventors(10);
        }

        $data = array();

        foreach ($inventors as $inventor) {
            $pays = $inventor["code"];
            $key = $inventor["fullName"] . ' (' . strtoupper($pays) . ')';
            $data[] = array('name' => $key, 'count' => intval($inventor["num"]));
        }
        $response = new JsonResponse($data);
        return $response;
    }

    public function evolutionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $patents = $em->getRepository('DbLensBundle:Patent')->countPatentsByPubDate();

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
