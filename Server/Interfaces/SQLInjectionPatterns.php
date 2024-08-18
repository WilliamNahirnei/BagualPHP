<?php
namespace Server\Interfaces;

/**
 * Interface SQLInjectionPatterns
 *
 * This interface defines a set of regular expression patterns used to identify 
 * and remove potential SQL injection commands.
 *
 * @package Server\Interfaces
 */
interface SQLInjectionPatterns {
    
    /**
     * Patterns for SQL Data Manipulation Language (DML) commands.
     * Includes SELECT, INSERT, UPDATE, DELETE, and others.
     */
    public const PATTERN_SELECT = '/select\b/i';
    public const PATTERN_INSERT = '/insert\b/i';
    public const PATTERN_UPDATE = '/update\b/i';
    public const PATTERN_DELETE = '/delete\b/i';

    public const DML_PATTERNS = [
        self::PATTERN_SELECT,
        self::PATTERN_INSERT,
        self::PATTERN_UPDATE,
        self::PATTERN_DELETE,
    ];

    /**
     * Patterns for SQL Data Definition Language (DDL) commands.
     * Includes CREATE, ALTER, DROP, TRUNCATE, and others.
     */
    public const PATTERN_CREATE = '/create\b/i';
    public const PATTERN_ALTER = '/alter\b/i';
    public const PATTERN_DROP = '/drop\b/i';
    public const PATTERN_TRUNCATE = '/truncate\b/i';

    public const DDL_PATTERNS = [
        self::PATTERN_CREATE,
        self::PATTERN_ALTER,
        self::PATTERN_DROP,
        self::PATTERN_TRUNCATE,
    ];

    /**
     * Patterns for SQL Data Control Language (DCL) commands.
     * Includes GRANT, REVOKE, and PRIVILEGES.
     */
    public const PATTERN_GRANT = '/grant\b/i';
    public const PATTERN_REVOKE = '/revoke\b/i';
    public const PATTERN_PRIVILEGES = '/privileges\b/i';

    public const DCL_PATTERNS = [
        self::PATTERN_GRANT,
        self::PATTERN_REVOKE,
        self::PATTERN_PRIVILEGES,
    ];

    /**
     * Patterns for SQL transaction-related commands.
     * Includes EXEC, UNION, and others.
     */
    public const PATTERN_EXEC = '/exec\b/i';
    public const PATTERN_UNION = '/union\b/i';

    public const TRANSACTION_PATTERNS = [
        self::PATTERN_EXEC,
        self::PATTERN_UNION,
    ];

    /**
     * Patterns for potential SQL injection symbols and logical operators.
     * Includes --, #, ;, *, OR, and AND.
     */
    public const PATTERN_COMMENT_DASH = '/--/';
    public const PATTERN_COMMENT_HASH = '/#/';
    public const PATTERN_SEMICOLON = '/;/';
    public const PATTERN_ASTERISK = '/\*/';
    public const PATTERN_OR = '/or\b/i';
    public const PATTERN_AND = '/and\b/i';

    public const INJECTION_SYMBOLS_PATTERNS = [
        self::PATTERN_COMMENT_DASH,
        self::PATTERN_COMMENT_HASH,
        self::PATTERN_SEMICOLON,
        self::PATTERN_ASTERISK,
        self::PATTERN_OR,
        self::PATTERN_AND,
    ];

    /**
     * Patterns for database user and role management commands.
     * Includes USER, ROLE, and ADMIN.
     */
    public const PATTERN_USER = '/user\b/i';
    public const PATTERN_ROLE = '/role\b/i';
    public const PATTERN_ADMIN = '/admin\b/i';

    public const USER_MANAGEMENT_PATTERNS = [
        self::PATTERN_USER,
        self::PATTERN_ROLE,
        self::PATTERN_ADMIN,
    ];

    public const SQL_PATTERNS = [
        ...self::DML_PATTERNS,
        ...self::DDL_PATTERNS,
        ...self::DCL_PATTERNS,
        ...self::TRANSACTION_PATTERNS,
        ...self::INJECTION_SYMBOLS_PATTERNS,
        ...self::USER_MANAGEMENT_PATTERNS
    ];
}


?>