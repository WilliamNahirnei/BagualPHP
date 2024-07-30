<?php

namespace Server\Routing;

class ApiManager {

    private const MODULES_PATH = '/../../Src/Modules';
    private const API_FILE = 'Api.php';
    private const API_METHOD = 'defineEndpointList';
    private const BASE_NAMESPACE = 'Src\\Modules';

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

    private function getNamespaceFromPath(string $path): string {
        $relativePath = str_replace(realpath(__DIR__ . self::MODULES_PATH), '', realpath($path));
        $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
        return self::BASE_NAMESPACE . $namespace;
    }
}
?>