<?php

namespace ChrGriffin\LaravelDefer;

class LaravelDefer
{
    /**
     * The variable name of the images to load in JavaScript.
     *
     * @var string
     */
    protected static $objectName = 'deferredImages';

    /**
     * Return thr objectName property.
     * 
     * @return string
     */
    public static function getObjectName()
    {
        return self::$objectName;
    }

    /**
     * Set the objectName property.
     * 
     * @param string $objectName
     */
    public static function setObjectName($objectName)
    {
        self::$objectName = $objectName;
    }

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
     * Images to be loaded in the script.
     *
     * @var array
     */
    protected static $images = [];

    /**
     * Add an image to the array of deferred images.
     *
     * @param $path
     * @param $class
     * @return void
     */
    public static function addImage($path, $class)
    {
        self::$images[] = [
            'path'  => $path,
            'class' => $class
        ];
    }

    /**
     * Write a JavaScript object with the images.
     *
     * @param string $objectName
     * @return string
     */
    public static function writeJsObj()
    {
        return 'var ' . self::$objectName . ' = ' . json_encode(self::$images) . ';';
    }

    /**
     * Write the JavaScript function to load deferred images.
     *
     * @return string
     */
    public static function writeJsFunction()
    {
        return "function " . self::$functionName . "() {
            " . self::writeJsObj() . "
            for(i=0; i<" . self::$objectName . ".length; i++) {
                elements = document.getElementsByClassName(" . self::$objectName . "[i].class);
                for(e=0; e<elements.length; e++) {
                    if(elements[e].tagName === 'IMG') {
                        elements[e].src = " . self::$objectName . "[i].path;
                    }
                }
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