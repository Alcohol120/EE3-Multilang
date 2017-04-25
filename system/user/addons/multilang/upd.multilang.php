<?php

class Multilang_upd {

    public $version = MULTILANG_VERSION;

    public function __construct() {

        ee()->load->dbforge();

    }

    public function install() {

        $data = [
            "module_name" => "MultiLang",
            "module_version" => $this->version,
            "has_cp_backend" => "y",
            "has_publish_fields" => "n"
        ];

        ee()->db->insert("modules", $data);

        $this->create_languages_table();
        $this->create_groups_table();
        $this->create_data_table();

        return TRUE;

    }

    public function update($current = "") {

        return TRUE;

    }

    public function uninstall() {

        ee()->dbforge->drop_table('multilang_languages');
        ee()->dbforge->drop_table('multilang_groups');
        ee()->dbforge->drop_table('multilang_data');

        ee('Model')->get('Module')->filter('module_name', '==', 'MultiLang')->delete();

        return TRUE;

    }

    private function create_languages_table() {

        $fields = [
            "lang_id" => [ "type" => "int", "constraint" => "11", "unsigned" => TRUE, "auto_increment" => TRUE ],
            "lang_name" => [ "type" => "varchar", "constraint" => "100" ],
            "lang_code" => [ "type" => "varchar", "constraint" => "2" ],
            "lang_aliases" => [ "type" => "varchar", "constraint" => "200" ],
            "permanent" => [ "type" => "int", "constraint" => "1" ],
            "is_default" => [ "type" => "int", "constraint" => "1" ]
        ];

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key("lang_id", TRUE);
        ee()->dbforge->create_table("multilang_languages");

        ee()->db->insert("multilang_languages", [
            "lang_name" => "English",
            "lang_code" => "en",
            "lang_aliases" => "",
            "permanent" => 1,
            "is_default" => 1
        ]);

    }

    private function create_groups_table() {

        $fields = [
            "group_id" => [ "type" => "int", "constraint" => "11", "unsigned" => TRUE, "auto_increment" => TRUE ],
            "group_name" => [ "type" => "varchar", "constraint" => "100" ],
            "prefix" => [ "type" => "varchar", "constraint" => "100" ]
        ];

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key("group_id", TRUE);
        ee()->dbforge->create_table("multilang_groups");

    }

    private function create_data_table() {

        $fields = [
            "data_id" => [ "type" => "int", "constraint" => "11", "unsigned" => TRUE, "auto_increment" => TRUE ],
            "group_id" => [ "type" => "int", "constraint" => "11" ],
            "data_key" => [ "type" => "varchar", "constraint" => "100" ],
            "data_val" => [ "type" => "varchar", "constraint" => "5000" ],
        ];

        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key("data_id", TRUE);
        ee()->dbforge->create_table("multilang_data");

    }

}