<?php declare(strict_types = 1);

namespace rguezque\RouteCollection\Interfaces;

interface CollectionInterface {
    public function get(string $name, mixed $default = null): mixed;
    public function set(string $name, mixed $value): void;
    public function all(): array;
    public function has(string $name): bool;
    public function remove(string $name): void;
    public function clear(): void;
}

?>