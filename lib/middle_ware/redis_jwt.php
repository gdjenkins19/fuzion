<?PHP 

namespace Middleware;

class RedisJwt {

    public function __invoke(\Request &$request) {

        $jwt = $request->getAttribute('jwt');
        $jti = $jwt->jti;
        
        //call redis and revoke if jwt id is there
        if($jti === 0) {
            throw new Response(401,'JWT Token has been blacklisted.');
        }
    }
    
}