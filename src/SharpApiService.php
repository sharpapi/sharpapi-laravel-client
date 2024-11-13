<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use SharpAPI\Core\Client\SharpApiClient;
use SharpAPI\SharpApiService\Dto\JobDescriptionParameters;
use SharpAPI\SharpApiService\Enums\SharpApiJobTypeEnum;

class SharpApiService extends SharpApiClient
{
    /**
     * Initializes a new instance of the class.
     *
     * @throws InvalidArgumentException if the API key is empty.
     */
    public function __construct()
    {
        parent::__construct(config('sharpapi-client.api_key'));
        $this->setApiBaseUrl(config('sharpapi-client.base_url', 'https://sharpapi.com/api/v1'));
        $this->setApiJobStatusPollingInterval((int) config('sharpapi-client.api_job_status_polling_interval', 5));
        $this->setApiJobStatusPollingWait((int) config('sharpapi-client.api_job_status_polling_wait', 180));
        $this->setUserAgent('SharpAPILaravelAgent/1.2.2');

    }

    /**
     * Parses a resume (CV) file from multiple formats (PDF/DOC/DOCX/TXT/RTF)
     * and returns an extensive JSON object of data points.
     *
     * An optional language parameter can also be provided (`English` value is set as the default one) .
     *
     * @param  string  $filePath  The path to the resume file.
     * @param  string|null  $language  The language of the resume file. Defaults to 'English'.
     * @return string The parsed data or an error message.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function parseResume(
        string $filePath,
        ?string $language = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::HR_PARSE_RESUME->url(),
            ['language' => $language],
            $filePath
        );

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a job description based on a set of parameters
     * provided via JobDescriptionParameters DTO object.
     * This endpoint provides concise job details in the response format,
     * including the short description, job requirements, and job responsibilities.
     *
     * Only the job position `name` parameter is required inside $jobDescriptionParameters
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function generateJobDescription(JobDescriptionParameters $jobDescriptionParameters): string
    {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::HR_JOB_DESCRIPTION->url(),
            $jobDescriptionParameters->toArray());

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a list of related skills with their weights as a float value (1.0-10.0)
     * where 10 equals 100%, the highest relevance score.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function relatedSkills(
        string $skillName,
        ?string $language = null,
        ?int $maxQuantity = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::HR_RELATED_SKILLS->url(),
            [
                'content' => $skillName,
                'language' => $language,
                'max_quantity' => $maxQuantity,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a list of related job positions with their weights as float value (1.0-10.0)
     * where 10 equals 100%, the highest relevance score.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function relatedJobPositions(
        string $jobPositionName,
        ?string $language = null,
        ?int $maxQuantity = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::HR_RELATED_JOB_POSITIONS->url(),
            [
                'content' => $jobPositionName,
                'language' => $language,
                'max_quantity' => $maxQuantity,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Parses the customer's product review and provides its sentiment (POSITIVE/NEGATIVE/NEUTRAL)
     * with a score between 0-100%. Great for sentiment report processing for any online store.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function productReviewSentiment(string $review): string
    {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::ECOMMERCE_REVIEW_SENTIMENT->url(),
            ['content' => $review]
        );

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a list of suitable categories for the product with relevance weights as a float value (1.0-10.0)
     * where 10 equals 100%, the highest relevance score. Provide the product name and its parameters
     * to get the best category matches possible. Comes in handy with populating
     * product catalogue data and bulk products' processing.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function productCategories(
        string $productName,
        ?string $language = null,
        ?int $maxQuantity = null,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::ECOMMERCE_PRODUCT_CATEGORIES->url(),
            [
                'content' => $productName,
                'language' => $language,
                'max_quantity' => $maxQuantity,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a shorter version of the product description.
     * Provide as many details and parameters of the product to get the best marketing introduction possible.
     * Comes in handy with populating product catalog data and bulk products processing.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function generateProductIntro(
        string $productData,
        ?string $language = null,
        ?int $maxLength = null,
        ?string $voiceTone = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::ECOMMERCE_PRODUCT_INTRO->url(),
            [
                'content' => $productData,
                'language' => $language,
                'max_length' => $maxLength,
                'voice_tone' => $voiceTone,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a personalized thank-you email to the customer after the purchase.
     * The response content does not contain the title, greeting or sender info at the end,
     * so you can personalize the rest of the email easily.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function generateThankYouEmail(
        string $productName,
        ?string $language = null,
        ?int $maxLength = null,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::ECOMMERCE_THANK_YOU_EMAIL->url(),
            [
                'content' => $productName,
                'language' => $language,
                'max_length' => $maxLength,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Parses the provided text for any phone numbers and returns the original detected version and its E.164 format.
     * Might come in handy in the case of processing and validating big chunks of data against phone numbers
     * or f.e. if you want to detect phone numbers in places where they're not supposed to be.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function detectPhones(string $text): string
    {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_DETECT_PHONES->url(),
            ['content' => $text]
        );

        return $this->parseStatusUrl($response);
    }

    /**
     * Parses the provided text for any possible emails. Might come in handy in case of processing and validating
     * big chunks of data against email addresses or f.e. if you want to detect emails in places
     * where they're not supposed to be.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function detectEmails(string $text): string
    {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_DETECT_EMAILS->url(),
            ['content' => $text]
        );

        return $this->parseStatusUrl($response);
    }

    /**
     * Parses the provided text for any possible spam content.
     * It returns
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function detectSpam(string $text): string
    {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_DETECT_SPAM->url(),
            ['content' => $text]
        );

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a summarized version of the provided content.
     * Perfect for generating marketing introductions of longer texts.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function summarizeText(
        string $text,
        ?string $language = null,
        ?int $maxLength = null,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_SUMMARIZE->url(),
            [
                'content' => $text,
                'language' => $language,
                'max_length' => $maxLength,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a list of unique keywords/tags based on the provided content.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function generateKeywords(
        string $text,
        ?string $language = null,
        ?int $maxQuantity = null,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_KEYWORDS->url(),
            [
                'content' => $text,
                'language' => $language,
                'max_quantity' => $maxQuantity,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Translates the provided text into selected language
     * Perfect for generating marketing introductions of longer texts.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function translate(
        string $text,
        string $language,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_TRANSLATE->url(),
            [
                'content' => $text,
                'language' => $language,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a paraphrased version of the provided text.
     * Only the `content` parameter is required. You can define the output language,
     * maximum character length, and tone of voice. Additional instructions
     * on how to process the text can be provided in the context parameter.
     * Please keep in mind that `max_length` serves as a strong suggestion
     * for the Language Model, rather than a strict requirement,
     * to maintain the general sense of the outcome.
     * You can set your preferred writing style by providing an optional `voice_tone` parameter.
     * It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.
     * This API method also provides an optional context parameter,
     * which can be used to supply additional flexible instructions for content processing.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function paraphrase(
        string $text,
        ?string $language = null,
        ?int $maxLength = null,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_PARAPHRASE->url(),
            [
                'content' => $text,
                'language' => $language,
                'max_length' => $maxLength,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Proofreads (and checks grammar) of the provided text.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function proofread(string $text): string
    {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_PROOFREAD->url(),
            ['content' => $text]
        );

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates all most important META tags based on the content provided.
     * Make sure to include link to the website and pictures URL to get as many tags populated as possible.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function generateSeoTags(
        string $text,
        ?string $language = null,
        ?string $voiceTone = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::SEO_GENERATE_TAGS->url(),
            [
                'content' => $text,
                'language' => $language,
                'voice_tone' => $voiceTone,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Parses the Travel/Hospitality product review and provides its sentiment (POSITIVE/NEGATIVE/NEUTRAL)
     * with a score between 0-100%. Great for sentiment report processing for any online store.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function travelReviewSentiment(string $text): string
    {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::TTH_REVIEW_SENTIMENT->url(),
            ['content' => $text]
        );

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a list of suitable categories for the Tours & Activities product
     * with relevance weights as float value (1.0-10.0) where 10 equals 100%, the highest relevance score.
     * Provide the product name and its parameters to get the best category matches possible.
     * Comes in handy with populating product catalogue data and bulk product processing.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function toursAndActivitiesProductCategories(
        string $productName,
        ?string $city = null,
        ?string $country = null,
        ?string $language = null,
        ?int $maxQuantity = null,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::TTH_TA_PRODUCT_CATEGORIES->url(),
            [
                'content' => $productName,
                'city' => $city,
                'country' => $country,
                'language' => $language,
                'max_quantity' => $maxQuantity,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }

    /**
     * Generates a list of suitable categories for the Hospitality type product
     * with relevance weights as float value (1.0-10.0) where 10 equals 100%, the highest relevance score.
     * Provide the product name and its parameters to get the best category matches possible.
     * Comes in handy with populating products catalogs data and bulk products' processing.
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function hospitalityProductCategories(
        string $productName,
        ?string $city = null,
        ?string $country = null,
        ?string $language = null,
        ?int $maxQuantity = null,
        ?string $voiceTone = null,
        ?string $context = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::TTH_HOSPITALITY_PRODUCT_CATEGORIES->url(),
            [
                'content' => $productName,
                'city' => $city,
                'country' => $country,
                'language' => $language,
                'max_quantity' => $maxQuantity,
                'voice_tone' => $voiceTone,
                'context' => $context,
            ]);

        return $this->parseStatusUrl($response);
    }
}
