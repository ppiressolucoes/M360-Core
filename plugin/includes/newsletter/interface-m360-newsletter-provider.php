<?php
if (!defined('ABSPATH')) { exit; }

/** Provider boundary: business rules must never depend on MailPoet directly. */
interface M360_Newsletter_Provider_Interface
{
    public function subscribe(string $email, string $name, array $context = []);
    public function confirm(string $email);
    public function unsubscribe(string $email);
    public function status(string $email);
    public function healthCheck(): array;
}
