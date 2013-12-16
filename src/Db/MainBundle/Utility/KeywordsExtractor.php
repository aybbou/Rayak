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
        'a', 'of', 'to', 'is', 'as', 'when',
        'an', 'in', 'for', 'with', 'are',
        'at', 'be', 'that', 'many', 'on', 'from');
    private $symbols = array('.', ',', '\\', '(', ')', '/', ';', ':');

    public function getKeywordsFromPatents($patents) {
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

        foreach ($data as $keyword => $count) {
            if ($count > 10 && !in_array($keyword, $this->notKeywords) && !is_int($keyword) && strlen($keyword) > 1) {
                $d[] = array('keyword' => $keyword, 'count' => $count);
            }
        }

        return $d;
    }

}
