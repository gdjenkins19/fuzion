<?PHP 

namespace Controllers;

use \Response;

trait DefaultGet {
    public function GET( \Request $request, $args) {
        $this->authorize($request, 1);

        try {
            $params = $request->getParams($args);
            $data = $this->model->read($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }

        throw new Response(200,NULL,$data);        
    }
}

trait DefaultPut {
    public function PUT($request, $args) {
        $this->authorize($request, 2);
        
        try {
            $params = $request->getParams($args);
            $data = $this->model->update($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }
        
        throw new Response(200,NULL,$data);        
    }        
}

trait DefaultPost {
    public function POST($request, $args) {
        $this->authorize($request, 2);
        
        try {
            $params = $request->getParams($args);
            $data = $this->model->create($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }
        
        throw new Response(200,NULL,$data);        
    }    
}

trait DefaultDelete {
    public function DELETE($request, $args) {
        $this->authorize($request, 2);
        
        try {
            $params = $request->getParams($args);
            $data = $this->model->delete($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }
        
        throw new Response(200,NULL,$data);        
    }    
}