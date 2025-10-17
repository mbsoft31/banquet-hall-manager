<?php

namespace Mbsoft\BanquetHallManager\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * Allow mass assignment for testing fixtures.
     */
    protected $guarded = [];
}

