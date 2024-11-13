<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Dto;

use Spatie\LaravelData\Data;

class JobDescriptionParameters extends Data
{
    public function __construct(
        public string $name,
        public ?string $company_name = null,
        public ?string $minimum_work_experience = null,
        public ?string $minimum_education = null,
        public ?string $employment_type = null,
        public ?array $required_skills = null,
        public ?array $optional_skills = null,
        public ?string $country = null,
        public ?bool $remote = null,
        public ?bool $visa_sponsored = null,
        public ?string $voice_tone = null,
        public ?string $context = null,
        public ?string $language = null
    ) {}
}
