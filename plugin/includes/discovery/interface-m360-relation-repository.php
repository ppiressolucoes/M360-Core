<?php
if (!defined('ABSPATH')) { exit; }

interface M360_Relation_Repository_Interface
{
    /**
     * @return array<int,array<string,mixed>>
     */
    public function active(int $source_post_id, string $locale, string $kind, int $limit = 3): array;
}
