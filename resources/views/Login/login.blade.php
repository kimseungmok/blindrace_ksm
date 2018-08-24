<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">

    <title>Document</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <style>
        body {
            font-family: arial, sans-serif;
            background-color: #f7f8fa;
            font-size: 13px;
            color: #5f5f5f;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
            width: 100%;

        }
        body.disabled {
            overflow: hidden;
        }

        .main-body {
            max-width: 1220px;
            min-width: 955px;
            /*     overflow: hidden; */
            margin: 0 auto;
            position: relative;
            height: 1024px;
        }
        .page-small .main-body {
            max-width: 768px;
            min-width: 320px;
        }

        #wrapper {
            margin: 0 0 0 220px;
            padding: 0;
            transition: all 0.4s ease 0s;
            position: relative;
            /*     min-height: 100% */
            min-height: 705px;
            min-width: 1000px;
        }

        #menu-main {
            width: 220px;
            hegight: 100%;
            left: 0;
            bottom: 0;
            float: left;
            position: absolute;
            /*     min-height: 1000px; */
            top: 0;
            transition: all 0.4s ease 0s;
            background-color: #ffffff;
            border-left: 1px solid #e1e2e3;
            border-right: 1px solid #e1e2e3;
            border-bottom: 1px solid #e1e2e3;
        }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script
            src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>


</head>





<input type="hidden" name="_token" value="{{csrf_token()}}">



<nav>
    @include('Navigation.main_nav')
</nav>

<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
<hr>

<div class="row">
    <div class="col-md-6">
        <div class="main-box no-header clearfix">
            <div class="main-box-body clearfix">
                <div class="table-responsive">
                    <table class="table user-list">
                        <thead>
                        <tr>
                            <th><span><h2>합격!</h2></span></th>
                            <th><h2>참 잘했어요</h2></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <img src="https://i.imgur.com/LlQi7HQ.jpg" alt="">
                                <a class="user-link">김민수</a>
                                <span class="user-subhead">85점</span>

                            </td>
                            <td>
                                <span><img src="https://i.imgur.com/lxlPPOZ.jpg" style="width: 100px"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="https://i.imgur.com/LlQi7HQ.jpg" alt="">
                                <a class="user-link">김민수</a>
                                <span class="user-subhead">85점</span>

                            </td>
                            <td>
                                <span><img src="https://i.imgur.com/lxlPPOZ.jpg" style="width: 100px"></span>
                            </td>
                        </tr><tr>
                            <td>
                                <img src="https://i.imgur.com/LlQi7HQ.jpg" alt="">
                                <a class="user-link">김민수</a>
                                <span class="user-subhead">85점</span>

                            </td>
                            <td>
                                <span><img src="https://i.imgur.com/lxlPPOZ.jpg" style="width: 100px"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>

    <div class="col-md-6">
        <div class="main-box no-header clearfix">
            <div class="main-box-body clearfix">
                <div class="table-responsive">
                    <table class="table user-list">
                        <thead>
                        <tr>
                            <th><span><h2>불합격!</h2></span></th>
                            <th><h2>노력하세요</h2></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <img src="https://i.imgur.com/LlQi7HQ.jpg" alt="">
                                <a class="user-link">김민수</a>
                                <span class="user-subhead">85점</span>

                            </td>
                            <td>
                                <span><img src="https://i.imgur.com/0YUZZ2m.png" style="width: 100px"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="https://i.imgur.com/LlQi7HQ.jpg" alt="">
                                <a class="user-link">김민수</a>
                                <span class="user-subhead">85점</span>

                            </td>
                            <td>
                                <span><img src="https://i.imgur.com/0YUZZ2m.png" style="width: 100px"></span>
                            </td>
                        </tr>  <tr>
                            <td>
                                <img src="https://i.imgur.com/LlQi7HQ.jpg" alt="">
                                <a class="user-link">김민수</a>
                                <span class="user-subhead">85점</span>

                            </td>
                            <td>
                                <span><img src="https://i.imgur.com/0YUZZ2m.png" style="width: 100px"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>






<style>
    body{
        background:#eee;
    }
    .main-box.no-header {
        padding-top: 20px;
    }
    .main-box {
        background: #FFFFFF;
        -webkit-box-shadow: 1px 1px 2px 0 #CCCCCC;
        -moz-box-shadow: 1px 1px 2px 0 #CCCCCC;
        -o-box-shadow: 1px 1px 2px 0 #CCCCCC;
        -ms-box-shadow: 1px 1px 2px 0 #CCCCCC;
        box-shadow: 1px 1px 2px 0 #CCCCCC;
        margin-bottom: 16px;
        -webikt-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }
    .table a.table-link.danger {
        color: #e74c3c;
    }
    .label {
        border-radius: 3px;
        font-size: 0.875em;
        font-weight: 600;
    }
    .user-list tbody td .user-subhead {
        font-size: 0.875em;
        font-style: italic;
    }
    .user-list tbody td .user-link {
        display: block;
        font-size: 1.25em;
        padding-top: 3px;
        margin-left: 60px;
    }
    a {
        color: #3498db;
        outline: none!important;
    }
    .user-list tbody td>img {
        position: relative;
        max-width: 50px;
        float: left;
        margin-right: 15px;
    }

    .table thead tr th {
        text-transform: uppercase;
        font-size: 0.875em;
    }
    .table thead tr th {
        border-bottom: 2px solid #e7ebee;
    }
    .table tbody tr td:first-child {
        font-size: 1.125em;
        font-weight: 300;
    }
    .table tbody tr td {
        font-size: 0.875em;
        vertical-align: middle;
        border-top: 1px solid #e7ebee;
        padding: 12px 8px;
    }
</style>

</html>