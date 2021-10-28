<?php
/**
 * 日期时间
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate\field;

class Datetime extends Field
{
    public static string $html = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" readonly name="[FIELD_NAME]" value="{\$data.[FIELD_NAME]|default='[FIELD_DEFAULT]'}" placeholder="请选择[FORM_NAME]" type="text" class="form-control filedDatetime">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'datetime',
    });
</script>\n
EOF;

    public static array $rules = [
        'required'  => '非空',
    ];


    public static function create($data)
    {
        return str_replace(array('[FORM_NAME]', '[FIELD_NAME]', '[FIELD_DEFAULT]'), array($data['form_name'], $data['field_name'], $data['field_default']), self::$html);
    }
}