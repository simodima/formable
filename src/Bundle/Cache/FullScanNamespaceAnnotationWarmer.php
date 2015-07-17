<?php

namespace Formable\Bundle\Cache;

use Doctrine\Common\Annotations\Reader;
use Formable\Generator\Generator;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class FullScanNamespaceAnnotationWarmer implements CacheWarmerInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $paths;

    /**
     * @param Reader $reader
     * @param array  $paths
     */
    public function __construct(Reader $reader, array $paths = [])
    {
        $this->reader = $reader;
        $this->paths = $paths;
    }

    /**
     * @param string $cacheDir
     */
    public function warmUp($cacheDir)
    {
        $classes = $this->getAllClasses();

        foreach ($classes as $reflectionObject) {
            foreach ($reflectionObject->getProperties() as $reflectionProperty) {
                $this->reader->getPropertyAnnotation($reflectionProperty, Generator::ANNOTATION_CLASS);
            }
        }
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @return \ReflectionClass[]
     */
    private function getAllClasses()
    {
        $includedFiles = [];
        $classes = [];

        foreach ($this->paths as $path) {

            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+' . preg_quote('.php') . '$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (!preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                require_once $sourceFile;

                $includedFiles[] = $sourceFile;
            }
        }
        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();
            if (in_array($sourceFile, $includedFiles)) {
                $classes[] = $reflectionClass;
            }
        }

        return $classes;
    }
}