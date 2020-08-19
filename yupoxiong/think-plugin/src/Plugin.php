<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace yupoxiong\plugin;

use Closure;
use RuntimeException;
use think\App;
use think\exception\HttpException;
use think\Request;
use think\Response;

class Plugin
{
    /** @var App */
    protected $app;

    /**
     * 应用名称
     * @var string
     */
    protected $name;

    /**
     * 应用名称
     * @var string
     */
    protected $appName;


    /**
     * 应用路径
     * @var string
     */
    protected $path;

    public function __construct(App $app)
    {
        $this->app  = $app;
        $this->name = $this->app->http->getName();
        $this->path = $this->app->http->getPath();
    }

    /**
     * 插件解析
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {

        if (!$this->parseMultiApp()) {
            return $next($request);
        }

        return $this->app->middleware->pipeline('app')
            ->send($request)
            ->then(function ($request) use ($next) {
                return $next($request);
            });
    }

    /**
     * 获取路由目录
     * @access protected
     * @param $appName
     * @return string
     */
    protected function getRoutePath($appName): string
    {
        return $this->getPluginPath() . $appName . DIRECTORY_SEPARATOR . 'route' . DIRECTORY_SEPARATOR;
    }

    /**
     * 解析多应用
     * @return bool
     */
    protected function parseMultiApp(): bool
    {

        $defaultApp = $this->app->config->get('plugin.default_plugin') ?: 'index';


        // 自动多应用识别
        $this->app->http->setBind(false);
        $appName       = null;
        $this->appName = '';

        $bind = $this->app->config->get('app.domain_bind', []);

        if (!empty($bind)) {
            // 获取当前子域名
            $subDomain = $this->app->request->subDomain();
            $domain    = $this->app->request->host(true);

            if (isset($bind[$domain])) {
                $appName = $bind[$domain];
                $this->app->http->setBind();
            } elseif (isset($bind[$subDomain])) {
                $appName = $bind[$subDomain];
                $this->app->http->setBind();
            } elseif (isset($bind['*'])) {
                $appName = $bind['*'];
                $this->app->http->setBind();
            }
        }

        if (!$this->app->http->isBind()) {
            $path = $this->app->request->pathinfo();
            $map  = $this->app->config->get('app.app_map', []);
            $deny = $this->app->config->get('app.deny_app_list', []);
            $name = current(explode('/', $path));

            if (strpos($name, '.')) {
                $name = strstr($name, '.', true);
            }

            if (isset($map[$name])) {
                if ($map[$name] instanceof Closure) {
                    $result  = call_user_func_array($map[$name], [$this->app]);
                    $appName = $result ?: $name;
                } else {
                    $appName = $map[$name];
                }
            } elseif ($name && (false !== array_search($name, $map) || in_array($name, $deny))) {
                throw new HttpException(404, 'app not exists:' . $name);
            } elseif ($name && isset($map['*'])) {
                $appName = $map['*'];
            } else {
                $appName = $name ?: $defaultApp;
                $appPath = $this->path ?: $this->app->getBasePath() . $appName . DIRECTORY_SEPARATOR;

                if (!is_dir($appPath)) {
                    $express = $this->app->config->get('app.app_express', false);
                    if ($express) {
                        $this->setPlugin($defaultApp);
                        return true;
                    }
                    return false;
                }
            }

            if ($name) {
                $this->app->request->setRoot('/' . $name);
                $this->app->request->setPathinfo(strpos($path, '/') ? ltrim(strstr($path, '/'), '/') : '');
            }
        }


        $this->setPlugin($appName ?: $defaultApp);
        return true;
    }


    /**
     * 设置应用
     * @param string $pluginName
     */
    protected function setPlugin(string $pluginName): void
    {
        $this->appName = $pluginName;
        $this->app->http->name($pluginName);

        $pluginPath = $this->path ?: $this->getPluginPath() . $pluginName . DIRECTORY_SEPARATOR;

        $this->app->setAppPath($pluginPath);
        // 设置应用命名空间
        $this->app->setNamespace($this->app->config->get('plugin.app_namespace') ?: 'plugin\\' . $pluginName);

        if (is_dir($pluginPath)) {
            $this->app->setRuntimePath($this->app->getRuntimePath() . $this->getPluginPath() . $pluginName . DIRECTORY_SEPARATOR);
            $this->app->http->setRoutePath($this->getRoutePath($pluginName));

            //加载应用
            $this->loadPlugin($pluginName, $pluginPath);
        }
    }

    /**
     * 加载应用文件
     * @param string $appName 应用名
     * @param string $pluginPath 插件目录
     * @return void
     */
    protected function loadPlugin(string $appName, string $pluginPath): void
    {
        $currentPluginPath = $pluginPath . DIRECTORY_SEPARATOR . $appName. DIRECTORY_SEPARATOR ;

        if (is_file($currentPluginPath . 'common.php')) {
            include_once $currentPluginPath. 'common.php';
        }

        $files = [];

        $files = array_merge($files, glob($currentPluginPath . 'config' . DIRECTORY_SEPARATOR . '*' . $this->app->getConfigExt()));

        foreach ($files as $file) {
            $this->app->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }

        if (is_file($currentPluginPath . 'event.php')) {
            $this->app->loadEvent(include $currentPluginPath . 'event.php');
        }

        if (is_file($currentPluginPath . 'middleware.php')) {
            $this->app->middleware->import(include $currentPluginPath . 'middleware.php', 'app');
        }

        if (is_file($currentPluginPath . 'provider.php')) {
            $this->app->bind(include $currentPluginPath . 'provider.php');
        }

        // 加载应用默认语言包
        $this->app->loadLangPack($this->app->lang->defaultLangSet());
    }

    /**
     * 获取插件目录
     * @return string
     */
    public function getPluginPath(): string
    {
        $plugin_path = $this->app->getRootPath() . 'plugin' . DIRECTORY_SEPARATOR;

        if (!is_dir($plugin_path) && !mkdir($plugin_path, 0755, true) && !is_dir($plugin_path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $plugin_path));
        }
        return $plugin_path;
    }
}