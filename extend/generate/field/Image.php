<?php
/**
 * 上传单图
 * @author yupoxiong<i@yufuping.com>
 */

namespace generate\field;

class Image extends Field
{
    public static $html = <<<EOF
    <div class="form-group">
        <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
        <div class="col-sm-10 col-md-4"> 
            <div class="input-group">
                <input id="[FIELD_NAME]" name="[FIELD_NAME]"  value="{\$data.[FIELD_NAME]|default='[FIELD_DEFAULT]'}"   placeholder="请输入[FORM_NAME]的URL" type="text" class="form-control field-image" >
                <div class="input-group-append">
                     <span class="input-group-text" onclick="showFileUpload('[FIELD_NAME]','image')">
                          <i class="fas fa-upload"></i>上传
                     </span>
                </div>
            </div>
        </div>
        
    </div>
    
    <div>
    <img id="[FIELD_NAME]Show" class="imgViewer" src="{\$data.[FIELD_NAME]|default='[FIELD_DEFAULT]'}" alt="[FORM_NAME]">
    </div>\n
EOF;

    public static $rules = [
        'required'   => '非空',
        'file_size'  => '文件大小限制',
        'file_image' => '图片类型',
        'regular'    => '自定义正则'
    ];


    //控制器添加上传
    public static $controllerAddCode =
        <<<EOF
         
EOF;


    //控制器修改上传
    public static $controllerEditCode =
        <<<EOF
           
EOF;


    public static function create($data)
    {
        $html = self::$html;
        $html = str_replace(array('[FORM_NAME]', '[FIELD_NAME]', '[FIELD_DEFAULT]'), array($data['form_name'], $data['field_name'], $data['field_default']), $html);
        return $html;
    }
}