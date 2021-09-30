<?php
/**
 * api控制器相关生成
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace generate;


class ApiControllerBuild
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

        $this->code = file_get_contents($this->template['controller']);
    }

    /**
     * 创建API模块控制器相关代码
     * @return bool|string
     */
    public function create()
    {

        $this->createAction();

        $out_file = $this->config['file_dir']['api_controller'] . $this->data['api_controller']['name'] . 'Controller' . '.php';

        $replace_content = [
            '[NAME]'              => $this->data['cn_name'],
            '[TABLE_NAME]'        => $this->data['table'],
            '[CONTROLLER_NAME]'   => $this->data['api_controller']['name'],
            '[CONTROLLER_MODULE]' => $this->data['api_controller']['module'],
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
        } catch (\Exception $e) {
            $msg    = $e->getMessage();
            $result = false;
        }
        return $result ?? $msg;

    }

    protected function createAction(): void
    {

        foreach ($this->actionList as $action) {
            if (!in_array($action, $this->data['api_controller']['action'], true)) {
                $upper      = strtoupper($action);
                $this->code = str_replace('[ACTION_' . $upper . ']', '', $this->code);
            }
        }

        foreach ($this->data['api_controller']['action'] as $action) {

            $upper = strtoupper($action);
            if (false !== strpos($this->code, $upper)) {
                $tmp_code   = file_get_contents($this->template['controller_' . $action]);
                $this->code = str_replace('[ACTION_' . $upper . ']', $tmp_code, $this->code);
            }
        }

    }


}