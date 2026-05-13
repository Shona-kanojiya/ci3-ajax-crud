<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_persons extends CI_Migration {

    public function up()
    {
        $this->load->dbforge();

        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => FALSE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => FALSE,
                'unique' => TRUE   // ✅ FIX: proper unique
            ),
            'mobile' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => FALSE
            ),
            'gender' => array(
                'type' => "ENUM('Male','Female','Other')",
                'null' => FALSE
            ),
            'state' => array(
                'type' => 'VARCHAR',
                'constraint' => '60',
                'null' => FALSE
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ));

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table('persons', TRUE);
    }

    public function down()
    {
        $this->dbforge->drop_table('persons', TRUE);
    }
}