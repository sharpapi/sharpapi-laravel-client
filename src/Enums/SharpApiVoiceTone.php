<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService\Enums;

use Kongulov\Traits\InteractWithEnum;

enum SharpApiVoiceTone: string
{
    use InteractWithEnum;
    case ADVENTUROUS = 'Adventurous';
    case ACADEMIC = 'Academic';
    case ARTICULATE = 'Articulate';
    case ASSERTIVE = 'Assertive';
    case AUTHORITATIVE = 'Authoritative';
    case CAPTIVATING = 'Captivating';
    case CASUAL = 'Casual';
    case CANDID = 'Candid';
    case COMPELLING = 'Compelling';
    case COMICAL = 'Comical';
    case CULTURED = 'Cultured';
    case ECLECTIC = 'Eclectic';
    case EDUCATIONAL = 'Educational';
    case EFFORTLESS = 'Effortless';
    case ELOQUENT = 'Eloquent';
    case EMPATHETIC = 'Empathetic';
    case EMPOWERING = 'Empowering';
    case ENCOURAGING = 'Encouraging';
    case ENGAGING = 'Engaging';
    case ENLIGHTENING = 'Enlightening';
    case ENTHUSIASTIC = 'Enthusiastic';
    case EXPRESSIVE = 'Expressive';
    case FORMAL = 'Formal';
    case FRIENDLY = 'Friendly';
    case FUNNY = 'Funny';
    case HEARTENING = 'Heartening';
    case HEARTFELT = 'Heartfelt';
    case HUMOROUS = 'Humorous';
    case IMPASSIONED = 'Impassioned';
    case INSPIRATIONAL = 'Inspirational';
    case INSTRUCTIONAL = 'Instructional';
    case INTELLECTUAL = 'Intellectual';
    case INFORMAL = 'Informal';
    case INVENTIVE = 'Inventive';
    case LIVELY = 'Lively';
    case LYRICAL = 'Lyrical';
    case LUXURIOUS = 'Luxurious';
    case MINIMALIST = 'Minimalist';
    case NARRATIVE = 'Narrative';
    case NEUTRAL = 'Neutral';   // the default one
    case NOSTALGIC = 'Nostalgic';
    case OPTIMISTIC = 'Optimistic';
    case PERSUASIVE = 'Persuasive';
    case PESSIMISTIC = 'Pessimistic';
    case PROVOCATIVE = 'Provocative';
    case QUIRKY = 'Quirky';
    case RESPECTFUL = 'Respectful';
    case SERIOUS = 'Serious';
    case SINCERE = 'Sincere';
    case STORYTELLING = 'Storytelling';
    case SYMPATHETIC = 'Sympathetic';
    case TECH_SAVVY = 'Tech-Savvy';
    case THOUGHTFUL = 'Thoughtful';
    case TOUCHING = 'Touching';
    case WITTY = 'Witty';
}
