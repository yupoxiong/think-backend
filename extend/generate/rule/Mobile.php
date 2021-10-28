<?php
/**
 * 手机号规则相关
 */

namespace generate\rule;

class Mobile extends Rule
{

    public string  $validateRule = 'mobile';

    public string $validateMsg = <<<EOF
    '[FIELD_NAME].mobile' => '[FORM_NAME]格式不正确',\n
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