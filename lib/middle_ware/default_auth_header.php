<?PHP 

namespace Middleware;

class DefaultAuthorizationHeader {
    public function __invoke(\Request &$request) {
        $request->setDefaultAuthorizationHeader("some token");
    }
}