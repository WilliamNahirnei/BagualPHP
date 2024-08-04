<?php

namespace Server\Auth;

use Config\ConfigLoader;

/**
 * Class AuthConfig
 * 
 * This class reads an authentication configuration file and loads the configurations from it.
 * It extends the ConfigLoader class and implements the Singleton pattern.
 *
 * @package Server\Auth
 * @author William Nahirnei Lopes
 */
class AuthConfig extends ConfigLoader {
    /**
     * @var string The path to the authentication configuration file.
     */
    protected const FILE_NAME = '.auth.env';

    /**
     * The default namespace for class to auth.
     *
     * @var string
     */
    public const DEFAULT_CLASS_NAMESPACE = "DEFAULT_CLASS_NAMESPACE";

    /**
     * @var array The list of authentication configuration keys to load.
     */
    protected const CONFIG_KEYS = [
        self::DEFAULT_CLASS_NAMESPACE,
    ];
}
?>