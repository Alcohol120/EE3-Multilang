<div id="multilang">

    <?=form_open()?>
    <div class="panel">
        <div class="panel-header"><?=lang("multilang_conf_languages")?></div>
        <div class="panel-body">
            <table id="multilang_languages">
                <thead>
                <tr>
                    <th><?=lang("multilang_language")?></th>
                    <th><?=lang("multilang_language_code")?></th>
                    <th class="min-td"></th>
                </tr>
                </thead>
                <tbody>
                <tr class="blank">
                    <td><input type="text" name="language_name[]" value=""></td>
                    <td><input type="text" name="language_code[]"  value=""></td>
                    <td class="min-td">
                        <button class="btn remove remove_lang"><?=lang("multilang_remove")?></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" class="disabled" value="English">
                    </td>
                    <td>
                        <input type="text" class="disabled" value="en">
                    </td>
                    <td class="min-td">
                        <button class="btn remove disabled remove_lang"><?=lang("multilang_remove")?></button>
                    </td>
                </tr>
                <? foreach($languages as $lang) { ?>
                <? if($lang["is_default"] == 1) continue; ?>
                <tr>
                    <td>
                        <input type="text" name="language_name[]" value="<?=$lang["lang_name"]?>">
                    </td>
                    <td>
                        <input type="text" name="language_code[]" value="<?=$lang["lang_code"]?>">
                    </td>
                    <td class="min-td">
                        <button class="btn remove remove_lang"><?=lang("multilang_remove")?></button>
                    </td>
                </tr>
                <? } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3">
                        <button class="btn" id="add_lang"><?=lang("multilang_add_row")?></button>
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn" name="save_languages" value="<?=lang("multilang_save")?>">
        </div>
    </div>
    <?=form_close()?>

    <div class="panel">
        <div class="panel-header"><?=lang("multilang_conf_default")?></div>
        <div class="panel-body">
            <table>
                <thead>
                <tr>
                    <th><?=lang("multilang_language")?></th>
                    <th class="min-td"><?=lang("multilang_default")?></th>
                </tr>
                </thead>
                <tbody>
                <? foreach($languages as $lang) { ?>
                <tr>
                    <td><?=$lang["lang_name"]?></td>
                    <td class="min-td">
                        <input type="radio" <?= $default_lang == $lang["lang_code"] ? "checked" : "" ?> name="default_lang" value="<?=$lang["lang_code"]?>">
                    </td>
                </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn" name="save_default" value="<?=lang("multilang_save")?>">
        </div>
    </div>

</div>