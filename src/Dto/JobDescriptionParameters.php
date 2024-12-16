<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Dto;

class JobDescriptionParameters
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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'company_name' => $this->company_name,
            'minimum_work_experience' => $this->minimum_work_experience,
            'minimum_education' => $this->minimum_education,
            'employment_type' => $this->employment_type,
            'required_skills' => $this->required_skills,
            'optional_skills' => $this->optional_skills,
            'country' => $this->country,
            'remote' => $this->remote,
            'visa_sponsored' => $this->visa_sponsored,
            'voice_tone' => $this->voice_tone,
            'context' => $this->context,
            'language' => $this->language,
        ];
    }
}
