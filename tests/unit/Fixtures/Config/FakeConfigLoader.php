<?php
namespace Fixtures\Config;

use Config\ConfigLoader;

class FakeConfigLoader extends ConfigLoader {
    protected const FILE_NAME = '.fake-config-loader-test.env';

    protected const CONFIG_KEYS = [
        'ALLOWED_KEY',
        'COMMENTED_KEY',
    ];
}
