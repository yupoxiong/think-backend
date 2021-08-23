/**
 * 初始化上传单图
 * @param field
 * @param fileId
 */
function initUploadImg(field, fileId = '') {
    fileId = fileId || field + '_file';
    let $fileDom = $("#" + fileId);

    $fileDom.fileinput({
        theme: 'fas',
        language: 'zh',
        showDrag: false,
        showClose: false,
        showBrowse: true,
        showUpload: false,
        showRemove: false,
        uploadAsync: true,
        showCaption: false,
        showCancel: false,
        showDownload: false,
        browseOnZoneClick: true,
        overwriteInitial: true,
        initialPreviewAsData: true,
        uploadUrl: uploadImgUrl,
        minFileCount: 1,
        maxFileCount: 1,
        autoReplace: true,
        initialPreviewShowDelete: false,
        dropZoneTitle: '点此上传图片或将图片拖到这里…',
        dropZoneClickTitle: '(或点击下方选择按钮)',
        fileActionSettings: {
            showDrag: false,
            showDownload: false,
        },
        uploadExtraData: {
            file_field: fileId,
        },
    }).on("filebatchselected", function (event, files) {
        if (adminDebug) {
            console.log('选择文件');
            console.log(event, files);
        }
        $fileDom.fileinput("upload");
    }).on('fileuploaded', function (event, data, previewId, index) {
        if (adminDebug) {
            console.log('上传文件');
            console.log(event, data, previewId, index);
        }

        let response = data.response;
        if (response.code === 200 && response.initialPreview !== undefined) {
            $('#' + field).val(response.initialPreview[0]);
        }
    });
}


/**
 * 初始化上传多图
 * @param field
 * @param fileId
 */

function initUploadMultiImg(field, fileId = '') {
    let $field = $('#' + field);
    fileId = fileId || field + '_file';
    let $fileDom = $("#" + fileId);
    let initialPreview;
    let initialPreviewConfig = [];

    if ($field.val().length > 0) {
        initialPreview = $field.val().split(',');

        $.each(initialPreview, function (index, value) {
            initialPreviewConfig[index] = {
                downloadUrl: value,
                url: fileDelUrl + '?file=' + value,
            };
        });
    }

    $fileDom.fileinput({
        theme: 'fas',
        language: 'zh',
        showDrag: true,
        showClose: false,
        showBrowse: true,
        showUpload: false,
        showRemove: false,
        uploadAsync: true,
        showCaption: false,
        showCancel: false,
        showDownload: false,
        overwriteInitial: false,
        browseOnZoneClick: true,
        initialPreviewAsData: true,
        uploadUrl: uploadImgUrl,
        minFileCount: 1,
        maxFileCount: 10,
        initialPreviewShowDelete: true,
        dropZoneTitle: '点此上传图片或将图片拖到这里(支持多图上传)<br/>',
        dropZoneClickTitle: '(或点击下方选择按钮)',
        fileActionSettings: {
            showDownload: false,
            showDrag: true,
        },
        initialPreviewFileType: 'image',
        uploadExtraData: {
            file_field: fileId,
        },
        initialPreview: initialPreview,
        initialPreviewConfig: initialPreviewConfig,
    }).on("filebatchselected", function (event, files) {
        if (adminDebug) {
            console.log('选择文件');
            console.log(event, files);
        }
        $fileDom.fileinput("upload");
    }).on('fileuploaded', function (event, data, previewId, index) {
        setNewContent($fileDom, $field);
        if (adminDebug) {
            console.log('上传文件');
            console.log(event, data, previewId, index);
        }
    }).on('filedeleted', function (event, key, data) {
        setNewContent($fileDom, $field);
        if (adminDebug) {
            console.log('删除文件');
            console.log(event, key, data);
        }
    }).on('filesorted', function (event, params) {
        setNewContent($fileDom, $field);
        if (adminDebug) {
            console.log('排序文件');
            console.log(event, params);
        }
    });
}


/**
 * 初始化上传单文件
 * @param field
 * @param fileId
 */
function initUploadFile(field, fileId = '') {
    fileId = fileId || field + '_file';
    let $fileDom = $("#" + fileId);

    $fileDom.fileinput({
        theme: 'fas',
        language: 'zh',
        showDrag: false,
        showClose: false,
        showBrowse: true,
        showUpload: false,
        showRemove: false,
        uploadAsync: true,
        showCaption: false,
        showCancel: false,
        showDownload: false,
        browseOnZoneClick: true,
        overwriteInitial: true,
        initialPreviewAsData: true,
        uploadUrl: uploadImgUrl,
        maxTotalFileCount: 2,
        autoReplace: true,
        initialPreviewShowDelete: false,
        fileActionSettings: {
            showDrag: false,
            showDownload: false,
        },
        initialPreviewFileType: 'image',
        uploadExtraData: {
            file_field: fileId,
        },
    }).on("filebatchselected", function (event, files) {
        if (adminDebug) {
            console.log('选择文件');
            console.log(event);
            console.log(files);
        }

        $fileDom.fileinput("upload");
    }).on('fileuploaded', function (event, data, previewId, index) {
        if (adminDebug) {
            console.log('上传文件');
            console.log(event);
            console.log(data);
            console.log(previewId);
            console.log(index);
        }

        let response = data.response;
        if (response.code === 200 && response.initialPreview !== undefined) {
            $('#' + field).val(response.initialPreview[0]);
        }
    });
}


/**
 * 设置新的图片
 * @param $fileDom
 * @param $field
 * @returns {*}
 */
function setNewContent($fileDom, $field) {
    let current_preview = ($fileDom.fileinput('getPreview'));
    let preview_content = current_preview.content;
    let new_content = preview_content.join(",");
    $field.val(new_content);

    if (adminDebug) {
        console.log(preview_content);
    }

    return new_content;
}
