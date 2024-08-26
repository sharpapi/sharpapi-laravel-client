
![SharpAPI GitHub cover](https://sharpapi.com/sharpapi-github-laravel-bg.jpg "SharpAPI Laravel Client")

# SharpAPI Laravel Client SDK

### 🚀 Automate with AI in just two lines of code. Save countless hours and enhance your app effortlessly.

## Leverage AI API to streamline workflows in E-Commerce, Marketing, Content Management, HR Tech, Travel, and more.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sharpapi/sharpapi-laravel-client.svg?style=flat-square)](https://packagist.org/packages/sharpapi/sharpapi-laravel-client)
[![Total Downloads](https://img.shields.io/packagist/dt/sharpapi/sharpapi-laravel-client.svg?style=flat-square)](https://packagist.org/packages/sharpapi/sharpapi-laravel-client)

#### Save time on repetitive content analysis and generation tasks that your app users perform daily.

See more at [SharpAPI.com Website &raquo;](https://sharpapi.com/)

---

## Requirements

- PHP >= 8.1
- Laravel >= 9.0

If you don't use Laravel then you can find
[Generic SharpAPI PHP Client here &raquo;](https://github.com/sharpapi/sharpapi-php-client)

---

## ⛲ What can it do for you?
* 🛒 **E-commerce**
    - Quickly generate engaging product introductions to attract customers.
    - Automatically create personalized thank-you emails for enhanced customer experience.
    - Streamline product categorization for a well-organized catalog.
    - Sentiment Analysis: Understand and analyze sentiment in product reviews for data-driven decision-making.
* 📝️ **Content & Marketing Automation**
    - Easily translate text for a global audience.
    - Paraphrase and proofread any text (including grammar check)
    - Spam Content Detection: Identify and filter out spam content effectively.
    - Contact Information Extraction: Extract phone numbers and email addresses from non-standard formats for streamlined communication.
    - Generate concise summaries and unique keywords/tags for improved content consumption.
    - Boost SEO efforts by automatically generating META tags based on content.
* ‍💻 **HR Tech**
  - Generate complex job descriptions effortlessly, saving time in the hiring process.
  - Skills and Position Insights: Identify related job positions and skills to streamline recruitment.
  - Automated Resume Parsing: Efficiently parse and extract information from resumes files for easy processing.
* ✈️ **Travel, Tourism & Hospitality**
    - Analyze sentiment in travel reviews to improve services.
    - Streamline categorization for tours, activities, and hospitality products.

---

## Features

Please refer to the official:

- [API Documentation](https://sharpapi.com/documentation)
- **Multi-language Support**:
  Supporting 80 languages for every content or data analysis API endpoint.
  [Check the list here](https://botpress.com/blog/list-of-languages-supported-by-chatgpt).
- **Easy-to-Use RESTful Format**:
  With standardized set of endpoints - gain valuable insights through analysis endpoints, covering product categories,
  skills, and job positions, providing relevant scores.
- **Always the same, clean data formats**:
  Rest assured with consistent, predictable JSON format
  for all returned data. No need to worry about fuzzy AI data.
- **Tech Support**:
  Crafted by developers for developers, we provide continuous
  assistance throughout your journey.

---

## Installation

1. You can install the package via `composer`:

```bash
composer require sharpapi/sharpapi-laravel-client
php artisan vendor:publish --tag=sharpapi-laravel-client
```

2. Register at [SharpApi.com](https://sharpapi.com/) and get the API key.

3. Set the API key inside `.env`

```bash
SHARP_API_KEY=key
```

**That's it!**

---

## Usage

You can inject `SharpApiService` class or use the facade `\SharpApiService` singleton.

We recommend you to use Laravel queuing system to optimize dispatched jobs
and the process of checking the results, especially if you process bigger batches of data.

Typical use case require these steps:

1. Dispatch one of the available AI processing methods (this will return job processing status URL)
2. Run `fetchResults($statusUrl)` method which operates in polling mode, sending underneath
   requests every 10 seconds for 180 seconds (these values [can be customized](#optional-custom-configuration)).
3. `SharpApiJob` object will be returned.
4. For a job finished with `success` return status you can obtain the results with one
   of the methods, for example `$jobResultJson = $jobResult->getResultJson()`.

**Each dispatched job usually takes somewhere between a couple of seconds to a minute.**

After that period a returned job will usually have `success` status and it's results will
be available for further processing.
Each API method returns different return format.
[Go to List of API methods/endpoints below for details&raquo;](#list-of-api-methodsendpoints)

Our API guarantees to return correct format every time. AI engines that SharpAPI
use in rare cases have a tendency to misbehave and timeout
or return incorrect data.
In those cases the returned `status` for the job will be `failed`.
You can rerun the exact same job request in that case.

As long as the job is still being processed by our engine it will keep
returning `pending` status.

### Controller usage example

```php
<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use SharpAPI\SharpApiService\SharpApiService;

class SharpTest extends Controller
{
    public function __construct(public SharpApiService $sharpApiService)
    {
    }

    /**
     * @throws GuzzleException
     */
    public function detect_phones(): void
    {
        $statusUrl = $this->sharpApiService->detectPhones(
            'Where to find us? Call with a sales tech advisor:
            Call: 1800-394-7486 or our Singapore office +65 8888 8888'
        );
        
        $result = $this->sharpApiService->fetchResults($statusUrl);
        
        dd($result->getResultJson());
        /* returned:
        [
            {
                "detected_number": "1800-394-7486",
                "parsed_number": "+18003947486"
            },
            {
                "detected_number": "+65 8888 8888",
                "parsed_number": "+6588888888"
            }
        ]
         */
    }
}
```

#### Guzzle Exceptions
The underlying HTTP requests are facilitated by
[Laravel HTTP Client/Guzzle](https://laravel.com/docs/10.x/http-client),
making it advisable to familiarize yourself with common
[Guzzle Exceptions](https://docs.guzzlephp.org/en/stable/quickstart.html#exceptions).
```php
use GuzzleHttp\Exception\ClientException;

// Step 1: dispatch the job to the API with one of the methods, for example:
try {
    $statusUrl = \SharpApiService::summarizeText(
        $text, 
        'German',   // optional language
        500,    // optional length
        'neutral voice tone'    // optional voice tone
      );
    // $statusUrl example value: 'http://sharpapi.com/api/v1/job/status/75acb6dc-a975-4969-9ef1-c62cebc511cb'
} catch (ClientException $e) {
     $e->getResponse()
}

// Step 2: request to check job status in polling mode and wait for the result
$jobResult = \SharpApiService::fetchResults($statusUrl);

// Step 3: get results of dispatched API job, f.e. this returns job result as a prettied JSON
$jobResultJson = $jobResult->getResultJson();
// ..or PHP array:
$jobResultArray = $jobResult->getResultArray();
// ..or PHP stdClass:
$jobResultObject = $jobResult->getResultObject();
```

---

## OPTIONAL custom configuration

So you can overwrite these values with `.env` settings:

```php
SHARP_API_KEY=XXX_key_XXX
SHARP_API_JOB_STATUS_POLLING_WAIT=200
SHARP_API_JOB_STATUS_USE_POLLING_INTERVAL=true
SHARP_API_JOB_STATUS_POLLING_INTERVAL=5
SHARP_API_BASE_URL=MOCK_SERVER
```

---

## 📋 List of API methods/endpoints

Each method always returns `SharpApiJob` object, where its
`getResultJson / getResultArray / getResultObject`
methods will return different data structure.
Please refer to the detailed examples provided
at [SharpAPI.com](https://sharpapi.com/)

For methods that have `language` parameter you can also use
`SharpApiLanguages` Enum values to make your code more readable.

---

### 🧑‍💻 HR

#### ⭐ Parse Resume/CV File

Parses a resume (CV) file from multiple formats (PDF/DOC/DOCX/TXT/RTF) and returns an extensive object of data points.

An optional output language parameter can also be provided (`English` value is set as the default one) .

```php
$statusUrl = \SharpApiService::parseResume('/test/resume.pdf', 'English');
```

#### ⭐ Generate Job Description

Based on the list of extensive parameters this endpoint provides concise job details in the response format, including
the short description, job requirements, and job responsibilities.
The only mandatory parameter is `name`.

This functionality utilizes a specialized `DTO` class (`Data Transfer Object`) parameter
named `JobDescriptionParameters` to aid in the validation of input parameters.
Only the `name` parameter in the constructor of this `DTO` is mandatory.

You can set your preferred writing style by providing a voice_tone parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

This API method also provides an optional context parameter, which can be used 
to supply additional flexible instructions for content processing.

```php
$jobDescriptionParameters = new JobDescriptionParameters(
    name: "PHP Senior Engineer",
    company_name: "ACME LTD",   // optional
    minimum_work_experience: "5 years",   // optional
    minimum_education: "Bachelor Degree",   // optional
    employment_type: "full time",   // optional
    required_skills: ['PHP8', 'Laravel'],   // optional
    optional_skills: ['AWS', 'Redis'],   // optional
    country: "United Kingdom",   // optional
    remote: true,   // optional
    visa_sponsored: true,   // optional
    voice_tone: 'Professional and Geeky',   // optional voice tone
    context: null,   // optional context, additional AI processing instructions
    language: null   // optional output language
);

$statusUrl = \SharpApiService::generateJobDescription($jobDescriptionParameters);
```

#### ⭐ Related Skills

Generates a list of related skills with their weights as a float
value (1.0-10.0) where 10 equals 100%, the highest relevance score.

Only first parameter (`name`) is required.

You can limit the output with the `max_quantity` parameter.

```php
$statusUrl = \SharpApiService::relatedSkills(
    'MySQL', 
    'English',   // optional language
    10  // optional quantity
  );
```

#### ⭐ Related Job Positions

Generates a list of related job positions with their weights as
float value (1.0-10.0) where 10 equals 100%, the highest relevance score.

Only first parameter (`name`) is required.

You can limit the output with the `max_quantity` parameter.

```php
$statusUrl = \SharpApiService::relatedJobPositions(
    'Senior PHP Engineer', 
    'English',   // optional language
    10  // optional quantity
  );
```

---


### 🛒 E-commerce

#### ⭐ Product Review Sentiment

Parses the customer's product review and provides its sentiment (POSITIVE/NEGATIVE/NEUTRAL)
with a score between 0-100%. Great for sentiment report processing for any online store.

```php
$statusUrl = \SharpApiService::productReviewSentiment('customer review contents');
```

#### ⭐ Product Categories

Generates a list of suitable categories for the product with relevance
weights as a float value (1.0-10.0) where 10 equals 100%, the highest relevance score.
Provide the product name and its parameters to get the best category matches possible.
Comes in handy with populating product catalogue data and bulk products' processing.

You can limit the output with the `max_quantity` parameter.

You can set your preferred writing style by providing a `voice_tone` parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

Within an additional optional parameter context, you can provide a list of other categories 
that will be taken into consideration during the mapping process 
(for example your current e-commerce categories).


```php
$statusUrl = \SharpApiService::productCategories(
    'Sony Playstation 5', 
    'English',   // optional language
    5,   // optional quantity
    'Tech-savvy',   // optional voice tone
    'Game Console, PS5 Console'    // optional context, current categories to match
  );
```

#### ⭐ Generate Product Intro

Generates a shorter version of the product description. Provide as many details
and parameters of the product to get the best marketing introduction possible.
Comes in handy with populating product catalog data and bulk products processing.

You can limit the output with the `max_length` parameter. Please keep in mind that `max_length` serves as a strong 
suggestion for the Language Model, rather than a strict requirement, to maintain the general sense of the outcome.

You can set your preferred writing style by providing a `voice_tone` parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

```php
$statusUrl = \SharpApiService::generateProductIntro(
    'Sony Playstation 5', 
    SharpApiLanguages::ENGLISH,   // optional language
    300,   // optional length
    'Funny'   // optional voice tone
  );
```

#### ⭐ Generate Thank You E-mail

Generates a personalized thank-you email to the customer after the purchase.
The response content does not contain the title, greeting or sender info at the end,
so you can personalize the rest of the email easily.

You can limit the output with the max_length parameter. Please keep in mind that `max_length` serves 
as a strong suggestion for the Language Model, rather than a strict requirement, 
to maintain the general sense of the outcome.

You can set your preferred writing style by providing a `voice_tone` parameter.
It can be adjectives like funny or joyous, or even the name of a famous writer.

This API method also provides an optional context parameter, which can be used to supply additional
flexible instructions for content processing.

```php
$statusUrl = \SharpApiService::generateThankYouEmail(
    'Sony Playstation 5', 
    SharpApiLanguages::ENGLISH,    // optional language
    250,    // optional length
    'Neutral',    // optional voice tone
    'Must invite customer to visit again before Holidays'   // optional context
   );
```

---


### 📝️ Content

#### ⭐ Translate Text

Translates provided text to selected language. 80 languages are supported.
Please check included `SharpApiLanguages` _Enum_ class for details.

You can set your preferred writing style by providing a `voice_tone` parameter.
It can be adjectives like funny or joyous, or even the name of a famous writer.

An optional `context` parameter is also available. It can be used to provide more context to the translated text,
like the use case example or some additional explanations.

```php
$statusUrl = \SharpApiService::translate(
    'turn', 
    SharpApiLanguages::FRENCH,    // optional language
    'neutral',    // optional voice tone
    'to turn a page'   // optional context
    );

// will result in :
// {"content": "tourner", "to_language": "French", "from_language": "English"}
```

#### ⭐ Paraphrase / Rephrase

Generates a paraphrased version of the provided text.
Only the `content` parameter is required. You can define the output language,
maximum character length, and tone of voice. 

Additional instructions on how to process the text can be provided in the context parameter.
Please keep in mind that `max_length` serves as a strong suggestion
for the Language Model, rather than a strict requirement,
to maintain the general sense of the outcome.

You can set your preferred writing style by providing an optional `voice_tone` parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

This API method also provides an optional `context` parameter,
which can be used to supply additional flexible instructions for content processing.

```php
$statusUrl = \SharpApiService::paraphrase(
    $text, 
    SharpApiLanguages::FRENCH,   // optional language
    500, // optional length
    'neutral',    // optional voice tone
    'avoid using abbreviations'   // optional context
    );
```

#### ⭐ Proofread Text + Grammar Check 

Proofreads (and checks grammar) a provided text.

```php
$statusUrl = \SharpApiService::proofread($text);
```

#### ⭐ Detect Spam

Checks if provided content passes a spam filtration test.
Provides a percentage confidence score and an explanation
for whether it is considered spam or not.
This information is useful for moderators to make a final decision.

```php
$statusUrl = \SharpApiService::detectSpam($text);
```

#### ⭐ Detect Phones Numbers

Parses the provided text for any phone numbers and returns the original detected
version and its E.164 format. Might come in handy in the case of processing
and validating big chunks of data against phone numbers or f.e. if you want
to detect phone numbers in places where they're not supposed to be.

```php
$statusUrl = \SharpApiService::detectPhones($text);
```

#### ⭐ Detect Emails

Parses the provided text for any possible emails. Might come in handy in case
of processing and validating big chunks of data against email addresses
or f.e. if you want to detect emails in places where they're not supposed to be.

```php
$statusUrl = \SharpApiService::detectEmails($text);
```

#### ⭐ Generate Keywords/Tags

Generates a list of unique keywords/tags based on the provided content.

You can limit the output with the `max_quantity` parameter.

You can set your preferred writing style by providing a `voice_tone` parameter.

```php
$statusUrl = \SharpApiService::generateKeywords(
    $text, 
    'English',    // optional language
    5,  // optional length
    'Freaky & Curious'    // optional voice tone
  );
```

#### ⭐ Summarize Text

Generates a summarized version of the provided content. Perfect for generating
marketing introductions of longer texts.

You can limit the output with the `max_length` parameter. 
Please keep in mind that `max_length` serves as a strong suggestion for the Language Model, 
rather than a strict requirement, to maintain the general sense of the outcome.

You can set your preferred writing style by providing a `voice_ton`e parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

```php
$statusUrl = \SharpApiService::summarizeText(
    $text, 
    'English',     // optional language
    'David Attenborough'    // optional voice tone
  );
```

---


### 🗒 SEO

#### ⭐ Generate SEO Tags

Generates all most important META tags based on the content provided. Make sure to include
link to the website and pictures URL to get as many tags populated as possible.

You can set your preferred writing style by providing a `voice_ton`e parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

```php
$statusUrl = \SharpApiService::generateSeoTags(
    $text, 
    'English',      // optional language
    'David Attenborough'    // optional voice tone
  );
```

---


### ✈️ Travel, Tourism & Hospitality

#### ⭐ Travel Review Sentiment

Parses the Travel/Hospitality product review and provides its sentiment
(POSITIVE/NEGATIVE/NEUTRAL) with a score between 0-100%.
Great for sentiment report processing for any online store.

```php
$statusUrl = \SharpApiService::travelReviewSentiment($text);
```

#### ⭐ Tours & Activities Product Categories

Generates a list of suitable categories for the Tours & Activities product
with relevance weights as float value (1.0-10.0) where 10 equals 100%,
the highest relevance score. Provide the product name and its parameters
to get the best category matches possible. Comes in handy with populating
product catalogue data and bulk product processing.
Only first parameter `productName` is required.

You can limit the output with the `max_quantity` parameter.

You can set your preferred writing style by providing a `voice_tone` parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

Within an additional optional parameter `context`, you can provide a list of other categories
that will be taken into consideration during the mapping process
(for example your current e-commerce categories).


```php
$statusUrl = \SharpApiService::toursAndActivitiesProductCategories(
        'Oasis of the Bay'
        'Ha Long',     // optional city
        'Vietnam',     // optional country
        'English',     // optional language
        10,     // optional quantity
        'Adventurous',     // optional voice tone
        'Bay Hotels, Ha Long Hotels'     // optional context, current categories to match
    );
```

#### ⭐ Hospitality Product Categories

Generates a list of suitable categories for the Hospitality type product
with relevance weights as float value (1.0-10.0) where 10 equals 100%,
the highest relevance score. Provide the product name and its parameters
to get the best category matches possible. Comes in handy with populating
products catalogs data and bulk products' processing.
Only first parameter `productName` is required.

You can limit the output with the `max_quantity` parameter.

You can set your preferred writing style by providing a `voice_tone` parameter.
It can be adjectives like `funny` or `joyous`, or even the name of a famous writer.

Within an additional optional parameter `context`, you can provide a list of other categories
that will be taken into consideration during the mapping process
(for example your current e-commerce categories).

```php
$statusUrl = \SharpApiService::hospitalityProductCategories(
        'Hotel Crystal 大人専用'
        'Tokyo',     // optional city
        'Japan',    // optional country
        'English',     // optional language
        10,    // optional quantity
        'Adventurous',    // optional voice tone
        'Tokyo Hotels, Crystal Hotels'     // optional context, current categories to match
    );
```

---

### 🤖 Technical API Endpoints

#### ⭐ Subscription information / quota check
Endpoint to check details regarding the subscription's current period

```php
$statusUrl = \SharpApiService::quota();
```

will result in:
```json
{
    "timestamp": "2024-03-19T12:49:41.445736Z",
    "on_trial": false,
    "trial_ends": "2024-03-17T07:57:46.000000Z",
    "subscribed": true,
    "current_subscription_start": "2024-03-18T12:37:39.000000Z",
    "current_subscription_end": "2024-04-18T12:37:39.000000Z",
    "subscription_words_quota": 100000,
    "subscription_words_used": 9608,
    "subscription_words_used_percentage": 0.1
}
```

`subscription_words_used_percentage` is a percentage of current monthly quota usage
and might serve as an alert to the user of the depleted credits.
With a value above 80%, it's advised to subscribe to more credits
at https://sharpapi.com/dashboard/credits to avoid service disruption.

These values are also available in the Dashboard at https://sharpapi.com/dashboard

#### ⭐ Ping

Simple PING endpoint to check the availability of the API and it's internal timze zone (timestamp).

```php
$statusUrl = \SharpApiService::ping();
```

will result in:
```json
{
  "ping": "pong",
  "timestamp": "2024-03-12T08:50:11.188308Z"
}
```

---


### Do you think our API is missing some obvious functionality?

- [Please let us know via GitHub »](https://github.com/sharpapi/sharpapi-laravel-client/issues)
- or [Join our Telegram Group »](https://t.me/sharpapi_community)

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Credits

- [A2Z WEB LTD](https://github.com/a2zwebltd)
- [Dawid Makowski](https://github.com/makowskid)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
