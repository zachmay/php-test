<?php

namespace Uparts\Http;

class JsonHttpClient implements HttpClient
{
    public function get($url)
    {
        /**
         * @todo: Throw an exception when this fails.
         */
        $responseBody = file_get_contents($url);

        return json_decode($responseBody, true);
    }
}
