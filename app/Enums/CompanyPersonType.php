<?php

namespace App\Enums;

enum CompanyPersonType: string
{
    case Founder = 'founder';

    case Executive = 'executive';

    case SeniorManager = 'senior-manager';

    case Investor = 'investor';

    case Operational = 'operational';

    case Academic = 'academic';
}
