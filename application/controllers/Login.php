<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		date_default_timezone_set('Asia/Jakarta');

		// maka halaman akan di alihkan kembali ke halaman login.
		if($this->session->userdata('status')=="telah_login"){
			redirect(base_url('/dashboard'));
		}
		
	}

	public function index()
	{
		$this->load->view('v_login');
	}

	public function aksi()
	{

		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if($this->form_validation->run() != false){

			// menangkap data username dan password dari halaman login
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			$where = array(
				'admin_username' => $username,
				'admin_password' => md5($password)
			);

			$this->load->model('m_data');

			// cek kesesuaian login pada table admin
			$cek = $this->m_data->cek_login('admin',$where)->num_rows();

			// cek jika login benar
			if($cek > 0){

				// ambil data admin yang melakukan login
				$data = $this->m_data->cek_login('admin',$where)->row();

				// buat session untuk admin yang berhasil login
				$data_session = array(
					'id' => $data->admin_id,
					'username' => $data->admin_username,
					'status' => "telah_login"
				);
				$this->session->set_userdata($data_session);

				// alihkan halaman ke halaman dashboard admin

				redirect(base_url().'dashboard');
			}else{
				// menangkap data username dan password dari halaman login
				$username = $this->input->post('username');
				$password = $this->input->post('password');

				$where = array(
				'customer_email' => $username,
				'customer_password' => md5($password)
				);

				$this->load->model('m_data');

				// cek kesesuaian login pada table customer
				$cek = $this->m_data->cek_login('customer',$where)->num_rows();
				if($cek > 0){

					// ambil data admin yang melakukan login
					$data = $this->m_data->cek_login('customer',$where)->row();
	
					// buat session untuk customer yang berhasil login
					$data_session = array(
						'id' => $data->admin_id,
						'username' => $data->customer_email,
						'status' => "telah_login"
					);
					$this->session->set_userdata($data_session);
	
					// alihkan halaman ke halaman dashboard customer
	
					redirect(base_url().'dashboard/cust');
				}else{
				redirect(base_url().'login');
				}
			}

		}else{
			$this->load->view('v_login');
			
		}
	}
}