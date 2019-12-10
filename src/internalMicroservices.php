<?php
namespace isoft\fmtsf4;

class internalMicroServices
{
    public static function callComm($url, $data = array())
    {
        $http_client = new GuzzleHttp\Client();
        $response = $request = null;
        try {
            $request = $http_client->request(
                'POST',
                parametroTable::getParametroValue("host_api_comm_internal") . "/comm/int" . $url,
                [
                    'json' => $data
                ]
            );
            $response = $request->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()) {
                $msg = json_decode(
                    $e
                        ->getResponse()
                        ->getBody()
                        ->getContents()
                );
                throw new Exception($msg->message);
            } else {
                throw $e;
            }
        }
        return true;
    }

    public static function callTrading($method, $url, $data = array())
    {
        $http_client = new GuzzleHttp\Client();
        $response = $request = null;
        try {
            $request = $http_client->request(
                $method,
                parametroTable::getParametroValue("host_api_trading_internal") . "/trading/int" . $url,
                [
                    'json' => $data
                    //    "headers"=>["X-Consumer-Username"=>"XDEBUG_SESSION=netbeans-xdebug"]
                ]
            );
            $response = $request->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()) {
                throw $e;
                throw new Exception(
                    json_decode(
                        $e
                            ->getResponse()
                            ->getBody()
                            ->getContents()
                    )
                );
            } else {
                throw $e;
            }
        }
        return json_decode($response, true);
    }

    public static function trading_getPrice($currency_base, $currency_dst, $category, $priceType)
    {
        $json = self::callTrading(
            'GET',
            '/currency/' . $currency_base . '/' . $currency_dst . '/' . $category . '/' . $priceType
        );
        //        var_dump($json);
        //        $price['BID']=floatval($json['BID']);
        //        $price['ASK']=floatval($json['ASK']);
        return $json;
    }

    public static function user_getSecurityChallenge($request, $msTransId)
    {
        $http_client = new \GuzzleHttp\Client(["base_uri" => $GLOBALS["fmt_api_user_url_int"]]);
        $username = $request->getHttpHeader("X-CONSUMER-USERNAME");
        $response = $new_request = null;

        $domain = '172.17.0.1';
        $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

        try {
            $new_request = $http_client->request('POST', '/user/int/security', [
                'json' => [
                    "microservice" => "remittance",
                    "msTransId" => $msTransId
                ],
                "headers" => [
                    "accept-language" => $request->getHttpHeader("accept-language"),
                    "x-consumer-username" => $request->getHttpHeader("x-consumer-username"),
                    "fastoken" => $request->getHttpHeader("fastoken")
                ],
                "cookies" => $cookieJar
            ]);
            $response = $new_request->getBody()->getContents();
            return json_decode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()) {
                $msg = json_decode(
                    $e
                        ->getResponse()
                        ->getBody()
                        ->getContents()
                );
                throw new Exception($msg->description);
            } else {
                throw $e;
            }
        }
    }

    public static function user_respondSecurityChallenge($request, $msTransId, $answer)
    {
        $http_client = new \GuzzleHttp\Client(["base_uri" => $GLOBALS["fmt_api_user_url_int"]]);
        $username = $request->getHttpHeader("X-CONSUMER-USERNAME");
        $response = $new_request = null;

        $domain = '172.17.0.1';
        $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

        try {
            $new_request = $http_client->request('PUT', '/user/int/security', [
                'json' => [
                    "username" => $username,
                    "msTransId" => $msTransId,
                    "answer" => $answer
                ],
                "headers" => [
                    "accept-language" => $request->getHttpHeader("accept-language"),
                    "x-consumer-username" => $request->getHttpHeader("x-consumer-username")
                ],
                "cookies" => $cookieJar
            ]);
            $response = $new_request->getBody()->getContents();
            return json_decode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()) {
                $msg = json_decode(
                    $e
                        ->getResponse()
                        ->getBody()
                        ->getContents()
                );
                throw new Exception($msg->description);
            } else {
                throw $e;
            }
        }
    }

    public static function user_checkPermissionPerRoute($request)
    {
        $http_client = new \GuzzleHttp\Client(["base_uri" => $GLOBALS["fmt_api_user_url_int"]]);
        $response = $new_request = null;

        $domain = '172.17.0.1';
        $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

        try {
            $new_request = $http_client->request('POST', '/user/int/checkCredentials', [
                'json' => [
                    "username" => $request->getHttpHeader("x-consumer-username"),
                    "microservice" => \getenv("SELF_MS_NAME"),
                    "method" => $request->getMethod(),
                    "route" => $request->getPathInfo(),
                    "actionName" => $request->getParameter("action"),
                    "Fastoken" => $request->getHttpHeader("FASTOKEN"),
                    "User-Agent" => $request->getHttpHeader("User-Agent"),
                    "platform" => $request->getHttpHeader("platform"),
                    "x-real-ip" => $request->getHttpHeader("x-real-ip")
                ],
                "cookies" => $cookieJar
            ]);
            $response = $new_request->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception('UNAUTOHRIZED', 403);
        }
    }
}
