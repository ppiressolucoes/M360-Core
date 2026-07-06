<?php
/**
 * View registry for M360 Core.
 */

if (!defined('ABSPATH')) {
    exit;
}

final class M360_View_Registry
{
    /** @var array<string, array<string, mixed>> */
    private array $views = [];

    /**
     * @param array<string, mixed> $args
     */
    public function register(string $name, array $args = []): void
    {
        $name = sanitize_key($name);

        if ($name === '') {
            return;
        }

        $defaults = [
            'title' => $name,
            'description' => '',
            'template' => $name,
            'public' => true,
            'schema' => null,
        ];

        $this->views[$name] = array_merge($defaults, $args);
    }

    public function has(string $name): bool
    {
        return isset($this->views[sanitize_key($name)]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function get(string $name): ?array
    {
        $name = sanitize_key($name);

        return $this->views[$name] ?? null;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->views;
    }
}
