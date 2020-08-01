/* 表单提交 */
function formSubmit(form) {

    swal('ddddd');

    let loadT = layer.msg('正在提交，请稍候…', {icon: 16, time: 0, shade: [0.3, "#000"],scrollbar: false,});
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
                layer.close(loadT);
                layer.msg(result.msg, {
                    icon: result.code ? 1 : 2,
                    scrollbar: false,
                });
                if (adminDebug) {
                    console.log('submit success!');
                    if (result.code === 1) {
                        console.log('%cresult success', ';color:#00a65a');
                    } else {
                        console.log('%cresult fail', ';color:#f39c12');
                    }
                }
                goUrl(result.url);
            },
            error: function (xhr, type, errorThrown) {
                //异常处理；
                if (adminDebug) {
                    console.log('%csubmit fail!', ';color:#dd4b39');
                    console.log();
                    console.log("type:" + type + ",readyState:" + xhr.readyState + ",status:" + xhr.status);
                    console.log("url:" + action);
                    console.log("data:" + data);
                    layer.close(loadT);
                }
                layer.msg('访问错误,代码' + xhr.status, {icon: 2,scrollbar: false,});
            }
        }
    );
    return false;
}
