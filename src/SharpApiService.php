<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use SharpAPI\SharpApiService\Dto\JobDescriptionParameters;
use SharpAPI\SharpApiService\Dto\SharpApiJob;
use SharpAPI\SharpApiService\Dto\SharpApiSubscriptionInfo;
use SharpAPI\SharpApiService\Enums\SharpApiJobStatusEnum;
use SharpAPI\SharpApiService\Enums\SharpApiJobTypeEnum;

class SharpApiService
{
    protected string $apiBaseUrl;

    protected string $apiKey;

    protected int $apiJobStatusPollingInterval = 5;

    protected int $apiJobStatusPollingWait = 180;

    protected string $userAgent;

    /**
     * Initializes a new instance of the class.
     *
     * @throws InvalidArgumentException if the API key is empty.
     */
    public function __construct()
    {
        $this->setApiKey(config('sharpapi-client.api_key'));
        if (empty($this->apiKey)) {
            throw new InvalidArgumentException('API key is required.');
        }
        $this->setApiBaseUrl(config('sharpapi-client.base_url', 'https://sharpapi.com/api/v1'));
        $this->setApiJobStatusPollingInterval((int) config('sharpapi-client.api_job_status_polling_interval', 5));
        $this->setApiJobStatusPollingWait((int) config('sharpapi-client.api_job_status_polling_wait', 180));
        $this->setUserAgent(config('sharpapi-client.user_agent'));
    }

    /**
     * Fetch the main API URL
     */
    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    /**
     * Might come in handy if case some API mocking is needed
     */
    public function setApiBaseUrl(string $apiBaseUrl): void
    {
        $this->apiBaseUrl = $apiBaseUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * Handy method to set custom User-Agent header for Affiliate Program members.
     *
     * More at: https://sharpapi.com/affiliate_program
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getApiJobStatusPollingInterval(): int
    {
        return $this->apiJobStatusPollingInterval;
    }

    public function setApiJobStatusPollingInterval(int $apiJobStatusPollingInterval): void
    {
        $this->apiJobStatusPollingInterval = $apiJobStatusPollingInterval;
    }

    public function getApiJobStatusPollingWait(): int
    {
        return $this->apiJobStatusPollingWait;
    }

    public function setApiJobStatusPollingWait(int $apiJobStatusPollingWait): void
    {
        $this->apiJobStatusPollingWait = $apiJobStatusPollingWait;
    }

    /**
     * Generic request method to run Guzzle client
     *
     * @throws GuzzleException
     */
    private function makeRequest(
        string $method,
        string $url,
        array $data = [],
        ?string $filePath = null
    ): ResponseInterface {
        $client = new Client();
        $options = [
            'headers' => $this->getHeaders(),
        ];
        if ($method === 'POST') {
            if (is_string($filePath) && strlen($filePath)) {
                $options['multipart'][] =
                    [
                        'name' => 'file',
                        'contents' => file_get_contents($filePath),
                        'filename' => basename($filePath),
                    ];
            } else {
                $options['json'] = $data;
            }
        }

        return $client->request($method, $this->getApiBaseUrl().$url, $options);
    }

    private function parseStatusUrl(ResponseInterface $response)
    {
        return json_decode($response->getBody()->__toString(), true)['status_url'];
    }

    /**
     * Generic method to check job status in polling mode and then fetch results of the dispatched job
     *
     * @throws ClientException|GuzzleException
     *
     * @api
     */
    public function fetchResults(string $statusUrl): SharpApiJob
    {
        $client = new Client();
        $waitingTime = 0;

        do {
            $response = $client->request(
                'GET',
                $statusUrl,
                ['headers' => $this->getHeaders()]
            );
            $jobStatus = json_decode($response->getBody()->__toString(), true)['data']['attributes'];

            if (
                $jobStatus['status'] === SharpApiJobStatusEnum::SUCCESS->value
                ||
                $jobStatus['status'] === SharpApiJobStatusEnum::FAILED->value
            ) {
                break;
            }   // it's still `pending` status, let's wait a bit more
            $retryAfter = isset($response->getHeader('Retry-After')[0])
                ? (int) $response->getHeader('Retry-After')[0]
                : $this->getApiJobStatusPollingInterval(); // fallback if no Retry-After header

            if (config('sharpapi-client.api_job_status_use_polling_interval')) {
                // let's force to use the value from config
                $retryAfter = $this->getApiJobStatusPollingInterval();
            }
            $waitingTime = $waitingTime + $retryAfter;
            if ($waitingTime >= $this->getApiJobStatusPollingWait()) {
                break;
            } // otherwise wait a bit more and try again
            sleep($retryAfter);
        } while (true);
        $data = json_decode($response->getBody()->__toString(), true)['data'];

        return new SharpApiJob(
            id: $data['id'],
            type: $data['attributes']['type'],
            status: $data['attributes']['status'],
            result: $data['attributes']['result'] ?? null
        );
    }

    /**
     * Prepare shared headers
     *
     * @return string[]
     */
    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->getApiKey(),
            'Accept' => 'application/json',
            'User-Agent' => $this->getUserAgent(),
        ];
    }

    /**
     * Simple PING endpoint to check the availability of the API and its internal time zone (timestamp).
     * {
     *  "ping": "pong",
     *  "timestamp": "2024-03-12T08:50:11.188308Z"
     * }
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function ping(): ?array
    {
        $response = $this->makeRequest('GET', '/ping');

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Endpoint to check details regarding the subscription's current period
     *
     * "subscription_words_used_percentage" is a percentage of current monthly quota usage
     * and might serve as an alert to the user of the depleted credits.
     * With a value above 80%, it's advised to subscribe to more credits
     * at https://sharpapi.com/dashboard/credits to avoid service disruption.
     *
     * These values are also available in the Dashboard at https://sharpapi.com/dashboard
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function quota(): ?SharpApiSubscriptionInfo
    {
        $response = $this->makeRequest('GET', '/quota');
        $info = json_decode($response->getBody()->__toString(), true);
        if (! array_key_exists('timestamp', $info)) {
            return null;
        }

        return new SharpApiSubscriptionInfo(
            timestamp: new Carbon($info['timestamp']),
            on_trial: $info['on_trial'],
            trial_ends: new Carbon($info['trial_ends']),
            subscribed: $info['subscribed'],
            current_subscription_start: new Carbon($info['current_subscription_start']),
            current_subscription_end: new Carbon($info['current_subscription_end']),
            subscription_words_quota: $info['subscription_words_quota'],
            subscription_words_used: $info['subscription_words_used'],
            subscription_words_used_percentage: $info['subscription_words_used_percentage']
        );
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
        ?string $voiceTone = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_SUMMARIZE->url(),
            [
                'content' => $text,
                'language' => $language,
                'max_length' => $maxLength,
                'voice_tone' => $voiceTone,
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
        ?string $voiceTone = null
    ): string {
        $response = $this->makeRequest(
            'POST',
            SharpApiJobTypeEnum::CONTENT_KEYWORDS->url(),
            [
                'content' => $text,
                'language' => $language,
                'max_quantity' => $maxQuantity,
                'voice_tone' => $voiceTone,
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
