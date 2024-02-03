<?php

namespace Everest\Http\Requests\Api\Application\Mounts;

use Everest\Http\Requests\Api\Application\ApplicationApiRequest;

class MountEggsRequest extends ApplicationApiRequest
{
    public function rules(array $rules = null): array
    {
        return $rules ?? ['eggs' => 'required|exists:eggs,id'];
    }
}
