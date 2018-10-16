<?php

namespace ChrGriffin\LaravelDefer\Compilers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

class ImageDeferCompiler extends BladeCompiler
{
    /**
     * Blade compilers.
     *
     * @var array
     */
    public $compilers = [];

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
        if ($this->ignoredPaths) {
            $path = str_replace('\\', '/', $this->getPath());
            foreach ($this->ignoredPaths as $ignoredPath) {
                if (strpos($path, $ignoredPath) !== false) {
                    return $value;
                }
            }
        }

        return $this->render($value);
    }

    /**
     * Render a Blade template.
     *
     * @param string $value
     * @return string
     */
    public function render($value)
    {
        return $value;
    }
}