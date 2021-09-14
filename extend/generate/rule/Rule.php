<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace generate\rule;


class Rule
{
    public  string $validateRuleBaseStr = <<<EOF
    '[FIELD_NAME]|[FORM_NAME]' => '
EOF;
    public string $validateRule;
    public string $validateMsg;

    public  string $formRule;
    public string $formMsg;

    public  function getValidateRuleCode($value, $rule_code, $is_end = false): string
    {
        if ($rule_code === '') {
            $rule_code = str_replace(array('[FORM_NAME]', '[FIELD_NAME]'), array($value['form_name'], $value['field_name']),  $this->validateRuleBaseStr);
            $rule_code .= $this->validateRule;
        } else {
            $rule_code .= '|' . $this->validateRule;
        }
        return $is_end ? $rule_code . "',\n" : $rule_code;
    }

    public function getValidateMsgCode($value)
    {
        return str_replace(array('[FORM_NAME]', '[FIELD_NAME]'), array($value['form_name'], $value['field_name']), $this->validateMsg);
    }


}