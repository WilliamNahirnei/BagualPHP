<?php
namespace Fixtures\Config;

use Config\ConfigLoader;

class FakeConfigLoaderMissingFile extends ConfigLoader {
    protected const FILE_NAME = '.does-not-exist.env';

    protected const CONFIG_KEYS = [];
}
