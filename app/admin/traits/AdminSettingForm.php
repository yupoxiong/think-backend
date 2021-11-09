<?php

namespace app\admin\traits;

use generate\field\Field;

trait AdminSettingForm
{

    protected function getFieldForm($type, $name, $field, $content, $option)
    {
        /** @var Field $fieldClass */
        $fieldClass ='\\generate\\field\\'. parse_name($type, 1, 0) ;
        $form = $fieldClass::$html;

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
                    if ($content === $option_key_value[0]) {
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

        return str_replace(array('[FIELD_NAME]', '[FORM_NAME]', '[FIELD_CONTENT]'), array($field, $name, $content), $form);
    }

}