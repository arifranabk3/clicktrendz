<?php

namespace App\Services\Wholesalers;

use App\Models\Wholesaler;
use Illuminate\Support\Facades\Http;

abstract class AbstractWholesalerService implements WholesalerServiceInterface
{
    protected Wholesaler $wholesaler;

    public function setWholesaler(Wholesaler $wholesaler): self
    {
        $this->wholesaler = $wholesaler;
        return $this;
    }

    protected function getHttpClient()
    {
        return Http::baseUrl($this->wholesaler->website_url)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->wholesaler->api_key,
                'Accept' => 'application/json',
            ]);
    }
}
