
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">


    <script src="https://code.jquery.com/jquery-1.11.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        html{
            width: 100%;
            height: 100%;
        }
        body{
            background-image: url("/img/race_play/pop_bg.png");
            min-height: 100%;
            background-position: center;
            background-size: cover;
            width: 100%;
            height: 100%;
        }

        #wait_room_nav{
            box-shadow:  60px 60px 100px -90px #000000, 60px 0px 100px -70px #000000;
            /*background-color: rgba(255,255,255,.84);*/
            background-color:white;
            width: 100%;
            height: 100px;
            border-radius: 10px;
            font-weight:bold;
            font-size:50px;
        }

        .user_in_room{
            display:inline-block;

            margin-right:50px;
            border-radius: 15px 50px 30px;
        }
        .student {
            margin-top: 3%;
            display: block;
            text-align: right;
        }

        .student form{
            display: inline;
            background-color: white;
            margin-right: 1%;
        }
        #counting_student{
            text-align:center;
            font-size:30px;
            position:absolute;
            left:25%;
        }

        .counting{
            text-align: center;
        }



        .waitingTable {
            margin-top: 20px;
            margin-left: 2%;
            margin-right: 2%;
        }
        #room_Pin {
            width: 467px;
            height: 200px;
            z-index: 0;
            font-size: 40px;
            color: white;
            position: absolute;
            top: 5%;
            left: 35%;
            display: table-cell;
            line-height: 280px;
            background-image: url("/img/race_waiting/pin_case.png");
            background-position: center;
            background-size: cover;
        }

        #messages { list-style-type: none; }
        #messages li { padding: 5px 10px; }

    </style>
    <style>
        p ,div ,th ,tr  {
            font-family: 'Nanum Gothic', sans-serif;
        }
        .centerbutton{
            width: 100% ;
            height: 100% ;
            background-size: contain;
            background-image: url("https://i.imgur.com/NYAOWGv.png");
            border: 0px;
        }
        table {
            font-family: arial, sans-serif;
            width: 100%;
            border-spacing: 0px 6px !important;
        }

        .shadow {
            box-shadow: -60px 0px 100px -90px #000000, 70px 60px 100px -90px #000000;
        }

        #waiting_area {
            width: 90%;
            height: 50%;
            margin-left: 5%;
            border-radius: 30px;
            background-color: #ebfaff;
            padding:100px;
        }

        #pop_timer{
            position:absolute;
            top:30%;
            left:45%;
        }

        .fade:not(.show) {
            opacity: 10;
        }

        .container {

            padding-right: 0px;
            padding-left: 0px;

        }
        .col-lg-5{
            padding-right: 0px;
            padding-left: 0px;
        }
        .col-lg-1 {


            background-image: url("https://i.imgur.com/NYAOWGv.png");
            background-size: auto;
            padding-right: 0px;
            padding-left: 0px;
        }
        .row {

            margin-right: -100px;

        }
        .pen {
            margin-left: 20px;
            margin-top: 10px;
            font-size: 20px;
            color : #203a8e; !important;
        }
        th {
            background-color: #DFDFDF ; !important;
        }

        .in {
            display: inline-block;
        }
        .ma {
            margin-left: 80%;
            margin-top: 10px;
        }
        .jumbotrons{
            padding-right: 0px;
            padding-left: 0px;
            height: 280px; !important;
            width: 105%; !important;
            background-image: url("https://i.imgur.com/f22XeGk.png");

            background-size: contain;

        }
        .btn-primary-outline {
            background-color: transparent;
            border-color: #ccc;
        }
        .btn-round-lg{
            border-radius: 20.5px;

        }

        button {
            display: inline-block;
        }



        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }


        tr:nth-child(even) {
            background-color: #e6eaed;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .input[type=text] {
            width: 130px;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            background-color: white;
            background-image: url("https://i.imgur.com/LCkVIxO.png");
            background-position: 10px 10px;
            background-repeat: no-repeat;
            padding: 12px 20px 12px 40px;
            -webkit-transition: width 0.4s ease-in-out;
            transition: width 0.4s ease-in-out;
        }

        .input[type=text]:focus {
            width: 100%;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0,0,0);
            /* Fallback color */
            background-color: rgba(0,0,0,0.4);
            /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            border: 1px solid #888;
            width: 80%;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
            -webkit-animation-name: animatetop;
            -webkit-animation-duration: 0.4s;
            animation-name: animatetop;
            animation-duration: 0.4s;
        }

        /* Add Animation */
        @-webkit-keyframes animatetop {
            from {
                top: -300px;
                opacity: 0;
            }
            to {
                top: 0;
                opacity: 1;
            }
        }

        @keyframes animatetop {
            from {
                top: -300px;
                opacity: 0;
            }
            to {
                top: 0;
                opacity: 1;
            }
        }

        /* The Close Button */
        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:focus,
        .close:hover {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            padding: 2px 16px;
            background-color: #5cb85c;
            color: white;
        }

        .modal-body {
            padding: 2px 16px;
        }

        .modal-footer {
            padding: 2px 16px;
            background-color: #5cb85c;
            color: white;
        }
        .modal-backdrop {
            position: relative !important;
        }
        .light {
            margin-left: 840px;
        }
        .inline {
            display: inline-block;
        }
    </style>
    <style>
        * {margin: 0; padding: 0;}

        .container {
            padding: 10px;
            text-align: center;
        }

        .timer {
            padding: 10px;
            background: linear-gradient(top, #222, #444);
            overflow: hidden;
            display: inline-block;
            border: 7px solid #efefef;
            border-radius: 5px;
            position: relative;

            box-shadow:
                    inset 0 -2px 10px 1px rgba(0, 0, 0, 0.75),
                    0 5px 20px -10px rgba(0, 0, 0, 1);
        }

        .cell {
            /*Should only display 1 digit. Hence height = line height of .numbers
            and width = width of .numbers*/
            width: 0.60em;
            height: 40px;
            font-size: 50px;
            overflow: hidden;
            position: relative;
            float: left;
        }

        .numbers {
            width: 0.6em;
            line-height: 40px;
            font-family: digital, arial, verdana;
            text-align: center;
            color: #fff;

            position: absolute;
            top: 0;
            left: 0;

            /*Glow to the text*/
            text-shadow: 0 0 5px rgba(255, 255, 255, 1);
        }

        /*Styles for the controls*/
        #timer_controls {
            margin-top: -5px;
        }
        #timer_controls label {
            cursor: pointer;
            padding: 5px 10px;
            background: #efefef;
            font-family: arial, verdana, tahoma;
            font-size: 11px;
            border-radius: 0 0 3px 3px;
        }
        input[name="controls"] {display: none;}

        /*Control code*/
        #stop:checked~.timer .numbers {animation-play-state: paused;}
        #start:checked~.timer .numbers {animation-play-state: running;}
        #reset:checked~.timer .numbers {animation: none;}

        .moveten {
            /*The digits move but dont look good. We will use steps now
            10 digits = 10 steps. You can now see the digits swapping instead of
            moving pixel-by-pixel*/
            animation: moveten 1s steps(10, end) infinite;
            /*By default animation should be paused*/
            animation-play-state: paused;
        }
        .movesix {
            animation: movesix 1s steps(6, end) infinite;
            animation-play-state: paused;
        }

        /*Now we need to sync the animation speed with time speed*/
        /*One second per digit. 10 digits. Hence 10s*/
        .second {animation-duration: 10s;}
        .tensecond {animation-duration: 60s;} /*60 times .second*/

        .milisecond {animation-duration: 1s;} /*1/10th of .second*/
        .tenmilisecond {animation-duration: 0.1s;}
        .hundredmilisecond {animation-duration: 0.01s;}

        .minute {animation-duration: 600s;} /*60 times .second*/
        .tenminute {animation-duration: 3600s;} /*60 times .minute*/

        .hour {animation-duration: 36000s;} /*60 times .minute*/
        .tenhour {animation-duration: 360000s;} /*10 times .hour*/

        @keyframes moveten {
            0% {top: 0;}
            100% {top: -400px;}
            /*height = 40. digits = 10. hence -400 to move it completely to the top*/
        }

        @keyframes movesix {
            0% {top: 0;}
            100% {top: -240px;}
            /*height = 40. digits = 6. hence -240 to move it completely to the top*/
        }
        p ,div ,th ,tr  {
            font-family: 'Nanum Gothic', sans-serif;
        }
    </style>
    <script>
        var quiz_member = 0;
        var submit_count=0;
        var quiz_answer_list = [1,2,3,4];
        var rightAnswer;
        var real_A;
        var roomPin ='<?php echo $response['roomPin']; ?>';
        var t_sessionId = '<?php echo $response['sessionId']; ?>';
        var quiz_JSON = JSON.parse('<?php echo json_encode($response['quizs']['quiz']); ?>');

        var listName = '<?php echo $response['list']['listName']; ?>';
        var quizCount = '<?php echo $response['list']['quizCount']; echo "問"; ?>';
        var groupName = '<?php echo $response['group']['groupName']; ?>';
        var groupStudentCount = '<?php echo "総人員: "; echo $response['group']['groupStudentCount']; echo "人"; ?>';

        var start_check = false;
        var answer_count = 0;
        var all_member_count;
        window.onload = function() {

            $('#nav_img').attr('src','/img/race_student/exam.png');
            $("#student_guide").text("学生たちが全員参加したら「 Start 」ボタンを押してください。");

            //정답뒤섞기
            function shuffle(a) {
                var j, x, i;
                for (i = a.length; i; i -= 1) {
                    j = Math.floor(Math.random() * i);
                    x = a[i - 1];
                    a[i - 1] = a[j];
                    a[j] = x;
                }
            }
            function Create2DArray(rows) {
                var arr = [];

                for (var i=0;i<rows;i++) {
                    arr[i] = [];
                }

                return arr;
            }
            real_A = Create2DArray(quiz_JSON.length);

            for(var i = 0; i <quiz_JSON.length; i++){
                if( quiz_JSON[i].makeType == "obj"){
                    shuffle(quiz_answer_list);

                    real_A[i][quiz_answer_list[0]] = quiz_JSON[i].right;
                    real_A[i][quiz_answer_list[1]] = quiz_JSON[i].example1;
                    real_A[i][quiz_answer_list[2]] = quiz_JSON[i].example2;
                    real_A[i][quiz_answer_list[3]] = quiz_JSON[i].example3;

                    for(var j = 0; j<=3; j++){
                        switch(quiz_answer_list[j]){
                            case 1: quiz_JSON[i].right = real_A[i][quiz_answer_list[j]];
                                break;
                            case 2: quiz_JSON[i].example1 = real_A[i][quiz_answer_list[j]];
                                break;
                            case 3: quiz_JSON[i].example2 = real_A[i][quiz_answer_list[j]];
                                break;
                            case 4: quiz_JSON[i].example3 = real_A[i][quiz_answer_list[j]];
                                break;
                        }
                    }
                }
            }

            console.log(JSON.stringify(quiz_JSON));

            var socket = io(':8890');

            $('#race_name').html(listName);
            $('#race_count').html(quizCount);
            $('#group_name').html(groupName);
            $('#group_student_count').html(groupStudentCount);

            $('#room_Pin_text').html("PIN:"+roomPin);
            socket.emit('join', roomPin);

            socket.on('android_join',function(roomPin,sessionId){

                $.ajax({
                    type: 'POST',
                    url: "{{url('/raceController/studentIn')}}",
                    dataType: 'json',
                    async: false ,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data:"roomPin="+roomPin+"&sessionId="+sessionId,
                    success: function (result) {
                        var append_info;

                        if(result['check'] == true) {
                            if(start_check){
                                socket.emit('android_join_check', true, sessionId, "popQuiz");

                                setTimeout(function(){


                                    socket.emit('pop_quiz_start',roomPin,JSON.stringify(result['quizs']), listName, sessionId , quizCount);

                                }, 3000);

                            }else{
                                socket.emit('android_join_check', true, sessionId, "popQuiz");
                                quiz_member++;
                                $('#member_count').text(quiz_member);

                                append_info= '<tr id="'+sessionId+'" class="header">';
                                append_info+='<td id="'+sessionId+"Name"+'" style="width:60%"><i class="fas fa-user-circle"></i>'+result['userName']+'</td>';
                                append_info+='<td style="width:40%"><i class="fas fa-user"></i>受験中..</td></tr>';

                                $('#playing_student').append(append_info);


                                console.log(start_check);
                                console.log(result);
                            }

                        }
                        else
                            socket.emit('android_join_check',false, sessionId ,"popQuiz");
                    },
                    error: function(request, status, error) {
                        console.log("안드로이드 join 실패"+roomPin);
                    }
                });

            });

            socket.on('web_test_enter',function(roomPin){
                quiz_member++;
                $('#member_count').text(quiz_member);
            });
        };

        function pop_end(){
            $(location).attr('href', "/race_result?roomPin="+roomPin+"&pop");
        }

        function btn_click(){

            $('#start_btn').hide();
            $('#pop_timer').css("display","inline-block");
            $('#pop_timer').show();
            start_check = true;

            var h1 = document.getElementsByTagName('h1')[0],
                realtime=0,seconds = 0, minutes = 0, hours = 0,
                t;

            function add() {
                seconds++;
                realtime++;
                if (seconds >= 60) {
                    seconds = 0;
                    minutes++;
                    if (minutes >= 60) {
                        minutes = 0;
                        hours++;
                    }
                }

                h1.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);

                socket.emit('pop_timer',roomPin,realtime);

                timer();
            }
            function timer() {
                t = setTimeout(add, 1000);
            }
            timer();


            var socket = io(':8890'); //14
            socket.emit('join', roomPin);

            //socket.emit('web_enter_room',roomPin,listName,quizCount,groupName,groupStudentCount, sessionId,true);
            socket.emit('pop_quiz_start',roomPin,JSON.stringify(quiz_JSON),listName,"X",quizCount);

            // $('<audio id="play_bgm" autoplay><source src="/bgm/sound.mp3"></audio>').appendTo('body');

            //대기방에 입장된 캐릭터와 닉네임이 없어짐
            socket.on('answer-sum', function(answer ,sessionId , quizId){


                $.ajax({
                    type: 'POST',
                    url: "{{url('/raceController/answerIn')}}",
                    dataType: 'json',
                    async:false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data:"roomPin="+roomPin+"&answer="+answer+"&sessionId="+sessionId+"&quizId="+quizId,
                    success: function (result) {

                    },
                    error: function(request, status, error) {
                        alert("AJAX 에러입니다. ");
                    }
                });

                console.log('답변자수 ' , answer_count);
                console.log('입장플레이어수 ', quiz_member);
            });

            socket.on('pop_quiz_status',function(sessionId){
                submit_count++;

                all_member_count = quiz_member - submit_count;

                $('#playing_member_count').text(":"+all_member_count+"명");
                $('#member_count').text(all_member_count);
                $('#submit_count').text(submit_count);

                var userName= $('#'+sessionId+"Name").text();
                $('#'+sessionId).remove();

                var append_info= '<tr id="'+sessionId+'" class="header">';
                append_info+='<td id="'+sessionId+"Name"+'" style="width:60%"><i class="fas fa-user-circle"></i>'+userName+'</td>';
                append_info+='<td style="width:40%"><i class="fas fa-user"></i>完了</td></tr>';

                $('#finish_student').append(append_info);

                if(all_member_count == 0 ){
                    swal("試験終わり!", "全員、試験を完了しました。", "success");
                    $('#playing_member_count').text('시험완료');
                }

            });

            socket.on('leaveRoom', function(user_num) {
                quiz_member--;

                $('#member_count').text(quiz_member);
                if(quiz_member < 1){
                    $('#member_count').text("Player");
                }
                $.ajax({
                    type: 'POST',
                    url: "{{url('/raceController/studentOut')}}",
                    dataType: 'json',
                    async: false ,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data:"roomPin="+roomPin+"&sessionId="+user_num,
                    success: function (result) {
                        console.log("학생퇴장"+user_num);

                        if( result['characters'] != 'false'){
                            socket.emit('enable_character',roomPin,result['characters']);
                            $('#'+user_num).remove();
                        }
                    },
                    error: function(request, status, error) {
                        alert("AJAX 에러입니다. ");
                    }
                });

            });

        };
    </script>
</head>
<body>

@include('Navigation.race_nav');

<div id="wait_room">
    <div class="student">

        <button onclick="btn_click();" id="start_btn" class="btn btn-lg btn-primary" style="">試験開始</button>
        <button onclick="pop_end();" class="btn btn-lg btn-danger">試験終了</button>
        <div id="room_Pin" class="counting">
            <span id="room_Pin_text"></span>



        </div>

        <div id="counting_student">
            <span id="playing_member_count" ></span>
        </div>

    </div>
    <div id="pop_timer" class="container" style="display:none; width: 200px;">
        <h1><time>00:00:00</time></h1>
    </div>

</div>


<div id="waiting_area" class="shadow">
    <div style="width:33%; height:40%;   overflow:auto; position:absolute; top:40%; left:15%;">
        <table id="playing_student">
            <tr class="header">
                <th style="width:60%"><i class="fas fa-user-circle"></i>　名前</th>
                <th id="member_count" style="width:40%"><i class="fas fa-user"></i>0人</th>
            </tr>
        </table>

    </div>

    <div  style="position:absolute; width:33%; height:40%;   overflow:auto;  top:40%; right:15%;">
        <table id="finish_student">
            <tr class="header">
                <th style="width:60%"><i class="fas fa-user-circle"></i>　名前</th>
                <th id="submit_count"  style="width:40%"><i class="fas fa-user"></i>0人</th>
            </tr>
        </table>
    </div>

</div>

@include('Race.race_footer')
</body>
</html>