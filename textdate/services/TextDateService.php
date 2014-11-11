<?php
namespace Craft;

class TextDateService extends BaseApplicationComponent
{
    public function validate($value)
    {
        $datePattern = '/^([1-2][0-9]|99)[0-9][0-9](0[1-9]|1[0-2]|99)(0[1-9]|[1-2][0-9]|3[0-1]|99)$/';

        if (preg_match($datePattern, $value)) 
        {
            return true;
        }
        else
        {
            return false;
        }
    }       
}