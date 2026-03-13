<?php

declare(strict_types=1);

namespace App\Services;

use OpenSearch\Client;
use OpenSearch\ClientBuilder;

final class OpenSearchService
{
    public function client(): Client
    {
        return ClientBuilder::create()
            ->setHosts([
                'https://admin:' . env('OPENSEARCH_PASSWORD') . '@opensearch:9200'
            ])
            ->setSSLVerification(false)
            ->build();
    }
}
