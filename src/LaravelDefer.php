<?php

namespace ChrGriffin\LaravelDefer;

class LaravelDefer
{
    /**
     * The name of the JavaScript function to load the deferred images.
     *
     * @var string
     */
    protected static $functionName = 'loadDeferredImages';

    /**
     * Whether or not to echo script tags along with the JavaScript.
     *
     * @var bool
     */
    protected static $withScriptTags = true;

    /**
     * Return the functionName property.
     *
     * @return string
     */
    public static function getFunctionName()
    {
        return self::$functionName;
    }

    /**
     * Set the functionName property.
     *
     * @param string $functionName
     */
    public static function setFunctionName($functionName)
    {
        self::$functionName = $functionName;
    }

    /**
     * Return the withScriptTags property.
     *
     * @return bool
     */
    public static function isWithScriptTags()
    {
        return self::$withScriptTags;
    }

    /**
     * Set the withScriptTags property.
     *
     * @param bool $withScriptTags
     */
    public static function setWithScriptTags($withScriptTags)
    {
        self::$withScriptTags = $withScriptTags;
    }

    /**
     * Write the JavaScript function to load deferred images.
     *
     * @return string
     */
    public static function writeJsFunction()
    {
        return "function " . self::$functionName . "() {
            elements = document.querySelectorAll('img[data-ldsrc]');
            for(e=0; e<elements.length; e++) {
                elements[e].src = elements[e].getAttribute('data-ldsrc');
            }
        }";
    }

    /**
     * Create the JavaScript to load deferred images.
     *
     * @param bool $scriptTags
     * @return string
     */
    public static function js()
    {
        $string = '';
        if(self::$withScriptTags) {
            $string .= '<script>';
        }

        $string .= self::writeJsFunction();

        if(self::$withScriptTags) {
            $string .= '</script>';
        }

        return $string;
    }
}