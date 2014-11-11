<?php
namespace Craft;

class TextDate_DateFieldType extends BaseFieldType
{
    public function getName()
    {
        return Craft::t('Text Date');
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('textdate/textdatefield/_settings', array(
            'options' => array(
                array(
                    'label' => 'YYYYMMDD',
                    'value' => 'YYYYMMDD'
                ),
                array(
                    'label' => 'MMDDYYYY',
                    'value' => 'MMDDYYYY'
                ),
                array(
                    'label' => 'DDMMYYYY',
                    'value' => 'DDMMYYYY'
                )
            ),
            'dateOrder'         => $this->getSettings()->dateOrder,
            'separatorValue'    => $this->getSettings()->separator
        ));
    }

    public function defineContentAttribute()
    {
        return array(AttributeType::String, 'column' => ColumnType::Varchar, 'maxLength' => 8);
    }

    public function getInputHtml($name, $value)
    {
        // Reformat input name as an ID
        $id = craft()->templates->formatInputId($name);

        // Get final namespaced ID so we can select it with JQuery
        $namespacedId = craft()->templates->namespaceInputId($id);

        $separator = $this->getSettings()->separator;

        // Get date pieces from existing value if it exists
        if (!empty($value)) {
            $isoDate = $this->prepValueFromPost($value);
            $year = substr($isoDate, 0, 4);
            $month = substr($isoDate, 4, 2);
            $day = substr($isoDate, 6, 2);
        }

        // Put together masks for input
        $jsMask = '99'.$separator.'99'.$separator.'9999'; // Default mask

        if ($this->getSettings()->dateOrder === "YYYYMMDD") {
            $placeholderText = 'YYYY'.$separator.'MM'.$separator.'DD';
            $jsMask = '9999'.$separator.'99'.$separator.'99'; // Mask is different for ISO 8601
            $value = (!empty($value) ? $year.$separator.$month.$separator.$day : '');
        } elseif ($this->getSettings()->dateOrder === "DDMMYYYY") {
            $placeholderText = 'DD'.$separator.'MM'.$separator.'YYYY';
            $value = (!empty($value) ? $day.$separator.$month.$separator.$year : '');
        } else { // MMDDYYYY
            $placeholderText = 'MM'.$separator.'DD'.$separator.'YYYY';
            $value = (!empty($value) ? $month.$separator.$day.$separator.$year : '');
        }

        // Add a jQuery input mask only if a mask separator is set
        if (!empty($separator)) {
            craft()->templates->includeJsResource('textdate/js/jquery.inputmask.js');
            craft()->templates->includeJs("$('#{$namespacedId}').inputmask('$jsMask', { placeholder: ' '});");
        }

        return craft()->templates->render('textdate/input', array(
            'name'              => $name,
            'id'                => $id,
            'value'             => $value,
            'placeholderText'   => $placeholderText
        ));
    }

    /**
     * Since we're using VARCHAR(8) instead of an actual
     * DateTime field, we need to do a little extra validation.
     */
    public function validate($value)
    {
        if (!craft()->textDate->validate($value))
        {
            return Craft::t('Invalid date');
        }
        else
        {
            return true;
        }
    }

    /**
     * Covert user-inputted value to YYYYMMDD format
     */
    public function prepValueFromPost($value)
    {
        $dateOrder = $this->getSettings()->dateOrder;
        $find = array($this->getSettings()->separator, ' ');
        $replace = array('', '9');
        $strippedVal = str_replace($find, $replace, $value); // remove separator and replace spaces with 0s

        if ($dateOrder === 'YYYYMMDD') {
            $year = substr($strippedVal, 0, 4);
            $month = substr($strippedVal, 4, 2);
            $day = substr($strippedVal, 6, 2);
        } elseif ($dateOrder === 'MMDDYYYY') {
            $month = substr($strippedVal, 0, 2);
            $day = substr($strippedVal, 2, 2);
            $year = substr($strippedVal, 4, 4);
        } else { // DDMMYYYY
            $day = substr($strippedVal, 0, 2);
            $month = substr($strippedVal, 2, 2);
            $year = substr($strippedVal, 4, 4);
        }

        return $year.$month.$day;
    }

    protected function defineSettings()
    {
        return array(
            'dateOrder' => array(AttributeType::String, 'default' => 'YYYYMMDD'),
            'separator' => array(AttributeType::String, 'default' => '-')
        );
    }
}