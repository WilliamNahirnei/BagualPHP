<?php
namespace Fixtures\Auth;

use Server\Auth\AbstractAuthenticable;

class FakeAuth extends AbstractAuthenticable {
    public static bool $shouldAuthenticate = true;

    public static ?string $calledMethod = null;

    public static function authenticate(): bool {
        self::$calledMethod = 'authenticate';
        return self::$shouldAuthenticate;
    }

    public static function alternateAuthenticate(): bool {
        self::$calledMethod = 'alternateAuthenticate';
        return self::$shouldAuthenticate;
    }

    public function nonStaticMethod(): bool {
        return self::$shouldAuthenticate;
    }

    public static function methodWithParam(string $id): bool {
        return self::$shouldAuthenticate;
    }

    protected static function callAuthError(): void {
    }
}
