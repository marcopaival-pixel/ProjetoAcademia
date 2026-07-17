<?php

namespace App\Models\Traits;

/**
 * Isolamento por empresa via users.academy_company_id (coluna local: user_id).
 */
trait BelongsToUserCompany
{
    use BelongsToCompany;

    /** @var string */
    protected $companyColumn = 'user_id';
}
