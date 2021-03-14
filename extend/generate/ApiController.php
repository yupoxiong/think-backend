<?php
/**
 * api相关
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace generate;


class ApiController
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


        $this->createActionIndex();
        $this->createActionAdd();
        $this->createActionInfo();
        $this->createActionEdit();
        $this->createActionDel();
        $this->createActionDisable();
        $this->createActionEnable();


        $this->code = str_replace(
            array('[NAME]', '[CONTROLLER_NAME]', '[CONTROLLER_MODULE]',
                '[MODEL_NAME]', '[MODEL_MODULE]', '[VALIDATE_NAME]',
                '[VALIDATE_MODULE]'),
            array($this->data['cn_name'], $this->data['api_controller']['name'], $this->data['api_controller']['module'], $this->data['model']['name'], $this->data['model']['module'], $this->data['validate']['name'], $this->data['validate']['module'] ),
            $this->code
        );

        $msg = '';
        try {
            file_put_contents($this->config['file_dir']['api_controller'] . $this->data['api_controller']['name'] . 'Controller' . '.php', $this->code);
            $result = true;
        } catch (\Exception $e) {
            $msg    = $e->getMessage();
            $result = false;
        }
        return $result ?? $msg;

    }

    protected function createActionIndex(): void
    {
        $index_code = '';
        if (in_array('index', $this->data['api_controller']['action'], true)) {
            $index_code = file_get_contents($this->template['controller_index']);
        }

        $this->code = str_replace('[ACTION_INDEX]', $index_code, $this->code);
    }

    protected function createActionAdd(): void
    {
        $index_code = '';
        if (in_array('add', $this->data['api_controller']['action'], true)) {
            $index_code = file_get_contents($this->template['controller_add']);
        }

        $this->code = str_replace('[ACTION_ADD]', $index_code, $this->code);
    }


    protected function createActionInfo(): void
    {
        $index_code = '';
        if (in_array('info', $this->data['api_controller']['action'], true)) {
            $index_code = file_get_contents($this->template['controller_info']);
        }

        $this->code = str_replace('[ACTION_INFO]', $index_code, $this->code);
    }


    protected function createActionEdit(): void
    {
        $index_code = '';
        if (in_array('edit', $this->data['api_controller']['action'], true)) {
            $index_code = file_get_contents($this->template['controller_edit']);
        }

        $this->code = str_replace('[ACTION_EDIT]', $index_code, $this->code);
    }


    protected function createActionDel(): void
    {
        $index_code = '';
        if (in_array('del', $this->data['api_controller']['action'], true)) {
            $index_code = file_get_contents($this->template['controller_del']);
        }

        $this->code = str_replace('[ACTION_DEL]', $index_code, $this->code);
    }


    protected function createActionDisable(): void
    {
        $index_code = '';
        if (in_array('disable', $this->data['api_controller']['action'], true)) {
            $index_code = file_get_contents($this->template['controller_disable']);
        }

        $this->code = str_replace('[ACTION_DISABLE]', $index_code, $this->code);
    }

    protected function createActionEnable(): void
    {
        $index_code = '';
        if (in_array('enable', $this->data['api_controller']['action'], true)) {
            $index_code = file_get_contents($this->template['controller_enable']);
        }

        $this->code = str_replace('[ACTION_ENABLE]', $index_code, $this->code);
    }

}