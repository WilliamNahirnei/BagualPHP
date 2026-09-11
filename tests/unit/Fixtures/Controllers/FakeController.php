<?php
namespace Fixtures\Controllers;

class FakeController {
    public static function staticNoParams(): string {
        return 'executed';
    }

    public static function staticWithRequiredParam(string $id): string {
        return $id;
    }

    public static function staticThrowsException(): string {
        throw new \Exception('erro genérico do controller');
    }

    private string $marker = 'instance-state';

    public function nonStaticUsingThis(): string {
        return $this->marker;
    }

    public function nonStaticNotUsingThis(): string {
        return 'executed-without-this';
    }
}
