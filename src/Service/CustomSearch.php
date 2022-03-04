<?php

namespace App\Service;

use App\Entity\CarModel;

class CustomSearch
{
    public function searchClassByLabel($classes, $input)
    {
        $response = [];
        $scores = []; 
        $index = $this->index($input);

        foreach($classes as $class) {
            $scores[$class->getId()] = 1;
            $labelIndex = explode(' ', $class->getLabel());
            
            foreach ($labelIndex as $position => $label) {
                $processedLabel = $this->processText($label);
                $labelIndex[$position] = $processedLabel;

                $scores[$class->getId()] += $this->scoreRelevance($position, count($labelIndex), $processedLabel, $index);
                
                if ($scores[$class->getId()] < 30) {
                    $altIndex = $index;

                    foreach ($index as $key => $term) {
                        
                        if (preg_match('~(?<=\d)([a-z]+)~', $term, $numbers)) {
                            array_push($altIndex, $numbers[0]);
                            $altIndex[$key] = preg_replace('~(?<=\d)([a-z]+)~', '', $term);
                        
                        } elseif (preg_match('~(?<=[a-z])((\d+)[a-z]?)~', $term, $numbers)) {
                            array_push($altIndex, $numbers[0]);
                            $altIndex[$key] = preg_replace('~(?<=[a-z])((\d+)[a-z]?)~', '', $term);
                        }
                        
                        if(count_chars($term) > 2 && count_chars($processedLabel) > 2) {
                            $lev = levenshtein($term, $processedLabel);
                            if ($lev <= 1) $scores[$class->getId()] += 6;
                        }
                    }

                    if (in_array($processedLabel, $altIndex)){
                        $scores[$class->getId()] += 17.5;
                    }
                    
                }
            }
        }

        if (max($scores) > 24) {
            $response = array_keys($scores, max($scores));
        }

        return $response;
    }

    /**
     * "normalization" of user inputs
     * by replacement accented characters & deletion of common separators
     */
    public function processText($text)
    {
        $text = strtolower($text);
        //replace accented characters
        /* $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text); TRANSLIT not supported in alpine....replace by following */
        $text = htmlentities($text, ENT_NOQUOTES, 'utf-8');
        $text = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $text);
        $text = html_entity_decode($text);
        //delete usual separators and special characters
        $text = preg_replace('~[\s",`\'\^.-]~', '', $text);

        return $text;
    }

    public function index($input)
    {
        preg_match('~([a-z]?[a-z]?)(\s+)(\d+)~', $input, $match);

        if (count($match)) {
            $input = str_ireplace($match[0], str_ireplace(' ', '', $match[0]), $input);
        }

        preg_match('~([a-z]?)(\d+)(\s+)([a-z]?)([a-z]?)$~', $input, $match);

        if (count($match)) {
            $input = str_ireplace($match[0], str_ireplace(' ', '', $match[0]), $input);
        }

        $index = explode(' ', $input);

        foreach ($index as $position => $term) {
            $index[$position] = $this->processText($term);
        }

        return $index;
    }

    public function scoreRelevance($position, $count, $processedLabel, $index)
    {
        if (in_array($processedLabel, $index)) {
            $score = 30;
            
            if ($count == 1) {
                $score += 5; 
            }
            if (!in_array($processedLabel, CarModel::$commonName)) { 
                $score += 10; 
            } 
            if(count($index) > 1 && $position == 0) {
                $score += 2.5;
            }
        }

        return $score ?? 0;
    }
}