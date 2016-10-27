<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Api接口文档</title>
    <link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <style>
        table td {
            font-size: 14px;
            color: black;
            font-weight: bolder;
        }
        
        #page2, #page1 {
            font-size: 15px;
            font-family: "Microsoft YaHei";
        }

        table thead tr {
            background-color: #dedede !important;
        }

        table tr:hover {
            background-color: #dedede !important;
        }

        .step1 td:nth-child(1) {
            padding-left: 28px !important;
        }

        .step2 td:nth-child(1) {
            padding-left: 48px !important;
        }

        .step3 td:nth-child(1) {
            padding-left: 68px !important;
        }

        .step4 td:nth-child(1) {
            padding-left: 88px !important;
        }

        .step5 td:nth-child(1) {
            padding-left: 108px !important;
        }

        .step6 td:nth-child(1) {
            padding-left: 128px !important;
        }

        .step7 td:nth-child(1) {
            padding-left: 148px !important;
        }

        .step1 td {
            font-size: 16px;
            color: #483D8B;
            font-weight: bold;
        }

        .step2 td {
            font-size: 14px;
            color: #6A5ACD;
        }

        .step3 td {
            font-size: 12px;
            color: #7B68EE;
        }

        .step4 td {
            font-size: 10px;
            color: black;
        }

        .step5 td {
            font-size: 8px;
            color: black;
        }

        #menu {
            position: fixed;
            z-index: 999;
            background-color: white;
            width: 100%;
        }

        #page1, #page2 {
            padding-top: 42px;
        }
    </style>
</head>
<body>
<div>
    <div class="col-md-12" id="menu">
        <ul class="nav nav-tabs">
            @foreach($apis as $key => $value)
                <li><a href="?group={{ $key }}">{{ $key }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
<div id="page1">
    <div class="col-md-12" id="mainT">
        <table class="table table-striped">
            <thead>
            <tr>
                <td>路由</td>
                <td>请求方法</td>
                <td>名称</td>
                <td>描述</td>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<div id="page2" hidden>
    <div>
        <div class="col-md-12">
            <h3 class="text-center route-name"></h3>
        </div>
    </div>
    <div>
        <div class="col-md-12 text-center">
            路由： <span class="route" style="padding-right: 10px;"></span>
            方法： <span class="route-method" style="padding-right: 10px;"></span>
            创建时间： <span class="route-created" style="padding-right: 10px;"></span>
            更新时间： <span class="route-updated" style="padding-right: 10px;"></span>
            说明：<span class="route-description"></span>
        </div>
    </div>
    <div>
        <div class="col-md-12">
            <div class="col-md-6">
                <h4>接口参数</h4>
                <table id="toback" class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <td>名称</td>
                        <td>类型</td>
                        {{--<td>默认</td>--}}
                        <td>描述</td>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h4>返回结果</h4>
                <table id="frback" class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <td>名称</td>
                        <td>类型</td>
                        <td>描述</td>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<input type="text" id="now" hidden>
<script>
    var data = {!!json_encode(isset($apis[$group]) ? $apis[$group] :[], JSON_UNESCAPED_UNICODE)!!};
    var ui = {
        $page1: $('#page1')
        , $page2: $('#page2')
        , $main: $('#mainT>table tbody')
        , $now: $('#now')
        , $time: $('.route-time')
        , $routeName: $('.route-name')
        , $routeMethod: $('.route-method')
        , $routeCreated: $('.route-created')
        , $routeUpdated: $('.route-updated')
        , $routeDescription: $('.route-description')
        , $frback: $('#frback tbody')
        , $toback: $('#toback tbody')
        , $back: $('#back')
        , $menu: $('#menu')
        , $route: $('.route')
    };
    var oPage = {
        init: function () {
            this.listen();
            this.setData(data);
            var location = parseURL(window.location);
            var n = location.query.indexOf("route");
            if (n != -1) {
                oEachPage.init();
            }
            if (location.query != "") {

            }
        }
        , setData: function (data) {
            var self = this;
            for (var i = 0; i < data.length; i++) {
                ui.$main.append('<tr>'
                        + '<td>'
                        + '<a onclick="oPage.showEach()">' + data[i].route + '</a>'
                        + '</td>'
                        + '<td>'
                        + data[i].method
                        + '</td>'
                        + '<td>'
                        + data[i].name
                        + '</td>'
                        + '<td>'
                        + data[i].description
                        + '</td>'
                        + '</tr>')
            }
        }
        , listen: function () {
        }
        , showEach: function () {
            var route = $(event.target).text();
            var location = parseURL(window.location);
            if (location.query != "") {
                location.query += ("&route=" + route);
            } else {
                location.query += ("?route=" + route);
            }
            window.location = location.path + location.query;
        }
    };


    var oEachPage = {
        init: function () {
            ui.$page2.show();
            ui.$page1.hide();
            //ui.$menu.hide();
            this.listen();
            this.data = this.getData(data);
            //this.toggleShow();
        }
        , listen: function () {
            var self = this;
            ui.$back.on('click', function () {
                self.back();
            })
        }
        , getData: function (data) {
            var self = this;
            var location = parseURL(window.location);
            var routeN = location.query.indexOf("route");
            route = location.query.slice(routeN + 6);
            for (var i = 0; i < data.length; i++) {
                if (data[i].route == route) {
                    console.log(data[i]);
                    self.putData(data[i]);
                    return data[i];
                }
            }
        }
        , putData: function (data) {

            var self = this;
            ui.$route.html(data.route);
            ui.$routeName.html(data.name);
            ui.$routeMethod.html(data.method);
            ui.$routeCreated.html(data.created);
            ui.$routeUpdated.html(data.updated);
            ui.$routeDescription.html(data.description);

            var frback = ui.$frback;
            var toback = ui.$toback;
            self.putDataR(data.args, toback);
            self.putDataL(data.returns, frback);
        }
        , putDataL: function (data, tar) {
            for (var n in data) {
                tar.append('<tr id="' + n + '">'
                        + '<td>' + n + '</td>'
                        + '<td>' + data[n].type + '</td>'
                        + '<td>' + data[n].description + '</td>'
                        + '</tr>');
                this.putDataE(n, data[n], 0, tar);
            }
        }
        , putDataR: function (data, tar) {
            for (var n in data) {
                var a = data[n].default ? 1 : 0;

                tar.append('<tr>'
                        + '<td>' + n + '</td>'
                        + '<td>' + data[n].type + '</td>'
                        //+'<td>'+(typeof (data[n].default) == 'undefined'?"":data[n].default)+'</td>'
                        + '<td>' + data[n].description + '</td>'
                        + '</tr>');
                this.putDataE(n, data[n], 0, tar);
            }
        }
        , putDataE: function (name, d, index, tar) {
            var self = this;
            index++;
            if (d.type == "array" && d.detail != null) {
                for (var i in d.detail) {
                    tar.append('<tr class="' + name + ' step' + index + '" id="' + i + '">'
                            + '<td>' + i + '</td>'
                            + '<td>' + d.detail[i].type + '</td>'
                            + '<td>' + d.detail[i].description + '</td>'
                            + '</tr>');
                    var cnt = d.detail[i];
                    self.putDataE(i, cnt, index, tar);
                }
            }
        }
        , toggleShow: function () {
            var self = this;
            var a = ui.$frback.children().on('click', function () {
                self.toggle1($(this));
            });
        }
        , toggle1: function (obj) {
            var self = this;
            var t = $(obj).attr("id");
            if ($("." + t)) {
                $("." + t).hide();
                $("." + t).each(function () {
                    self.toggle1(this);
                })
            }
        }
        , back: function () {
            var location = parseURL(window.location);
            window.location = location.path;
        }
    };

    function parseURL(url) {
        var a = document.createElement('a');
        a.href = url;
        return {
            source: url,
            protocol: a.protocol.replace(':', ''),
            host: a.hostname,
            port: a.port,
            query: a.search,
            params: (function () {
                var ret = {},
                        seg = a.search.replace(/^\?/, '').split('&'),
                        len = seg.length, i = 0, s;
                for (; i < len; i++) {
                    if (!seg[i]) {
                        continue;
                    }
                    s = seg[i].split('=');
                    ret[s[0]] = s[1];
                }
                return ret;
            })(),
            file: (a.pathname.match(/\/([^\/?#]+)$/i) || [, ''])[1],
            hash: a.hash.replace('#', ''),
            path: a.pathname.replace(/^([^\/])/, '/$1'),
            relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [, ''])[1],
            segments: a.pathname.replace(/^\//, '').split('/')
        };
    }
    oPage.init();
</script>
</body>
</html>