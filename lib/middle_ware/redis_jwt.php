<?PHP 

namespace Middleware;

class RedisJwt {

    public function __invoke(\Request &$request) {

        $jwt = $request->getAttribute('jwt');
        $id = $jwt->getId();
        
        //call redis and revoke if jwt id is there
        if($jwt->getId() === 0) {
            throw new \Response(401,'JWT Token has been blacklisted.');
        }
    }
    
}