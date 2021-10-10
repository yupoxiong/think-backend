<?php
/**
 * 验证器生成
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace generate;

use Exception;
use generate\exception\GenerateException;
use generate\field\Field;

class ValidateBuild extends Build
{

    /**
     * ValidateBuild constructor.
     * @param array $data 数据
     * @param array $config 配置
     */
    public function __construct(array $data, array $config)
    {
        $this->data   = $data;
        $this->config = $config;

        $this->template = $this->config['template']['validate'];

        $this->code = file_get_contents($this->template);
    }

    /**
     * 生成验证器
     * @return bool
     * @throws GenerateException
     */
    public function run(): bool
    {
        // 不生成验证器
        if (!$this->data['validate']['create']) {
            return true;
        }

        $file = $this->config['template']['validate'];
        $code = file_get_contents($file);
        $code = str_replace(array('[NAME]', '[VALIDATE_NAME]', '[VALIDATE_MODULE]'), array($this->data['cn_name'], $this->data['validate']['name'], $this->data['validate']['module']), $code);

        $rule_code      = '';
        $msg_code       = '';
        $scene_code     = Field::$validateSceneCode;
        $scene_code_tmp = '';
        foreach ($this->data['data'] as $key => $value) {
            $temp_rule_code = '';
            if ($value['is_form']) {
                foreach ($value['form_validate'] as $rule_key => $value_name) {
                    $class = '\\generate\\rule\\' . parse_name($value_name, 1);
                    if (class_exists($class)) {
                        $newClass = (new $class);
                        $is_end    = isset($value['form_validate'][$rule_key + 1]) ? false : true;
                        $temp_rule_code = $newClass->getValidateRuleCode($value, $temp_rule_code, $is_end);
                        $msg_code  .= $newClass->getValidateMsgCode($value);
                    }
                }
                $scene_code_tmp .= "'" . $value['field_name'] . "', ";
            }
            $rule_code.= $temp_rule_code;
        }

        $scene_code = str_replace('[RULE_FIELD]', $scene_code_tmp, $scene_code);

        $code = str_replace(array('[VALIDATE_RULE]', '[VALIDATE_MSG]', '[VALIDATE_SCENE]', '[VALIDATE_SCENE_FUNC]'), array($rule_code, $msg_code, $scene_code, ''), $code);

        try {
            file_put_contents($this->config['file_dir']['validate'] . $this->data['validate']['name'] . 'Validate' . '.php', $code);
        } catch (Exception $e) {
            throw new GenerateException($e->getMessage());
        }

        return  true;
    }

}
