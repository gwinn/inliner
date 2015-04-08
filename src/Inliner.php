<?php

class Inliner
{
    /**
     * fileExt
     *
     * @var mixed
     */
    protected $fileExt;

    /**
     * pathTop
     *
     * @var mixed
     */
    protected $pathTop;

    /**
     * Inline all classes from path to single file
     *
     */
    public function inline($path, $ext, $filename)
    {
        $this->setPath($path);
        $this->setFileExt($ext);

        $classes = $this->readClasses();
        $inlined = new SplFileObject(__DIR__ . '/' . $filename, 'w');
        $inlined->fwrite('<?php');

        foreach ($classes as $class) {
            $inlined->fwrite($this->cleanClass($class));
        }

    }

    /**
     * Read all classes from path
     *
     * @return array $classes
     */
    protected function readClasses()
    {
        $classes = array();
        $directory = new RecursiveDirectoryIterator($this->pathTop, RecursiveDirectoryIterator::SKIP_DOTS);
        $fileIterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($fileIterator as $file) {
            $fileClass = new SplFileObject($file->getPathname());
            $fileExtension = new SplFileInfo($file->getPathname());

            if($fileExtension->getExtension() == $this->fileExt)
            {
                $classes[] = $fileClass->fread($fileClass->getSize());
            }
        }

        return $classes;
    }

    /**
     * Cleanup:
     * remove <?php
     * remove namespaces
     * remove uses
     * remove backslashes from ClassNames
     *
     * @params string $class
     *
     * @return string $class
     */
    protected function cleanClass($class)
    {
        $class = preg_replace('/\<\?php/', '', $class);
        $class = preg_replace('/\?\>/', '', $class);
        $class = preg_replace('/namespace\s[\w+\\\]+;/', '', $class);
        $class = preg_replace('/use\s[\w+\\\]+;/', '', $class);
        $class = preg_replace('/(\s)(\\\)(\w)/i', '${1}${3}', $class);
        $class = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n\n", $class);
        return $class;
    }

    private function setFileExt($fileExt)
    {
        $this->fileExt = $fileExt;
    }

    private function setPath($path)
    {
        $this->pathTop = $path;
    }
}

