<?php
/**
 * 16进制颜色规则
 */

namespace generate\rule;

class Color16 extends Rule
{

    public string  $validateRule = 'color16';

    public string $validateMsg = <<<EOF
    '[FIELD_NAME].color16' => '[FORM_NAME]格式不正确',\n
EOF;

    public string $ruleForm = <<<EOF
    '[FIELD_NAME]': {
        mobile: true,
    },\n
EOF;

    public string $msgForm = <<<EOF
    '[FIELD_NAME]': {
        mobile: "[FORM_NAME]格式不正确",
    },\n
EOF;

}