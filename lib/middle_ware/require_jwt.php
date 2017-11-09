<?PHP 

namespace Middleware;

class RequireJwt {
    public function __invoke(\Request &$request) {

        $jwt = $request->getAttribute('jwt');

        if($jwt === NULL) {

            $header = $request->getAuthorizationHeader();
            
            if(!isset($header)) {
                throw new Response(401,'Missing Authorization Header.');
            }

            if(!\preg_match('/^Bearer /',$header)) {
                throw new Response(401,'Not a Bearer Authorization Header.');
            }

            $token_string = str_replace('Bearer ','', $header);

            try {
                $fuzion_jwt = new \Middleware\FuzionJwt($token_string);
            } catch (Exception $e) {
                throw new Response(401,'Invalid JWT: '.$e->getMessage());
            }

            $request->setAttribute('jwt',$fuzion_jwt);
        }
    }
}



    //$this->jwt = $this->decodeJwt($defaultTokenString);
    // protected function decodeJwt($defaultTokenString) {
    //     $jwt = NULL;

    //     $token_string = $defaultTokenString;

    //     if( isset($this->auth_header) && 
    //         !empty($this->auth_header) && 
    //         strpos($this->auth_header, 'Bearer ') === 0) {

    //         $token_string = str_replace('Bearer ','',$this->auth_header);
    //     }

    //     if(isset($token_string)) {
    //         try {
    //             $jwt = JWT::decode($token_string, 'THE_SECRET_KEY', array('HS256'));
    //         } catch (Exception $e) {
    //             throw new Response(401,'Request: '.$e->getMessage());
    //         }               
    //     } else {
    //         throw new Response(401,'Missing Bearer JWT Token.');
    //     }

    //     return $jwt;
    // }

    // public function isAuthorized($resource) {
    //     $required = ($this->getMethod() === 'GET') ? 1 : 2;
    //     $permissions = (array) ($this->jwt->resources);
    //     $max = isset($permissions['ALL']) ? $permissions['ALL'] : 0;
    //     $max = isset($permissions[$resource]) ? max([$max,$permissions[$resource]]) : $max;
    //     return $max >= $required;
    // }

