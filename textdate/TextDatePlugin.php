<?php
namespace Craft;

class TextDatePlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('Text Date');
    }

    function getVersion()
    {
        return '1.0';
    }

    function getDeveloper()
    {
        return 'Kevin Ouellette';
    }

    function getDeveloperUrl()
    {
        return 'http://kevinouellette.com';
    }

    public function addTwigExtension()
    {
        Craft::import('plugins.textdate.twigextensions.TextDateTwigExtension');
        return new TextDateTwigExtension();
    }
}