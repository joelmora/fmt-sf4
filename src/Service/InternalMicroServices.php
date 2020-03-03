<?php
namespace isoft\fmtsf4\Service;
use isoft\fmtsf4\Helpers\WebSocketsTrait;

class InternalMicroServices
{
    use WebSocketsTrait;

    private static function monitorInternalRequest($dstService, $url, $uniqid, $stage)
    {
        $wsClient = WebSocketsTrait::startClientWS();
        $json = [
            "uniqid" => $uniqid,
            "from" => \getenv("SELF_MS_NAME"),
            "to" => $dstService,
            "microtime" => microtime(1),
            "stage" => $stage,
            "url" => $url
        ];
        WebSocketsTrait::logClientWS($wsClient, [
            's' => [
                'event' => '/internalRequests'
            ],
            'd' => [
                "data" => $json
            ]
        ]);
    }

    public static function callComm($url, $data = array())
    {
        try {
            $http_client = new \GuzzleHttp\Client();
            $response = $request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $uniqid = \uniqid();
            self::monitorInternalRequest("COMM", $url, $uniqid, "REQUEST");

            $request = $http_client->request('POST', \getenv("API_COMM_URI") . "/comm/int" . $url, [
                'json' => $data,
                'cookies' => $cookieJar
            ]);

            self::monitorInternalRequest("COMM", $url, $uniqid, "RESPONSE");

            $response = $request->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("COMM", $url, $uniqid, "ERROR");

            if ($e->getResponse()) {
                $msg = json_decode(
                    $e
                        ->getResponse()
                        ->getBody()
                        ->getContents()
                );
                throw new \Exception($msg->message);
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

            $uniqid = \uniqid();
            self::monitorInternalRequest("TRADING", $url, $uniqid, "REQUEST");

            $request = $http_client->request($method, \getenv("API_TRADING_URI") . "/trading/int" . $url, [
                'json' => $data,
                'cookies' => $cookieJar
            ]);

            self::monitorInternalRequest("TRADING", $url, $uniqid, "RESPONSE");

            $response = $request->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("TRADING", $url, $uniqid, "ERROR");

            if ($e->getResponse()) {
                $msg = json_decode(
                    $e
                        ->getResponse()
                        ->getBody()
                        ->getContents()
                );
                throw new \Exception($msg->description);
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
        $url = '/user/int/security';
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $username = $request->getHttpHeader("X-CONSUMER-USERNAME");
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $uniqid = \uniqid();
            self::monitorInternalRequest("USER", $url, $uniqid, "REQUEST");

            $new_request = $http_client->request('POST', $url, [
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

            self::monitorInternalRequest("USER", $url, $uniqid, "RESPONSE");

            $response = $new_request->getBody()->getContents();
            return json_decode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("USER", $url, $uniqid, "ERROR");

            if ($e->getResponse()) {
                $msg = json_decode(
                    $e
                        ->getResponse()
                        ->getBody()
                        ->getContents()
                );
                throw new \Exception($msg->description);
            } else {
                throw $e;
            }
        }
    }

    public static function user_respondSecurityChallenge($request, $msTransId, $answer)
    {
        $url = '/user/int/security';
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $username = $request->getHttpHeader("X-CONSUMER-USERNAME");
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $uniqid = \uniqid();
            self::monitorInternalRequest("USER", $url, $uniqid, "REQUEST");

            $new_request = $http_client->request('PUT', $url, [
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

            self::monitorInternalRequest("USER", $url, $uniqid, "RESPONSE");

            $response = $new_request->getBody()->getContents();
            return json_decode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("USER", $url, $uniqid, "ERROR");

            if ($e->getResponse()) {
                $msg = json_decode(
                    $e
                        ->getResponse()
                        ->getBody()
                        ->getContents()
                );
                throw new \Exception($msg->description);
            } else {
                throw $e;
            }
        }
    }

    public static function user_checkPermissionPerRoute($request)
    {
        $wsClient = WebSocketsTrait::startClientWS();

        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            //Ignora Chequeo de Seguridad para Rutas Internas
            $routeArray = \explode("/", $request->getPathInfo());
            if ($routeArray[2] == "int" or $routeArray[2] == "pub") {
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
                    $json["app"] = "frontend";
                }
                if ($request->headers->has("adminToken")) {
                    $json["adminToken"] = $request->headers->get("ADMINTOKEN");
                    $json["app"] = "backend";
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
                    $json["app"] = "frontend";
                }
                if ($request->getHttpHeader("adminToken")) {
                    $json["adminToken"] = $request->getHttpHeader("ADMINTOKEN");
                } elseif (!isset($json["adminToken"])) {
                    $json["adminToken"] = $request->getAttribute("adminToken");
                    $json["app"] = "backend";
                }
            }

            $json["seq"] = \uniqid();

            $new_request = $http_client->request('POST', '/user/int/checkCredentials', [
                "json" => $json,
                "cookies" => $cookieJar
            ]);
            $response = $new_request->getBody()->getContents();

            unset($json["User-Agent"]);
            unset($json["adminToken"]);
            unset($json["fastoken"]);
            $json["response"] = 200;
            WebSocketsTrait::logClientWS($wsClient, [
                's' => [
                    'event' => '/internalRequests'
                ],
                'd' => [
                    "data" => $json
                ]
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            unset($json["User-Agent"]);
            unset($json["adminToken"]);
            unset($json["fastoken"]);
            $json["response"] = 403;
            WebSocketsTrait::logClientWS($wsClient, [
                's' => [
                    'event' => '/internalRequests'
                ],
                'd' => [
                    "data" => $json
                ]
            ]);
            throw new \Exception('UNAUTOHRIZED', 403);
        }
    }

    public static function user_getCategories($username)
    {
        $url = '/user/int/userCategories';
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $json = [
                "username" => $username
            ];

            $uniqid = \uniqid();
            self::monitorInternalRequest("USER", $url, $uniqid, "REQUEST");

            $new_request = $http_client->request('POST', $url, [
                "json" => $json,
                "cookies" => $cookieJar
            ]);

            self::monitorInternalRequest("USER", $url, $uniqid, "RESPONSE");

            $response = \json_decode($new_request->getBody()->getContents(), true);
            return $response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("USER", $url, $uniqid, "ERROR");
            throw new \Exception('ERROR GETTING CREDENTIALS', 500);
        }
    }

    public static function user_getUser($username)
    {
        $url = '/user/int/user/';
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $json = [
                "username" => $username
            ];

            $uniqid = \uniqid();
            self::monitorInternalRequest("USER", $url, $uniqid, "REQUEST");

            $new_request = $http_client->request('GET', $url . $username, [
                "cookies" => $cookieJar
            ]);

            self::monitorInternalRequest("USER", $url, $uniqid, "RESPONSE");

            $response = \json_decode($new_request->getBody()->getContents(), true);
            return $response['data'];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("USER", $url, $uniqid, "ERROR");
            throw new \Exception('ERROR GETTING USER DATA', 500);
        }
    }

    public static function user_getCategoryUsers($categoryName)
    {
        $url = '/user/int/categories/' . $categoryName . "/users";
        try {
            $http_client = new \GuzzleHttp\Client(["base_uri" => \getenv("API_USER_URI")]);
            $response = $new_request = null;

            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $uniqid = \uniqid();
            self::monitorInternalRequest("USER", $url, $uniqid, "REQUEST");

            $new_request = $http_client->request('GET', $url, [
                "cookies" => $cookieJar
            ]);

            self::monitorInternalRequest("USER", $url, $uniqid, "RESPONSE");

            $response = \json_decode($new_request->getBody()->getContents(), true);
            return $response['data'];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("USER", $url, $uniqid, "ERROR");
            throw new \Exception('ERROR GETTING USER DATA', 500);
        }
    }

    //Sends mail
    public static function comm_send_email($ms, $to, $subject, $content, $data, $block = null)
    {
        $url = '/comm/int/mail';
        //Prepares request body
        $body = [
            'microservice' => $ms,
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
            'data' => $data,
            'block' => $block
        ];
        //Prepares client
        $client = new \GuzzleHttp\Client(['base_uri' => getenv('API_COMM_URI')]);

        //Sends request
        try {
            $domain = '172.17.0.1';
            $values = ['XDEBUG_SESSION' => 'netbeans-xdebug'];
            $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($values, $domain);

            $uniqid = \uniqid();
            self::monitorInternalRequest("COMM", $url, $uniqid, "REQUEST");

            $request = $client->request('POST', $url, [
                'body' => json_encode($body),
                'headers' => ['Accept-Language' => 'ES', 'Content-Type' => 'application/json'],
                'cookies' => $cookieJar
            ]);

            self::monitorInternalRequest("COMM", $url, $uniqid, "RESPONSE");
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            self::monitorInternalRequest("COMM", $url, $uniqid, "ERROR");
            throw new \Exception('ERROR SENDING EMAIL', 500);
        }
    }
}
