<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persons extends CI_Controller {

    private $states = [
        'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
        'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
        'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram',
        'Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu',
        'Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
        'Andaman and Nicobar Islands','Chandigarh','Dadra & Nagar Haveli and Daman & Diu',
        'Delhi','Jammu & Kashmir','Ladakh','Lakshadweep','Puducherry'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Person_model');
        $this->load->library(['form_validation', 'pagination', 'session']);
        $this->load->helper(['url', 'form']);
    }

    // ----------------------------------------------------------------
    //  INDEX – List with pagination
    // ----------------------------------------------------------------
    public function index()
    {
        $per_page = 8;
        $total    = $this->Person_model->count_all();
        $page     = (int)($this->input->get('page') ?: 1);
        $offset   = ($page - 1) * $per_page;

        // Pagination config
        $config = [
            'base_url'   => site_url('persons?page='),
            'total_rows' => $total,
            'per_page'   => $per_page,
            'cur_page'   => $page,
            'use_page_numbers' => TRUE,
            'reuse_query_string' => FALSE,
            'full_tag_open'  => '<ul class="pagination">',
            'full_tag_close' => '</ul>',
            'first_link'  => '&laquo;',
            'last_link'   => '&raquo;',
            'prev_link'   => '&#8249;',
            'next_link'   => '&#8250;',
            'attributes'  => ['class' => 'page-link'],
            'cur_tag_open'  => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open'  => '<li class="page-item">',
            'num_tag_close' => '</li>',
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close'=> '</li>',
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close'=> '</li>',
            'first_tag_open'=> '<li class="page-item">',
            'first_tag_close'=> '</li>',
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close'=> '</li>',
        ];
        $this->pagination->initialize($config);

        $data['persons']    = $this->Person_model->get_all($per_page, $offset);
        $data['pagination'] = $this->pagination->create_links();
        $data['total']      = $total;
        $data['flash']      = $this->session->flashdata('msg');
        $data['flash_type'] = $this->session->flashdata('msg_type');

        $this->load->view('layout/header', $data);
        $this->load->view('persons/index', $data);
        $this->load->view('layout/footer');
    }

    // ----------------------------------------------------------------
    //  CREATE – show form
    // ----------------------------------------------------------------
    public function create()
    {
        $data['states']     = $this->states;
        $data['action']     = 'create';
        $data['person']     = NULL;

        $this->load->view('layout/header', $data);
        $this->load->view('persons/form', $data);
        $this->load->view('layout/footer');
    }

    public function save($id = null)
    {
        $this->_set_rules();

        if ($this->form_validation->run() === FALSE) {

            echo json_encode([
                'status' => 'error',
                'errors' => [
                    'name'   => strip_tags(form_error('name')),
                    'email'  => strip_tags(form_error('email')),
                    'mobile' => strip_tags(form_error('mobile')),
                    'gender' => strip_tags(form_error('gender')),
                    'state'  => strip_tags(form_error('state')),
                ],
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
            return;
        }

        $email = $this->input->post('email', TRUE);

        // ✅ Check duplicate email AFTER validation
        if ($this->Person_model->email_exists($email, $id)) {

            echo json_encode([
                'status' => 'error',
                'errors' => [
                    'email' => 'Email already exists'
                ],
                'csrf_token' => $this->security->get_csrf_hash(),
                'csrf_name'  => $this->security->get_csrf_token_name()
            ]);
            return;
        }

        $data = $this->_post_data();

        if ($id) {
            $this->Person_model->update($id, $data);
            $msg = 'Record updated successfully';
        } else {
            $this->Person_model->insert($data);
            $msg = 'Record created successfully';
        }

        echo json_encode([
            'status' => 'success',
            'message' => $msg,
            'redirect' => base_url('persons'),
            'csrf_token' => $this->security->get_csrf_hash(),
            'csrf_name'  => $this->security->get_csrf_token_name()
        ]);
    }

    // ----------------------------------------------------------------
    //  UPDATE – handle POST
    // ----------------------------------------------------------------
    public function update($id)
    {
        $person = $this->Person_model->get_by_id($id);
        if ( ! $person) { show_404(); }

        $this->_set_rules('edit', $id);

        if ($this->form_validation->run() === FALSE) {
            $data['states']  = $this->states;
            $data['action']  = 'edit';
            $data['person']  = $person;
            $this->load->view('layout/header', $data);
            $this->load->view('persons/form', $data);
            $this->load->view('layout/footer');
            return;
        }

        $this->Person_model->update($id, $this->_post_data());
        $this->session->set_flashdata('msg', 'Record updated successfully.');
        $this->session->set_flashdata('msg_type', 'success');
        redirect('persons');
    }

    // ----------------------------------------------------------------
    //  DELETE
    // ----------------------------------------------------------------
    public function delete($id)
    {
        // CSRF check – only accept POST
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            show_404();
        }
        $person = $this->Person_model->get_by_id($id);
        if ( ! $person) { show_404(); }

        $this->Person_model->delete($id);
        $this->session->set_flashdata('msg', 'Record deleted.');
        $this->session->set_flashdata('msg_type', 'danger');
        redirect('persons');
    }

    // ----------------------------------------------------------------
    //  Helpers
    // ----------------------------------------------------------------
    private function _set_rules($mode = 'create', $id = 0)
    {
        $email_rule = 'required|valid_email|max_length[150]';
        if ($mode === 'edit') {
            // allow same email for same record
            $email_rule .= '|callback__email_unique[' . $id . ']';
        } else {
            $email_rule .= '|callback__email_unique[0]';
        }

        $this->form_validation->set_rules('name',   'Name',   'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('email',  'Email',  $email_rule);
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|trim|min_length[10]|max_length[15]|regex_match[/^[0-9+\-\s]+$/]');
        $this->form_validation->set_rules('gender', 'Gender', 'required|in_list[Male,Female,Other]');
        $this->form_validation->set_rules('state',  'State',  'required|trim|max_length[60]');
    }

    public function _email_unique($email, $exclude_id)
    {
        if ($this->Person_model->email_exists($email, (int)$exclude_id)) {
            $this->form_validation->set_message('_email_unique', 'The {field} is already in use.');
            return FALSE;
        }
        return TRUE;
    }

    private function _post_data()
    {
        return [
            'name'   => $this->input->post('name',   TRUE),
            'email'  => $this->input->post('email',  TRUE),
            'mobile' => $this->input->post('mobile', TRUE),
            'gender' => $this->input->post('gender', TRUE),
            'state'  => $this->input->post('state',  TRUE),
        ];
    }
}
