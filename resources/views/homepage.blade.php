<!DOCTYPE html>

<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script
            defer="defer"
            src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
            integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
            crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <title>十分十分 - 10minutes Everyday!</title>
    <meta charset="utf-8">
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <link href="{{url('css/homemain.css')}}" rel="stylesheet" type="text/css" media="all">

    <script>
        function loginCheck(){
            $.ajax({
                type: 'POST',
                url: "{{url('/userController/loginCheck')}}",
                dataType: 'json',
                async:false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (result) {
                    if(result['check'] == true) {

                        switch(result['classification'])
                        {
                            case 'student' :
                                $('#home_race').attr("href", "/race_student");
                                break;
                            case 'teacher' :
                                $('#home_race').attr("href", "/race_list");
                                break;
                        }

                        $('#login_button').text("Log-Out");
                        $('#login_button').attr("onclick","tryLogout()");
                    }
                    else{
                        $('#home_race').attr("href", "#");
                    }

                },
                error: function(request, status, error) {

                }
            });
            //ajax끝
        }
        function tryLogin(){
            var p_id = $('#web_ID').val();
            var p_pw = $('#web_PW').val();
            $.ajax({
                type: 'POST',
                url: "{{url('/userController/webLogin')}}",
                dataType: 'json',
                async:false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data:"p_ID="+p_id+"&p_PW="+p_pw,
                success: function (result) {
                    if(result['check'] == true) {

                        switch(result['classification'])
                        {
                            case 'student' :
                                $('#home_race').attr("href", "/race_student");
                                break;
                            case 'teacher' :
                                $('#home_race').attr("href", "/race_list");
                                break;
                        }

                        $('#login_button').text("Log-Out");
                        $('#login_button').attr("onclick","tryLogout()");

                    }
                    else{
                        swal("로그인 실패.", " ", "warning");
                    }

                },
                error: function(request, status, error) {

                }
            });
            //ajax끝
        }

        function tryLogout(){
            $.ajax({
                type: 'POST',
                url: "{{url('/userController/webLogout')}}",
                dataType: 'json',
                async:false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (result) {
                    $('#login_button').text("Log-in");
                    $('#login_button').attr("onclick","tryLogin()");
                    alert("로그아웃되었습니다.");
                    window.location.reload();
                },
                error: function(request, status, error) {
                    alert("로그아웃 실패 ");
                }
            });
        }
    }

    </script>

</head>
<body
        id="top"
        onload="loginCheck()";
        class="bgded fixed"
>


@include('Navigation.main_nav')


<div style="width: 100%; height: 45%">
    <a href="/"><img style="width: 100% ; height: 100%" src="https://i.imgur.com/selEFaM.png" alt="" title=""/></a>
</div>

<div style="width: 100%; height: 5%; background-color: #9fcdff; color: white; font-size: 21px; line-height: 2;">
    <div style="width: 19%; height: 100%; display: inline-block; "><center>My class</center></div>
    <div style="width: 20%; height: 100%; display: inline-block "><center>Quiz_Race</center></div>
    <div style="width: 20%; height: 100%; display: inline-block "><center>Recordbox</center></div>
    <div style="width: 20%; height: 100%; display: inline-block "><center>Quiz Tree</center></div>
    <div style="width: 19%; height: 100%; display: inline-block "><center>Feedback</center></div>
</div>

<div style="width: 100%; height: 34%; font-size:0;line-height:0 ">
    <div style="width: 20%; height: 100%;!important; display: inline-block "><a href="{{ url('/mygroup') }}"><img class="menu_img" src="https://i.imgur.com/33elQUd.png"> </div>
    <div style="width: 20%; height: 100%;!important; display: inline-block "><a href="{{ url('race_list') }}"><img class="menu_img" src="https://i.imgur.com/mbuwQ0O.png"></div>
    <div style="width: 20%; height: 100%;!important; display: inline-block "><a href="#" onclick="moveToAnotherPage('recordbox')"><img class="menu_img" src="https://i.imgur.com/ExqGuJx.png"></div>
    <div style="width: 20%; height: 100%;!important; display: inline-block "><a href="{{ url('quiz_list') }}"><img class="menu_img" src="https://i.imgur.com/PPBQX37.png"></div>
    <div style="width: 20%; height: 100%;!important; display: inline-block "><a href="#" onclick="moveToAnotherPage('feedback')"><img class="menu_img" src="https://i.imgur.com/TU94pvS.png"></div>
</div>

<div  style="width: 100% ; height: 10%; background-color: white">

</div>
<div class="row5" >
    <div id="copyright" class="clear">
    </div>
</div>
<style>
    .menu_img{
        width: 100%;
        max-width: 100%;
        height: 100%;

    }
    div {
        margin: 0;
    }
    html{
        width: 100%;
        height:100%;
    }
    body{
        width: 100%;
        height: 100%;
    }

    .navbar-brand {
        position: relative;
        z-index: 2;
    }
    .navbar-nav.navbar-right .btn {
        position: relative;
        z-index: 2;
        padding: 4px 20px;
        margin: 10px auto;
    }
    .navbar .navbar-collapse {
        position: relative;
    }
    .navbar .navbar-collapse .navbar-right > li:last-child {
        padding-left: 22px;
    }
    .navbar .nav-collapse {
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: 0;
        padding-right: 120px;
        padding-left: 80px;
        width: 100%;
    }
    .navbar.navbar-default .nav-collapse {
        background-image: url("https://i.imgur.com/7nT1LDd.png"); !important;
        margin: 0;
    }
    .navbar-default {
        background-image: url("https://i.imgur.com/7nT1LDd.png"); !important;
    }
    .navbar.navbar-inverse .nav-collapse {
        background-color: black;
    }
    .navbar .nav-collapse .navbar-form {
        border-width: 0;
        box-shadow: none;
    }
    .nav-collapse > li {
        float: right;
    }
    .btn.btn-circle {
        border-radius: 50px;
    }
    .btn.btn-outline {
        background-color: transparent;
    }
    @media screen and (max-width: 767px) {
        .navbar .navbar-collapse .navbar-right > li:last-child {
            padding-left: 15px;
            padding-right: 15px;
        }
        .navbar .nav-collapse {
            margin: 7.5px auto;
            padding: 0;
        }
        .navbar .nav-collapse .navbar-form {
            margin: 0;
        }
        .nav-collapse > li {
            float: none;
        }
    }
    ::-webkit-scrollbar {
        display: none;
    }
</style>

</body>

</html>