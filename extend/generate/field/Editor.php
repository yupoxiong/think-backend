<?php
/**
 * 富文本编辑器
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate\field;

class Editor extends Field
{
    public static string $html = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
        <div class="col-sm-10">
            <div id="[FIELD_NAME]Editor"><p>{\$data.[FIELD_NAME]|raw|default='[FIELD_DEFAULT]'}</p></div>
            <textarea id="[FIELD_NAME]" name="[FIELD_NAME]" style="display: none">{\$data.[FIELD_NAME]|raw|default='[FIELD_DEFAULT]'}</textarea>
        </div>
    </div>
<script>
    var E = E||window.wangEditor;
    if(editor!==undefined){
        editor.destroy();
    }
    var editor = new E('#[FIELD_NAME]Editor');
    editor.create();
    editor.config.onchange = function (newHtml) {
        $('#[FIELD_NAME]').val(newHtml);
    };
</script>\n
EOF;

    /**
     * @var string 富文本字段添加处理
     */
    public static string $controllerAddCode =<<<EOF
\$param['[FIELD_NAME]'] = \$request->param(false)['[FIELD_NAME]'];
\n
EOF;
    /**
     * @var string 富文本字段修改处理
     */
    public static string $controllerEditCode =<<<EOF
\$param['[FIELD_NAME]'] = \$request->param(false)['[FIELD_NAME]'];
\n
EOF;

    public static function create($data)
    {
        return str_replace(array('[FORM_NAME]', '[FIELD_NAME]', '[FIELD_DEFAULT]'), array($data['form_name'], $data['field_name'], $data['field_default']), self::$html);
    }
}