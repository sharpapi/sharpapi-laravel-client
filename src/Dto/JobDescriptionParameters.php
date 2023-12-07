<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Dto;

use Spatie\LaravelData\Data;

class JobDescriptionParameters extends Data
{
    public function __construct(
        public string $name,
        public ?string $company_name,
        public ?string $minimum_work_experience,
        public ?string $minimum_education,
        public ?string $employment_type,
        public ?array $required_skills,
        public ?array $optional_skills,
        public ?string $country,
        public ?bool $remote,
        public ?bool $visa_sponsored,
        public ?string $language = 'English',
    ) {
    }
}
