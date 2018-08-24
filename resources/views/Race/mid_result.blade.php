<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

<link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">

<script
        src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link   rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<link rel="stylesheet" href="../css/circle.css">
<style type="text/css">

    body{
        min-height: 100%;
        background-position: center;
        background-size: cover;
    }

    #curve_chart {
        margin-top: 1em;
    }
    #Mid_Q_Name{
        font-size:40px;
    }
    #Mid_A_Right{
        font-size:40px;
        color:red;
    }

    .orange {
        background-color: #e67e22;
        box-shadow: 0px 5px 0px 0px #CD6509;
    }

    .orange:hover {
        background-color: #FF983C;
    }

    /* button div */

    /* start da css for da buttons */
    .btn {
        border-radius: 5px;
        padding: 15px 25px;
        font-size: 22px;
        text-decoration: none;
        margin: 20px;
        color: #fff;
        position: relative;
        display: inline-block;
    }

    .btn:active {
        transform: translate(0px, 5px);
        -webkit-transform: translate(0px, 5px);
        box-shadow: 0px 1px 0px 0px;
    }
    .rank_th{
        text-align:center;
        color:white;
        background-color:deepskyblue;
    }
    .big_font_70{
        font-size:70px;
        color:white;
    }
    .opinion{
        font-size: 20px;
        border-bottom: 2px solid navy;
        padding-top: 10px;
        text-align:center;
    }
    #buttons{
        background-image: url("/img/race_play/next_btn.png");
        width: 220px;
        height: 75px;
        background-position: center;
        background-size: cover;
        position: absolute;
        top: 15%;
        right: 2%;
    }
</style>

<div class="" style="display: inline-block;
    margin-left: 5%;
    width: 90%;
    height: 20%;
    top: 0;
    position: absolute;
    background: #033981;
    border-radius: 0px 0px 50px 50px; ">

    <div class="" style="margin-top:2%; margin-left:4%; border-radius:10px;  background-color: white; display:inline-block; width:60%; height:60%; vertical-align: top; ">
        <div style="width:13%; height:100%; display:inline-block; border-radius:8px; background-color: #df4467; font-size:30px; color:white; text-align: center;
    line-height: 100px;">正解</div>
        <span id="Mid_A_Right" style="font-size:30px;" >かたわら</span>
    </div>

    <div style=" width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 10px solid #53cdff;
    display: inline-block;
    margin-left: 3%;
    margin-right: 3%;
    vertical-align: top;
    margin-top: 1%;
    background: white;
        text-align: center;
    line-height: 100px;
    ">
        <sup><span class="" id="quiz_number">1</span></sup>
        <span   class="" > / </span>
        <span id="mid_all_quiz" class="">30</span>
    </div>

    <div class="clearfix" style="display:inline-block; width:100px; height:100px; margin-top:1%; margin-left:1%;" >
        <div id="mid_circle" class="c100 p50 green">
            <span id="mid_percent"></span>
            <div class="slice">
                <div class="bar"></div>
                <div class="fill"></div>
            </div>
        </div>
    </div>
</div>

<div id="mid_content" style="" >

    <a id="Mid_skip_btn" href="#" role="button">
        <div id="buttons">
        </div>
    </a>

    <div style="width: 27%;
    height: 8%;
    background-color: #ffbd6e;
    position: absolute;
    top: 25%;
    left: 17%;
    z-index: 5;
    border-radius: 50px;
    text-align: center;
    line-height: 60px;
    color: white;
    font-size: 20px;"
    > 問題解説 </div>

    <div style="width: 27%;
    height: 8%;
    background-color: #ff6e76;
    position: absolute;
    top: 25%;
    right: 13%;
    z-index: 5;
    border-radius: 50px;
    text-align: center;
    line-height: 60px;
    color: white;
    font-size: 20px;"> 中間結果ランキング</div>


    <div style="width: 90%;
    height: 68%;
    position: absolute;
    top: 30%;
    left: 5%;
    z-index: 2;
    background: white;
    border-radius: 50px;">

        <div style="display: inline-block; width:45%; height:90%; position:absolute; right:2%;
        margin-top:3%;
        background-image:url('/img/race_play/rank_bg.png');
         background-position: center;
        background-size: cover;
        ">
            <table id="student_rank" style="width:90%; border-collapse: separate; border-spacing:0px 10px; margin-top:5%; margin-left:5%;">

                <tr class="rank_hr">
                    <td  style="width:50px; height:50px; text-align:center; border:none;">
                        <div style="width:30px; height:30px; background-color:white;">1</div>
                    </td>

                    <td style="width:50px; height: 50px; background-color:skyblue; ">
                        <img src="/img/character/char1.png" style="width:50px; height: 50px;"  alt="">
                    </td>
                    <td style="width:250px; background-color:white;">LONDON SPITFIRE</td>
                    <td  style="width:150px; text-align:left; background-color:white;">100 Point</td>
                    <td style=" background-color:white;"><img src="/img/right_circle.png" style="width:50px; height: 50px;"  alt=""></td>
                </tr>

                <tr class="rank_hr">
                    <td  style="width:50px; height:50px; text-align:center; border:none;">
                        <div style="width:30px; height:30px; background-color:white;">2</div>
                    </td>

                    <td style="width:50px; height: 50px; background-color:yellow;">
                        <img src="/img/character/char2.png" style="width:50px; height: 50px;"  alt="">
                    </td>
                    <td style="width:250px; background-color:white;">LONDON SPITFIRE</td>
                    <td  style="width:150px; text-align:left; background-color:white;">100 Point</td>
                    <td style=" background-color:white;"><img src="/img/right_circle.png" style="width:50px; height: 50px;"  alt=""></td>
                </tr>

                <tr class="rank_hr">
                    <td  style="width:50px; height:50px; text-align:center; border:none;">
                        <div style="width:30px; height:30px; background-color:white;">3</div>
                    </td>

                    <td style="width:50px; height: 50px; background-color:#e75480;">
                        <img src="/img/character/char3.png" style="width:50px; height: 50px;"  alt="">
                    </td>
                    <td style="width:250px; background-color:white;">LONDON SPITFIRE</td>
                    <td  style="width:150px; text-align:left; background-color:white;">100 Point</td>
                    <td style=" background-color:white;"><img src="/img/right_circle.png" style="width:50px; height: 50px;"  alt=""></td>
                </tr>
            </table>
        </div>

        <div id="mid_q" style="    width: 40%;
    height: 90%;
    margin-top: 3%;
    margin-left: 4%;
    display: inline-block;">
            <div style="margin-left:10%; width:100%; height:30%; min-height:200px; border-radius:10px; background:white;
                        box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);"
            >
                <span  id="Mid_Q_Name" style="font-size:30px; color:black;font-weight:bold; "></span>
            </div>


            <div class='grafico bar-chart'>
                <div id="obj_opinion" style="display:none;">
                    <div class="choice_status" style="background-image: url('/img/race_play/result_a.png')">
                        <div  class="choice_two_tone"></div>
                        <div class="B_class" id="B1">1</div>

                        <div class="B_class" style="display:inline-block;  width:10%; font-size:20px;">
                            <img src="/img/race_play/person.png" style="width:20px; height:20px;" alt="">
                            <span id="A_count" style="color:black; font-weight: bold;">0</span>
                        </div>

                    </div>

                    <div class="choice_status" style="background-image: url('/img/race_play/result_b.png')">
                        <div  class="choice_two_tone"></div>
                        <div  class="B_class" id="B2">2</div>

                        <div class="B_class" style="display:inline-block;  width:10%; font-size:20px;">
                            <img src="/img/race_play/person.png" style="width:20px; height:20px;" alt="">
                            <span id="B_count" style="color:black; font-weight: bold;">0</span>
                        </div>

                    </div>

                    <div class="choice_status" style="background-image: url('/img/race_play/result_c.png')">
                        <div class="choice_two_tone"></div>
                        <div class="B_class" id="B3">3</div>

                        <div class="B_class" style="display:inline-block;  width:10%; font-size:20px;">
                            <img src="/img/race_play/person.png" style="width:20px; height:20px;" alt="">
                            <span id="C_count" style="color:black; font-weight: bold;">0</span>
                        </div>
                    </div>

                    <div class="choice_status" style="background-image: url('/img/race_play/result_d.png')">
                        <div class="choice_two_tone"></div>
                        <div class="B_class" id="B4">4</div>

                        <div class="B_class" style="display:inline-block;  width:10%; font-size:20px;">
                            <img src="/img/race_play/person.png" style="width:20px; height:20px;" alt="">
                            <span id="D_count" style="color:black; font-weight: bold;">0</span>
                        </div>
                    </div>
                </div>
                <div id="sub_opinion" style="">
                    <h2 class='titular'><img src="/img/race_play/speech_bubble.png" style="width:30px; height:30px;" alt="">学生の答え</h2>
                    <div id="opinion_box" style="margin-left: 10%;
    width: 100%;
    height: 30%;
    min-height: 200px;
    border-radius: 10px;
    background: white;
    box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>

    /************************
    Css orignal https://codepen.io/jlalovi/details/bIyAr
    ************************/
    .choice_status{
        width:100%;
        height:20%;
        margin-top:3%;
        margin-left:10%;
        border-radius:10px;
        background-position: center;
        background-size: cover;
    }
    .choice_two_tone{
        width:26%;
        height:100%;
        font-size:30px;
        border-radius: 10px 0px 0px 10px;
        display:inline-block;
    }
    .B_class{
        display:inline-block;
        font-size:30px;
        width:62%;
        vertical-align: top;
    }

    @import url(https://fonts.googleapis.com/css?family=Ubuntu:400,700);
    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    ul {
        list-style-type: none;
        margin: 0;
        padding-left:0;
    }

    h1 {
        font-size: 23px;
    }

    h2 {
        font-size: 17px;
    }

    p {
        font-size: 15px;
    }
    #mid_content h1,#mid_content h2,#mid_content p,#mid_content a,#mid_content span{
        color: #fff;
    }
    .scnd-font-color {
        color: white;
    }
    .titular {
        display: block;
        line-height: 60px;
        margin: 0;
        text-align: center;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        background: #033981;
        border-radius: 50px;
        margin-left: 22%;
        width: 80%;"
    }
    .horizontal-list {
        margin: 0;
        padding: 0;
        list-style-type: none;
    }
    .horizontal-list li {
        float: left;
    }
    .block {
        margin: 25px 25px 0 0;
        border-radius: 10px;
        width: 300px;
        height:450px;
        overflow: hidden;
    }
    /******************************************** LEFT CONTAINER *****************************************/
    .left-container {}
    .menu-box {
        height: 360px;
    }

    .donut-chart-block {
        overflow: hidden;
    }
    .donut-chart-block .titular {
        padding: 10px 0;
    }
    .os-percentages li {
        width: 75px;
        border-left: 1px solid #394264;
        text-align: center;
        background: #50597b;
    }
    .os {
        margin: 0;
        padding: 10px 0 5px;
        font-size: 15px;
    }
    .os.ios {
        border-top: 4px solid #9e7ac2;
    }
    .os.mac {
        border-top: 4px solid #e24d66;
    }
    .os.linux {
        border-top: 4px solid #55dea8;
    }
    .os.win {
        border-top: 4px solid #f9cd36;
    }
    .os-percentage {
        margin: 0;
        padding: 0 0 15px 10px;
        font-size: 25px;
    }
    .bar-chart-block {
        height: 400px;
    }
    .line-chart {
        height: 200px;
        background: #11a8ab;
    }
    .time-lenght {
        padding-top: 22px;
        padding-left: 38px;
        overflow: hidden;
    }
    .time-lenght-btn {
        display: block;
        width: 70px;
        line-height: 32px;
        background: #50597b;
        border-radius: 5px;
        font-size: 14px;
        text-align: center;
        margin-right: 5px;
        -webkit-transition: background .3s;
        transition: background .3s;
    }
    .time-lenght-btn:hover {
        text-decoration: none;
        background: #e64c65;
    }
    .month-data {
        padding-top: 28px;
    }
    .month-data p {
        display: inline-block;
        margin: 0;
        padding: 0 25px 15px;
        font-size: 16px;
    }
    .month-data p:last-child {
        padding: 0 25px;
        float: right;
        font-size: 15px;
    }
    .increment {
        color: #e64c65;
    }

    /******************************************
    ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓ ↓
    ESTILOS PROPIOS DE LOS GRÄFICOS
    ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑ ↑
    GRAFICO LINEAL
    ******************************************/

    .grafico {
        padding: 2rem 1rem 1rem;
        width: 100%;
        height: 100%;
        position: relative;
        color: #fff;
        font-size: 80%;
    }

    .grafico span > span {
        left: 100%; bottom: 0;
    }
    [data-valor='25'] {width: 75px; transform: rotate(-45deg);}
    [data-valor='8'] {width: 24px; transform: rotate(65deg);}
    [data-valor='13'] {width: 39px; transform: rotate(-45deg);}
    [data-valor='5'] {width: 15px; transform: rotate(50deg);}
    [data-valor='23'] {width: 69px; transform: rotate(-70deg);}
    [data-valor='12'] {width: 36px; transform: rotate(75deg);}
    [data-valor='15'] {width: 45px; transform: rotate(-45deg);}

    [data-valor]:before {
        content: '';
        position: absolute;
        display: block;
        right: -4px;
        bottom: -3px;
        padding: 4px;
        background: #fff;
        border-radius: 50%;
    }
    [data-valor='23']:after {
        content: '+' attr(data-valor) '%';
        position: absolute;
        right: -2.7rem;
        top: -1.7rem;
        padding: .3rem .5rem;
        background: #50597B;
        border-radius: .5rem;
        transform: rotate(45deg);
    }
    [class^='eje-'] {
        position: absolute;
        left: 0;
        bottom: 0rem;
        width: 100%;
        padding: 1rem 1rem 0 2rem;
        height: 80%;
    }
    .eje-x {
        height: 2.5rem;
    }
    .eje-y li {
        height: 25%;
        border-top: 1px solid #777;
    }
    [data-ejeY]:before {
        content: attr(data-ejeY);
        display: inline-block;
        width: 2rem;
        text-align: right;
        line-height: 0;
        position: relative;
        left: -2.5rem;
        top: -.5rem;
    }
    .eje-x li {
        width: 33%;
        float: left;
        text-align: center;
    }

    /******************************************
    GRAFICO CIRCULAR PIE CHART
    ******************************************/
    .donut-chart {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto 2rem;
        border-radius: 100%
    }
    p.center-date {
        background: #394264;
        position: absolute;
        text-align: center;
        font-size: 28px;
        top:0;left:0;bottom:0;right:0;
        width: 130px;
        height: 130px;
        margin: auto;
        border-radius: 50%;
        line-height: 35px;
        padding: 15% 0 0;
    }
    .center-date span.scnd-font-color {
        line-height: 0;
    }
    .recorte {
        border-radius: 50%;
        clip: rect(0px, 200px, 200px, 100px);
        height: 100%;
        position: absolute;
        width: 100%;
    }
    .quesito {
        border-radius: 50%;
        clip: rect(0px, 100px, 200px, 0px);
        height: 100%;
        position: absolute;
        width: 100%;
        font-family: monospace;
        font-size: 1.5rem;
    }
    #porcion1 {
        transform: rotate(0deg);
    }

    #porcion1 .quesito {
        background-color: #E64C65;
        transform: rotate(76deg);
    }
    #porcion2 {
        transform: rotate(76deg);
    }
    #porcion2 .quesito {
        background-color: #11A8AB;
        transform: rotate(140deg);
    }
    #porcion3 {
        transform: rotate(215deg);
    }
    #porcion3 .quesito {
        background-color: #4FC4F6;
        transform: rotate(113deg);
    }
    #porcionFin {
        transform:rotate(-32deg);
    }
    #porcionFin .quesito {
        background-color: #FCB150;
        transform: rotate(32deg);
    }
    .nota-final {
        clear: both;
        color: #4FC4F6;
        font-size: 1rem;
        padding: 2rem 0;
    }
    .nota-final strong {
        color: #E64C65;
    }
    .nota-final a {
        color: #FCB150;
        font-size: inherit;
    }
    /**************************
    BAR-CHART
    **************************/
    .grafico.bar-chart {
        padding: 0 1rem 2rem 1rem;
        width: 100%;
        height: 50%;
        position: relative;
        color: #fff;
        font-size: 80%;
    }
    .bar-chart [class^='eje-'] {
        padding: 0 1rem 0 2rem;
        bottom: 1rem;
    }
    .bar-chart .eje-x {
        bottom: 0;
    }
    .bar-chart .eje-y li {
        height: 20%;
        border-top: 1px solid #fff;
    }
    .bar-chart .eje-x li {
        width: 14%;
        position: relative;
        text-align: left;
    }
    .bar-chart .eje-x li i {
        transform: rotatez(-45deg) translatex(-1rem);
        transform-origin: 30% 60%;
        display: block;
        visibility:hidden
    }
    .bar-chart .eje-x li:before {
        content: '';
        position: absolute;
        bottom: 0.1rem;
        width: 100%;
        right: 5%;
        box-shadow: 3px 0 rgba(0,0,0,.1), 3px -3px rgba(0,0,0,.1);
    }
    .bar-chart .eje-x li:nth-child(1):before {

        background: #11A8AB;
        height: 100px;
    }
    .bar-chart .eje-x li:nth-child(2):before {
        background: #3598db;
        height: 200px;
        left:35px;
    }
    .bar-chart .eje-x li:nth-child(3):before {
        background: #FCB150;
        height: 400%;
        left:75px;
    }
    .bar-chart .eje-x li:nth-child(4):before {
        background: #E64C65;
        height: 290%;
        left:110px;
    }
    .bar-chart .eje-x li:nth-child(5):before {
        background: #FFED0D;
        height: 720%;
    }
    .bar-chart .eje-x li:nth-child(6):before {
        background: #F46FDA;
        height: 820%;
    }
    .bar-chart .eje-x li:nth-child(7):before {
        background: #15BFCC;
        height: 520%;
    }
    /*****************************
    USO NÚMEROS MÁGICOS EN ALGUNOS VALORES
    POR NO PARARME A ESTUDIAR A FONDO
    EL CSS DEL PEN ORIGINAL
    *****************************/




    @import url(http://fonts.googleapis.com/css?family=Open+Sans:400,700);

    @keyframes bake-pie {
        from {
            transform: rotate(0deg) translate3d(0,0,0);
        }
    }
    main {
        width: 400px;
        margin: 30px auto;
    }
    section {
        margin-top: 30px;
    }
    .pieID {
        display: inline-block;
        vertical-align: top;
    }
    .pie {
        height: 200px;
        width: 200px;
        position: relative;
        margin: 0 30px 30px 0;
    }
    .pie::before {
        text-align: center;
        font-size: 35pt;
        content: "";
        display: block;
        position: absolute;
        z-index: 1;
        width: 100px;
        height: 100px;
        background: #EEE;
        border-radius: 50%;
        top: 50px;
        left: 50px;
        line-height:100px;
    }
    .pie::after {
        content: "";
        display: block;
        width: 120px;
        height: 2px;
        background: rgba(0,0,0,0.1);
        border-radius: 50%;
        box-shadow: 0 0 3px 4px rgba(0,0,0,0.1);
        margin: 220px auto;

    }
    .slice {
        position: absolute;
        width: 200px;
        height: 200px;
        clip: rect(0px, 200px, 200px, 100px);
        animation: bake-pie 1s;
    }
    .slice span {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        background-color: black;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        clip: rect(0px, 200px, 200px, 100px);
    }
    .legend {
        list-style-type: none;
        padding: 0;
        margin: 0;
        background: #FFF;
        padding: 15px;
        font-size: 13px;
        box-shadow: 1px 1px 0 #DDD, 2px 2px 0 #BBB;
    }
    .legend li {
        width: 110px;
        height: 1.25em;
        margin-bottom: 0.7em;
        padding-left: 0.5em;
        border-left: 1.25em solid black;
    }
    .legend em {
        font-style: normal;
    }
    .legend span {
        float: right;
    }
    footer {
        position: fixed;
        bottom: 0;
        right: 0;
        font-size: 13px;
        background: #DDD;
        padding: 5px 10px;
        margin: 5px;
    }

    .progress {
        width: 150px;
        height: 150px;
        line-height: 150px;
        background: none;
        margin: 0 auto;
        box-shadow: none;
        position: relative;
    }
    .progress:after {
        content: "";
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 12px solid #fff;
        position: absolute;
        top: 0;
        left: 0;
    }
    .progress > span {
        width: 50%;
        height: 100%;
        overflow: hidden;
        position: absolute;
        top: 0;
        z-index: 1;
    }
    .progress .progress-left {
        left: 0;
    }
    .progress .progress-bar {
        width: 100%;
        height: 100%;
        background: none;
        border-width: 12px;
        border-style: solid;
        position: absolute;
        top: 0;
    }
    .progress .progress-left .progress-bar {
        left: 100%;
        border-top-right-radius: 80px;
        border-bottom-right-radius: 80px;
        border-left: 0;
        -webkit-transform-origin: center left;
        transform-origin: center left;
    }
    .progress .progress-right {
        right: 0;
    }
    .progress .progress-right .progress-bar {
        left: -100%;
        border-top-left-radius: 80px;
        border-bottom-left-radius: 80px;
        border-right: 0;
        -webkit-transform-origin: center right;
        transform-origin: center right;
        animation: loading-1 1.8s linear forwards;
    }
    .progress .progress-value {
        width: 90%;
        height: 90%;
        border-radius: 50%;
        background: #44484b;
        font-size: 24px;
        color: #fff;
        line-height: 135px;
        text-align: center;
        position: absolute;
        top: 5%;
        left: 5%;
    }
    .progress.blue .progress-bar {
        border-color: #049dff;
    }
    .progress.blue .progress-left .progress-bar {
        animation: loading-2 1.5s linear forwards 1.8s;
    }

    .progress.pink .progress-bar {
        border-color: #ed687c;
    }
    .progress.pink .progress-left .progress-bar {
        animation: loading-4 0.4s linear forwards 1.8s;
    }
    .progress.green .progress-bar {
        border-color: #1abc9c;
    }
    .progress.green .progress-left .progress-bar {
        animation: loading-5 1.2s linear forwards 1.8s;
    }
    @keyframes loading-1 {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }
    }
    @keyframes loading-2 {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(144deg);
            transform: rotate(144deg);
        }
    }
    @keyframes loading-3 {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
        }
    }
    @keyframes loading-4 {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(36deg);
            transform: rotate(36deg);
        }
    }
    @keyframes loading-5 {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(126deg);
            transform: rotate(126deg);
        }
    }
    @media only screen and (max-width: 990px) {
        .progress {
            margin-bottom: 20px;
        }
    }
    .well {
        width: 500px;
        height:200px;
        padding: 19px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
        font-weight:bold;
        display: inline-block;
        font-size: 20px;
        text-align:center;
    }
    .nextbutton {
        margin-left: 550px;
    }
</style>


