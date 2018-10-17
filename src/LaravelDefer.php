<?php

namespace ChrGriffin\LaravelDefer;

class LaravelDefer
{
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
    public static function writeJsObj($objectName = 'deferredImages')
    {
        return 'var ' . $objectName . ' = ' . json_encode(self::$images) . ';';
    }

    /**
     * Write the JavaScript function to load deferred images.
     *
     * @return string
     */
    public static function writeJsFunction($objectName = 'deferredImages', $functionName = 'loadDeferredImages')
    {
        return "function $functionName() {
            " . self::writeJsObj($objectName) . "
            for(i=0; i<$objectName.length; i++) {
                elements = document.getElementsByClassName({$objectName}[i].class);
                for(e=0; e<elements.length; e++) {
                    if(elements[e].tagName === 'IMG') {
                        elements[e].src = {$objectName}[i].path;
                    }
                }
            }
        }";
    }

    /**
     * Echo the JavaScript to load deferred images.
     *
     * @param bool $scriptTags
     * @return void
     */
    public static function js($scriptTags = true, $objectName = 'deferredImages', $functionName = 'loadDeferredImages')
    {
        $string = '';
        if($scriptTags) {
            $string .= '<script>';
        }

        $string .= self::writeJsFunction($objectName, $functionName);

        if($scriptTags) {
            $string .= '</script>';
        }

        echo $string;
    }
}