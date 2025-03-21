<?php

declare(strict_types=1);

namespace LPTS\Shared\Enums;

enum OptionKey: string
{
    // Main settings
    case MAIN_OPTION = 'lpts_main_option';

    // API key
    case API_KEY_V3 = 'lpts_api_key';

    // Sync settings
    case CUSTOMER_ATTRIBUTES = 'lpts_woocommerce_customer_attributes';
    case SENDINBLUE_ATTRIBUTES = 'lpts_sendinblue_contact_attributes';
}
