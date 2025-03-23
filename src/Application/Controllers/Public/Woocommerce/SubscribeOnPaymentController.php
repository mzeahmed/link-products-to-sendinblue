<?php

declare(strict_types=1);

namespace LPTS\Application\Controllers\Public\Woocommerce;

use LPTS\Application\Contract\PublicControllerInterface;
use LPTS\Infrastructure\WordPress\Hook\Public\Woocommerce\SubscribeOnPaymentHook;

/**
 * Handles the logic for subscribing customers to Brevo lists
 *  after a WooCommerce payment is completed, based on product-specific
 *  conditions and list associations.
 *
 * @package LPTS\Admin\Woocommerce
 * @since   1.0.0
 */
class SubscribeOnPaymentController implements PublicControllerInterface
{
    public function __construct(
        private SubscribeOnPaymentHook $subscribeOnPaymentHook
    ) {}

    public function register(): void
    {
        $this->subscribeOnPaymentHook->register();
    }
}
