<?php

declare(strict_types=1);

namespace LPTS\Infrastructure\WordPress\Hook\Public\Woocommerce;

use LPTS\Application\Contract\HookInterface;
use LPTS\Domain\Services\Public\Woocommerce\SubscribeOnPaymentService;

class SubscribeOnPaymentHook implements HookInterface
{
    public function __construct(
        private SubscribeOnPaymentService $subscribeOnPaymentService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        add_action('woocommerce_payment_complete', [$this, 'paymentComplete']);
    }

    /**
     * Triggered when a WooCommerce order is marked as paid.
     * Iterates through each purchased product, retrieves the linked
     * Sendinblue lists and their conditions, and subscribes the customer
     * if the conditions are met.
     *
     * @param int $orderId Id of the order.
     *
     * @since 1.0.0
     */
    public function paymentComplete(int $orderId): void
    {
        $this->subscribeOnPaymentService->paymentComplete($orderId);
    }
}
