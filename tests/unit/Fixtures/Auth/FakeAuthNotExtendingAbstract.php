<?php
namespace Fixtures\Auth;

class FakeAuthNotExtendingAbstract {
    public static function authenticate(): bool {
        return true;
    }
}
