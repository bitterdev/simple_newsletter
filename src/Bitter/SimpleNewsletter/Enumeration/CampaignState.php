<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Enumeration;

abstract class CampaignState
{
    const DRAFT = 'draft';
    const QUEUED = 'queued';
    const SENT = 'sent';
}