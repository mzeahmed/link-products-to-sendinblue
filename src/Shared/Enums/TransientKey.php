<?php

declare(strict_types=1);

namespace LPTS\Shared\Enums;

/**
 * @since 2.0.0
 */
enum TransientKey: string
{
    case BREVO_ATTRIBUTES = 'lpts_attributes';
    case BREVO_CLIENT_CREDIT = 'lpts_client_credit_';
}
