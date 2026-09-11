<?php
namespace Fixtures\Config;

use Config\ConfigLoader;

// existe só para provar que o singleton de ConfigLoader é por classe concreta (self::$instances[$class]),
// não compartilhado entre subclasses que leem o mesmo arquivo .env
class FakeConfigLoaderSecondary extends ConfigLoader {
    protected const FILE_NAME = '.fake-config-loader-test.env';

    protected const CONFIG_KEYS = [];
}
