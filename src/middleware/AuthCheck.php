<?php
    namespace dackou\middleware;

    use Webman\MiddlewareInterface;
    use Webman\Http\Response;
    use Webman\Http\Request;
    use dackou\service\Token\TokenService;

    class AuthCheck implements MiddlewareInterface
    {
        public function process(Request $request, callable $handler) : Response
        {
            // var_dump('generate new token: ',TokenService::generateNewToken($request));
            
            $obj = TokenService::checkToken($request);
            if($obj === true || is_object($obj)){
                // 请求继续向洋葱芯穿越
                return $handler($request);
            }

            return \dackou\Json::show($obj);
        }
    }
?>