<?php

namespace ChrGriffin\LaravelDefer\Compilers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

class ImageDeferCompiler extends BladeCompiler
{
    /**
     * Paths to ignore when compiling templates.
     *
     * @var array
     */
    public $ignoredPaths = [];

    /**
     * Images to ignore when compiling templates.
     *
     * @var array
     */
    public $ignoredImages = [];

    /**
     * ImageDeferCompiler constructor.
     *
     * @param Filesystem $files
     * @param string $cachePath
     * @param array $ignoredPaths
     * @param array $ignoredImages
     * @return void
     */
    public function __construct(
        Filesystem $files,
        $cachePath,
        $ignoredPaths = [],
        $ignoredImages = []
    ) {
        parent::__construct($files, $cachePath);

        $this->ignoredPaths = $ignoredPaths;
        $this->ignoredImages = $ignoredImages;

        // by putting the image defer compiler at the start of the array, we allow other
        // functionality like custom blade directives to remain unaffected
        array_unshift($this->compilers, 'Defer');
    }

    /**
     * Compile a Blade template.
     *
     * @param string $value
     * @return string
     */
    public function compileDefer($value)
    {
        if(!empty($this->ignoredPaths)) {
            $thisPath = str_replace('\\', '/', $this->getPath());
            foreach($this->ignoredPaths as $ignoredPath) {
                if(strpos($thisPath, $ignoredPath) !== false) {
                    return $value;
                }
            }
        }

        return $this->renderDefer($value);
    }

    /**
     * Render a Blade template.
     *
     * @param string $value
     * @return string
     */
    public function renderDefer($value)
    {
        preg_match_all("/<img\s[^>]*?src\s*=\s*['\"]([^'\"]*?)['\"][^>]*?>/", $value, $matches);

        foreach($matches[0] as $i => $imgTag) {
            foreach($this->ignoredImages as $ignoredImage) {
                if(strpos($matches[1][$i], $ignoredImage) !== false) {
                    continue 2;
                }
            }

            // copy the src attribute to the data-src attribure
            $newTag = preg_replace(
                "/(<img\s[^>]*?)([^>]*?>)/",
                '$1data-ldsrc="' . $matches[1][$i] . '"$2',
                $imgTag
            );

            // remove the src attribute from the img tag
            $newTag = preg_replace("/\ssrc\s*=\s*['\"]([^'\"]*?)['\"]/", '', $newTag);

            // add the edited img tag back into the template
            $value = str_replace($imgTag, $newTag, $value);
        }
        
        return $value;
    }
}