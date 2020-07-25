<?php

namespace Queue\Controller;

use GuzzleHttp\Client;
use Perfumer\Framework\Controller\PlainController;

class QueueController extends PlainController
{
    public function task($url, $method, $headers = [], $json = [], $query_string = [], $body = null)
    {
        $url = rtrim($url, '?&');

        try {
            $client = new Client();

            $options = [
                'connect_timeout' => 15,
                'read_timeout'    => 15,
                'timeout'         => 15,
//                'debug' => true,
                'headers' => $headers
            ];

            if ($json) {
                $options['json'] = $json;
            }

            if ($query_string) {
                if ($method === 'get' || $method === 'head') {
                    $url_query = http_build_query($query_string);

                    $sign_place = strpos($url, '?');

                    if ($sign_place !== false && $sign_place >= 0) {
                        $url .= '&';
                    } else {
                        $url .= '?';
                    }

                    $url .= $url_query;
                } else {
                    $options['form_params'] = $query_string;
                }
            }

            if ($body) {
                $options['body'] = $body;
            }

            echo "Request to $method $url";

            $client->request($method, $url, $options);

            echo 'Request finished successfully';
        } catch (\Throwable $e) {
            echo 'Request failed: ' . $e->getMessage();
        }
    }
}
