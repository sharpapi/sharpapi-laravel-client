<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SharpAPI\SharpApiService\SharpApiService
 *
 * @api
 */
class SharpApiService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SharpAPI\SharpApiService\SharpApiService::class;
    }
}
