<link rel="stylesheet" href="css/bootstrap.min.css">

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>

    body{
        background-color:whitesmoke;
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
    .main {
        /* Same width as the sidebar + left position in px */
        font-size: 28px;
        /* Increased text to enable scrolling */
        padding: 0 10px;
    }

    .column {
        width: 24%;
        height: 200px;
        border-radius: 10px;
        border-right:5px solid #DCDCDC;
        border-bottom:5px solid #DCDCDC;
    }

    @media screen and (max-height: 450px) {
        .sidenav {
            padding-top: 15px;
        }
        .sidenav a {
            font-size: 18px;
        }
    }
    #counter{
        box-shadow: 60px 60px 100px -90px #000000, 60px 0px 100px -70px #000000;
        background-color: rgba(255,255,255,.84);
        width: 150px;
        height: 150px;
        border-radius: 20px;
        font-size: 30px;
        position: absolute;
        left: 5%;
        top: 15%;
    }
    #mondai {
        margin-top: 3%;
        margin-left:3%;
        box-shadow: 60px 60px 100px -90px #000000, 60px 0px 100px -70px #000000;
        background-color: rgba(255,255,255,.84);
        width: 95%;
        height: 125px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 40px;
    }
    .obj{
        margin-top:2%;
        margin-left:50px;
    }
    #sub{


        width: 80%;
        height: 30%;
        font-weight: bold;
        font-size: 30px;
        position: absolute;
        left: 10%;
        top: 40%;
    }
    .inline-class{
        display:inline-block;
    }
    #my_character {
        box-shadow: 60px 60px 100px -90px #000000, 60px 0px 100px -70px #000000;
        background-color: rgba(255,255,255,.84);
        width: 150px;
        height: 150px;
        border-radius: 20px;
        font-size: 30px;
        position: absolute;
        right: 5%;
        top: 15%;
    }
    .answer_font{
        font-size:45px;
        text-align:center;
        line-height:100px;
    }
    .user_info{
        margin-right:10%;
        color:gold;
    }

    #web_race_midresult {
        top: 40%;
        position: absolute;
        left: 5%;
        box-shadow: 60px 60px 100px -90px #000000, 60px 0px 100px -70px #000000;
        background-color: rgba(255,255,255,.84);
        width: 90%;
        height: 35%;
        border-radius: 20px;
        font-weight: bold;
        font-size: 40px;
    }
    #race_result{
        top: 40%;
        position: absolute;
        left: 5%;
        box-shadow: 60px 60px 100px -90px #000000, 60px 0px 100px -70px #000000;
        background-color: rgba(255,255,255,.84);
        width: 90%;
        height: 50%;
        border-radius: 20px;
        font-weight: bold;
        font-size: 40px;
    }
</style>

<script>

</script>

<div class="main" style="">
    <!-- 후에 인클루드 -->
    <div id="wait_room_nav" class="inline-class">
        <img  class="inline-class" src="/img/blind_race.png" width="100" height="100">
        <span>Race</span>
        <span  id="race_name"  style="position: absolute;  left:40%; top:2%;"> </span>
        <span  id="race_count" style="position: absolute;  right:20%; top:4%; font-size:20px;" > </span>
        <span  id="group_name" style="position: absolute;  right:10%; top:4%; font-size:20px;"> </span>
        <span id="group_student_count" style="font-size:20px; position: absolute;  right: 2%; top:4%;"></span>
    </div>

    <div id='content'>

        <div class="inline-class" id="mondai">
            <img  id="character_info" src="#" style="width:125px; height:100%;" alt="">
            <span class="user_info"  id="nickname_info"> </span>
            <span class="user_info"  id="ranking_info">  </span>
            <span class="user_info"  id="point_info"> </span>
        </div>

        <div id="makeTypes">
            <!-- 객관식 문제 -->
            <div class="obj" style="display:none;">

                <!-- 1번 -->
                <button class="column btn-success" onclick="web_answer(1);">
                    <!-- style="background-color:#1bbc9b;" -->
                    <p class="answer_font" id="answer1">A</p>
                </button>

                <!-- 2번 -->
                <button class="column btn-primary" onclick="web_answer(2);">
                    <p class="answer_font" id="answer2">B</p>
                </button>

                <!-- 3번 -->
                <button class="column btn-warning" onclick="web_answer(3);">
                    <p class="answer_font" id="answer3">C</p>
                </button>

                <!-- 4번 -->
                <button class="column btn-danger" onclick="web_answer(4);">
                    <p class="answer_font" id="answer4">D</p>
                </button>
            </div>

            <div  id="sub" style="display:none;">
                <div style="text-align:center;">※書き問題です。入力してください。</div>
                <input id="subanswer" type="text" style="  width:100%; height:150px; font-size:70px; color:black; border:2px solid silver;">
                <button onclick="web_answer('sub');" class="btn-primary" style=" border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; width:100%; height:40%; border:none;">확인</button>
            </div>
        </div>

        <div id="web_race_midresult" style="display:none;">
            <!-- 정답이나 오답 이미지  -->
            <img id="answer_check_img" src="#" style="width:200px; height:200px;">
            <!-- 정답내용 -->
            <span id="answer_content" style="margin-left:100px; font-size:100px;" ></span>
            <br>
            <!-- "정답" OR "오답" -->
            <span id="answer_check" style="margin-left:60px; font-size:30px;"></span>

        </div>

        <!-- 재시험 해야할 여부 -->
        <div id="race_result" style="display:none;"></div>

    </div>
</div>
