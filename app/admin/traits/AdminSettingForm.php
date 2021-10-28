<?php

namespace app\admin\traits;

trait AdminSettingForm
{

    /**
     * @var string 文本输入
     */
    public string $textHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="text" class="form-control field-text">
    </div>
</div>\n
EOF;


    /**
     * @var string 多选
     */
    public string $multiSelectHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
       
        <select name="[FIELD_NAME][]" id="[FIELD_NAME]" data-placeholder="请选择[FORM_NAME]" class="form-control field-multi-select" multiple="multiple">
            <option value=""></option>
        </select>
    </div>
</div>
<script>
 $('#[FIELD_NAME]').select2();
</script>\n
EOF;

    /**
     * @var string 单图上传
     */
    public string $imageHtml = <<<EOF
    <div class="form-group row">
        <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
        <div class="col-sm-10 col-md-4 formInputDiv"> 
            <div class="input-group">
                <input id="[FIELD_NAME]_file" name="[FIELD_NAME]_file" type="file" class="file-loading " data-initial-preview="[FIELD_CONTENT]">
                <input name="[FIELD_NAME]" id="[FIELD_NAME]" value="[FIELD_CONTENT]" hidden placeholder="请上传图片" class="fieldImage">
                <script>
                     initUploadImg('[FIELD_NAME]');
                </script>            
            </div>
        </div>             
    </div>\n
EOF;

    /**
     * @var string 多图上传
     */
    public string $multiImageHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label> 
    <div class="col-sm-10 col-md-4 formInputDiv">
        <div class="input-group">
            <input id="[FIELD_NAME]_file" name="[FIELD_NAME]_file" type="file" class="file-loading " multiple>
            <input name="[FIELD_NAME]" id="[FIELD_NAME]" value="[FIELD_CONTENT]" hidden placeholder="请上传[FORM_NAME]" class="fieldMultiImage">
            <script>
                initUploadMultiImg('[FIELD_NAME]');
            </script>            
        </div>
    </div>
</div>\n
EOF;



    public string $emailHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="email" class="form-control fieldEmail">
    </div>
</div>\n
EOF;



    /**
     * @var string 手机号输入
     */
    public string $mobileHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="tel" maxlength="11" class="form-control field-mobile">
    </div>
</div>\n
EOF;


    /**
     * @var string 身份证输入
     */
    public string $idCardHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="text" maxlength="18" class="form-control fieldIdCard">
    </div>
</div>\n
EOF;



    public string $colorHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <div class="input-group" id="color-[FIELD_NAME]">
            <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="text" class="form-control fieldMap">
            <div class="input-group-addon"><i></i></div>
        </div>
    </div>
</div>
<script>
    $('#color-[FIELD_NAME]').colorpicker();
</script>\n
EOF;


    /**
     * @var string ip输入
     */
    public string $ipHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="text" class="form-control field-map">
    </div>
</div>\n
EOF;

    /**
     * @var string 地图选点
     */
    public string $mapHtml = <<<EOF
<div class="form-group row">
    <label class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-8 ">
        <div id="map-container" style="width: 100%; height: 350px;position: relative; background-color: rgb(229, 227, 223);overflow: hidden;transform: translateZ(0px);">
        </div>
        <input name="[FIELD_NAME_LNG]" hidden id="[FIELD_NAME_LNG]" value="{\$data.[FIELD_NAME_LNG]|default='[FIELD_DEFAULT_LNG]'}">
        <input name="[FIELD_NAME_LAT]" hidden id="[FIELD_NAME_LAT]" value="{\$data.[FIELD_NAME_LAT]|default='[FIELD_DEFAULT_LAT]'}" >
    </div>
</div>
    
<script>
    AMapUI.loadUI(['misc/PositionPicker'], function(PositionPicker) {
        var map = new AMap.Map('map-container', {
            zoom: 16,
            scrollWheel: true
        })
        var positionPicker = new PositionPicker({
            mode: 'dragMap',
            map: map
        });

        positionPicker.on('success', function(positionResult) {
            console.log(positionResult);
            console.log('success');
            $('#[FIELD_NAME_LNG]').val(positionResult.position.lng);
            $('#[FIELD_NAME_LAT]').val(positionResult.position.lat);
        });
        positionPicker.on('fail', function(positionResult) {
            console.log(positionResult);
        });
        positionPicker.start( 
            {if isset(\$data)}
            new AMap.LngLat({\$data.[FIELD_NAME_LNG]}, {\$data.[FIELD_NAME_LAT]})
            {/if}
            ); 
        map.panBy(0, 1);
        map.addControl(new AMap.ToolBar({
            liteStyle: true
        }))
    });
</script>\n
EOF;


    public string $dateHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control fieldDate">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
    });
</script>\n
EOF;




    public string $dateRangeHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control fieldDateRange">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        range: true
    });
</script>\n
EOF;


    public string $datetimeHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control fieldDatetime">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'datetime',
    });
</script>\n
EOF;


    public string $datetimeRangeHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control fieldDatetimeRange">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'datetime',
        range: true,
    });
</script>\n
EOF;


    public string $editorHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
        <div class="col-sm-10">
            <script id="[FIELD_NAME]" name="[FIELD_NAME]" type="text/plain">[FIELD_CONTENT]</script>
        </div>
    </div>
<script>
    UE.delEditor('[FIELD_NAME]');
    var UE_[FIELD_NAME] = UE.getEditor('[FIELD_NAME]',{
        serverUrl :UEServer
    });
</script>\n
EOF;


    /**
     * @var string 图标选择
     */
    public string $iconHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <div class="input-group iconpicker-container">
            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
            <input maxlength="50" id="[FIELD_NAME]" name="[FIELD_NAME]"
                   value="[FIELD_CONTENT]" class="form-control "
                   placeholder="请选择[FORM_NAME]">
        </div>
    </div>
</div>
<script>
    $('#[FIELD_NAME]').iconpicker({placement: 'bottomLeft'});
</script>\n
EOF;



    public string $multiFileHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-8"> 
        <input id="[FIELD_NAME]" name="[FIELD_NAME]"  placeholder="请上传[FORM_NAME]" type="file" class="form-control field-multi-file" >
    </div>
</div>
<script>
    $('#[FIELD_NAME]').fileinput({
        //theme: 'fas',
        language: 'zh',
    
        browseLabel: '浏览',
        initialPreviewAsData: false,
        initialPreviewShowDelete:false,
        dropZoneEnabled: false,
        showUpload:false,
        showRemove: false,
        allowedFileExtensions: ['jpg', 'png', 'gif','bmp','svg','jpeg','mp4','doc','docx','pdf','xls','xlsx','ppt','pptx','txt'],
        {if isset(\$data)}
        initialPreview:{\$data->getData('[FIELD_NAME]')|raw},
        {/if}
        //默认限制10M
        maxFileSize:10240
    });
</script>\n
EOF;


    /**
     * @var string 数字输入
     */
    public string $numberHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
         <div class="input-group">
            <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="number" class="form-control field-number">
        </div>
    </div>
</div>
<script>
    $('#[FIELD_NAME]')
        .bootstrapNumber({
            upClass: 'success',
            downClass: 'primary',
            center: true
        });
</script>\n
EOF;


    public string $passwordHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="password" class="form-control field-password">
    </div>
</div>\n
EOF;


    public string $selectHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <select name="[FIELD_NAME]" id="[FIELD_NAME]" class="form-control field-select" data-placeholder="请选择[FORM_NAME]">
            <option value=""></option>
            [OPTION_DATA]
        </select>
    </div>
</div>
<script>
 $('#[FIELD_NAME]').select2();
</script>\n
EOF;


    public string $switchHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
    <input class="input-switch"  id="[FIELD_NAME]" value="1" [SWITCH_CHECKED] type="checkbox" />
    <input class="switch field-switch" placeholder="[FORM_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" hidden />
    </div>
</div>\n
<script>
    $('#[FIELD_NAME]').bootstrapSwitch({
        onText: "[ON_TEXT]",
        offText: "[OFF_TEXT]",
        onColor: "success",
        offColor: "danger",
        onSwitchChange: function (event, state) {
            $(event.target).closest('.bootstrap-switch').next().val(state ? '1' : '0').change();
        }
    });
</script>
EOF;





    public string $textareaHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <textarea id="[FIELD_NAME]" name="[FIELD_NAME]" class="form-control" rows="[ROWS]" placeholder="请输入[FORM_NAME]">[FIELD_CONTENT]</textarea>
    </div>
</div>\n
EOF;


    public string $timeHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control filed-time">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'time',
    });
</script>\n
EOF;


    public string $timeRangeHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control filed-time-range">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'time',
        range: true,
    });
</script>\n
EOF;


    public string $urlHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请输入[FORM_NAME]" type="text" class="form-control field-map">
    </div>
</div>\n
EOF;


    public string $yearHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control filed-year">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'year',
    });
</script>\n
EOF;


    public string $yearMonthHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control filed-year-month">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'month',
    });
</script>\n
EOF;


    public string $yearMonthRangeHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control filed-year-month-range">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'month',
        range: true,
    });
</script>\n
EOF;


    public string $yearRangeHtml = <<<EOF
<div class="form-group row">
    <label for="[FIELD_NAME]" class="col-sm-2 control-label">[FORM_NAME]</label>
    <div class="col-sm-10 col-md-4 formInputDiv">
        <input id="[FIELD_NAME]" name="[FIELD_NAME]" value="[FIELD_CONTENT]" placeholder="请选择[FORM_NAME]" type="text" class="form-control filed-year-range">
    </div>
</div>
<script>
    laydate.render({
        elem: '#[FIELD_NAME]',
        type: 'year',
        range: true,
    });
</script>\n
EOF;


    protected function getFieldForm($type, $name, $field, $content, $option)
    {

        $html_var = parse_name($type, 1, 0) . 'Html';


        $form = $this->$html_var;
        switch ($type) {
            case 'switch':
                $form  = str_replace(array('[ON_TEXT]', '[OFF_TEXT]', '[SWITCH_CHECKED]'), array('是', '否', $content ? 'checked' : ''), $form);
                break;

            case 'select':

                $option_html = '';
                $option      = explode("\r\n", $option);
                foreach ($option as $item) {
                    $option_key_value = explode('||', $item);

                    $select='';
                    if ($content == $option_key_value[0]) {
                        $select = 'selected';
                    }
                    $option_html .= '<option value="' . $option_key_value[0] . '" ' . $select . '>' . $option_key_value[1] . '</option>';
                }

                $form = str_replace('[OPTION_DATA]', $option_html, $form);
                break;
            default:
                //$form = '';

                break;
        }

        $form = str_replace(array('[FIELD_NAME]', '[FORM_NAME]', '[FIELD_CONTENT]'), array($field, $name, $content), $form);
        return $form;
    }

}