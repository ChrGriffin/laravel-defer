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
        $this->compilers[] = 'Defer';
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

            $class = 'ld' . md5(uniqid(mt_rand(), true));
            $src = $matches[1][$i];

            preg_match("/class\s*=\s*['\"]([^'\"]*?)['\"]/", $imgTag, $classMatches);
            if(!empty($classMatches[1])) {
                // add the new class to the existing classes
                $newTag = preg_replace("/(<img\s[^>]*?class\s*=\s*['\"][^'\"]*?)(['\"][^>]*?>)/", '$1 ' . $class . '$2', $imgTag);
            }
            else {
                // add a class attribute with the class
                $newTag = preg_replace("/(<img\s[^>]*?)([^>]*?>)/", '$1 class="' . $class . '" $2', $imgTag);
            }

            // remove the src attribute from the img tag
            $newTag = preg_replace("/src\s*=\s*['\"]([^'\"]*?)['\"]/", '', $newTag);

            // add the edited img tag back into the template
            $value = str_replace($imgTag, $newTag, $value);

            LaravelDefer::addImage($src, $class);
        }

        return $value;
    }
}