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
        $fortunes_list->addItem(lang('multilang_groups'), ee('CP/URL', 'addons/settings/multilang/groups'));
        $fortunes_list->addItem(lang('multilang_conf_languages'), ee('CP/URL', 'addons/settings/multilang/settings'));

    }

    public function index() {

        $tags = [];

        $view = ee("View")->make("multilang:main");

//        $languages = ee()->db->select("*")
//            ->from("multilang_languages")
//            ->get();
//
//        $default_lang = ee()->db->select("conf_val")
//            ->from("multilang_settings")
//            ->where("conf_key", "default_lang")
//            ->get();
//
//        $tags["languages"] = $languages->result_array();
//        $tags["default_lang"] = $default_lang->row_array()["conf_val"];
//
//        if(ee()->input->post("save_languages")) {
//
//            $lang_names = ee()->input->post("language_name");
//            $lang_codes = ee()->input->post("language_code");
//
//            unset($lang_names[0]);
//            unset($lang_codes[0]);
//
//            try {
//
//                if(count($lang_names) != count($lang_codes)) throw new Exception("Unknown error!");
//
//                $new_lang = [];
//                $old_lang = [];
//
//                ee()->db->s("multilang_languages", [ "is_default" => 0 ]);
//
//                foreach($lang_codes as $key=>$code) {
//
//                    if(!$code) throw new Exception(lang("multilang_err_0"));
//                    if(!$lang_names[$key]) throw new Exception(lang("multilang_err_1"));
//
//                    if(!(bool)preg_match("/^[a-zA-Z]{2}$/", $code)) throw new Exception(lang("multilang_err_2"));
//
//                    $q = ee()->db->select("lang_id")
//                        ->from("multilang_languages")
//                        ->where("language_code", $code)
//                        ->get();
//
////                    $lang[count($lang)] = [
////                        "name" => $lang_names[$key],
////                        "code" => $code
////                    ];
//
//                }
//
//            } catch (Exception $e) {
//                echo $e->getMessage();
//            }
//
//        }

        return [
            "body" => "<h1>hello</h1>",
            "breadcrumb" => [
                ee("CP/URL")->make("addons/settings/multilang")->compile() => lang("multilang_module_name")
            ],
            "heading" => lang("multilang_module_name")
        ];

    }

    public function settings() {

        $tags = [];

        $view = ee("View")->make("multilang:settings");

//        echo "<textarea>";
//        var_dump(ee()->input->post("language_name"));
//        var_dump(ee()->input->post("language_code"));
//        var_dump(ee()->input->post("is_default"));
//        echo "</textarea>";

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

                    // @TODO make folders

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

}