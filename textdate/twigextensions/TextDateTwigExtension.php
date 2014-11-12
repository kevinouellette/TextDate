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

    // replace individual formatting options. yuck.
    function getReturnDate($fallback, $yearNum, $monthNum, $dayNum, $dayUsable, $monthUsable, $yearUsable)
    {
        $returnDate = $fallback;

        // we have to go through each option separately to prevent erroneous replacements (the 'y' in May, etc.)
        // because of this, the unlikely scenario of multiple formattings of the same date segment won't work

        if ($dayUsable === true) {
            if (strpos($fallback, 'd') !== false) {
                $returnDate = str_replace('d', $dayNum, $returnDate);
            } elseif (strpos($fallback, 'j') !== false) {
                $returnDate = str_replace('j', ltrim($dayNum, '0'), $returnDate);
            }
        }

        if ($monthUsable === true) {
            if (strpos($returnDate, 'F') !== false) {
                $returnDate = str_replace('F', date('F', mktime(0, 0, 0, $monthNum, 10)), $returnDate);
            } elseif (strpos($returnDate, 'm') !== false) {
                $returnDate = str_replace('m', date('m', mktime(0, 0, 0, $monthNum, 10)), $returnDate);
            } elseif (strpos($returnDate, 'M') !== false) {
                $returnDate = str_replace('M', date('M', mktime(0, 0, 0, $monthNum, 10)), $returnDate);
            } elseif (strpos($returnDate, 'n') !== false) {
                $returnDate = str_replace('n', date('n', mktime(0, 0, 0, $monthNum, 10)), $returnDate);
            } elseif (strpos($returnDate, 't') !== false) {
                $returnDate = str_replace('t', date('t', mktime(0, 0, 0, $monthNum, 10)), $returnDate);
            } elseif (strpos($returnDate, 'N') !== false) {
                $returnDate = str_replace('N', date('N', mktime(0, 0, 0, $monthNum, 10)), $returnDate);
            }
        }

        if ($yearUsable === true) {
            if (strpos($fallback, 'Y') !== false) { 
                $returnDate = str_replace('Y', $yearNum, $returnDate);
            } elseif (strpos($fallback, 'y') !== false) { 
                $returnDate = str_replace('y', substr($yearNum, 2, 2), $returnDate);
            }
        }

        return $returnDate;
    }

    public function textdate($isoDate, $format, $fallback = "", $fallback2 = "", $fallback3 = "")
    {
        $returnDate = $yearNum = $monthNum = $dayNum = "";

        if (!empty($isoDate)) {
            $isoDate = str_replace('-', '', $isoDate); // ISO 8601 date values sometimes have dashes

            // Split date value into its individual parts
            $yearNum = substr($isoDate, 0, 4);
            $monthNum = substr($isoDate, 4, 2);
            $dayNum = substr($isoDate, 6, 2);

            if ($yearNum !== '9999' && $monthNum  !== '99' && $dayNum !== '99') {
                $d = strtotime($isoDate);
                $returnDate = date($format, $d);
            } else {
                if (empty($fallback)) return ''; // if there's no fallback set, we can't return a value

                // fill an array with all unusable date-specific format values
                $missing = array('W', 'l', 'z', 'w', 'D', 'N', 'S');

                if ($dayNum === '99') {
                    array_push($missing, 'd', 'j');
                    $dayUsable = false;
                } else {
                    $dayUsable = true;
                }
                if ($monthNum === '99') {
                    array_push($missing, 'F', 'm', 'M', 'n', 't', 'N');
                    $monthUsable = false;
                } else {
                    $monthUsable = true;
                }
                if ($yearNum === '9999') { 
                    array_push($missing, 'Y', 'y');
                    $yearUsable = false;
                } else {
                    $yearUsable = true;
                }

                // check date format fallbacks. if nothing works, return empty string.
                if ($this->strposa($fallback, $missing) === false) {
                    // first fallback is safe. we can send back a date.
                    $returnDate = $this->getReturnDate($fallback, $yearNum, $monthNum, $dayNum, $dayUsable, $monthUsable, $yearUsable);
                } elseif (!empty($fallback2) && $this->strposa($fallback2, $missing) === false) {
                    // second fallback is safe.
                    $returnDate = $this->getReturnDate($fallback2, $yearNum, $monthNum, $dayNum, $dayUsable, $monthUsable, $yearUsable);
                } elseif (!empty($fallback3) && $this->strposa($fallback3, $missing) === false) {
                    // third fallback is safe.
                    $returnDate = $this->getReturnDate($fallback3, $yearNum, $monthNum, $dayNum, $dayUsable, $monthUsable, $yearUsable);
                }
            }
        }

        return $returnDate;
    }
}