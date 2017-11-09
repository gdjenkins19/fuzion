<?PHP 

namespace Middleware;

class RedisJwt {

    public function __invoke(\Request &$request) {

        $jwt = $request->getAttribute('jwt');
        $jid = $jwt->jid;
        
        //call redis and revoke if jwt id is there
        if($jid === '9999') {
            throw new Response(401,'JWT Token has been revoked.');
        }
    }
    
}