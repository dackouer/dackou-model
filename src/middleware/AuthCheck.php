<?php
    namespace dackou\middleware;

    use Webman\MiddlewareInterface;
    use Webman\Http\Response;
    use Webman\Http\Request;

    class AuthCheck implements MiddlewareInterface
    {
        public function process(Request $request, callable $handler) : Response
        {
            // return $handler($request);
            // var_dump('generate new token: ',\dackou\Token::generateToken($request));
            $obj = \dackou\Token::checkToken($request);
            if($obj === true || is_object($obj)){
                // 请求继续向洋葱芯穿越
                return $handler($request);
            }

            return \dackou\Json::show($obj);
        }
    }
?>