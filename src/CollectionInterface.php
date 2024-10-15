<?php declare(strict_types = 1);

namespace rguezque\RouteCollection;

interface CollectionInterface {
    public function get(string $name): mixed;
    public function set(string $name, mixed $value): void;
    public function all(): array;
    public function has(string $name): bool;
    public function clear(): void;
}

?>