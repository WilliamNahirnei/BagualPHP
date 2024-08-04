<?php

namespace Server\Routing;

/**
 * Class ApiManager
 * 
 * Manages the loading and initialization of API endpoints from module directories.
 *
 * @package Server\Routing
 * @author William Nahirnei Lopes
 */
class ApiManager {

    /**
     * The relative path to the modules directory.
     */
    private const MODULES_PATH = '/../../Src/Modules';

    /**
     * The name of the API file in each module directory.
     */
    private const API_FILE = 'Api.php';

    /**
     * The method name in the API file that defines the endpoints.
     */
    private const API_METHOD = 'defineEndpointList';

    /**
     * The base namespace for modules.
     */
    private const BASE_NAMESPACE = 'Src\\Modules';

    /**
     * Loads API endpoints by scanning module directories and initializing API files.
     *
     * @return void
     */
    public function loadApiEndpoints(): void {
        $modulesDirectory = realpath(__DIR__ . self::MODULES_PATH);
        $moduleDirs = glob($modulesDirectory . '/*', GLOB_ONLYDIR);

        foreach ($moduleDirs as $moduleDir) {
            $apiFiles = glob($moduleDir . '/' . self::API_FILE);
            
            foreach ($apiFiles as $apiFile) {
                $namespace = $this->getNamespaceFromPath($moduleDir);
                $className = $namespace . '\\Api';
                
                if (class_exists($className)) {
                    $apiInstance = new $className();
                    if (method_exists($apiInstance, self::API_METHOD)) {
                        $apiInstance->{self::API_METHOD}();
                    }
                }
            }
        }
    }

    /**
     * Constructs the namespace from the given path.
     *
     * @param string $path The directory path of the module.
     * @return string The namespace corresponding to the module path.
     */
    private function getNamespaceFromPath(string $path): string {
        $relativePath = str_replace(realpath(__DIR__ . self::MODULES_PATH), '', realpath($path));
        $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
        return self::BASE_NAMESPACE . $namespace;
    }
}
?>