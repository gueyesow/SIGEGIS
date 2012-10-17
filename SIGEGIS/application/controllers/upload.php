<?php

class Upload extends CI_Controller {
	
	function Upload()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
	}
	
	function index()
	{	
		$this->load->view('upload_form', array('error' => ' ' ));
	}

	function do_upload()
	{		
		$config['upload_path'] = dirname(BASEPATH).'/assets/uploads/';
		$config['allowed_types'] = 'csv';
		$config['overwrite'] = TRUE;
		
		$this->load->library('upload', $config);
	
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			
			$this->load->view('upload_form', $error);
		}	
		else
		{
			$data = array('upload_data' => $this->upload->data());
			
			$this->load->view('upload_success', $data);
		}
	}	
}
?>