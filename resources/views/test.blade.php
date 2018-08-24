<html>
<head>
    <title>Record Box</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    <!-- Bootstrap CSS CDN -->
    <style>
        .PAGE_RECORDBOX {
                @if($country == "jp")
                font-family: 'Meiryo UI';
                @else
                font-family: "a뉴고딕M";
                @endif
            background-color: #f7f8fa;
            font-size: 13px;
            color: #5f5f5f;
            margin: 0;
            padding: 0;
        }
        .recordbox_main {
            clear: both;
            width: 100%;
            height: 100%;
            display: block;
            position: relative;
        }
        .record_mainPage {
            padding: 0;
            position: relative;
            float: left;
            width: 86%;
            height: 95%;
        }
        .changePages {
            z-index: 1;
            position: relative;
            display: block;
            text-align: center;
            clear: both;
            margin-bottom: 50px;
        }
        /*modal-page*/
        .modal-dialog {
            width: 1000px;
        }
        .modal-content.studentGrade ,.modal-content.detail {
            margin-bottom: 5px;
            padding: 10px 20px 0 20px;
        }
        .modal-header {
            text-align: center;
        }
        .modal-header #modal_date{
            text-align: right;
        }
        .modal-body {
            text-align: left;
        }
        .modal-footer #modal_total_grades{
            float: right;
        }
        .modal_wrong {
            width: 100%;
            clear: both;
        }
        .wrong_left ,.wrong_right{
            position: relative;
            width: 50%;
            float: left;
            border: 1px solid #e5e6e8;
        }
        .objWrong {
            padding: 10px;
            border: 1px solid #e5e6e8;
        }
        .subWrong {
            padding: 10px;
            border: 1px solid #e5e6e8;
        }
        .wrong_right .objWrong ,.wrong_right .subWrong {
            border-left: 0;
        }
        .noBoardLine {
            border: 0;
        }
        .table_wrongList thead tr {
            vertical-align: top;
        }
        .table_wrongList thead tr > th:first-child{
            width: 30px;
        }
        .table_wrongList thead tr > th:first-child div{
            margin-top: 3px;
        }
        .table_wrongList thead tr > th:last-child{
            margin-top: 3px;
        }
        .table_wrongList thead tr > th:last-child div{
            margin-bottom: 15px;
        }
        .table_wrongList tbody ul{
            list-style-type: circle;
            padding: 0 0 0 25px;
            margin: 0;
            font-size: 15px;
        }
        .table_wrongList tbody ul > li:first-child{
            list-style-type: disc;
            color: red;
            margin-bottom: 3px;
        }
        .table_wrongList tbody .wrongExamples{
            margin-left: 20px;
            margin-bottom: 7px;
        }
        .table_wrongList tbody .wrongWriting {
            width: 440px;
            min-height: 70px;
            margin-top: 10px;
            margin-bottom: 15px;
            border:1px solid #e5e6e8;
        }
        .btnHomeworkCheck {
            color: black;
            text-align: center;
            border: solid 2px grey;
            border-radius: 12px;
        }
        #modal_allWrongAnswerList tr , #details_record tr , #wrongQuestions tr{
            border-bottom: 1px solid #e5e6e8;
        }
        #modal_allWrongAnswerList tr td , #details_record tr td , #wrongQuestions tr td {
            border-left: 1px solid #e5e6e8;
        }
    </style>


    <script>

        //스크롤하면 fixed로 변경하기
        $(window).scroll(function (event) {

            if($(window).scrollTop() == 0){

                //사이드바
                $('.recordbox_sidebar').removeClass('sidenav-up');
                $('.fake_sidebar').removeClass('addToFake');

                //레코드네비 바
                $('.recordbox_navbar').removeClass('nav-up');
                $('.fakeRecordnav').removeClass('addFakeToRecordNav');
                $('.navbar-brand').show();

                //학생 과제 확인 바
                $('.raceListDetail').removeClass('raceListDetail-up');

            }else {

                //사이드바
                $('.recordbox_sidebar').addClass('sidenav-up');
                $('.fake_sidebar').addClass('addToFake');

                //레코드네비 바
                $('.recordbox_navbar').addClass('nav-up');
                $('.fakeRecordnav').addClass('addFakeToRecordNav');
                $('.navbar-brand').hide();

                //학생 과제 확인 바
                $('.raceListDetail').addClass('raceListDetail-up');

            }
        });


        //모달 페이지 관리
        //모달 페이지 내용값 초기화 및 타입별 모달페이지 제작
        function makingModalPage(raceId,allData,type){

            //모달 페이지 내용값 초기화
            $('.modal-content.studentGrade .modal-header #modal_date').empty();
            $('.modal-content.studentGrade .modal-header #modal_raceName_teacher').empty();
            $('.modal-content.studentGrade .modal-body #modal_gradeList').empty();
            $('.modal-content.studentGrade .modal-footer #modal_total_grades').empty();

            $('.modal-content.detail .modal-body #modal_studentList').empty();
            $('.modal-content.detail .modal-body #modal_studentWrongAnswers').empty();
            $('.modal-content.detail .modal-body #modal_allWrongAnswerList').empty();

            var StudentData = new Array();
            var StudentScore = new Array();
            var wrongsData = new Array();

            var MODALID_gradeList_tr = "modal_grade_";
            var MODALID_studentList_tr = "modal_student_";
            var MODALID_wrongList_tr = "modal_wrong_";

            switch (type){

                //레이스 성적표 만들기
                case 0 :

                    $('.modal-content.studentGrade').show();                          //학생 성적표 표시하기
                    $('.modal-content.studentGrade #modal_total_grades').show();      //학생 성적표 표시하기
                    $('.modal-content.detail .modal_checkbox').show();                //체크박스 표시하기
                    $('.modal-content.detail #toggle_only_students').show();          //학생별 오답체트 표시하기
                    $('#wrongPercent').show();                                        //오답률 표시
                    $('.modal #modal_total_students').empty();

//Changing Langeage : modal
//$('.modal-content.detail .modal-title').text('오답 문제');
                    $('.modal-content.detail .modal-title').text("{{$language['modal']['Title']['wrongTest']}}");

                    var totalGrade = 0;
                    var totalVoca = 0;
                    var totalGrammer = 0;
                    var totalWord = 0;
                    var totalRight = 0;

                    //data -> 레이스에 관한 모든 데이터(리턴값 그대로)
                    StudentData = JSON.parse(allData['races'][0]);
                    StudentScore = makingStudentChartData(allData);

//Changing Langeage : modal
//$('.modal-content.studentGrade .modal-title').text("학생 점수");
                    $('.modal-content.studentGrade .modal-title').text("{{$language['modal']['Title']['allStudentGrade']}}");

//Changing Langeage : modal
//$('#modal_date').text(StudentData['year'] + "년 " + StudentData['month'] + "월 " + StudentData['day'] + "일");
                    $('#modal_date').text(StudentData['year'] + "{{$language['modal']['Date']['year']}} " 
                                        + StudentData['month'] + "{{$language['modal']['Date']['month']}} " 
                                        + StudentData['day'] + "{{$language['modal']['Date']['date']}} " );



                    $('#modal_raceName_teacher').text(StudentData['listName'] + "  /  " + StudentData['teacherName']);

//Changing Langeage : modal
//$('.modal #modal_total_students').append($('<a href="#" onclick="getRaceWrongAnswer('+raceId+')">').text('전체 학생'));
                    $('.modal #modal_total_students').append($('<a href="#" onclick="getRaceWrongAnswer('+raceId+')">').text('{{$language["modal"]["Title"]["allStudent"]}}'));


                    for(var i = 0 ; i < allData['races'].length ; i++){

                        StudentData = JSON.parse(allData['races'][i]);

                        $('.modal #modal_gradeList').append($('<tr>').attr('id', MODALID_gradeList_tr + i));

                        $('#' + MODALID_gradeList_tr + i).append($('<td>').append($('<a href="#">').text(StudentData['userName']))
                            .attr('id',StudentData['userId']).attr('name',StudentData['raceId']).attr('class','toggle_stdList'));

                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['total_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['voca_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['grammer_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['word_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentData['allRightCount']+"/"+StudentData['allCount']));

                        totalGrade += StudentScore['total_data'][1][i]['y'];
                        totalVoca += StudentScore['voca_data'][1][i]['y'];
                        totalGrammer += StudentScore['grammer_data'][1][i]['y'];
                        totalWord += StudentScore['word_data'][1][i]['y'];
                        totalRight += StudentData['allRightCount'];
                    }


//Changing Langeage : modal
// $('#modal_total_grades').text("전체 평균: "+parseInt(totalGrade / allData['races'].length)+
//                         " / 어휘: "+parseInt(totalVoca / allData['races'].length)+
//                         " / 문법: "+parseInt(totalGrammer / allData['races'].length)+
//                         " / 독해: "+parseInt(totalWord / allData['races'].length)+
//                         " / 갯수: "+parseInt(totalRight / allData['races'].length));

                    //modal-footer 총 점수들 표시
                    $('#modal_total_grades').text("{{$language['modal']['Grade']['allAverage']}}: "+parseInt(totalGrade / allData['races'].length)+
                        " / {{$language['modal']['Grade']['totalVoca']}}: "+parseInt(totalVoca / allData['races'].length)+
                        " / {{$language['modal']['Grade']['totalWord']}}: "+parseInt(totalGrammer / allData['races'].length)+
                        " / {{$language['modal']['Grade']['totalGrammer']}}: "+parseInt(totalWord / allData['races'].length)+
                        " / {{$language['modal']['Grade']['allCount']}}: "+parseInt(totalRight / allData['races'].length));


                    //오답들
                    getRaceWrongAnswer(raceId);

                    break;

                /*******************************************************************************************************/
                //학생개인 성적표 만들기
                case 1 :

                    $('.modal-content.studentGrade').show();                        //학생 성적표 표시
                    $('.modal-content.studentGrade #modal_total_grades').hide();    //학생 성적표 빼기
                    $('.modal-content.detail .modal_checkbox').hide();              //체크박스 빼기
                    $('.modal-content.detail #toggle_only_students').hide();        //학생별 오답체트 빼기
                    $('#wrongPercent').hide();                                      //오답률 빼기
                    $('.modal #modal_total_students').empty();

//Changing Langeage : modal
// $('.modal #modal_total_students').text("전체 학생");
// $('.modal-content.detail .modal-title').text('오답 문제');
                    $('.modal #modal_total_students').text("{{$language['modal']['Title']['allStudent']}}");
                    $('.modal-content.detail .modal-title').text("{{$language['modal']['Title']['wrongTest']}}");

                    //data -> 학생개인에 관한 모든 데이터(리턴값 그대로)
                    StudentScore = makingStudentChartData(allData);
                    StudentData = JSON.parse(allData['races'][0]);

//Changing Langeage : modal
//$('#modal_date').text(StudentData['year']+"년 "+StudentData['month']+"월 "+StudentData['day']+"일");
                    $('#modal_date').text(StudentData['year']+"{{$language['modal']['Date']['year']}} "
                                         +StudentData['month']+"{{$language['modal']['Date']['month']}} "
                                         +StudentData['day']+"{{$language['modal']['Date']['date']}}");


                    $('#modal_raceName_teacher').text(StudentData['listName'] +"  /  " +StudentData['teacherName'] );

                    for(var i = 0 ; i < allData['races'].length ; i++){

                        StudentData = JSON.parse(allData['races'][i]);

                        $('.modal #modal_gradeList').append($('<tr>').attr('id', MODALID_gradeList_tr + i));

                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentData['userName']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['total_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['voca_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['grammer_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentScore['word_data'][1][i]['y']));
                        $('#' + MODALID_gradeList_tr + i).append($('<td>').text(StudentData['allRightCount']+"/"+StudentData['allCount']));

                    }

                    //오답들
                    getStudentWrongAnswer(StudentData['userId'],raceId);

                    break;

                /*******************************************************************************************************/

                case 2 :
                    //오답노트 페이지 만들기
                    $('.modal-content.studentGrade').hide();                        //학생 성적표 표시
                    $('.modal-content.detail .modal_checkbox').hide();              //체크박스 빼기
                    $('.modal-content.detail #toggle_only_students').hide();        //학생별 오답체트 빼기
                    $('#wrongPercent').hide();                                      //오답률 빼기
                    $('.modal_list_wrong').hide();                                      //오답내용 빼기

//Changing Langeage : modal
//$('.modal-content.detail .modal-title').text('오답 노트');
                    $('.modal-content.detail.modal-title').text("{{$language['modal']['Title']['wrongTest']}}");

                    wrongsData = allData['wrongs'];
                    var leftOrRight = "";

                    $('.wrong_left').empty();
                    $('.wrong_right').empty();

                    if (wrongsData.length == 0) {
//Changing Langeage : modal
//                        $('.modal_wrong').text("오답 내용이 없습니다.");
                        $('.modal_wrong').text("{{$language['modal']['Grade']['noWrongData']}}");
                        $('.wrong_left').addClass("noBoardLine");
                        $('.wrong_right').addClass("noBoardLine");

                    } else {

                        for (var i = 0; i < wrongsData.length; i++) {

                            if (wrongsData[i]['wrong'] == null) {
                                wrongsData[i]['wrong'] = wrongsData[i]['number']+"번은 이러이러저러저러하다.";
                            }

                            if(i < 5){
                                leftOrRight = "wrong_left";
                                $('.wrong_left').removeClass("noBoardLine");
                                $('.wrong_right').addClass("noBoardLine");
                            }else{
                                leftOrRight = "wrong_right";
                                $('.wrong_right').removeClass("noBoardLine");
                            }

                            switch(wrongsData[i]['type']) {
                                case "obj" :

                                    $('.' + leftOrRight).append($('<div>').attr('class', 'objWrong')
                                        .append($('<table>').attr('class', 'table_wrongList')
                                            .append($('<thead>')
                                                .append($('<tr>')
                                                    .append($('<th>')
                                                        .append($('<div>').text(wrongsData[i]['number'])))
                                                    .append($('<th>')
                                                        .append($('<div>')
                                                            .append($('<b>').text(wrongsData[i]['question']))))))
                                            .append($('<tbody>')
                                                .append($('<tr>')
                                                    .append($('<td colspan="2">')
                                                        .append($('<div>').attr('class', 'wrongExamples')
                                                            .append($('<ul>')
                                                                .append($('<li>').text(wrongsData[i]['rightAnswer']).attr('class', 'example_' + i + '_1'))
                                                                .append($('<li>').text(wrongsData[i]['example1']).attr('class', 'example_' + i + '_1'))
                                                                .append($('<li>').text(wrongsData[i]['example2']).attr('class', 'example_' + i + '_1'))
                                                                .append($('<li>').text(wrongsData[i]['example3']).attr('class', 'example_' + i + '_1'))
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    );

                                    break;
                                case "sub" :

                                    $('.' + leftOrRight).append($('<div>').attr('class', 'subWrong')
                                        .append($('<table>').attr('class', 'table_wrongList')
                                            .append($('<thead>')
                                                .append($('<tr>')
                                                    .append($('<th>')
                                                        .append($('<div>').text(wrongsData[i]['number'])))
                                                    .append($('<th>')
                                                        .append($('<div>')
                                                            .append($('<b>').text(wrongsData[i]['question']))))))
                                            .append($('<tbody>')
                                                .append($('<tr>')
                                                    .append($('<td colspan="2">')
                                                        .append($('<div>').attr('class', 'wrongExamples')
                                                            .append($('<div>').text("정답 : " + wrongsData[i]['rightAnswer'])
                                                            )
                                                            .append($('<div>').text("힌트 : " + wrongsData[i]['hint']).css('color', 'blue')
                                                            )
                                                            .append($('<div>').text("작성답 : " + wrongsData[i]['wrongs'][0]['answer']).css('color', 'black')
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    );
                                    break;
                            }

                            for (var j = 1; j < 4; j++) {
                                if (wrongsData[i]['example' + j + 'Count'] == 1) {
                                    $('.example_' + i + '_' + j).css('color', 'blue');
                                }
                            }
                        }

                    }
            }
        }

    </script>
</head>
<div class="PAGE_RECORDBOX">

{{--메인 네비바 불러오기--}}
@include('Navigation.main_nav')

<div class="recordbox_main">

    {{--사이드바 불러오기--}}
    @include('Recordbox.record_sidebar')

    <div class="record_mainPage">

            {{--레코드 네비바 불러오기--}}
            @include('Recordbox.record_recordnav')

        <div class="changePages">
            {{--레코드 flz 불러오기--}}
            @include('Recordbox.record_'.$where)
        </div>

    </div>
</div>


<div class="modal_page">
    {{--Modal : Race Record--}}
    <div class="modal fade" id="modal_RaceGradeCard" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="width: 1000px
        ;" >

            {{--PAGE SPLIT 1. 모달 학생점수 페이지--}}
            <div class="modal-content studentGrade">

                <div class="modal-header">
                    <h3 class="modal-title" id="ModalLabel" >
                        {{--"학생점수"--}}
                    </h3>

                    {{--INSERT DATA 1. 날짜--}}
                    <div id="modal_date"> </div>

                    {{--INSERT DATA 2. 레이스이름과 교수님 성함--}}
                    <div id="modal_raceName_teacher"></div>

                </div>

                <div class="modal-body">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th id="modal_total_students">

                            </th>
                            <th>
                            <!--Changing Langeage : modal / 총점수-->
                                {{$language['modal']['Grade']['allGrade']}}
                            </th>

                            <th>
                            <!--Changing Langeage : modal / 어휘-->
                            {{$language['modal']['Grade']['totalVoca']}}
                            </th>

                            <th>
                            <!--Changing Langeage : modal / 문법-->
                            {{$language['modal']['Grade']['totalWord']}}
                            </th>
                            
                            <th>
                            <!--Changing Langeage : modal / 독해-->
                            {{$language['modal']['Grade']['totalGrammer']}}
                            </th>

                            <th>
                            <!--Changing Langeage : modal / 갯수-->
                            {{$language['modal']['Grade']['allCount']}}
                            </th>
                        </tr>
                        </thead>

                        {{--INSERT DATA 3. 학생들 성적 테이블--}}
                        <tbody id="modal_gradeList">

                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">

                    {{--PAGE SPLIT 2. 모달 전체 평균 점수들--}}
                    {{--INSERT DATA 4. 전체 평균 점수들--}}
                    <div id="modal_total_grades"> </div>
                </div>
            </div>

            {{--PAGE SPLIT 3. 모달 상세보기 페이지--}}
            <div class="modal-content detail">
                <div class="modal-header">
                    <h3 class="modal-title" id="ModalLabel">상세 보기</h3>
                </div>

                <div class="modal-body">

                    {{--PAGE SPLIT 6. 모달 레이스 전체 오답노트 리스트--}}
                    <div id="toggle_only_wrong_answers" class="modal_wrong">

                        <div class="wrong_left">

                        </div>
                        <div class="wrong_right">

                        </div>

                        <div>
                            <table class="table table-hover">
                                <tbody id="modal_allWrongAnswerList">

                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>


    {{--Modal : select group--}}
    <div class="modal fade" id="Modal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" >

                <div class="modal-header">
                    <h4 class="modal-title" id="ModalLabel">
                            <!--Changing Langeage : modal / 피드백-->
                            {{$language['modal']['Title']['feedback']}}
                    </h4>
                    <br>
                    <div class="request_date" style=";float: right;">
                        질문 날짜 : 2018-04-17
                    </div>
                    <br>
                    <div class="response_date" style="float: right;">
                        대답한 날짜 : 2018-04-17
                    </div>
                    <br>
                </div>

                <div class="modal-body" style="margin: 0; padding:0;">

                    <div class="request_contents" style="padding: 5px 10px 5px 10px;min-height: 150px;width: 100%;border-bottom: 1px solid #e5e6e8;">
                        오늘 푼 스쿠스쿠 퀴즈 3번 문제 답이<br>
                        왜 1번인지 이해가 안갑니다.<br>
                        4번이 해석에 더 맞지 않을까요? <br>
                    </div>

                    <style>
                        .images label {
                            display: inline-block;
                            padding: .5em .75em;
                            color: #999;
                            font-size: inherit;
                            line-height: normal;
                            vertical-align: middle;
                            background-color: #fdfdfd;
                            cursor: pointer;
                            border: 1px solid #ebebeb;
                            border-bottom-color: #e2e2e2;
                            border-radius: .25em;
                        }

                        .images input[type="file"] {
                            /* 파일 필드 숨기기 */ position: absolute;
                            width: 1px;
                            height: 1px;
                            padding: 0;
                            margin: -1px;
                            overflow: hidden;
                            clip:rect(0,0,0,0);
                            border: 0;
                        }
                    </style>

                    {{--사진 불러오기--}}
                    <div class="images" style="margin: 10px;">

                        <label for="ex_file">
                        <!--Changing Langeage : modal / 피드백 -> 파일 첨부 -->
                            {{$language['modal']['Feedback']['file']}}
                        </label>

                        <form id="myform" name="myform" method="post" enctype="multipart/form-data">
                            <input type="file" name="feedbackImg" onchange="loadFile()" id="ex_file">
                        </form>

                        <img id="output" style="max-width: 300px;max-height: 300px;"/>

                        {{--사진 불러오는 스크립트--}}
                        <script type="text/javascript">

                            function loadFile(){
                                var reader = new FileReader();

                                var ex_file = document.getElementById('ex_file');

                                reader.onload = function(){
                                    var output = document.getElementById('output');
                                    output.src = reader.result;
                                };
                                reader.readAsDataURL(event.target.files[0]);

                            };

                            $(document).on('click', '#modal_feedback_cancel', function (e) {
                                $('#output').attr("src","");
                                $('#teachersFeedback').val("");
                            });
                        </script>
                    </div>

                    {{--텍스트 창--}}
                    <div class="answer" style="padding: 5px 5px 5px 5px">
                        <input type="text" id="teachersFeedback" name="contents" style="width: 100%;height:120px;"></input>
                    </div>
                </div>

                <div class="modal-footer feedback"> </div>
            </div>
        </div>
    </div>
</div>



</div>
</html>