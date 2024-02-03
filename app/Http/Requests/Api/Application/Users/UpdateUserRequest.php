<?php

namespace Everest\Http\Requests\Api\Application\Users;

use Everest\Models\User;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(array $rules = null): array
    {
        return parent::rules($rules ?? User::getRulesForUpdate($this->route()->parameter('user')));
    }
}
