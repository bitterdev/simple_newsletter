<?php

namespace Bitter\SimpleNewsletter\API\V1\Serializer;

use League\Fractal\Serializer\DataArraySerializer;

class SimpleSerializer extends DataArraySerializer
{
    public function collection($resourceKey, array $data): array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    public function item($resourceKey, array $data): array
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }
}
