<?php
namespace isoft\fmtsf4;

class internalMicroServices
{
    public static function callComm($url, $data = array())
    {
        try {
            $http_client = new \GuzzleHttp\Client();
            $response = $request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $request = $http_client->request('POST', \getenv("API_COMM_URI") . "/comm/int" . $url, [
                'json' => $data,
                'cookies' => $cookieJar
            ]);
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
        try {
            $http_client = new \GuzzleHttp\Client();
            $response = $request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $request = $http_client->request($method, \getenv("API_TRADING_URI") . "/trading/int" . $url, [
                'json' => $data,
                'cookies' => $cookieJar
            ]);
            $response = $request->getBody()->getContents();
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
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $username = $request->getHttpHeader("X-CONSUMER-USERNAME");
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

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
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $username = $request->getHttpHeader("X-CONSUMER-USERNAME");
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

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
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            //Ignora Chequeo de Seguridad para Rutas Internas
            $routeArray = \explode("/", $request->getPathInfo());
            if ($routeArray[2] == "int") {
                return;
            }

            if (\get_class($request) == "Symfony\Component\HttpFoundation\Request") {
                $json = [
                    "username" => $request->headers->get("x-consumer-username"),
                    "microservice" => \getenv("SELF_MS_NAME"),
                    "method" => $request->getMethod(),
                    "route" => $request->getPathInfo(),
                    "actionName" => $request->attributes->get("_route"),
                    "User-Agent" => $request->headers->get("User-Agent"),
                    "platform" => $request->headers->get("platform"),
                    "x-forwarded-for" => $request->headers->get("x-forwarded-for")
                ];
                if ($request->headers->has("FASTOKEN")) {
                    $json["Fastoken"] = $request->headers->get("FASTOKEN");
                }
                if ($request->headers->has("adminToken")) {
                    $json["adminToken"] = $request->headers->get("ADMINTOKEN");
                }
            } else {
                $json = [
                    "username" => $request->getHttpHeader("x-consumer-username"),
                    "microservice" => \getenv("SELF_MS_NAME"),
                    "method" => $request->getMethod(),
                    "route" => $request->getPathInfo(),
                    "actionName" => $request->getParameter("action"),
                    "User-Agent" => $request->getHttpHeader("User-Agent"),
                    "platform" => $request->getHttpHeader("platform"),
                    "x-forwarded-for" => $request->getHttpHeader("x-forwarded-for")
                ];
                if ($request->getHttpHeader("FASTOKEN")) {
                    $json["Fastoken"] = $request->getHttpHeader("FASTOKEN");
                }
                if ($request->getHttpHeader("adminToken")) {
                    $json["adminToken"] = $request->getHttpHeader("ADMINTOKEN");
                } elseif (!isset($json["adminToken"])) {
                    $json["adminToken"] = $request->getAttribute("adminToken");
                }
            }

            $new_request = $http_client->request('POST', '/user/int/checkCredentials', [
                "json" => $json,
                "cookies" => $cookieJar
            ]);
            $response = $new_request->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception('UNAUTOHRIZED', 403);
        }
    }

    public static function user_getCategories($username)
    {
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $json = [
                "username" => $username
            ];

            $new_request = $http_client->request('POST', '/user/int/userCategories', [
                "json" => $json,
                "cookies" => $cookieJar
            ]);
            $response = \json_decode($new_request->getBody()->getContents(), true);
            return $response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception('ERROR GETTING CREDENTIALS', 500);
        }
    }
}
