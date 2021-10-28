<?php
/**
 * 列表多项选择
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate\field;

class MultiSelect extends Field
{
    public static string $html = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
       
        <select name="[FIELD_NAME][]" id="[FIELD_NAME]" data-placeholder="请选择[FORM_NAME]" class="form-control fieldMultiSelect" multiple="multiple">
            <option value=""></option>
        </select>
    </div>
</div>
<script>
 $('#[FIELD_NAME]').select2();
</script>\n
EOF;

    public static function create($data)
    {
        $html = self::$html;
        $html = str_replace(array('[FORM_NAME]', '[FIELD_NAME]'), array($data['form_name'], $data['field_name']), $html);
        return $html;
    }
}