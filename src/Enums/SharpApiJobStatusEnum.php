<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Enums;

use Kongulov\Traits\InteractWithEnum;

enum SharpApiJobStatusEnum: string
{
    use InteractWithEnum;

    case NEW = 'new';
    case PENDING = 'pending';
    case FAILED = 'failed';
    case SUCCESS = 'success';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::PENDING => 'Pending',
            self::FAILED => 'Failed',
            self::SUCCESS => 'Success',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'gray',
            self::PENDING => 'yellow',
            self::FAILED => 'red',
            self::SUCCESS => 'green',
        };
    }
}
