<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Dto;

use SharpAPI\SharpApiService\Enums\SharpApiJobStatusEnum;
use SharpAPI\SharpApiService\Enums\SharpApiJobTypeEnum;
use Spatie\LaravelData\Data;
use stdClass;

class SharpApiJob extends Data
{
    public function __construct(
        public string    $id,
        public string    $type,
        public string    $status,
        public ?stdClass $result
    ) {}

    /**
     * Returns SharpAPI job ID (UUID format)
     *
     * @api
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns one of the job types available in SharpApiJobTypeEnum
     *
     * @api
     */
    public function getType(): SharpApiJobTypeEnum
    {
        return SharpApiJobTypeEnum::from($this->type);
    }

    /**
     * Returns one of the job statuses available in SharpApiJobStatusEnum
     *
     * @api
     */
    public function getStatus(): SharpApiJobStatusEnum
    {
        return SharpApiJobStatusEnum::from($this->status);
    }

    /**
     * Returns job result as a prettied JSON
     *
     * @api
     */
    public function getResultJson(): string|bool|null
    {
        return $this->result ? json_encode($this->result, JSON_PRETTY_PRINT) : null;
    }

    /**
     * Returns job result contents as PHP associative array
     *
     * @api
     */
    public function getResultArray(): ?array
    {
        return (array)$this->result;
    }

    /**
     * Returns job result contents as PHP stdClass object
     *
     * @api
     */
    public function getResultObject(): ?stdClass
    {
        return $this->result;
    }
}
