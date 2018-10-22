<?php

namespace ChrGriffin\LaravelDefer\Compilers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use ChrGriffin\LaravelDefer\LaravelDefer;

class ImageDeferCompiler extends BladeCompiler
{
    /**
     * Paths to ignore when compiling templates.
     *
     * @var array
     */
    public $ignoredPaths = [];

    /**
     * ImageDeferCompiler constructor.
     *
     * @param Filesystem $files
     * @param string $cachePath
     * @param array $ignoredPaths
     * @return void
     */
    public function __construct(Filesystem $files, $cachePath, $ignoredPaths = [])
    {
        parent::__construct($files, $cachePath);

        $this->ignoredPaths = $ignoredPaths;
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