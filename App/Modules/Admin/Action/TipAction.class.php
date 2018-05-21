<?php 

class TipAction extends Action {
    
    public function error() {
        $this->msg = $_GET['msg'];
        $this->return_url = I('return_url');
        $this->display();
    }
}

 ?>