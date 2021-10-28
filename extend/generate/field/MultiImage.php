<?php
/**
 * 上传多图
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate\field;

class MultiImage extends Field
{
    public static string $html = <<<EOF

<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <div class="input-group">
            <input id="[FIELD_NAME]_file" name="[FIELD_NAME]_file" type="file" class="file-loading " multiple>
            <input name="[FIELD_NAME]" id="[FIELD_NAME]" value="{\$data.[FIELD_NAME]|default=''}" hidden placeholder="请上传图片" class="fieldMultiImage">
            <script>
                initUploadMultiImg('[FIELD_NAME]');
            </script>            
        </div>
    </div>
</div>
EOF;

    public static function create($data)
    {
        $html = self::$html;
        $html = str_replace(array('[FORM_NAME]', '[FIELD_NAME]'), array($data['form_name'], $data['field_name']), $html);
        return $html;
    }
}