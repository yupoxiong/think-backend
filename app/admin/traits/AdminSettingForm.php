<?php

namespace app\admin\traits;

use generate\field\Field;

trait AdminSettingForm
{

    protected function getFieldForm($type, $name, $field, $content, $option)
    {
        /** @var Field $fieldClass */
        $fieldClass = '\\generate\\field\\' . parse_name($type === 'switch' ? 'switch_field' : $type, 1, 0);
        $form       = $fieldClass::$html;
        switch ($type) {
            case 'switch':
                $content_int = (int)$content;
                $search1 = "{if(!isset(\$data) ||\$data.[FIELD_NAME]==1)}checked{/if}";
                $search2 = "{\$data.[FIELD_NAME]|default='[FIELD_DEFAULT]'}";
                $form = str_replace(array($search1,$search2, '[ON_TEXT]', '[OFF_TEXT]'), array($content ? 'checked' : '',$content_int,'是', '否', ), $form);
                break;
            case 'select':
            case 'multi_select':

                $option_html = '';
                $option      = explode("\r\n", $option);
                foreach ($option as $item) {
                    $option_key_value = explode('||', $item);

                    $select = '';
                    if ($content === $option_key_value[0]) {
                        $select = 'selected';
                    }
                    $option_html .= '<option value="' . $option_key_value[0] . '" ' . $select . '>' . $option_key_value[1] . '</option>';
                }

                $form = str_replace('[OPTION_DATA]', $option_html, $form);
                break;
            case 'image':
                $search1 = "{if isset(\$data)}{\$data.[FIELD_NAME]}{/if}";
                $form = str_replace(array($search1), array($content), $form);

                break;
            case 'editor':
                $search1 = "{\$data.[FIELD_NAME]|raw|default='[FIELD_DEFAULT]'}";

                $search2  = "{\$data.[FIELD_NAME]|raw|default='<p>[FIELD_DEFAULT]</p>'}";
                $replace2 = $content === '' ? $content : '<p></p>';

                $form = str_replace(array($search1, $search2), array($content, $replace2), $form);

                break;
            default:
                //$form = '';

                break;
        }

        $form_value = "{\$data.[FIELD_NAME]|default='[FIELD_DEFAULT]'}";

        return str_replace(array($form_value, '[FIELD_NAME]', '[FORM_NAME]',), array($content, $field, $name,), $form);

    }

}