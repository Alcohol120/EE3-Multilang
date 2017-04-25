<div id="multilang">

    <?=form_open()?>
    <div class="panel">
        <div class="panel-header"><?=lang("multilang_texts")?></div>
        <div class="panel-body">
            <?=ee('CP/Alert')->get('multilang_data_error')?>
            <?=ee('CP/Alert')->get('multilang_data_success')?>
            <table id="multilang_languages">
                <thead>
                <tr>
                    <th><?=lang("multilang_lang_key")?></th>
                    <? foreach($languages as $lang) { ?>
                    <th><?=$lang["lang_name"]?></th>
                    <? } ?>
                    <th class="min-td"></th>
                </tr>
                </thead>
                <tbody>
                <tr class="blank">
                    <td><input type="text" name="lang_key[]" value=""></td>
                    <? foreach($languages as $lang) { ?>
                    <td><input type="text" name="lang_<?=$lang["lang_code"]?>[]" value=""></td>
                    <? } ?>
                    <td class="min-td">
                        <button class="btn remove remove_lang"><?=lang("multilang_remove")?></button>
                    </td>
                </tr>
                <? foreach($data as $row) { ?>
                <tr>
                    <td><input type="text" name="lang_key[]" value="<?=$row["key"]?>"></td>
                    <? foreach($languages as $lang) { ?>
                        <? if(isset($row["lang"][$lang["lang_code"]])) { ?>
                            <td><input type="text" name="lang_<?=$lang["lang_code"]?>[]" value="<?=$row["lang"][$lang["lang_code"]]?>"></td>
                        <? } else { ?>
                            <td><input type="text" name="lang_<?=$lang["lang_code"]?>[]" value=""></td>
                        <? } ?>
                    <? } ?>
                    <td class="min-td">
                        <button class="btn remove remove_lang"><?=lang("multilang_remove")?></button>
                    </td>
                </tr>
                <? } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="<?=count($languages) + 2?>">
                        <button class="btn" id="add_lang"><?=lang("multilang_add_row")?></button>
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn" name="save" value="<?=lang("multilang_save")?>">
        </div>
    </div>
    <?=form_close()?>

</div>