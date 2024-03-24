<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Enums;

use Kongulov\Traits\InteractWithEnum;

enum SharpApiJobTypeEnum: string
{
    use InteractWithEnum;

    case ECOMMERCE_REVIEW_SENTIMENT = 'ecommerce_review_sentiment';
    case ECOMMERCE_PRODUCT_CATEGORIES = 'ecommerce_product_categories';
    case ECOMMERCE_PRODUCT_INTRO = 'ecommerce_product_intro';
    case ECOMMERCE_THANK_YOU_EMAIL = 'ecommerce_thank_you_email';
    case HR_PARSE_RESUME = 'hr_parse_resume';
    case HR_JOB_DESCRIPTION = 'hr_job_description';
    case HR_RELATED_SKILLS = 'hr_related_skills';
    case HR_RELATED_JOB_POSITIONS = 'hr_related_job_positions';
    case TTH_REVIEW_SENTIMENT = 'tth_review_sentiment';
    case TTH_TA_PRODUCT_CATEGORIES = 'tth_ta_product_categories';
    case TTH_HOSPITALITY_PRODUCT_CATEGORIES = 'tth_hospitality_product_categories';
    case CONTENT_DETECT_PHONES = 'content_detect_phones';
    case CONTENT_DETECT_EMAILS = 'content_detect_emails';
    case CONTENT_DETECT_SPAM = 'content_detect_spam';
    case CONTENT_SUMMARIZE = 'content_summarize';
    case CONTENT_KEYWORDS = 'content_keywords';
    case CONTENT_TRANSLATE = 'content_translate';
    case CONTENT_PARAPHRASE = 'content_paraphrase';
    case CONTENT_PROOFREAD = 'content_proofread';
    case SEO_GENERATE_TAGS = 'seo_generate_tags';

    public function url(): string
    {
        return match ($this) {
            self::ECOMMERCE_REVIEW_SENTIMENT => '/ecommerce/review_sentiment',
            self::ECOMMERCE_PRODUCT_CATEGORIES => '/ecommerce/product_categories',
            self::ECOMMERCE_PRODUCT_INTRO => '/ecommerce/product_intro',
            self::ECOMMERCE_THANK_YOU_EMAIL => '/ecommerce/thank_you_email',
            self::HR_PARSE_RESUME => '/hr/parse_resume',
            self::HR_JOB_DESCRIPTION => '/hr/job_description',
            self::HR_RELATED_SKILLS => '/hr/related_skills',
            self::HR_RELATED_JOB_POSITIONS => '/hr/related_job_positions',
            self::TTH_REVIEW_SENTIMENT => '/tth/review_sentiment',
            self::TTH_TA_PRODUCT_CATEGORIES => '/tth/ta_product_categories',
            self::TTH_HOSPITALITY_PRODUCT_CATEGORIES => '/tth/hospitality_product_categories',
            self::CONTENT_DETECT_PHONES => '/content/detect_phones',
            self::CONTENT_DETECT_EMAILS => '/content/detect_emails',
            self::CONTENT_DETECT_SPAM => '/content/detect_spam',
            self::CONTENT_SUMMARIZE => '/content/summarize',
            self::CONTENT_KEYWORDS => '/content/keywords',
            self::CONTENT_TRANSLATE => '/content/translate',
            self::CONTENT_PARAPHRASE => '/content/paraphrase',
            self::CONTENT_PROOFREAD => '/content/proofread',
            self::SEO_GENERATE_TAGS => '/seo/generate_tags',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ECOMMERCE_REVIEW_SENTIMENT => 'Product Review Sentiment',
            self::ECOMMERCE_PRODUCT_CATEGORIES => 'Product Categories',
            self::ECOMMERCE_PRODUCT_INTRO => 'Generate Product Intro',
            self::ECOMMERCE_THANK_YOU_EMAIL => 'Generate Thank You E-mail',
            self::HR_PARSE_RESUME => 'Parse Resume/CV File',
            self::HR_JOB_DESCRIPTION => 'Generate Job Description',
            self::HR_RELATED_SKILLS => 'Related Skills',
            self::HR_RELATED_JOB_POSITIONS => 'Related Job Positions',
            self::TTH_REVIEW_SENTIMENT => 'Travel Review Sentiment',
            self::TTH_TA_PRODUCT_CATEGORIES => 'Tours & Activities Product Categories',
            self::TTH_HOSPITALITY_PRODUCT_CATEGORIES => 'Hospitality Product Categories',
            self::CONTENT_DETECT_PHONES => 'Detect Phone Numbers',
            self::CONTENT_DETECT_EMAILS => 'Detect Emails',
            self::CONTENT_DETECT_SPAM => 'Detect Spam',
            self::CONTENT_SUMMARIZE => 'Summarize Content',
            self::CONTENT_KEYWORDS => 'Generate Keywords/Tags',
            self::CONTENT_TRANSLATE => 'Translate Text',
            self::CONTENT_PARAPHRASE => 'Paraphrase Text',
            self::CONTENT_PROOFREAD => 'Proofread & Grammar check',
            self::SEO_GENERATE_TAGS => 'Generate SEO Tags',
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::ECOMMERCE_REVIEW_SENTIMENT,
            self::ECOMMERCE_PRODUCT_CATEGORIES,
            self::ECOMMERCE_THANK_YOU_EMAIL,
            self::ECOMMERCE_PRODUCT_INTRO => 'E-commerce',
            self::HR_PARSE_RESUME,
            self::HR_JOB_DESCRIPTION,
            self::HR_RELATED_SKILLS,
            self::HR_RELATED_JOB_POSITIONS => 'HR Tech',
            self::TTH_REVIEW_SENTIMENT,
            self::TTH_TA_PRODUCT_CATEGORIES,
            self::TTH_HOSPITALITY_PRODUCT_CATEGORIES => 'Travel, Tourism & Hospitality',
            self::CONTENT_DETECT_PHONES,
            self::CONTENT_DETECT_EMAILS,
            self::CONTENT_DETECT_SPAM,
            self::CONTENT_TRANSLATE,
            self::CONTENT_PARAPHRASE,
            self::CONTENT_PROOFREAD,
            self::CONTENT_KEYWORDS,
            self::CONTENT_SUMMARIZE => 'Content',
            self::SEO_GENERATE_TAGS => 'SEO',
        };
    }
}
