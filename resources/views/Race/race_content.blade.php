<script src="//code.jquery.com/jquery-1.11.1.js"></script>
<link rel="stylesheet" href="css/bootstrap.min.css">

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<style>

    .sidenav {
        width: 130px;
        position: fixed;
        z-index: 1;
        top: 20px;
        left: 10px;
        background: #eee;
        overflow-x: hidden;
        padding: 8px 0;
    }

    .sidenav a {
        padding: 6px 8px 6px 16px;
        text-decoration: none;
        font-size: 10px;
        color: #2196F3;
        display: block;
        margin-right: 10px;
    }
    .sidenav a:hover {
        color: #064579;
    }

    .main {
        /* Same width as the sidebar + left position in px */
        font-size: 28px;
        /* Increased text to enable scrolling */
        padding: 0 10px;
    }

    .column {
        float: left;
        width: 45%;
        height: 10%;
        margin-left: 1%;
        margin-top: 15px;
        background-position: center;
        background-size: cover;
        border-radius: 20px;
    }

    @media screen and (max-height: 450px) {
        .sidenav {
            padding-top: 15px;
        }
        .sidenav a {
            font-size: 18px;
        }
    }
    #answer_cap{
        position: absolute;
        top: 30%;
        right: 7%;
        z-index: 5;
    }
    #answer_circle {
        margin-top: 50px;
        color: black;
        width: 100px;
        height: 100px;
        font-size: 40px;
        font-weight: bold;
        line-height: 100px;
        border: 10px solid orange;
        border-radius: 50%;
        position: absolute;
        right: 8%;
        background-color: rgba(255,255,255,.84);
        top: 27%;
    }
    #mondai {
        top: 25%;
        position: absolute;
        left: 22%;
        background-color: rgba(255,255,255,.84);
        width: 55%;
        height: 30%;
        border-radius: 20px;
        font-weight: bold;
        font-size: 40px;
    }
    .obj{
        position:absolute;
        width:80%;
        top:55%;
        left:12%;
    }
    #sub{
        width: 55%;
        height: 10%;
        font-weight: bold;
        font-size: 30px;
        position: absolute;
        left: 22%;
        top: 60%;
        border-bottom: 5px solid navy;
    }
    .inline-class{
        display:inline-block;
    }
    #mondai-content{

    }
    #answer_c {
        font-size:40px;
        color:#ef8747;
    }

    #all_member{
        color:#ffbd6e;
        font-size:25px;
    }
    progress {
        text-align:left;
        width: 300px;
        border: 0 none;
        position:absolute;
        top:15%;
        left:7.5%;
        background: #e6e6e6;
        border-radius: 14px;
        box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,0.2);
    }
    progress::-webkit-progress-bar {
        background: transparent;
    }
    progress::-webkit-progress-value {
        border-radius: 12px;
        background: #f27281;
        box-shadow: inset 0 -2px 4px rgba(0,0,0,0.4), 0 2px 5px 0px rgba(0,0,0,0.3);
    }
    .answer_font{
        font-size:45px;
        text-align:center;
        color:white;
    }

    #play_frame{
        width: 90%;
        height: 70%;
        background-color: white;
        position: absolute;
        top: 25%;
        left: 5%;
        border-radius: 50px;
        box-shadow: 60px 60px 100px -90px #000000, 60px 0px 100px -70px #000000
    }
    .progress_timer{
        width: 70px;
        height: 70px;
        position: absolute;
        left: 7.5%;
        top: 14%;
        z-index: 3;
        line-height: 70px;
    }
</style>

<div id="playing_contents" style="display:none;">

    <!-- 白い背景 -->
    <div id="play_frame"></div>

    <div class="main" style="">
        <div id='content'>

            <!-- 問題の限定時間 -->
            <span class="progress_timer" id="counter" style="z-index:8; color:#ff923a; margin-left:15px;"></span>
            <img class="progress_timer" src="/img/race_play/timer.png" alt="">
            <progress style="width:85%;  height:30px; margin-top:20px;"  value="0" max="30" id="progressBar"></progress>

            <div id="questions" style="height:250px;">


                <!-- 問題内容 -->
                <div class="inline-class" id="mondai">
                    <br>
                    <span id="mondai-content"></span>
                </div>

                <!-- 解いた学生の数 -->
                <img id="answer_cap" src="/img/race_play/answer_cap.png" alt="">
                <div id="answer_circle" class="inline-class">
                    <span id="answer_c" >0</span>
                    <span id="all_member">/0</span>
                </div>

            </div>

            <!--問題の選択肢-->
            <div class="obj" style="display:none;">
                <!-- style="margin-left:10%;" -->
                <div class="column" style="background-image:url('/img/race_play/answer_a.png'); ">
                    <p class="answer_font" id="answer1">1번</p>
                </div>
                <div class="column" style="background-image:url('/img/race_play/answer_b.png'); ">
                    <p class="answer_font" id="answer2">2번</p>
                </div>
                <div class="column" style="background-image:url('/img/race_play/answer_c.png'); ">
                    <p class="answer_font" id="answer3">3번</p>
                </div>
                <div class="column" style="background-image:url('/img/race_play/answer_d.png'); ">
                    <p class="answer_font" id="answer4">4번</p>
                </div>
            </div>

            <!--　書き問題のヒント　-->
            <div id="sub" style=" text-align:center">
                <span style="font-size:40px; color:navy; left:0; position:absolute;">Hint:</span>

                <!-- ヒントがでる「span」 -->
                <span class="answer_font" id="hint" style="color:navy;"></span>
            </div>

        </div>
    </div>

    <!-- 問題を解いた中間結果 -->
    <div id='mid_result' style='display:none;' >
        <div>
            @include('Race.mid_result')
        </div>
    </div>
</div>

























