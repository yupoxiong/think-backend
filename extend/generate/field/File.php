<?php
/**
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate\field;

class File extends Field
{

    public static string $html = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv"> 
        <input id="[FIELD_NAME]" name="[FIELD_NAME]"  placeholder="请上传[FORM_NAME]" data-initial-preview="{\$data.[FIELD_NAME]|default=''}" type="file" class="form-control fieldFile" >
    </div>
</div>
<script>
    $('#[FIELD_NAME]').fileinput({
        language: 'zh',
        browseLabel: '浏览',
        initialPreviewAsData: true,
        dropZoneEnabled: false,
        showUpload:false,
        showRemove: false,
        allowedFileExtensions: ['jpg', 'png', 'gif','bmp','svg','jpeg','mp4','doc','docx','pdf','xls','xlsx','ppt','pptx','txt'],
        //默认限制10M
        maxFileSize:10240
    });
</script>\n
EOF;

    public static function create($data)
    {
        $html = self::$html;
        $html = str_replace(array('[FORM_NAME]', '[FIELD_NAME]'), array($data['form_name'], $data['field_name']), $html);
        return $html;
    }
}
