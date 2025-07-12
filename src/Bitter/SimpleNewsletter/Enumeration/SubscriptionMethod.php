<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Enumeration;

abstract class SubscriptionMethod
{
    const SINGLE_OPT_IN = 'single_opt_in';
    const DOUBLE_OPT_IN = 'double_opt_in';
}