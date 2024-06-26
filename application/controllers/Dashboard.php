<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		date_default_timezone_set('Asia/Jakarta');

		$this->load->model('m_data');
		$this->load->helper('artikel');

		// cek session yang login, 
		// jika session status tidak sama dengan session telah_login, berarti admin belum login
		// maka halaman akan di alihkan kembali ke halaman login.
		if($this->session->userdata('status')!="telah_login"){
			redirect(base_url().'Login');
		}
	}

	public function index()
	{
		$data['jumlah_customer'] = $this->m_data->get_data('customer')->num_rows();
		$data['jumlah_admin'] = $this->m_data->get_data('admin')->num_rows();
		$data['jumlah_paket'] = $this->m_data->get_data('produk')->num_rows();
		$data['detailpembayaran'] = $this->m_data->get_data('detailpembayaran')->result();
		$this->load->view('dashboard/v_header',$data);
		$this->load->view('dashboard/v_index',$data);
		$this->load->view('dashboard/v_footer',$data);
	}


	public function keluar()
	{
		$this->session->sess_destroy();
		redirect(base_url().'login');
	}

	// CRUD CUSTOMER
	public function customer()
	{
		$data['customer'] = $this->m_data->get_data('customer')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_customer',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function customer_edit($id)
	{
		$where = array(
			'customer_id' => $id
		);
		$data['customer'] = $this->m_data->edit_data($where,'customer')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_customer_edit',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function customer_update()
	{
		$this->form_validation->set_rules('nama','Nama','required');
		$this->form_validation->set_rules('email','email','required');
		$this->form_validation->set_rules('hp','hp','required');
		$this->form_validation->set_rules('alamat','alamat','required');

		if($this->form_validation->run() != false){

			$id = $this->input->post('id');
			$nama = $this->input->post('nama');
			$email = $this->input->post('email');
			$hp = $this->input->post('hp');
			$alamat = $this->input->post('alamat');
			$password = $this->input->post('password');
			$lokasirsp = $this->input->post('lokasirsp');
			$kota = $this->input->post('kota');
			$paket= $this->input->post('paket');
			$status= $this->input->post('status');

			$where = array(
				'customer_id' => $id
			);
			if($this->input->post('password') == ""){
				$data = array(
					'customer_nama' => $nama,
					'customer_email' => $email,
					'customer_hp' => $hp,
					'customer_alamat' => $alamat,
					'customer_lokasirsp' => $lokasirsp,
					'customer_kota' => $kota,
					'customer_paket' => $paket,
					'customer_status' => $status,
				);
			}else{
				$data = array(
					'customer_nama' => $nama,
					'customer_email' => $email,
					'customer_hp' => $hp,
					'customer_alamat' => $alamat,
					'customer_password' => md5($password),
					'customer_lokasirsp' => $lokasirsp,
					'customer_kota' => $kota,
					'customer_paket' => $paket,
					'customer_status' => $status,
				);
			}

			$this->m_data->update_data($where, $data,'customer');

			redirect(base_url().'dashboard/customer?alert=berhasil');
			
		}else{

			$id = $this->input->post('id');
			$where = array(
				'customer_id' => $id
			);
			$data['customer'] = $this->m_data->edit_data($where,'customer')->result();
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_customer_edit',$data);
			$this->load->view('dashboard/v_footer');
		}
	}


	public function customer_hapus($id)
	{
		$where = array(
			'customer_id' => $id
		);

		$this->m_data->delete_data($where,'customer');

		redirect(base_url().'dashboard/customer?alert=hapus');
	}
	// END CRUD CUSTOMER



	// CRUD ARTIKEL
	public function paket()
	{
		$data['produk'] = $this->m_data->get_data('produk')->result();	
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_paket',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function paket_tambah()
	{
		$data['produk'] = $this->m_data->get_data('produk')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_paket_tambah',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function paket_aksi()
	{
		$this->form_validation->set_rules('nama','nama','required');
		$this->form_validation->set_rules('harga','harga','required');
		$this->form_validation->set_rules('keterangan','keterangan','required');

		if($this->form_validation->run() != false){
			$nama = $this->input->post('nama');
			$harga = $this->input->post('harga');
			$keterangan = $this->input->post('keterangan');
			$foto_paket = $_FILES["foto"] ["tmp_name"];

			$path = "/img/paket_produk/";
			$imagePath = $path . $nama. "_gambar.jpg";
			move_uploaded_file($foto_paket, $imagePath);

			$data = array(
				'produk_nama' => $nama,
				'produk_harga' => $harga,
				'produk_keterangan' => $keterangan,
				'produk_foto' => $imagePath,
			);

			$this->m_data->insert_data($data,'produk');

			redirect(base_url().'dashboard/paket?alert=tambah');	

		}else{
			$data['produk'] = $this->m_data->get_data('produk')->result();
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_paket_tambah',$data);
			$this->load->view('dashboard/v_footer');
		}
	}


	public function paket_edit($id)
	{
		$where = array(
			'produk_id' => $id
		);
		$data['produk'] = $this->m_data->edit_data($where,'produk')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_paket_edit',$data);
		$this->load->view('dashboard/v_footer');
	}


	public function paket_update()
	{
		// Wajib isi judul,konten dan kategori
		$this->form_validation->set_rules('nama','nama','required');
		$this->form_validation->set_rules('harga','harga','required');
		$this->form_validation->set_rules('keterangan','keterangan','required');

		if($this->form_validation->run() != false){

			$id = $this->input->post('id');
			$nama = $this->input->post('nama');
			$harga = $this->input->post('harga');
			$keterangan = $this->input->post('keterangan');
			$foto_paket = $_FILES["foto"] ["tmp_name"];

			$path = "img/paket_produk/";
			$imagePath = $path . $nama. "_gambar.jpg";
			move_uploaded_file($foto_paket, $imagePath);

			$where = array(
				'produk_id' => $id
			);

			$data = array(
				'produk_nama' => $nama,
				'produk_harga' => $harga,
				'produk_keterangan' => $keterangan,
				'produk_foto' => $imagePath,
			);

			$this->m_data->update_data($where,$data,'produk');

			redirect(base_url().'dashboard/paket?alert=berhasil');

		}else{
			$id = $this->input->post('id');
			$where = array(
				'produk_id' => $id
			);
			$data['produk'] = $this->m_data->edit_data($where,'produk')->result();
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_paket_edit',$data);
			$this->load->view('dashboard/v_footer');
		}
	}

	public function paket_hapus($id)
	{
		$where = array(
			'produk_id' => $id
		);

		$produk = $this->m_data->edit_data($where,'produk')->row();
		
		@chmod('./gambar/produk/'.$produk->produk_foto1, 0777);
		@unlink('./gambar/produk/'.$produk->produk_foto1);

		@chmod('./gambar/produk/'.$produk->produk_foto2, 0777);
		@unlink('./gambar/produk/'.$produk->produk_foto2);

		@chmod('./gambar/produk/'.$produk->produk_foto3, 0777);
		@unlink('./gambar/produk/'.$produk->produk_foto3);

		$this->m_data->delete_data($where,'produk');
		redirect(base_url().'dashboard/paket?alert=hapus');
	}
	// end crud paket

	// CRUD admin
	public function admin()
	{
		$data['admin'] = $this->m_data->get_data('admin')->result();	
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_admin',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function admin_tambah()
	{
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_admin_tambah');
		$this->load->view('dashboard/v_footer');
	}

	public function admin_aksi()
	{
		// Wajib isi
		$this->form_validation->set_rules('nama','Nama admin','required');
		$this->form_validation->set_rules('username','Username admin','required');
		$this->form_validation->set_rules('password','Password admin','required|min_length[8]');

		if($this->form_validation->run() != false){

			$nama = $this->input->post('nama');
			$username = $this->input->post('username');
			$password = md5($this->input->post('password'));

			$data = array(
				'admin_nama' => $nama,
				'admin_username' => $username,
				'admin_password' => md5($password),
			);

			$this->m_data->insert_data($data,'admin');

			redirect(base_url().'dashboard/admin?alert=tambah');	

		}else{
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_admin_tambah');
			$this->load->view('dashboard/v_footer');
		}
	}

	public function admin_edit($id)
	{
		$where = array(
			'admin_id' => $id
		);
		$data['admin'] = $this->m_data->edit_data($where,'admin')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_admin_edit',$data);
		$this->load->view('dashboard/v_footer');
	}


	public function admin_update()
	{
		// Wajib isi
		$this->form_validation->set_rules('nama','Nama admin','required');
		$this->form_validation->set_rules('username','Username admin','required');

		if($this->form_validation->run() != false){
			$id = $this->input->post('id');
			$nama = $this->input->post('nama');
			$username = $this->input->post('username');
			$password = md5($this->input->post('password'));

			$where = array(
				'admin_id' => $id
			);

			if($this->input->post('password') == ""){
				$data = array(
					'admin_nama' => $nama,
					'admin_username' => $username
				);
			}else{
				$data = array(
					'admin_nama' => $nama,
					'admin_username' => $username,
					'admin_password' => $password
				);
			}

			$this->m_data->update_data($where,$data,'admin');

			redirect(base_url().'dashboard/admin?alert=berhasil');
		}else{
			$id = $this->input->post('id');
			$where = array(
				'admin_id' => $id
			);
			$data['admin'] = $this->m_data->edit_data($where,'admin')->result();
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_admin_edit',$data);
			$this->load->view('dashboard/v_footer');
		}
	}

	public function admin_hapus($id)
	{
		$where = array(
			'admin_id' => $id
		);

		$this->m_data->delete_data($where,'admin');

		redirect(base_url().'dashboard/admin?alert=hapus');
	}
	// end crud admin

	/* CRUD PESANAN */
	public function pesanan()
	{

		$data['customer'] = $this->m_data->get_data('customer')->result();
		$data['detailpembayaran'] = $this->m_data->get_data('detailpembayaran')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_pesanan',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function detailbayar($id)
	{	
		$where = array(
			'customer_id' => $id
		);

		$data['customer'] = $this->m_data->edit_data($where,'customer')->result();
		$data['produk'] = $this->m_data->get_data('produk')->result();
		$data['pembayaran'] = $this->m_data->edit_data($where,'pembayaran')->result();
		$data['detailpembayaran'] = $this->m_data->edit_data($where,'detailpembayaran')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_detailbayar',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function pembayaran_status()
	{
		$invoice  = $this->input->post('invoice');
		$status  = $this->input->post('status');
		$customerid  = $this->input->post('customerid');
		$where = array(
			'bayar_id' => $invoice,
		);
		$data = array(
			'status_bayar' => $status,
		);
		
		$this->m_data->update_data($where,$data,'pembayaran');

		redirect(base_url().'dashboard/detailbayar/'.$customerid);
	}
	
	public function nominal()
	{
		$customerid  = $this->input->post('customerid');
		$nominal  = $this->input->post('nominal');
		$where = array(
			'customer_id' => $customerid
		);

		if($this->input->post('jenisbayar') == "DP1"){
			$data = array(
				'dp1' => $nominal * 1000000,
			);
		}elseif($this->input->post('jenisbayar') == "DP2"){
			$data = array(
				'dp2' => $nominal * 1000000,
			);
		}elseif($this->input->post('jenisbayar') == "LNS"){
			$data = array(
				'pelunasan' => $nominal * 1000000,
			);
		}

		$this->m_data->update_data($where,$data,'detailpembayaran');

		redirect(base_url().'dashboard/detailbayar/'.$customerid);
	}

	public function detailpesan($id)
	{	
		$where = array(
			'customer_id' => $id
		);

		$data['customer'] = $this->m_data->edit_data($where,'customer')->result();
		$data['produk'] = $this->m_data->get_data('produk')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_detailpesan',$data);
		$this->load->view('dashboard/v_footer');
	}

	//** CRUD Laporan */

	public function laporan()
	{
		$data['customer'] = $this->m_data->get_data('customer')->result();
		$data['pembayaran'] = $this->m_data->get_data('pembayaran')->result();
		$data['detailpembayaran'] = $this->m_data->get_data('detailpembayaran')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_laporan',$data);
		$this->load->view('dashboard/v_footer');
	}

}
