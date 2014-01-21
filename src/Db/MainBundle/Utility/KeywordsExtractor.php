<?php

namespace Db\MainBundle\Utility;

/**
 * Description of KeywordExtractor
 *
 * @author Abdelaziz
 */
class KeywordsExtractor
{

    private $notKeywords = array(
        ' ', 'and', 'or', 'the', 'i', 'by',
        'a', 'of', 'to', 'is', 'as', 'when', 'same',
        'an', 'in', 'for', 'with', 'are', 'thereof',
        'at', 'be', 'that', 'many', 'on', 'from');
    private $symbols = array('.', ',', '\\', '(', ')', '/', ';', ':');

    public function getKeywordsOfInventor($inventor, $number = 20)
    {
        $titles = array();
        $patents = $inventor->getPatents();

        foreach ($patents as $patent) {
            $titles[] = $patent->getTitle();
        }

        $keywordsCounted = $this->fromTitlesToKeywords($titles);

        $keywordsFinal = array_slice($keywordsCounted, 0, $number);
        $keys = array_keys($keywordsFinal);
        $counts = array_values($keywordsFinal);

        return array('fullName' => $inventor->getFullName(), 'keywords' => $keys, 'counts' => $counts);
    }

    private function fromTitlesToKeywords($titles)
    {
        $keywords = array();

        foreach ($titles as $title) {
            $var = str_replace($this->symbols, "", strtolower($title));
            $titleExploded = explode(" ", $var);
            foreach ($titleExploded as $value) {
                if (!in_array($value, $this->notKeywords) && strlen($value) > 2) {
                    $keywords[] = $value;
                }
            }
        }

        $final = array_count_values($keywords);
        arsort($final);

        return $final;
    }

    public function getKeywordsFromPatents($patents, $n = null)
    {
        $data = array();
        $titles = array();
        $keywords = array();

        foreach ($patents as $patent) {
            $titles[] = $patent->getTitle();
        }

        $data = $this->fromTitlesToKeywords($titles);

        if ($n === null) {
            $n = 100;
        }
        $motcles = array_slice($data, 0, $n);

        foreach ($motcles as $key => $value) {
            if (intval($value) > 10) {
                $keywords[] = array('keyword' => $key, 'count' => $value);
            } else {
                break;
            }
        }

        return $keywords;
    }

}
