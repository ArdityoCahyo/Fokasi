<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('m_member');
        $this->load->model('m_posting');
        $this->load->library('form_validation');
    }
    public function index()
    {
        $user = $this->session->userdata('username');
        if ($user != NULL) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['class_home'] = 'active';
            $this->load->view('template/header-dashboard', $data);
            $this->load->view('dashboard-user-home');
        } else {
            redirect('/Welcome');
        }
    }

    public function profileMember()
    {
        $user = $this->session->userdata('username');
        if ($user != NULL) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['profile'] = 'assets/css/style-profile.css';
            $data['class_profile'] = 'active';
            $this->load->view('template/header-dashboard', $data);
            $this->load->view('dashboard-user-profile');
        } else {
            redirect('/Welcome');
        }
    }

    public function edit_profileMember()
    {
        $user = $this->session->userdata('username');
        // $user = 'arditAja';
        if ($user != NULL) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            // $data['dataDok'] = $this->m_dokter->getprofile($this->session->userdata('username'));
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('number', 'Number', 'required');

            if ($this->form_validation->run() == false) {
                $data['profile'] = 'assets/css/style-profile.css';
                $data['edit_profile'] = 'assets/css/style-profile_edit.css';
                $this->load->view('template/header-dashboard', $data);
                $this->load->view('dashboard-user-profile_edit', $data);
            } else {
                $data = [
                    // 'usernamedok' => $this->session->userdata('username'),
                    'username' => $user,
                    'password' => $this->input->post('password'),
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'phonenumber' => $this->input->post('number')
                ];
                // echo 'Data diubah';
                $this->m_member->setprofile($data);

                // $user = $this->session->set_userdata('username', $data['usernamedok']); kalau mau ganti username aja
                redirect('/user/profileMember/');
            }
        } else {
            redirect('/Welcome');
        }
        // $user = $this->session->userdata('username');
        // if ($user != NULL) {
        //   $this->load->view('edit-Dokter');
        // } else {
        //   redirect('/Welcome');
        // }
    }
    public function forum()
    {
        $user = $this->session->userdata('username');
        if ($user != NULL) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['class_forum'] = 'active';
            $data['forum'] = 'assets/css/dashboard-forum1.css';
            $data['dataForum'] = $this->m_posting->loadingpost();
            $this->load->view('template/header-dashboard', $data);
            $this->load->view('forum2', $data);
        } else {
            redirect('/Welcome');
        }
    }
    public function myforum()
    {
        $user = $this->session->userdata('username');
        if ($user != NULL) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['class_myforum'] = 'active';
            $data['forum'] = 'assets/css/dashboard-forum1.css';
            $data['dataForum'] = $this->m_member->getmypost($user);
            $this->load->view('template/header-dashboard', $data);
            $this->load->view('dashboard-user-myforum', $data);
        } else {
            redirect('/Welcome');
        }
    }

    public function detailForum($posting_id)
    {
        $user = $this->session->userdata('username');
        if ($user != null) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['class_myforum'] = 'active';
            $data['forum'] = 'assets/css/dashboard-forum1.css';
            $data['dataForum'] = $this->m_posting->loadingpostbyID($posting_id);
            $data['komentar'] = $this->m_posting->loadDatakomentarbyID($posting_id);

            $this->load->view('template/header-dashboard', $data);
            $this->load->view('dashboard-user-forum', $data);
        } else {
            redirect('/Welcome');
        }
    }

    public function deleteMyPost($id)
    {
        $user = $this->session->userdata('username');
        if ($user != null) {
            $this->m_posting->hapusposting($id);
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['class_myforum'] = 'active';
            $data['forum'] = 'assets/css/dashboard-forum1.css';
            $data['dataForum'] = $this->m_member->getmypost($user);
            $this->load->view('template/header-dashboard', $data);
            $this->load->view('dashboard-user-myforum', $data);
        } else {
            redirect('/Welcome');
        }
    }

    public function editMyPost($id)
    {
        $user = $this->session->userdata('username');
        if ($user != null) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['editforum'] = 'assets/css/style-profile_edit.css';
            $data['dataForum'] = $this->m_member->getMyPostById($id);

            $this->form_validation->set_rules('Judul', 'Judul', 'required');
            $this->form_validation->set_rules('Isi', 'Isi', 'required');

            if ($this->form_validation->run() == false) {
                $this->load->view('template/header-dashboard', $data);
                $this->load->view('dashboard-user-editforum', $data);
            } else {
                $edit = [
                    'isipost' => $this->input->post('Isi'),
                    'judulpost' => $this->input->post('Judul'),
                ];
                $this->m_member->updateMyPost($id, $edit);
                $this->load->view('template/header-dashboard', $data);
                $this->load->view('dashboard-user-editforum', $data);
            }
        } else {
            redirect('/Welcome');
        }
    }

    public function makeApost()
    {
        $user = $this->session->userdata('username');
        if ($user != NULL) {
            $data['dataMember'] = $this->m_member->getprofile($user);
            $data['class_createforum'] = 'active';
            $data['editforum'] = 'assets/css/style-profile_edit.css';
            $data['dataForum'] = $this->m_member->getmypost($user);

            $this->form_validation->set_rules('Judul', 'Judul', 'required');
            $this->form_validation->set_rules('Isi', 'Isi', 'required');
            if ($this->form_validation->run() == false) {

                $this->load->view('template/header-dashboard', $data);
                $this->load->view('dashboard-user-createforum');

                $this->load->view('template/header-dashboard', $data);
                $this->load->view('dashboard-user-myforum', $data);
            } else {
                $new = [
                    'username' => $user,
                    'isipost' => $this->input->post('Isi'),
                    'judulpost' => $this->input->post('Judul')
                ];
                $this->m_member->insertpost($new);

                redirect('/User');
            }
        } else {
            redirect('/Welcome');
        }
    }
}
