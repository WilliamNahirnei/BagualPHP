<?php

namespace Server\Router;

use Config\ConfigLoader;

/**
 * Class RouterConfig
 * 
 * This class reads an Router configuration file and loads the configurations from it.
 * It extends the ConfigLoader class and implements the Singleton pattern.
 *
 * @package Server\Router
 * @author William Nahirnei Lopes
 */
class RouterConfig extends ConfigLoader {
    /**
     * @var string The path to the router configuration file.
     */
    protected const FILE_NAME = '.router.env';

    /**
     * The default namespace for class to auth.
     *
     * @var string
     */
    public const PREFIX_API = "PREFIX_API";

    /**
     * @var array The list of authentication configuration keys to load.
     */
    protected const CONFIG_KEYS = [
        self::PREFIX_API,
    ];
}
?>