<?php

namespace Config;

/**
 * Class ConfigLoader
 * 
 * This abstract class reads a configuration file and loads the configurations from it.
 * It allows derived classes to specify the file path and the list of configuration keys by overriding constants.
 * It implements the Singleton pattern to ensure only one instance is created.
 *
 * @package Config.
 * @author William Nahirnei Lopes
 */
abstract class ConfigLoader {

    /**
     * The name of the directory where configuration files are stored.
     *
     * @var string
     */
    private const CONFIGS_FILES_DIR_NAME = 'envsConfigs';

    /**
     * @var string The path to the configuration file.
     */
    protected const FILE_NAME = '';

    /**
     * @var array The list of configuration keys to load.
     */
    protected const CONFIG_KEYS = [];

    /**
     * @var static The single instance of the class.
     */
    private static $instances = [];

    /**
     * @var array The configurations loaded from the configuration file.
     */
    protected $config = [];

    /**
     * Protected constructor to prevent direct object creation.
     */
    protected function __construct() {
        $this->loadConfig(static::CONFIG_KEYS);
    }

    /**
     * Gets the single instance of the class.
     * 
     * @return static The single instance of the class.
     */
    public static function getInstance(): self {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }

    /**
     * Loads the configurations from the configuration file for the specified keys.
     * 
     * @param array $keys The list of keys to load from the configuration file.
     * @throws \Exception If the file cannot be read.
     */
    protected function loadConfig(array $keys): void {
        $filePath = $this->loadConfigsDirectoryPath() . '/' . static::FILE_NAME;
        
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception("The configuration file $filePath does not exist or is not readable.");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $keysToLoad = array_flip($keys);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Ignore comments
            }
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            if (isset($keysToLoad[$key])) {
                $this->config[$key] = trim($value);
            }
        }
    }

    /**
     * Loads the directory path where configuration files are stored.
     *
     * This method constructs the full path to the configuration files directory
     * by resolving the relative path from the current directory to the directory
     * specified by the constant `CONFIGS_FILES_DIR_NAME`.
     *
     * @return string The full path to the configuration files directory.
     */
    private function loadConfigsDirectoryPath(): string {
        $configsDirectory = realpath(__DIR__ . '/../') . '/' . self::CONFIGS_FILES_DIR_NAME;
        return $configsDirectory;
    }

    /**
     * Gets a configuration value by key.
     * 
     * @param string $key The configuration key.
     * @return string|null The configuration value, or null if the key does not exist.
     */
    public function getConfig(string $key): ?string {
        return $this->config[$key] ?? null;
    }
}
?>