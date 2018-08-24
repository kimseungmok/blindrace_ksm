<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.0/css/bulma.min.css" integrity="sha256-HEtF7HLJZSC3Le1HcsWbz1hDYFPZCqDhZa9QsCgVUdw=" crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-1.11.1.js"></script>
    <script>
        var r_result="";
        var success_rank = 0 ;
        var fail_rank = 0;
        window.onload = function() {

            var roomPin = "<?php if(isset($_GET['roomPin'])) echo $_GET['roomPin']; ?>" ;
            var pop_check = "<?php if(isset($_GET['pop'])) echo 1; else echo 0; ?>"

            var socket = io(':8890');


            socket.emit('join',roomPin);

            if(pop_check != 1)
                socket.emit('race_ending',roomPin);

            $.ajax({
                type: 'POST',
                url: "{{url('/raceController/raceEnd')}}",
                dataType: 'json',
                async:false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                success: function (result) {
                    //var r_result = JSON.parse(data);
                    r_result = result['students'];
                    for(var i=0;  i <r_result.length; i++){


                        var append_info = '<tr><td class="table_rank_td" style="background: none; box-shadow:none;">';
                        append_info += '<div class="rank_content_s" style="width:30px; height:30px; background-color:white;">';
                        if(r_result[i].retestState == false){
                            success_rank++;
                            append_info += success_rank;
                        }else{
                            fail_rank++;
                            append_info += fail_rank;
                        }
                        append_info += '</div></td>';

                        if(pop_check == 0)
                            append_info += ' <td><img src="/img/character/char'+r_result[i].characterId+'.png" >';
                        else if(pop_check == 1)
                            append_info += ' <td><img src="/img/character/student.jpg" >';

                        append_info += '<a class="user-link title">'+r_result[i].nick+'</a>';
                        append_info += '<span class="user-subhead subtitle">';
                        if(pop_check == 0)
                            append_info += r_result[i].rightCount*100+"point";
                        else if(pop_check == 1)
                            append_info += r_result[i].score+"点";
                        append_info +='</span></td>';



                        if(r_result[i].retestState == false){
                            append_info +='<td class="stu_img_frame" ><span><img class="stu_img" src="/img/race_ending/success.png"></span></td></tr>';
                            $('#pass_table').append(append_info);
                        }else{
                            append_info +='<td class="stu_img_frame_fail"><span><img class="stu_img" src="/img/race_ending/fail.png"></span></td></tr>';
                            $('#fail_table').append(append_info);
                        }
                    }
                },
                error: function(request, status, error) {
                    alert("AJAX 에러입니다. ");
                }
            });
            socket.emit('race_result',roomPin ,JSON.stringify(r_result));
        }
    </script>

    <style>
        html{
            width: 100%;
            height:100%;
        }
        body{
            width:100%;
            height:100%;
            background-image: url("/img/race_ending/result_bg.png");
            min-height: 100%;
            background-position: center;
            background-size: cover;
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
            width: 60px;
            height:60px;
            float: left;
            margin-right: 15px;
        }
        #pass_table tbody td>img{
            border:2px solid #f9cb46;
        }
        #fail_table tbody td>img{
            border:2px solid #a384c4;
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
        }
        #result_title{
            width: 50%;
            height: 70%;
            position: absolute;
            top: -5%;
            left: 25%;
            z-index: 1;
        }
        #content_bg{
            width: 90%;
            height: 85%;
            background-color: white;
            border-radius: 50px;
            opacity: 0.8;
            position: absolute;
            top: 10%;
            left: 5%;
        }
        #success_title{
            top: 32%;
            left: 15%;
        }
        #pass_table{
            width: 32%;
            position: absolute;
            top: 43%;
            left: 15%;
            z-index: 1;
            background: none;
            border-spacing:0px 5px;
        }
        #pass_table td, #fail_table td{
            background: #FFFFFF;
            padding:0px;
            box-shadow:0 0 0 0 rgba(0, 0, 0, 0.2), 0 3px 0 rgba(0, 0, 0, 0.19)
        }
        #fail_table{
            width: 32%;
            position: absolute;
            top: 43%;
            right: 15%;
            z-index: 1;
            background: none;
            border-spacing:0px 5px;
        }
        #fail_title{
            top: 32%;
            right: 15%;
        }
        .part_title{
            width: 33%;
            height: 10%;
            position: absolute;
            z-index: 2;
        }
        .table_rank_td{
            width:50px;
            height:50px;
            text-align:center;
            border:none;
            background:none;
        }
        .stu_img{
            width:50px; float:right;
        }
        .stu_img_frame{
            border-right: 5px solid #27abe2 !important;
        }
        .stu_img_frame_fail{
            border-right: 5px solid #f35f72 !important;
        }
        .rank_content_s{
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
    </style>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
</head>
<body>

<div> @include('Navigation.main_nav')</div>

<img id="result_title" src="/img/race_ending/finish_title.png" alt="">

<img id="success_title" class="part_title" src="/img/race_ending/success_title.png" alt="">
<img id="fail_title" class="part_title" src="/img/race_ending/fail_title.png" alt="">
<div id="race_result" >

    <table class="table user-list" id="pass_table">
    </table>

    <table class="table user-list" id="fail_table">
    </table>

    <div id="content_bg"></div>

    <audio autoplay><source src="/bgm/race_result.mp3"></audio>


    <a href="/"><button class="btn btn-primary" style="position: absolute;
    top: 25%;
    right: 6%;
    z-index: 3;
    background: mediumpurple;
    width: 10%;
    height: 5%;
    border-radius: 10px;">戻る</button></a>
</div>
</body>
</html>