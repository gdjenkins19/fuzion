<?PHP 

namespace Controllers;

use \Response;

trait DefaultGet {
    public function GET($args) {
        try {
            $params = $this->request->getParams($args);
            $data = $this->model->read($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }

        throw new Response(200,NULL,$data);        
    }
}

trait DefaultPut {
    public function PUT($args) {
        try {
            $params = $this->request->getParams($args);
            $data = $this->model->update($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }
        
        throw new Response(200,NULL,$data);        
    }        
}

trait DefaultPost {
    public function POST($args) {
        try {
            $params = $this->request->getParams($args);
            $data = $this->model->create($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }
        
        throw new Response(200,NULL,$data);        
    }    
}

trait DefaultDelete {
    public function DELETE($args) {
        try {
            $params = $this->request->getParams($args);
            $data = $this->model->delete($params);
        } catch (\Exception $e) {
            throw new Response(500,'Controller: '.$e->getMessage(),$args);
        }
        
        throw new Response(200,NULL,$data);        
    }    
}