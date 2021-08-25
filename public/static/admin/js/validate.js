$(function () {
    // 手机号
    $.validator.addMethod("mobile", function (value, element, params) {
        if (adminDebug) {
            console.log('验证手机号', value, element, params);
        }
        return params === true ? (/^1[3-9]\d{9}$/.test(value)) : true;

    }, "手机号格式不正确");

    // 身份证号
    $.validator.addMethod("idCard", function (value, element, params) {
        if (adminDebug) {
            console.log('验证身份证号', value, element, params);
        }
        return params === true ? (/^1[3456789]\d{9}$/.test(value)) : true;

    }, "身份证号格式不正确");

    // 邮箱
    $.validator.addMethod("email", function (value, element, params) {
        if (adminDebug) {
            console.log('验证邮箱', value, element, params);
        }
        return params === true ? (/^1[3456789]\d{9}$/.test(value)) : true;

    }, "邮箱格式不正确");
});