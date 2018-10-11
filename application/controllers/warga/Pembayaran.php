<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct(){
		parent::__construct();


		// $_SESSION["id"];

		// // cek jika bukan warga, maka tidak bisa akses
		// $user = $this->ion_auth->user()->row();
		// $user_groups = $this->ion_auth->get_users_groups($user->id)->result();
		// // print_r($user_groups[0]->name);
		// if ( $user_groups[0]->name != "warga" ) {
		// 	redirect('/');
		// }

		$this->load->model('pembayaran_model', 'pembayaran');
		$this->load->library('upload');
	}

	public function index()
	{
		$bulan[0] = "Januari";
		$bulan[1] = "Februari";
		$bulan[2] = "Maret";
		$bulan[3] = "April";
		$bulan[4] = "Mei";
		$bulan[5] = "Juni";
		$bulan[6] = "Juli";
		$bulan[7] = "Agustus";
		$bulan[8] = "September";
		$bulan[9] = "Oktober";
		$bulan[10] = "November";
		$bulan[11] = "Desember";

		$data["bulan"] = $bulan;
		$data["pembayaran"] = $this->pembayaran->get();
		$this->load->view('admin/warga/Pembayaran/index', $data);
	}

	public function tambah(){
		$this->load->view('admin/warga/pembayaran/tambah');
	}

	public function simpan(){
		// jika ada gambar yang diupload
		if ($_FILES["foto_bukti"]['name'] !== "") {
			$ext = pathinfo($_FILES["foto_bukti"]['name'], PATHINFO_EXTENSION);
			$filename = time().".".$ext;
			$this->upload_image("foto_bukti", $filename);
			$_POST["foto_bukti"] = $filename;	
		}

		// simpan data ke database
		$this->pembayaran->save($_POST);

		echo "<script>
		alert('Data pembayaran berhasil ditambahkan!');
		window.location.href='".base_url('warga/pembayaran')."';
		</script>";
	}

	public function edit($id){
		$data["pembayaran"] = $this->pembayaran->get_by_id($id);		
		$this->load->view('admin/warga/pembayaran/edit', $data);
	}

	public function update($id){
		
		if ( $_FILES["foto"]["name"] !== "" ) {	// jika ada gambar
			$ext = pathinfo($_FILES["foto"]['name'], PATHINFO_EXTENSION);
			$filename = time().".".$ext;
			$this->upload_image("foto", $filename);
			$_POST["foto"] = $filename;		
		}

		$this->pembayaran->update($_POST, $id);

		echo "<script>
			alert('Data pembayaran berhasil diubah!');
			window.location.href='".base_url('warga/pembayaran')."';
			</script>";
	}

	public function hapus($id){
		$this->pembayaran->delete($id);

		echo "<script>
			alert('Data pembayaran berhasil dihapus!');
			window.location.href='".base_url('warga/pembayaran')."';
			</script>";
	}

	function upload_image($fileinput, $filename){
		$config['file_name'] = $filename;
        $config['upload_path'] = './assets/gambar/'; //path folder
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp'; //type yang dapat diakses bisa anda sesuaikan

        // $config['encrypt_name'] = TRUE; //Enkripsi nama yang terupload
 
        $this->upload->initialize($config);
        if(!empty($_FILES[$fileinput]['name'])){
 
            if ($this->upload->do_upload($fileinput)){
                $gbr = $this->upload->data();
                //Compress Image
                $config['image_library']='gd2';
                $config['source_image']='./assets/gambar/'.$gbr['file_name'];
                $config['create_thumb']= FALSE;
                $config['maintain_ratio']= TRUE;
                $config['quality']= '50%';
                $config['width']= 600;
                $config['height']= 400;
                $config['new_image']= './assets/gambar/'.$gbr['file_name'];
                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
 
                // $gambar=$gbr['file_name'];
                // $judul=$this->input->post('xjudul');
                // $this->m_upload->simpan_upload($judul,$gambar);
                // echo "Image berhasil diupload";
                return $filename;
            } else{

				$error = array('error' => $this->upload->display_errors());
				echo "<pre>";
				print_r($error);            	
            }
                      
        }else{
            // echo "Image yang diupload kosong";
            return "error";
        }
                 
    }
}
