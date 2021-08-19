

/**
 * 初始化上传单图
 * @param field
 * @param fileId
 */
function initUploadImg(field, fileId = '') {
    fileId = fileId || field+'_file'  ;
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
        dropZoneTitle:'点此上传图片或将图片拖到这里…',
        dropZoneClickTitle:'(或可点击下方选择按钮)',
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
 * 初始化上传单图
 * @param field
 * @param fileId
 */
function initUploadFile(field, fileId = '') {
    fileId = fileId || field+'_file'  ;
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
 * 初始化上传多图
 * @param field
 * @param fileId
 */
function initUploadImgList(field, fileId = '') {
    fileId = fileId || 'img_' + field;
    let $fileDom = $("#" + fileId);

    $fileDom.fileinput({
        theme: 'fas',
        language: 'zh',
        //showDrag: true,
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
        initialPreviewShowDelete: false,
        fileActionSettings: {
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
    }).on('filedeleted', function (event, key, data) {
        if (adminDebug) {
            console.log('删除文件');
            console.log(event);
            console.log(key);
            console.log(data);
        }

    }).on('filesorted', function (event, params) {
        if (adminDebug) {
            console.log('排序文件');
            console.log(event);
            console.log(params);
        }
    });
}