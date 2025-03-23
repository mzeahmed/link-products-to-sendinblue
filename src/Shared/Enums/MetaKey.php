<?php

declare(strict_types=1);

namespace LPTS\Shared\Enums;

/**
 * @since 1.2.0
 */
enum MetaKey: string
{
    case PRODUCT_LIST = '_lpts_list';
    case VARIATION_PRODUCT_LISTS = '_lpts_list_variation';
}
