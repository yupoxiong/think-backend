<?php
/**
 * api相关
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace generate;


use Exception;

class ApiService
{

    /**
     * @var array 数据
     */
    protected array $data;
    /**
     * @var array 配置
     */
    protected array $config;

    protected array $template;

    protected string $code;

    protected array $actionList = [
        'index', 'add', 'info', 'edit', 'del', 'disable', 'enable'
    ];

    public function __construct($data, $config)
    {
        $this->data   = $data;
        $this->config = $config;

        $this->template = $this->config['template']['api'];

        $this->code = file_get_contents($this->template['service']);
    }

    /**
     * 创建API模块控制器相关代码
     * @return bool|string
     */
    public function create()
    {

        $out_file = $this->config['file_dir']['api_service'] . $this->data['api_controller']['name'] . 'Service' . '.php';

        $replace_content = [
            '[NAME]'              => $this->data['cn_name'],
            '[TABLE_NAME]'        => $this->data['table'],
            '[MODEL_NAME]'        => $this->data['model']['name'],
            '[MODEL_MODULE]'      => $this->data['model']['module'],
            '[VALIDATE_NAME]'     => $this->data['validate']['name'],
            '[VALIDATE_MODULE]'   => $this->data['validate']['module'],
            '[SERVICE_NAME]'      => $this->data['api_controller']['name'],
            '[SERVICE_MODULE]'    => $this->data['api_controller']['module'],
        ];

        foreach ($replace_content as $key => $value) {
            $this->code = str_replace($key, $value, $this->code);
        }


        $msg = '';
        try {
            file_put_contents($out_file, $this->code);
            $result = true;
        } catch (Exception $e) {
            $msg    = $e->getMessage();
            $result = false;
        }
        return $result ?? $msg;

    }

}