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

        $inventors = $em->getRepository('DbCreatorBundle:Inventor')->findAll();

        foreach ($inventors as $inventor) {
            $patents = $inventor->getPatents();
            $data[$inventor->getFullName()] = array();
            foreach ($patents as $patent) {
                foreach ($patent->getInventors() as $inv) {
                    if (strcmp($inv->getFullName(), $inventor->getFullName())!=0) {
                        if (isset($data[$inventor->getFullName()][$inv->getFullName()])) {
                            $data[$inventor->getFullName()][$inv->getFullName()] ++;
                        } else {
                            $data[$inventor->getFullName()][$inv->getFullName()] = 1;
                        }
                    }
                }
            }
        }
        $d=array();
        foreach($data as $key=>$collabs){
            $c=array();
            foreach($collabs as $i=>$count){
                if($count<=2){
                    continue;
                }
                $c[]=array('inv'=>$key,'count'=>$count);
            }
            if(count($c)==0){continue;}
            $d[]=array('inv'=>$key,'collabs'=>$c);
        }

        $response = new JsonResponse($d);
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
