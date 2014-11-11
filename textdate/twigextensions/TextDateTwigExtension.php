<?php

namespace Craft;

class TextDateTwigExtension extends \Twig_Extension
{
    protected $env;

    public function getName()
    {
        return 'Text Date';
    }

    public function getFilters()
    {
        return array('textdate' => new \Twig_Filter_Method($this, 'textdate'));
    }
    
    public function getFunctions()
    {
        return array('textdate' => new \Twig_Function_Method($this, 'textdate'));
    }

    public function initRuntime(\Twig_Environment $env)
    {
        $this->env = $env;
    }

    // search a string for an array of possible values
    function strposa($haystack, $needle, $offset = 0)
    {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $query) {
            if (strpos($haystack, $query, $offset) !== false) return true;
        }
        return false;
    }

    public function textdate($isoDate, $format, $fallback = "", $fallback2 = "", $fallback3 = "")
    {
        $returnDate = $yearNum = $monthNum = $dayNum = "";

        if (!empty($isoDate)) {
            // Split date value into its individual parts
            $yearNum = substr($isoDate, 0, 4);
            $monthNum = substr($isoDate, 4, 2);
            $dayNum = substr($isoDate, 6, 2);

            if ($yearNum !== '9999' && $monthNum  !== '99' && $dayNum !== '99') {
                $d = strtotime($isoDate);
                $returnDate = date($format, $d);
            } else {
                // fill an array with all unusable format values
                $missing = array('W');
                if ($yearNum === '9999') {
                    array_push($missing, 'Y', 'y');
                }

                if ($monthNum === '99') {
                    array_push($missing, 'F', 'm', 'M', 'n', 't');
                }

                if ($dayNum === '99') {
                    array_push($missing, 'd', 'D', 'j', 'l', 'N', 'S', 'w', 'z', 'W');
                }

                // check date format fallbacks. if nothing works, return empty string.
                if ($this->strposa($fallback, $missing) === false) {
                    // first fallback is safe. we can send back a date.
                    $d = strtotime(str_replace('99', '00', $isoDate));
                    $returnDate = date($fallback, $d);
                } elseif ($this->strposa($fallback2, $missing) === false) {
                    // second fallback is safe.
                    $d = strtotime(str_replace('99', '00', $isoDate));
                    $returnDate = date($fallback2, $d);
                } elseif ($this->strposa($fallback3, $missing) === false) {
                    // third fallback is safe.
                    $d = strtotime(str_replace('99', '00', $isoDate));
                    $returnDate = date($fallback3, $d);
                }
            }
        }

        return $returnDate;
    }
}