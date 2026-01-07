<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use Cachable;
}

