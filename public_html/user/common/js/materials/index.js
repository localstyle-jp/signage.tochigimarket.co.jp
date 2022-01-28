function ajax_get(url, method, params, success, responseJson) {
    var data = null;
    var xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.onload = function(event) {
        if (typeof responseJson === "undefined") {
            responseJson = true;
        }
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                data = xhr.responseText;
                if (responseJson) {
                    data = JSON.parse(xhr.responseText);
                }
                success(data);
            } else {
                console.log(xhr.statusText);
            }
        }
    };
    xhr.onerror = function(event) {
        console.log(event.type);
    };

    if (method == 'GET') {
        xhr.send(null);
    } else {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send( EncodeHTMLForm(params));
    }

    return data;
}
// HTMLフォームの形式にデータを変換する
function EncodeHTMLForm( data )
{
    var params = [];

    for( var name in data )
    {
        var value = data[ name ];
        var param = encodeURIComponent( name ) + '=' + encodeURIComponent( value );

        params.push( param );
    }

    return params.join( '&' ).replace( /%20/g, '+' );
}

$(function () {
    // 日付
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    //日付変更時に実行
    $('.datepicker').on('change', function() {
        var $this = $(this);
        if (! $this.val() && $this.data('auto-date')) {
            var dt = new Date(), d = [];
            var m = '0' + (dt.getMonth() + 1);
            var dd = '0' + dt.getDate();
            d.push(dt.getFullYear()),
            d.push(m.substr(m.length-2,2)),
            d.push(dd.substr(dd.length-2,2));
            $(this).val(d.join('-'));
        }
    });
});