<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace yupoxiong\plugin;


use yupoxong\plugin\command\Create;

use think\Service as ThinkService;

class Service extends ThinkService
{

    public function boot()
    {
        $this->app->event->listen('HttpRun', function () {
            $this->app->middleware->add(Plugin::class);
        });

        $this->commands([
            'plugin:create' => Create::class,
        ]);

        $this->app->bind([
            'think\route\Url' => Url::class,
        ]);
    }
}