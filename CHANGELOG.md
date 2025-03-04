# Changelog

## March 4, 2025 - v1.2.4
- Laravel 12 compatibility

## Dec 10, 2024 - v1.2.3
- removing spatie/laravel-data dependency

## Nov 13, 2024 - v1.2.2
- extracting core into a package

## November 10, 2024 - v1.2.1
- removing the requirement to publish config file 

## August 26, 2024 - v1.2.0 update
- Generate Keywords/Tags & Summarize methods aquired optional `context` that allows to pass additional processing instructions for the provided `content`
- API usage optimized internally, switched to AI job dispatch/result endpoint pairing mode

## March 23, 2024 - v1.1.0 update

### 1. new methods added

#### 1.2. Paraphrase text: `paraphrase()`
Generates a paraphrased version of the provided text. 

[Check Documentation](https://documenter.getpostman.com/view/31106842/2s9Ye8faUp#aea28008-ac67-4245-a79b-26788bce3f44)

#### 1.2 Proofread & Grammar Check: `proofread()`

Proofreads (and checks grammar) of the provided text.

[Check Documentation](https://documenter.getpostman.com/view/31106842/2s9Ye8faUp#dcb4a490-1243-4001-93fc-652c570dbcd7)

#### 1.3 Subscription Info / Quota Check: `quota()`

Endpoint to check details regarding the subscription's current period.

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
* "subscription_words_used_percentage" is a percentage of current monthly quota usage
* and might serve as an alert to the user of the depleted credits.
* With a value above 80%, it's advised to subscribe to more credits
* at https://sharpapi.com/dashboard/credits to avoid service disruption.

[Check Documentation](https://documenter.getpostman.com/view/31106842/2s9Ye8faUp#7c401a21-8354-4589-a20a-573d1ae00d65)

#### 1.4 Subscription Info / Quota Check: `ping()`

Simple PING endpoint to check the availability of the API and its internal time zone (timestamp).

```json
{
    "ping": "pong",
    "timestamp": "2024-03-12T08:50:11.188308Z"
}
```

[Check Documentation](https://documenter.getpostman.com/view/31106842/2s9Ye8faUp#12a4aa9e-15cd-49a9-84ff-204ddc1116a3)

### 2. New Parameters added

#### 2.1 `max_quantity` - allows to limit the amount of returned items

**Added to methods:**
- E-commerce > Product Categories / `productCategories()`
- Travel, Tourism & Hospitality > Tours & Activities Product Categories / `toursAndActivitiesProductCategories()`
- Travel, Tourism & Hospitality > Hospitality Product Categories / `hospitalityProductCategories()`
- HR Tech > Related Job Positions / `relatedJobPositions()`
- HR Tech > Related Skills / `relatedSkills()`

#### 2.2 `max_length` - allows to instruct AI model to limit the output of generated text

Please keep in mind that max_length serves as a strong suggestion for the Language Model,
rather than a strict requirement, to maintain the general sense of the outcome.

**Added to methods:**
- E-commerce > Generate Product Intro / `generateProductIntro()`
- E-commerce > Generate Thank You E-mail / `generateThankYouEmail()`
- Content & Marketing Automation > Summarize Content / `summarizeText()`
- Content & Marketing Automation > Paraphrase Text / `paraphrase()`

#### 2.3 `voice_tone` - Tone of voice of the generated text

You can set your preferred writing style by providing 
an optional voice_tone parameter. It can be adjectives like
`funny` or `joyous`, or even the name of a famous writer.
You can provide multiple tones at the same time.

**Added to methods:**
- SEO > Generate SEO Tags / `generateSeoTags()`
- Content & Marketing Automation > Generate Keywords/Tags / `generateKeywords()`
- Content & Marketing Automation > Summarize Content / `summarizeText()`
- Content & Marketing Automation > Paraphrase Text / `paraphrase()`
- Content & Marketing Automation > Translate Text / `translate()`
 - Travel, Tourism & Hospitality > Tours & Activities Product Categories / `toursAndActivitiesProductCategories()`
- Travel, Tourism & Hospitality > Hospitality Product Categories / `hospitalityProductCategories()`
- HR Tech > Generate Job Description / `generateJobDescription()`
- E-commerce > Product Categories / `productCategories()`
- E-commerce > Generate Product Intro / `generateProductIntro()`
- E-commerce > Generate Thank You E-mail / `generateThankYouEmail()`

#### 2.4 `context` - adds more context/instructions for content processing

**Added to methods:**
- HR Tech > Generate Job Description / `generateJobDescription()`
- E-commerce > Generate Thank You E-mail / `generateThankYouEmail()`
- E-commerce > Product Categories / `productCategories()`
- Travel, Tourism & Hospitality > Tours & Activities Product Categories / `toursAndActivitiesProductCategories()`
- Travel, Tourism & Hospitality > Hospitality Product Categories / `hospitalityProductCategories()`
- Content & Marketing Automation > Translate Text / `translate()`
- Content & Marketing Automation > Paraphrase Text / `paraphrase()`

### 3.0 Added ENV variable to set custom User-Agent for Affiliate Program members.

Now you can set this inside `.env` file of your app:
```bash
SHARP_API_USER_AGENT="SharpAPILaravelAgent/1.1.0"
```

More info at https://sharpapi.com/affiliate_program

## December 10, 2023 - v1.0.2
- v1.0.2 initial release
