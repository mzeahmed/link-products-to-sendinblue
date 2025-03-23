<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Admin;

use MzeAhmed\WpToolKit\Utils\Ajax;
use LPTS\Infrastructure\Database\Upgrade;
use LPTS\Application\Contract\AdminControllerInterface;

/**
 * @since 1.2.0
 */
class AdminDatabaseController implements AdminControllerInterface
{
    private string $dbVersion;

    public function __construct()
    {
        $this->dbVersion = get_option(LPTS_DB_VERSION_OPTION, '1.0.0');

        if (version_compare($this->dbVersion, LPTS_CURRENT_DB_VERSION, '<')) {
            add_action('admin_notices', [$this, 'displayUpgradeDbNotice']);
        }
    }

    public function register(): void
    {
        Ajax::addAction('lpts_upgrade_db', [$this, 'ajaxDbUpgrade']);
    }

    public function ajaxDbUpgrade(): void
    {
        if (version_compare($this->dbVersion, LPTS_CURRENT_DB_VERSION, '<')) {
            $upgrade = new Upgrade();
            $upgrade->dbUpgrade();

            update_option(LPTS_DB_VERSION_OPTION, LPTS_CURRENT_DB_VERSION);

            Ajax::sendJsonSuccess(__('Database upgraded successfully', 'link-products-to-sendinblue'));
        }

        Ajax::sendJsonSuccess(__('Database is already up to date', 'link-products-to-sendinblue'));
    }

    public function displayUpgradeDbNotice(): void
    {
        echo '<div class="notice notice-warning is-dismissible">
                <p>' . esc_html__(
                'A database upgrade is available for Link products to Sendinblue plugin.',
                'link-products-to-sendinblue'
            ) . ' 
                    <button id="lpts-update-db" class="button button-primary">'
             . esc_html__('Upgrade now', 'link-products-to-sendinblue') .
             '</button>
                </p>
            </div>';

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('lpts-update-db').addEventListener('click', function() {
                    fetch(ajaxurl, {
                        method: 'POST',
                        body: new URLSearchParams({
                            action: 'lpts_upgrade_db',
                            _ajax_nonce: '" . wp_create_nonce('lpts_upgrade_db') . "'
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.data.message);
                        // location.reload();
                    });
                });
            });
        </script>";
    }
}
