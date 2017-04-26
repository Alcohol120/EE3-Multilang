<?php

class Multilang_ext {

    public $name = MULTILANG_NAME;
    public $version = MULTILANG_VERSION;
    public $description = MULTILANG_DESCRIPTION;
    public $settings_exist = "n";
    public $docs_url = MULTILANG_DOCS;

    private $prefix = "lang";

    public function __construct() {
        ee()->load->helper("cookie");
    }

    public function activate_extension() {

        $data = [
            "class" => __CLASS__,
            "method" => "parse_language_vars",
            "hook" => "template_fetch_template",
            "priority" => 1,
            "version" => $this->version,
            "enabled" => "y"
        ];

        ee()->db->insert("extensions", $data);

    }

    public function update_extension($current = "") {

        return TRUE;

    }

    public function disable_extension() {

        ee()->db->where("class", __CLASS__);
        ee()->db->delete("extensions");

    }

    public function parse_language_vars($template) {

        $user_lang = $this->get_user_lang();

        ee()->config->_global_vars[$this->prefix] = $user_lang;

        $q = ee()->db->select("*")
            ->from("multilang_data")
            ->get();

        $data = $q->result_array();

        foreach($data as $row) {
            $lang = json_decode($row["data_val"], TRUE);
            $word = isset($lang[$user_lang]) ? $lang[$user_lang] : "";
            ee()->config->_global_vars[$this->prefix . "-" . $row["data_key"]] = $word;
        }

    }

    private function get_user_lang() {

        if(defined("LANGUAGE_CODE")) {

            if(isset($_COOKIE["multilang_lang"]) && LANGUAGE_CODE !== $_COOKIE["multilang_lang"]) {
                setcookie("multilang_lang", LANGUAGE_CODE);
            }

            return LANGUAGE_CODE;

        } elseif(!isset($_COOKIE["multilang_lang"]) || $_COOKIE["multilang_lang"] == "") {

            $q = ee()->db->select("*")
                ->from("multilang_languages")
                ->get();

            $default = "";
            $languages = [];

            foreach($q->result_array() as $row) {
                if($row["is_default"] == 1) {
                    $default = $row["lang_code"];
                }
                $aliases = trim($row["lang_aliases"], "|");
                if($aliases !== "") {
                    $aliases = explode("|", $aliases);
                } else {
                    $aliases = [];
                }
                if(count($aliases) < 1) {
                    $aliases = $row["lang_code"];
                } else {
                    $aliases = array_merge([$row["lang_code"]], $aliases);
                }
                $languages[$row["lang_code"]] = $aliases;
            }

            $user_lang = $this->detect_lang($default, $languages);
            setcookie("multilang_lang", $user_lang);
            return $user_lang;

        } else {

            $user_lang = $_COOKIE["multilang_lang"];
            return $user_lang;

        }

    }

    private function detect_lang($default, $lang) {

        $language = [];

        if ($list = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']) : null) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $language = array_combine($list[1], $list[2]);
                foreach ($language as $n => $v) {
                    $language[$n] = $v ? $v : 1;
                }
                arsort($language, SORT_NUMERIC);
            }
        } else $language = [];

        $languages=array();
        foreach ($lang as $lng => $alias) {
            if (is_array($alias)) {
                foreach ($alias as $alias_lang) {
                    $languages[strtolower($alias_lang)] = strtolower($lng);
                }
            } else {
                $languages[strtolower($alias)]=strtolower($lng);
            }
        }
        foreach ($language as $l => $v) {
            $s = strtok($l, '-');
            if (isset($languages[$s])) {
                return $languages[$s];
            }
        }

        return $default;

    }

}