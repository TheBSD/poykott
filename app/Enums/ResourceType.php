<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum ResourceType: string
{
    use EnumHelper;

    case TechAviv = 'techaviv';

    case Wikipedia = 'wikipedia';

    case Twitter = 'twitter';

    case LinkedIn = 'linkedin';

    case Wikitia = 'wikitia';

    case Wikidata = 'wikidata';

    case Golden = 'golden';

    case VerifyWiki = 'verify_wiki';

    case OfficialWebsite = 'official_website';

    case Bloomberg = 'bloomberg';

    case BuyIsraeliTech = 'buyisraelitech';

}
