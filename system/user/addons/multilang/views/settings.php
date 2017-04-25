<div id="multilang">

    <?=form_open()?>
    <div class="panel">
        <div class="panel-header"><?=lang("multilang_conf_languages")?></div>
        <div class="panel-body">
            <?=ee('CP/Alert')->get('multilang_languages_error')?>
            <?=ee('CP/Alert')->get('multilang_languages_success')?>
            <table id="multilang_languages">
                <thead>
                <tr>
                    <th><?=lang("multilang_language")?></th>
                    <th><?=lang("multilang_language_code")?></th>
                    <th><?=lang("multilang_language_aliases")?></th>
                    <th class="min-td"></th>
                </tr>
                </thead>
                <tbody>
                <tr class="blank">
                    <td><input type="text" name="language_name[]" value=""></td>
                    <td><input type="text" name="language_code[]"  value=""></td>
                    <td><input type="text" name="language_aliases[]"  value=""></td>
                    <td class="min-td">
                        <button class="btn remove remove_lang"><?=lang("multilang_remove")?></button>
                    </td>
                </tr>
                <? foreach($languages as $lang) { ?>
                    <? if($lang["permanent"] == 1) { ?>
                        <tr>
                            <td>
                                <input type="text" class="disabled" value="<?=$lang["lang_name"]?>">
                            </td>
                            <td>
                                <input type="text" class="disabled" value="<?=$lang["lang_code"]?>">
                            </td>
                            <td>
                                <input type="text" class="disabled" value="<?=$lang["lang_aliases"]?>">
                            </td>
                            <td class="min-td">
                                <button class="btn remove disabled remove_lang"><?=lang("multilang_remove")?></button>
                            </td>
                        </tr>
                    <? } else { ?>
                        <tr>
                            <td>
                                <input type="text" name="language_name[]" value="<?=$lang["lang_name"]?>">
                            </td>
                            <td>
                                <input type="text" name="language_code[]" value="<?=$lang["lang_code"]?>">
                            </td>
                            <td>
                                <input type="text" name="language_aliases[]" value="<?=$lang["lang_aliases"]?>">
                            </td>
                            <td class="min-td">
                                <button class="btn remove remove_lang"><?=lang("multilang_remove")?></button>
                            </td>
                        </tr>
                    <? } ?>
                <? } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4">
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

    <?=form_open()?>
    <div class="panel">
        <div class="panel-header"><?=lang("multilang_conf_default")?></div>
        <div class="panel-body">
            <?=ee('CP/Alert')->get('multilang_default_success')?>
            <select name="default_lang">
                <? foreach($languages as $lang) { ?>
                    <option value="<?=$lang["lang_id"]?>" <?= $lang["is_default"] == 1 ? "selected" : "" ?>><?=$lang["lang_name"]?></option>
                <? } ?>
            </select>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn" name="save_default" value="<?=lang("multilang_save")?>">
        </div>
    </div>
    <?=form_close()?>

</div>