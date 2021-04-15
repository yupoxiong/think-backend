<?php
/**
 * required规则相关
 */
namespace generate\rule;

class Required
{
    public static string $ruleValidate =  <<<EOF
    '[FIELD_NAME]|[FORM_NAME]' => 'require',\n
EOF;

    public static string $msgValidate =  <<<EOF
    '[FIELD_NAME].require' => '[FORM_NAME]不能为空',\n
EOF;

    public static string $ruleForm =  <<<EOF
    '[FIELD_NAME]': {
        required: true,
    },\n
EOF;

    public static string $msgForm =  <<<EOF
    '[FIELD_NAME]': {
        required: "[FORM_NAME]不能为空",
    },\n
EOF;

}