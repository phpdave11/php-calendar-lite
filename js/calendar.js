function AjaxGetXmlHttpRequestInstance()
{
    var http_request = false;

    if (window.XMLHttpRequest) {
        http_request = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }
    return http_request;
}
function AjaxInitXmlHttpRequest(func, isXML)
{
    var http_request = AjaxGetXmlHttpRequestInstance();
    if (isXML == undefined)
        isXML = true;
    if (isXML && http_request.overrideMimeType)
        http_request.overrideMimeType("text/xml");

    if (func)
    {
        http_request.onreadystatechange = function()
        {
            if (http_request.readyState == 4 && http_request.status == 200)
            {
                if (isXML)
                    eval(func + '(http_request.responseXML);');
                else
                    eval(func + '(http_request.responseText);');
            }
        }
    }
    return http_request;
}
function AjaxHttpGet(url, params, func, isXML)
{
    var http_request = AjaxInitXmlHttpRequest(func, isXML);
    if (params != "")
        params = "?" + params;
    http_request.open("GET", url + params, true);
    http_request.send(null);
}
function ajaxLoadLink(link)
{
    link = link.replace(baseURL, '');
    window.location.href = '#' + link;
    AjaxHttpGet(link + '/ajax', '', 'ajaxLoadLinkCallback', false);
}
function ajaxLoadLinkCallback(data)
{
    document.getElementById('calendar').innerHTML = data;
}
window.onload = function()
{
    var hash = window.location.hash;
    if (hash.length > 0)
    {
        hash = hash.replace('#', '');
        if (hash.length > 0)
        {
            ajaxLoadLink(baseURL + hash);
        }
    }
};
window.onhashchange = function()
{
    ajaxLoadLink(window.location.hash.replace('#', ''));
};
