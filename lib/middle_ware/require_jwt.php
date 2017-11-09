<?PHP 

namespace Middleware;

class RequireJwt {
    public function __invoke(\Request &$request) {

        $jwt = $request->getAttribute('jwt');

        if($jwt === NULL) {

            $header = $request->getAuthorizationHeader();
            
            if(!isset($header)) {
                throw new \Response(401,'Missing Authorization Header.');
            }

            if(!\preg_match('/^Bearer /',$header)) {
                throw new \Response(401,'Not a Bearer Authorization Header.');
            }

            $token_string = str_replace('Bearer ','', $header);

            try {
                $fuzion_jwt = FuzionJwt::Decode($token_string);
            } catch (Exception $e) {
                throw new \Response(401,'Invalid JWT: '.$e->getMessage());
            }

            $request->setAttribute('jwt',$fuzion_jwt);
        }
    }
}
