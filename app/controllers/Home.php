<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Home extends Controller {

    public function __construct() {
        parent::__construct();
        
        if(! logged_in()) {
            redirect('auth');
        }
    }

	public function index() {
        $this->call->view('homepage');
    }
}
?>
