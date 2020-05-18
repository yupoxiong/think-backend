
try {
    /** pjax相关 */
    $.pjax.defaults.timeout = 3000;	//超时3秒(可选)
    $.pjax.defaults.type = 'GET';
    $.pjax.defaults.container= '#pjax-container';	//存储容器id
    $.pjax.defaults.fragment='#pjax-container';	//目标id
    $.pjax.defaults.maxCacheLength = 0;	//最大缓存长度(可选)
    $(document).pjax('a:not(a[target="_blank"])', {	//
        container: '#pjax-container',	//存储容器id
        fragment:'#pjax-container'	//目标id
    });
    $(document).ajaxStart(function(){	//ajax请求开始时执行
        NProgress.start();	//启动进度条
    }).ajaxStop(function(){	//ajax请求结束后执行
        NProgress.done();	//关闭进度条
    });
}catch (e) {
    console.log(e.message);
}
