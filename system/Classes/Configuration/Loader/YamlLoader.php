<?php
namespace Asylamba\Classes\Configuration\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlLoader extends FileLoader
{
	/**
	 * @param string $resource
	 * @param string $type
	 * @return array
	 */
    public function load($resource, $type = null)
    {
        return Yaml::parse(file_get_contents($resource));
    }
	
	/**
	 * @param string $resource
	 * @param string $type
	 * @return boolean
	 */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}