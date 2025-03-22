<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\Database;

/**
 * @since 1.2.0
 */
class Upgrade
{
    private \wpdb $wpdb;

    public function __construct()
    {
        global $wpdb;
        
        $this->wpdb = $wpdb;
    }

    /**
     * Methode called when an upgrade is needed
     *
     * @return void
     */
    public function dbUpgrade(): void {}
}
