<?php

namespace Orchestra\Auth;

use Illuminate\Auth\RequestGuard as BaseGuard;
use Orchestra\Contracts\Auth\Guard as GuardContract;

class RequestGuard extends BaseGuard implements GuardContract
{
    use Concerns\HasRoles,
        Concerns\ProvidesRoles;
}
