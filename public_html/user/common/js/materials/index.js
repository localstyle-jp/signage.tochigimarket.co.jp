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