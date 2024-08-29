<?php

namespace app\modules\admin\modules\v1\components\job\company\handlers;

use Cloudflare\API\Auth\Auth;

/**
 * TODO: временная мера (нужно будет обновить клиент для Cloudflare)
 */
class CloudflareApiKey implements Auth
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getHeaders(): array
    {
        return [
            'Authorization'   => 'Bearer ' . $this->apiKey,
        ];
    }
}
