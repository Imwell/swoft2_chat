function ajaxGet(url, data, callback) {
    $.ajax(url, {
        type: 'GET',
        data: data,
        dataType: 'json',
        cache: false,
        success: (res) => {
            if (typeof callback === 'function') {
                callback(res);
            } else {
                console.log(res);
            }
        },
        error: (res) => {
            console.log(res);
        }
    });
}
function ajaxPost(url, data, callback) {
    $.ajax(url, {
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        success: (res) => {
            if (typeof callback === 'function') {
                callback(res);
            } else {
                console.log(res);
            }
        },
        error: (res) => {
            console.log(res);
        }
    });
}
function isEmpty(value) {
    value = $.trim(value);
    if (value === '' || value === null || value === undefined) {
        return true;
    }
    return false;
}
