<?php

namespace PetrLatsko\JsonBundle\Client;

use Curl\Curl;
use PetrLatsko\JsonBundle\Client\Exception\ClientErrorJsonException;
use PetrLatsko\JsonBundle\Client\Exception\ClientParseJsonException;
use PetrLatsko\JsonBundle\Client\Exception\ClientTransportException;

class CurlClient
{
    /**
     * @var Curl
     */
    protected $transport;

    public function setTransport(Curl $transport)
    {
        $this->transport = new $transport;
    }

    /**
     * @param $url
     * @return array
     * @throws ClientErrorJsonException
     * @throws ClientParseJsonException
     * @throws ClientTransportException
     */
    public function getJson($url)
    {
        $this->transport->reset();
        $this->transport->get($url);

        $this->prepareTransportError();

        $response = $this->parseJson();
        $this->prepareParseJsonError();

        $jsonData = $this->prepareJsonData($response);

        return $jsonData;
    }

    /**
     * @throws ClientTransportException
     */
    protected function prepareTransportError()
    {
        if ($this->transport->curl_error)
        {
            throw new ClientTransportException($this->transport->curl_error_message, $this->transport->curl_error_code);
        }
    }

    /**
     * @return mixed
     */
    protected function parseJson()
    {
        return json_decode($this->transport->response, true);
    }

    /**
     * @throws ClientParseJsonException
     */
    protected function prepareParseJsonError()
    {
        $jsonLastError = json_last_error();
        if ($jsonLastError !== JSON_ERROR_NONE)
        {
            $jsonErrorMessage = 'Unknown error';
            switch ($jsonLastError)
            {
                case JSON_ERROR_DEPTH:
                    $jsonErrorMessage = 'Maximum stack depth exceeded';
                    break;

                case JSON_ERROR_STATE_MISMATCH:
                    $jsonErrorMessage = 'Underflow or the modes mismatch';
                    break;

                case JSON_ERROR_CTRL_CHAR:
                    $jsonErrorMessage = 'Unexpected control character found';
                    break;

                case JSON_ERROR_SYNTAX:
                    $jsonErrorMessage = 'Syntax error, malformed JSON';
                    break;

                case JSON_ERROR_UTF8:
                    $jsonErrorMessage = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;

                default:
                    # Unknown error
                    break;
            }

            throw new ClientParseJsonException($jsonErrorMessage, $jsonLastError);
        }
    }

    /**
     * @param array $jsonData
     * @return array
     * @throws ClientErrorJsonException
     */
    protected function prepareJsonData(array $jsonData)
    {
        if ($jsonData['success'] !== true)
        {
            throw new ClientErrorJsonException($jsonData['message'], $jsonData['code']);
        }

        return $jsonData['data'];
    }
}
