<?php
/**
 * 上传单图
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate\field;

class Image extends Field
{
    public static string $html = <<<EOF
    <div class="form-group row">
        <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
        <div class="col-sm-10 col-md-4 formInputDiv"> 
            <div class="input-group">
                <input id="[FIELD_NAME]_file" name="[FIELD_NAME]_file" type="file" class="file-loading " data-initial-preview="{if isset(\$data)}{\$data.[FIELD_NAME]}{/if}">
                <input name="[FIELD_NAME]" id="[FIELD_NAME]" value="{\$data.[FIELD_NAME]|default=''}" hidden placeholder="请上传图片" class="fieldImage">
                <script>
                     initUploadImg('[FIELD_NAME]');
                </script>            
            </div>
        </div>
    </div>
EOF;

    public static array $rules = [
        'required'   => '非空',
        'file_size'  => '文件大小限制',
        'file_image' => '图片类型',
        'regular'    => '自定义正则'
    ];


    //控制器添加上传
    public static string $controllerAddCode =
        <<<EOF
         
EOF;


    //控制器修改上传
    public static string $controllerEditCode =
        <<<EOF
           
EOF;


    public static function create($data)
    {
        $html = self::$html;
        $html = str_replace(array('[FORM_NAME]', '[FIELD_NAME]', '[FIELD_DEFAULT]'), array($data['form_name'], $data['field_name'], $data['field_default']), $html);
        return $html;
    }
}