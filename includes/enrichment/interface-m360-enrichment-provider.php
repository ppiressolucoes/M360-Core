<?php
if (!defined('ABSPATH')) { exit; }

/** Adapter must enforce read-only transport; no concrete DW connection is shipped. */
interface M360_Enrichment_Provider
{
    /** Stable non-secret ID, changed when mapping, source or contract semantics change. */
    public function cache_namespace(): string;
    /**
     * Explicit refresh only. Return context and zero/one standings section.
     * Contract v1 foundation: standings metrics + dw_updated_at (ISO 8601 offset).
     * Never include database errors, SQL, credentials or raw connection details.
     */
    public function fetch(array $context): array;
}
