<?php
if (!defined('ABSPATH')) { exit; }

interface M360_Catalog_Provider_Interface
{
    public function id(): string;

    /**
     * @return array<int,array<string,mixed>>
     */
    public function candidates(int $source_post_id, string $locale, array $target_types = []): array;
}
