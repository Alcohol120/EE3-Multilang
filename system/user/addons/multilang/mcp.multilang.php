<?php

class Multilang_mcp {

    public function __construct() {

        ee()->cp->add_to_head('<link rel="stylesheet" href="' . URL_THIRD_THEMES . 'multilang/css/alclab.css">');
        ee()->cp->add_to_head('<link rel="stylesheet" href="' . URL_THIRD_THEMES . 'multilang/css/multilang.css">');

        ee()->cp->add_to_foot('<script src="' . URL_THIRD_THEMES . 'multilang/js/multilang.js"></script>');

        ee()->cp->header = array(
            'toolbar_items' => array(
                'settings' => array(
                    'href' => ee('CP/URL')->make('addons/settings/multilang/settings'),
                    'title' => lang('multilang_conf_languages')
                )
            )
        );

        $sidebar = ee('CP/Sidebar')->make();

        $fortunes = $sidebar->addHeader(lang('multilang_module_name'));

        $fortunes_list = $fortunes->addBasicList();
        $fortunes_list->addItem(lang('multilang_texts'), ee('CP/URL', 'addons/settings/multilang'));
        $fortunes_list->addItem(lang('multilang_conf_languages'), ee('CP/URL', 'addons/settings/multilang/settings'));

    }

    public function index() {

        $tags = [];

        $view = ee("View")->make("multilang:main");

        $q = ee()->db->select("lang_name, lang_code")
            ->from("multilang_languages")
            ->get();

        $languages = [];

        foreach($q->result_array() as $row) {
            $languages[count($languages)] = $row;
        }

        $tags["languages"] = $languages;

        if(ee()->input->post("save")) {

            $keys = ee()->input->post("lang_key");
            unset($keys[0]);

            $key_names = [];
            $data = [];

            try {

                foreach($keys as $k=>$key) {

                    if($key == "") throw new Exception(lang("multilang_err_10"));
                    if(!(bool)preg_match("/^[a-zA-Z0-9_]+$/", $key)) throw new Exception(lang("multilang_err_11"));
                    if(array_search($key, $key_names) !== FALSE) throw new Exception(lang("multilang_err_12"));

                    $key_names[count($key)] = $key;

                    $lang = [];
                    foreach($languages as $lng) {
                        $str = ee()->input->post("lang_" . $lng["lang_code"])[$k];
                        $lang[$lng["lang_code"]] = $str;
                    }

                    $data[count($data)] = [
                        "key" => $key,
                        "lang" => $lang
                    ];

                }

                ee()->db->delete("multilang_data", [
                    "group_id" => 1
                ]);

                foreach($data as $row) {

                    ee()->db->insert("multilang_data", [
                        "group_id" => 1,
                        "data_key" => $row["key"],
                        "data_val" => json_encode($row["lang"])
                    ]);

                }

                ee('CP/Alert')->makeInline('multilang_data_success')
                    ->asSuccess()
                    ->withTitle(lang("multilang_success"))
                    ->addToBody(lang("multilang_saved"))
                    ->now();

            } catch(Exception $e) {

                ee('CP/Alert')->makeInline('multilang_data_error')
                    ->asIssue()
                    ->withTitle(lang("multilang_err"))
                    ->addToBody($e->getMessage())
                    ->now();

            }

        }

        $q = ee()->db->select("*")
            ->from("multilang_data")
            ->get();

        $data = [];

        foreach($q->result_array() as $row) {

            if($row["data_val"] == "") {
                $lang = [];
            } else {
                $lang = json_decode($row["data_val"], TRUE);
            }

            $data[count($data)] = [
                "key" => $row["data_key"],
                "lang" => $lang
            ];

        }

        $tags["data"] = $data;

        return [
            "body" => $view->render($tags),
            "breadcrumb" => [
                ee("CP/URL")->make("addons/settings/multilang")->compile() => lang("multilang_module_name")
            ],
            "heading" => lang("multilang_module_name")
        ];

    }

    public function settings() {

        $tags = [];

        $view = ee("View")->make("multilang:settings");

        if(ee()->input->post("save_languages")) {

            $names = ee()->input->post("language_name");
            $codes = ee()->input->post("language_code");
            $aliases = ee()->input->post("language_aliases");
            unset($names[0]);
            unset($codes[0]);
            unset($aliases[0]);

            if(count($names) !== count($codes) || count($names) !== count($aliases)) {

                ee('CP/Alert')->makeInline('multilang_languages_error')
                    ->asIssue()
                    ->withTitle("unknown error")
                    ->addToBody("unknown error")
                    ->now();

            } else {

                $lang = [];
                foreach($names as $key=>$name) {
                    $lang[count($lang)] = [
                        "name" => $name,
                        "code" => strtolower($codes[$key]),
                        "aliases" => strtolower(trim($aliases[$key], "|"))
                    ];
                }

                $names = [];
                $codes = [];
                $aliases = [];

                $permanent_lang = ee()->db->select("*")
                    ->from("multilang_languages")
                    ->where("permanent", 1)
                    ->get();

                $permanent_lang = $permanent_lang->result_array();

                foreach($permanent_lang as $lng) {
                    $names[count($names)] = $lng["lang_name"];
                    $codes[count($codes)] = strtolower($lng["lang_code"]);
                    $new_aliases = explode("|", trim(strtolower($lng["lang_aliases"]), "|"));
                    foreach($new_aliases as $n) {
                        if($n == "") continue;
                        $aliases[count($aliases)] = $n;
                    }
                }

                // validation

                try {

                    foreach($lang as $lng) {

                        if(!$lng["code"]) throw new Exception(lang("multilang_err_0"));
                        if(!$lng["name"]) throw new Exception(lang("multilang_err_1"));

                        if(!(bool)preg_match("/^[a-z]{2}$/", $lng["code"])) throw new Exception(lang("multilang_err_2"));
                        if($lng["aliases"] !== "") {
                            if(!(bool)preg_match("/^[a-z]{2}(|[a-z]{2})*$/", $lng["code"])) throw new Exception(lang("multilang_err_3"));
                        }

                        if(array_search($lng["code"], $codes) !== FALSE) throw new Exception(lang("multilang_err_4"));
                        if(array_search($lng["name"], $names) !== FALSE) throw new Exception(lang("multilang_err_5"));

                        $names[count($names)] = $lng["name"];
                        $codes[count($codes)] = $lng["code"];

                        $lng_aliases = explode("|", $lng["aliases"]);

                        foreach($lng_aliases as $alias) {

                            if($alias == "") continue;

                            if(array_search($alias, $aliases) !== FALSE) throw new Exception(lang("multilang_err_6"));

                            $aliases[count($aliases)] = $alias;

                        }

                    }

                    ee()->db->delete("multilang_languages", [ "permanent" => 0 ]);
                    ee()->db->update("multilang_languages", [
                        "is_default" => 1
                    ], [
                        "permanent" => 1
                    ]);

                    foreach($lang as $lng) {

                        ee()->db->insert("multilang_languages", [
                            "lang_name" => $lng["name"],
                            "lang_code" => $lng["code"],
                            "lang_aliases" => $lng["aliases"],
                            "permanent" => 0,
                            "is_default" => 0
                        ]);

                    }

                    ee('CP/Alert')->makeInline('multilang_languages_success')
                        ->asSuccess()
                        ->withTitle(lang("multilang_success"))
                        ->addToBody(lang("multilang_saved"))
                        ->now();

                    $this->create_start_files();

                } catch(Exception $e) {

                    ee('CP/Alert')->makeInline('multilang_languages_error')
                        ->asIssue()
                        ->withTitle(lang("multilang_err"))
                        ->addToBody($e->getMessage())
                        ->now();

                }

            }

        }

        if(ee()->input->post("save_default")) {

            ee()->db->update("multilang_languages", [ "is_default" => 0 ]);

            ee()->db->update("multilang_languages", [
                "is_default" => 1
            ], [
                "lang_id" => (int)ee()->input->post("default_lang")
            ]);

            ee('CP/Alert')->makeInline('multilang_default_success')
                ->asSuccess()
                ->withTitle(lang("multilang_success"))
                ->addToBody(lang("multilang_saved"))
                ->now();

        }

        $languages = ee()->db->select("*")
            ->from("multilang_languages")
            ->get();

        $tags["languages"] = $languages->result_array();

        return [
            "body" => $view->render($tags),
            "breadcrumb" => [
                ee("CP/URL")->make("addons/settings/multilang")->compile() => lang("multilang_module_name")
            ],
            "heading" => lang("multilang_module_name")
        ];

    }

    private function create_start_files() {

        $languages = ee()->db->select("*")
            ->from("multilang_languages")
            ->get();

        try {

            if(!@$file = file_get_contents(FCPATH . "index.php")) throw new Exception(lang("multilang_err_7"));

            $file = preg_replace("/system_path = '(.*?)'/", "system_path = '../$1'", $file);

            foreach($languages->result_array() as $lang) {

                if(file_exists(FCPATH . $lang["lang_code"] . "/index.php")) continue;

                if(!is_dir(FCPATH . $lang["lang_code"])) {
                    mkdir(FCPATH . $lang["lang_code"]);
                }

                $output = str_replace("<?php", "<?php\ndefine(\"LANGUAGE_CODE\", \"" . $lang["lang_code"] . "\");", $file);

                if(!@file_put_contents(FCPATH . $lang["lang_code"] . "/index.php", $output)) throw new Exception(lang("multilang_err_8"));

            }

        } catch(Exception $e) {

            ee('CP/Alert')->makeInline('multilang_languages_error')
                ->asIssue()
                ->withTitle(lang("multilang_err"))
                ->addToBody($e->getMessage())
                ->now();

        }

    }

}