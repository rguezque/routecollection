<?php declare(strict_types = 1);

namespace rguezque\RouteCollection\Interfaces;

interface GlobalsCollectionInterface {
    public static function get(string $name, mixed $default = null): mixed;
    public static function set(string $name, mixed $value): void;
    public static function all(): array;
    public static function has(string $name): bool;
    public static function remove(string $name): void;
    public static function clear(): void;
}

?>