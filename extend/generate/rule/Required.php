<?php
/**
 * required规则相关
 */

namespace generate\rule;

class Required extends Rule
{

    public string $validateRule = 'require';

    public string $validateMsg = <<<EOF
    '[FIELD_NAME].require' => '[FORM_NAME]不能为空',\n
EOF;

    public string $formRule = <<<EOF
    '[FIELD_NAME]': {
        required: true,
    },\n
EOF;

    public string $formMsg = <<<EOF
    '[FIELD_NAME]': {
        required: "[FORM_NAME]不能为空",
    },\n
EOF;

}