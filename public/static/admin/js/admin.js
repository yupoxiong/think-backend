try {
    /** pjax相关 */
    $.pjax.defaults.timeout = 3000;
    $.pjax.defaults.type = 'GET';
    $.pjax.defaults.container = '#pjaxContainer';
    $.pjax.defaults.fragment = '#pjaxContainer';
    $.pjax.defaults.maxCacheLength = 0;
    $(document).pjax('a:not(a[target="_blank"])', {	//
        container: '#pjaxContainer',
        fragment: '#pjaxContainer'
    });
    $(document).ajaxStart(function () {
        NProgress.start();
    }).ajaxStop(function () {
        NProgress.done();
    });
} catch (e) {
    console.log(e.message);
}

$(document).on('pjax:timeout', function (event) {
    event.preventDefault();
});
$(document).on('pjax:send', function (xhr) {
    NProgress.start();
});
$(document).on('pjax:complete', function (xhr) {
    initToolTip();
    NProgress.done();
});
//列表页搜索pjax
$(document).on('submit', '.searchForm', function (event) {
    $.pjax.submit(event, '#pjaxContainer');
});

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


/* 清除搜索表单 */
function clearSearchForm() {
    let url_all = window.location.href;
    let arr = url_all.split('?');
    let url = arr[0];
    $.pjax({url: url, container: '#pjaxContainer'});
}


$(function () {

    initToolTip();

    let $body = $('body');
    /* 返回按钮 */
    $body.on('click', '.BackButton', function (event) {
        event.preventDefault();
        history.back(1);
    });

    /* 刷新按钮 */
    $body.on('click', '.ReloadButton', function (event) {
        event.preventDefault();
        $.pjax.reload();
    });

});

/**
 * 初始化提示
 */
function initToolTip() {
    // 提示泡
    $('[data-toggle="tooltip"]').tooltip({
        container:'#pjaxContainer'
    });
}

/*列表中单个选择和取消*/
function checkThis(obj) {
    let id = $(obj).attr('value');
    if ($(obj).is(':checked')) {
        if ($.inArray(id, dataSelectIds) < 0) {
            dataSelectIds.push(id);
        }
    } else {
        if ($.inArray(id, dataSelectIds) > -1) {
            dataSelectIds.splice($.inArray(id, dataSelectIds), 1);
        }
    }

    let all_length = $("input[name='dataCheckbox']").length;
    let checked_length = $("input[name='dataCheckbox']:checked").length;
    if (all_length === checked_length) {
        $("#dataCheckAll").prop("checked", true);
    } else {
        $("#dataCheckAll").prop("checked", false);
    }
    console.log(dataSelectIds);
}

/*全部选择/取消*/
function checkAll(obj) {
    dataSelectIds = [];
    var all_check = $("input[name='dataCheckbox']");
    if ($(obj).is(':checked')) {
        all_check.prop("checked", true);
        $(all_check).each(function () {
            dataSelectIds.push(this.value);
        });
    } else {
        all_check.prop("checked", false);
    }
}


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
    showResultMsg(title, text, 'error', timer)
}


/**
 * 显示请求结果
 */
function showResultMsg(title = '', text = '', icon = 'error', timer = 1600) {
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
    } else if (url === 4 || url === 'url://close-refresh') {
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
                container: '#pjaxContainer'
            });
        } catch (e) {
            window.location.href = url;
        }
    }
}