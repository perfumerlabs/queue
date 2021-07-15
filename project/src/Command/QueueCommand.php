<?php

namespace Queue\Command;

use GuzzleHttp\Client;
use Perfumer\Framework\Controller\PlainController;
use Queue\Queue\Task;

class QueueCommand extends PlainController
{
    public function task(Task $task)
    {
        $url = $task->getUrl();
        $method = $task->getMethod();
        $headers = $task->getHeaders();
        $json = $task->getJson();
        $query_string = $task->getQueryString();
        $body = $task->getBody();
        $timeout = $task->getTimeout();

        $default_timeout = (int) $this->getContainer()->getParam('queue/default_timeout', 30);
        $debug = (int) $this->getContainer()->getParam('queue/debug', false);

        if ($default_timeout <= 0) {
            $default_timeout = 30;
        }

        if ($timeout <= 0) {
            $timeout = $default_timeout;
        }

        $url = rtrim($url, '?&');

        try {
            $client = new Client();

            $options = [
                'connect_timeout' => $timeout,
                'read_timeout'    => $timeout,
                'timeout'         => $timeout,
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

            if ($debug) {
                echo "Request to $method $url" . PHP_EOL;
            }

            $client->request($method, $url, $options);

            if ($debug) {
                echo 'Request finished successfully' . PHP_EOL;
            }
        } catch (\Throwable $e) {
            echo 'Request failed: ' . $e->getMessage() . PHP_EOL;
        }
    }
}
