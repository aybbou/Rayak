<?php

namespace Db\MainBundle\Utility;

/**
 * Description of KeywordExtractor
 *
 * @author Ayyoub
 */
class KeywordsExtractor {

    private $notKeywords = array(
        ' ', 'and', 'or', 'the', 'i', 'by',
        'a', 'of', 'to', 'is', 'as', 'when', 'same',
        'an', 'in', 'for', 'with', 'are', 'thereof',
        'at', 'be', 'that', 'many', 'on', 'from');
    private $symbols = array('.', ',', '\\', '(', ')', '/', ';', ':');

    public function getKeywordsInventor($inventor, $number=20)
    {
        $titles = array();
        $keywords = array();
        $keys = array();
        $counts = array();
        $patents = $inventor->getPatents();

        foreach ($patents as $patent) {
            $titles[] = $patent->getTitle();
        }

        foreach ($titles as $title) {
            $var = str_replace($this->symbols, " ", strtolower($title));
            $titleExploded = explode(" ", $var);
            foreach ($titleExploded as $value) {
                if(!in_array($value, $this->notKeywords)){
                    $keywords[] = $value;    
                }
            }
        }

        $keywordsCounted = array_count_values($keywords);
        arsort($keywordsCounted);

        $keywordsCounted = array_slice($keywordsCounted, 0, $number);
        foreach ($keywordsCounted as $key => $value) {
            $keys[] = $key;
            $counts[] = $value;
        }

        return array('fullName' => $inventor->getFullName(), 'keywords' => $keys, 'counts' => $counts);
    }

    public function getKeywordsFromPatents($patents,$n=null)
    {
        $data = array();

        foreach ($patents as $patent) {
            $text = str_replace($this->symbols, '', $patent->getTitle());
            $keywords = explode(' ', $text);
            foreach ($keywords as $keyword) {
                $keyword = trim(strtolower($keyword));
                if (isset($data[$keyword])) {
                    $data[$keyword] ++;
                } else {
                    $data[$keyword] = 1;
                }
            }
        }

        arsort($data);
        
        $break=false;
        if($n!=null && is_int($n)){
            $break=true;
        }
        $c=1;
        
        foreach ($data as $keyword => $count) {
            if ($count > 10 && !in_array($keyword, $this->notKeywords) && !is_int($keyword) && strlen($keyword) > 1) {
                $d[] = array('keyword' => $keyword, 'count' => $count);
                if($c==$n && $break==true){
                    break;
                }
                $c++;
            }
        }

        return $d;
    }
}
