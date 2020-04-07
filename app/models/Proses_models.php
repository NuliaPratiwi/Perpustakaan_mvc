<?php  

class Proses_models extends Controller
{	
	private $db;
	public function __construct()
	{
		$this->db = new Database;
	}

	/* ------------------------------> Tambah <--------------------------------- */

	public function addbuku($data)
	{
		$file_max_weight = 1000000; //limmit gambar

        $ok_ext = array('jpg','png','gif','jpeg'); // gambar yang diterima

        $destination = "img/daftar-buku/"; // simpen dmana nantik

        $file = $_FILES['gambar'];


        $filename = explode(".", $file["name"]); 


        $file_name = $file['name']; // nama asli gambar


        $file_name_no_ext = isset($filename[0]) ? $filename[0] : null; 

        $file_extension = $filename[count($filename)-1];

        $file_weight = $file['size'];

        $file_type = $file['type'];

        if ($file['error'] == 0 ) {

            if (in_array($file_extension, $ok_ext)) {
               if( $file_weight <= $file_max_weight ){
                   $fileNewName =  $file_name_no_ext[0].microtime().'.'.$file_extension ;

                   date_default_timezone_set('Asia/Kuala_Lumpur');
                   $date = date('Y-m-d');
                        // pindahin ke folder baru
                   if( move_uploaded_file($file['tmp_name'], $destination.$fileNewName) ){
                        // masukkan data ke database 

                      $query = "INSERT INTO tb_buku VALUES ('', :nama_buku, :pengarang, :id_kategori, :deskripsi, :gambar, :jumlah_buku, :tanggal_masuk ,:kondisi_buku)";
                      try{
                        $this->db->query($query);
                        $this->db->bind('nama_buku', $data['nama'] );
                        $this->db->bind('pengarang', $data['pengarang'] );
                        $this->db->bind('id_kategori', $data['kategori'] );
                        $this->db->bind('deskripsi', $data['deskripsi'] );
                        $this->db->bind('jumlah_buku', $data['jumlah_buku'] );
                        $this->db->bind('tanggal_masuk', $date);
                        $this->db->bind('kondisi_buku', $data['kondisi_buku'] );
                        $this->db->bind('gambar', $fileNewName);

                        $this->db->execute();
                            // return $this->db->rowCount();
                        return ['status' => true];
                    } catch (PDOException $e) {
                      return ['status' => false, 'msg' => $e->getMessage()];
                  }
              }else{
                $error = "File melebihi Kapasitas"; 
                var_dump($error);die;
            }
        }else{
            $error = "File melebihi Kapasitas"; 
            var_dump($error);die;
        }
    }else {
        $error = "Extensi Gambar salah"; 
        var_dump($error);die;
    }

}
}

public function addkategori($data)
{
  $query = "INSERT INTO tb_kategori VALUES ('', :kategori, :kode)";
  try{
      $this->db->query($query);
      $this->db->bind('kategori', $data['kategori'] );
      $this->db->bind('kode', $data['kode'] );

      $this->db->execute();
		// return $this->db->rowCount();
      return ['status' => true];
  } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function addUser($data)
{
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $date = date('Y-m-d');
    $username = strtolower(stripcslashes($data['username']));
    $nama = stripcslashes($data['nama']);
    $password = $data['password'];
    $password_konf = $data['password_konf'];

    //untuk mengecek username
    $queryu = "SELECT username FROM auth WHERE username = '$username'";
    $this->db->query($queryu);
    $cek_username = $this->db->single();

    if ($cek_username > 0) {
        Flasher::setFlash('Username Anda ','Sudah Terdaftar','error');
        header('Location: '. BASEURL . '/user/index');
        exit();
    }

    if ($password !== $password_konf) {
       Flasher::setFlash('Password Anda ','Harus Sama','error');
       header('Location: '. BASEURL . '/user/index');
       exit();
   }

   $query =  "INSERT INTO auth VALUES ('',:nama , :nis, :kelas, :username, :password, :id_level, :id_jurusan, '$date')";
   try{
    $this->db->query($query);
    $this->db->bind('nama',  $nama);
    $this->db->bind('nis',  $data['nis']);
    $this->db->bind('kelas',  $data['kelas']);
    $this->db->bind('username',  $username);
    $this->db->bind('password',  password_hash($password, PASSWORD_DEFAULT));
    $this->db->bind('id_level',  $data['level']);
    $this->db->bind('id_jurusan',  $data['jurusan']);

    $this->db->execute();
        // return $this->db->rowCount();
    return ['status' => true];
} catch (PDOException $e) {
  return ['status' => false, 'msg' => $e->getMessage()];
} 
}

public function addJurusan($data)
{
    $query = "INSERT INTO tb_jurusan VALUES ('', :jurusan)";
    try{
        $this->db->query($query);
        $this->db->bind('jurusan', $data['jurusan'] );

        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function addPinjam($data)
{
    $id = $data['buku'];

    date_default_timezone_set('Asia/Kuala_Lumpur');
    $date = date('Y-m-d');
    $waktu = $data['pinjam'];
    $sampai = mktime(0,0,0,date("n"),date("j")+$waktu, date("Y"));
    $kembali = date("Y-m-d", $sampai);
    $query = "INSERT INTO tb_pinjam VALUES ('', :id_auth, :id_buku, '$date', '$kembali', :lama_pinjam, '')";
    try{
        $this->db->query($query);
        $this->db->bind('id_auth', $data['nama']);
        $this->db->bind('id_buku', $data['buku']);
        $this->db->bind('lama_pinjam', $data['pinjam']);
        $this->db->execute();
            // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 


}

/* ------------------------------> Hapus <--------------------------------- */

public function hapus_buku($id)
{
   $query = "SELECT * FROM tb_buku WHERE id_buku = :id";
   try{
    $this->db->query($query);
    $this->db->bind('id', $id);
    $data = $this->db->single();
    $destination = "img/daftar-buku/";
    unlink($destination.$data['gambar']);



    $query ="DELETE FROM tb_buku WHERE id_buku = :id";
    $this->db->query($query);
    $this->db->bind('id', $id);

    $this->db->execute();
    	// return $this->db->rowCount();
    return ['status' => true];
} catch (PDOException $e) {
  return ['status' => false, 'msg' => $e->getMessage()];
} 
}

public function hapus_user($id)
{
    $query =  "DELETE FROM auth WHERE id_auth = :id";
    try{
        $this->db->query($query);
        $this->db->bind('id', $id);

        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function hapus_kategori($id)
{
    $query =  "DELETE FROM tb_kategori WHERE id_kategori = :id";
    try{
        $this->db->query($query);
        $this->db->bind('id', $id);

        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function hapus_jurusan($id)
{
    $query =  "DELETE FROM tb_jurusan WHERE id_jurusan = :id";
    try{
        $this->db->query($query);
        $this->db->bind('id', $id);

        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function selesai_kembali($id)
{

    $query = "DELETE FROM tb_kembali WHERE id_kembali = $id";
    try{
        $this->db->query($query);
        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

/* ------------------------------> Edit <--------------------------------- */

public function editbuku($data)
{
    $isigambar = $_FILES['gambar'];
    if ($isigambar['error'] == 0) {
             // var_dump($data);die;
        $query = "SELECT * FROM tb_buku WHERE id_buku = :id";
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $dataambil = $this->db->single();

        $file_max_weight = 1000000; //limmit gambar

        $ok_ext = array('jpg','png','gif','jpeg'); // gambar yang diterima

        $destination = "img/daftar-buku/"; // simpen dmana nantik

        $file = $_FILES['gambar'];


        $filename = explode(".", $file['name']); 


        $file_name = $file['name']; // nama asli gambar


        $file_name_no_ext = isset($filename[0]) ? $filename[0] : null; 

        $file_extension = $filename[count($filename)-1];

        $file_weight = $file['size'];

        $file_type = $file['type'];


        unlink($destination.$dataambil['gambar']); //data gambar database
        if ($file['error'] == 0 ) {

            if (in_array($file_extension, $ok_ext)) {

               if( $file_weight <= $file_max_weight ){

                   $fileNewName =  $file_name_no_ext[0].microtime().'.'.$file_extension ;
                            // pindahin ke folder baru
                   if( move_uploaded_file($file['tmp_name'], $destination.$fileNewName) ){
                            // masukkan data ke database 
                      $query = "UPDATE tb_buku SET nama_buku =:nama_buku, pengarang =:pengarang, id_kategori =:id_kategori, deskripsi =:deskripsi, gambar =:gambar , jumlah_buku = :jumlah_buku, kondisi_buku = :kondisi_buku WHERE id_buku =:id_buku";
                      try{
                        $this->db->query($query);
                        $this->db->bind('nama_buku', $data["nama"]);
                        $this->db->bind('pengarang', $data["pengarang"]);
                        $this->db->bind('id_kategori', $data["kategori"]);
                        $this->db->bind('deskripsi', $data["deskripsi"]);
                        $this->db->bind('jumlah_buku', $data["jumlah_buku"]);
                        $this->db->bind('kondisi_buku', $data["kondisi_buku"]);
                        $this->db->bind('gambar', $fileNewName);
                        $this->db->bind('id_buku', $data["id"] );
                        $this->db->execute();
                                // return $this->db->rowCount();
                        return ['status' => true];
                    } catch (PDOException $e) {
                      return ['status' => false, 'msg' => $e->getMessage()];
                  }
              }else{
                $error = "File melebihi Kapasitas"; 
                var_dump($error);die;
            }
        }else{
            $error = "File melebihi Kapasitas"; 
            var_dump($error);die;
        }
    }else {
        $error = "Extensi Gambar salah"; 
        var_dump($error);die;
    }

}
}else{
    $query = "UPDATE tb_buku SET nama_buku =:nama_buku, pengarang =:pengarang, id_kategori =:id_kategori, deskripsi =:deskripsi, jumlah_buku = :jumlah_buku, kondisi_buku = :kondisi_buku WHERE id_buku =:id_buku";

    try{
        $this->db->query($query);
        $this->db->bind('nama_buku', $data["nama"]);
        $this->db->bind('pengarang', $data["pengarang"]);
        $this->db->bind('id_kategori', $data["kategori"]);
        $this->db->bind('deskripsi', $data["deskripsi"]);
        $this->db->bind('jumlah_buku', $data["jumlah_buku"]);
        $this->db->bind('kondisi_buku', $data["kondisi_buku"]);
        $this->db->bind('id_buku', $data["id"] );
        $this->db->execute();
            // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  }

}

}

public function edit_user($data)
{
    $query = "UPDATE auth SET nama = :nama, nis = :nis, kelas = :kelas, id_jurusan = :id_jurusan WHERE id_auth = :id_auth";
    try{
        $this->db->query($query);
        $this->db->bind('nama', $data['nama'] );
        $this->db->bind('nis', $data['nis'] );
        $this->db->bind('kelas', $data['kelas'] );
        $this->db->bind('id_jurusan', $data['jurusan'] );
        $this->db->bind('id_auth', $data['id']);

        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function edit_kategori($data)
{
    $query = "UPDATE tb_kategori SET kategori = :kategori, kode = :kode WHERE id_kategori = :id_kategori";
    try{
        $this->db->query($query);
        $this->db->bind('kategori', $data['kategori']);
        $this->db->bind('kode', $data['kode']);
        $this->db->bind('id_kategori', $data['id']);

        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function edit_jurusan($data)
{
    $query = "UPDATE tb_jurusan SET jurusan = :jurusan  WHERE id_jurusan = :id";
    try{
        $this->db->query($query);
        $this->db->bind('jurusan', $data['jurusan'] );
        $this->db->bind('id', $data['id']);

        $this->db->execute();
        // return $this->db->rowCount();
        return ['status' => true];
    } catch (PDOException $e) {
      return ['status' => false, 'msg' => $e->getMessage()];
  } 
}

public function selesai($id)
{
    $query = "SELECT * FROM tb_pinjam WHERE id_pinjam = $id";
    $this->db->query($query);
    $this->db->execute();
    $data = $this->db->single();

    $buku = $data['id_buku'];

    $query = "SELECT * FROM tb_buku WHERE id_buku = $buku";
    $this->db->query($query);
    $this->db->execute();
    $bukuall =  $this->db->single();

    $plus = $bukuall['jumlah_buku'] + 1;

    $date = date('Y-m-d');
    $lama_pinjam = $data['lama_pinjam'];
    $tanggal_pinjam = strtotime($data['tanggal_pinjam']);
    $tanggal_kembali = strtotime($data['tanggal_kembali']);
    $harus_kembali = strtotime($date);
    /*
    / ini sistem untuk kembali dan mengatur denda
    /
    /
    */
        // var_dump($tanggal_kembali);var_dump($harus_kembali);die;
    $selisih = $harus_kembali -  $tanggal_kembali ;
        // var_dump($selisih);die;
        $hitung_hari = floor($selisih/(60*60*24)); //20
        // var_dump($hitung_hari);die;
        $selisih2 = (abs($tanggal_pinjam - $tanggal_kembali));
        $sampai = floor($selisih2/(60*60*24)); //12
        if($hitung_hari > 0){
            $denda = 1000 * $hitung_hari;
        }else{
            $denda = 0;
        }

        // var_dump($denda);die;
        $id_auth = $data['id_auth'];
        $id_buku = $data['id_buku'];
        $id_pinjam = $data['tanggal_pinjam'];
        $id_kembali = $data['tanggal_kembali'];
        // var_dump($denda);die;
        $query = "INSERT INTO tb_kembali VALUES ('','$id_auth','$id_buku','$id_pinjam','$id_kembali','$denda')";
        try{
            $this->db->query($query);
            $this->db->execute();

            $queryD = "DELETE FROM tb_pinjam WHERE id_pinjam = $id";
            $this->db->query($queryD);
            $this->db->execute();

            $buku = $data['id_buku'];

            $query = "UPDATE tb_buku SET jumlah_buku = $plus WHERE id_buku = $buku";
            $this->db->query($query);
            $this->db->execute();
        // return $this->db->rowCount();
            return ['status' => true];
        } catch (PDOException $e) {
          return ['status' => false, 'msg' => $e->getMessage()];
      }    

  }



  public function ubah_pinjam($data)
  {   

         /**
         * 
         * 1. kita  select untuk update buku lama
         * 
         * 
         * 
         */

         $bukup = $data['id_bukulama'];
         $queryA = "SELECT * FROM tb_buku WHERE id_buku = $bukup";
         $this->db->query($queryA);
         $this->db->execute();
         $bukuP =  $this->db->single();



         /**
         * 
         * 2. kita kita select untuk update buku baru
         * 
         * 
         * 
         */
         $buku = $data['buku'];
         $queryB = "SELECT * FROM tb_buku WHERE id_buku = $buku";
         $this->db->query($queryB);
         $this->db->execute();
         $bukuall =  $this->db->single();


         /**
         * 
         * 1. kita update tb_pinjam jika ada nama buku tangggal pinjam yang di perbaharui
         * 
         * 
         * 
         */

         date_default_timezone_set('Asia/Kuala_Lumpur');
         $date = date('Y-m-d');
         $waktu = $data['pinjam'];
         $sampai = mktime(0,0,0,date("n"),date("j")+$waktu, date("Y"));
         $kembali = date("Y-m-d", $sampai);
         $query = "UPDATE tb_pinjam SET id_auth = :nama, id_buku = :buku, tanggal_pinjam = '$date', tanggal_kembali = '$kembali' WHERE id_pinjam = :id";
         try{
            $this->db->query($query);
            $this->db->bind('nama', $data['nama']);
            $this->db->bind('buku', $data['buku']);
            $this->db->bind('id', $data['id']);
            $this->db->execute();
            
        /**
         * 
         * 1. kita cek apakah buku baru itu === dengan buku lama 
         * kalau tidak jangn di update jumlah bukunya
         * 
         * 
         * 
         */
        if ($bukup === $buku) {
            $minus = $bukuall['jumlah_buku'];
            $queryB = "UPDATE tb_buku SET jumlah_buku = $minus WHERE id_buku = $buku";
            $this->db->query($queryB);
            $this->db->execute();
        }else {

                /**
                 * 
                 * 2. kita  update buku baru pertama kurangkan buku baru (dipinjam)
                 * 
                 * 
                 * 
                 */
                
                $minus = $bukuall['jumlah_buku'] - 1;
                $queryB = "UPDATE tb_buku SET jumlah_buku = $minus WHERE id_buku = $buku";
                $this->db->query($queryB);
                $this->db->execute();

                 /**
                 * 
                 * 2. kita  update buku lama pertama ditambahkan 1 (dikembalikan)
                 * 
                 * 
                 * 
                 */

                 $plus = $bukuP['jumlah_buku'] + 1;
                 $queryL = "UPDATE tb_buku SET jumlah_buku = '$plus' WHERE id_buku = '$bukup'";
                 $this->db->query($queryL);
                 $this->db->execute();
             }
             return ['status' => true];
         } catch (PDOException $e) {
          return ['status' => false, 'msg' => $e->getMessage()];
      } 

  }   



}


