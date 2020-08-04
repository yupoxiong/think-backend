try {
    /** pjax相关 */
    $.pjax.defaults.timeout = 3000;	//超时3秒(可选)
    $.pjax.defaults.type = 'GET';
    $.pjax.defaults.container = '#pjax-container';	//存储容器id
    $.pjax.defaults.fragment = '#pjax-container';	//目标id
    $.pjax.defaults.maxCacheLength = 0;	//最大缓存长度(可选)
    $(document).pjax('a:not(a[target="_blank"])', {	//
        container: '#pjax-container',	//存储容器id
        fragment: '#pjax-container'	//目标id
    });
    $(document).ajaxStart(function () {	//ajax请求开始时执行
        NProgress.start();	//启动进度条
    }).ajaxStop(function () {	//ajax请求结束后执行
        NProgress.done();	//关闭进度条
    });
} catch (e) {
    console.log(e.message);
}

/** 表单验证 */
$.validator.setDefaults({
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.input-group').append(error);
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function (form) {
        console.log('前端验证成功，开始提交表单');

        submitForm(form);
        return false;
    }
});


function submitForm(form) {


    openLoading();

    let action = $(form).attr('action');
    let method = $(form).attr('method');
    let data = new FormData($(form)[0]);

    if (adminDebug) {
        console.log('%cajax submit start!', ';color:#333333');
        console.log('action:' + action);
        console.log('method:' + method);
        console.log('data:' + data);
    }

    $.ajax({
            url: action,
            dataType: 'json',
            type: method,
            data: data,
            contentType: false,
            processData: false,
            success: function (result) {
                closeLoading();
                showSuccessMsg(result.msg);

                if (adminDebug) {
                    console.log('submit success!');
                    console.log('%cresult success', ';color:#00a65a');
                }
                goUrl(result.url);
            },
            error: function (xhr, type, errorThrown) {
                closeLoading();

                let errorTitle = '';
                let errorText = '';

                // 调试信息
                if (adminDebug) {
                    console.log('%csubmit fail!', ';color:#dd4b39');
                    console.log("type:" + type + ",readyState:" + xhr.readyState + ",status:" + xhr.status);
                    console.log("errorThrown:" + errorThrown);
                }

                if (xhr.responseJSON.code !== undefined && xhr.responseJSON.code === 500) {
                    errorTitle = xhr.responseJSON.msg;
                } else {
                    errorTitle = '系统繁忙';
                    errorText = '代码' + xhr.status;
                }

                showErrorMsg(errorTitle, errorText);
            },

        }
    );
    return false;
}


/**
 * 打开请求加载弹窗
 * @param text 内容
 * @param title 标题
 * @param icon 图标
 */
function openLoading(text = '正在请求，请稍候…', title = '请求中', icon = 'info') {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        heightAuto: false,

    });
    Swal.showLoading();
}

/**
 * 关闭请求加载弹窗
 */
function closeLoading() {
    Swal.close();
}


function showSuccessMsg(title = '请求成功', text = '', timer = 1600) {
    showResultMsg(title, text, 'success', timer)
}

function showErrorMsg(title = '请求失败', text = '', timer = 1600) {
    showResultMsg( title, text, 'error', timer)
}


/**
 * 显示请求结果
 */
function showResultMsg( title = '', text = '', icon = 'error', timer = 1600) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        timer: timer,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        heightAuto: false,
    })
}


/** 跳转到指定url */
function goUrl(url = 1) {
    console.log(url);
    //清除列表页选择的ID
    if (url !== 'url://current' && url !== 1) {
        dataSelectIds = [];
    }
    if (url === 'url://current' || url === 1) {
        console.log('Stay current page.');
    } else if (url === 'url://reload' || url === 2) {
        console.log('Reload current page.');
        $.pjax.reload();
    } else if (url === 'url://back' || url === 3) {
        console.log('Return to the last page.');
        history.back(1);
    }else if (url === 4 || url === 'url://close-refresh') {
        console.log('Close this layer page and refresh parent page.');
        let indexWindow = parent.layer.getFrameIndex(window.name);
        //先刷新父级页面
        parent.goUrl(2);
        //再关闭当前layer弹窗
        parent.layer.close(indexWindow);
    } else if (url === 5 || url === 'url://close-layer') {
        console.log('Close this layer page.');
        let indexWindow = parent.layer.getFrameIndex(window.name);
        parent.layer.close(indexWindow);
    } else {
        console.log('Go to ' + url);
        try {
            $.pjax({
                url: url,
                container: '#pjax-container'
            });
        } catch (e) {
            window.location.href = url;
        }
    }
}