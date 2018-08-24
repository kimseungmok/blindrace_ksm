<?php

    echo $response;

?>

<html>
<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="generator" content="Bootply" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>

    <!-- Bootstrap CSS CDN -->
    <style>
        body {
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
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
        .changePages {
            padding: 0;
            position: relative;
            float: left;
            width: 85%;
        }
        .insertMargin {
            margin-left: 12%;
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
            float: left;
            position: relative;
            width: 50%;
            padding: 10px;
            border: 1px solid #e5e6e8;
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
            margin-bottom: 10px;
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

    <script type="text/javascript">

        // 1~9월 1~9일에 앞자리 0추가해주는 함수
        function fn_leadingZeros(n, digits) {

            var zero = '';
            n = n.toString();

            if (n.length < digits) {
                for (var i = 0; i < digits - n.length; i++){ zero += '0'; }
            }
            return zero + n;
        }

        // 날짜의 포맷을 ( YYYY-mm-dd ) 형태로 만들어줍니다.
        var loadDt = new Date();
        var defaultEndDate = loadDt.getFullYear() + '-' + fn_leadingZeros(loadDt.getMonth() + 1, 2) + '-' + fn_leadingZeros(loadDt.getDate(), 2);
        var tempdate = new Date(defaultEndDate);
        tempdate.setMonth(tempdate.getMonth()-1);
        var defaultStartDate = tempdate.getFullYear() + '-' + fn_leadingZeros(tempdate.getMonth() + 1, 2) + '-' + fn_leadingZeros(tempdate.getDate(), 2);

        /***********************************날짜 구하기**************************************************************************************************/

            //teacher에 세션값을 받아서 넣어주기
        var group_id = 0;
        var teacher = 1; //의미 없는값
        var chartData = "";

        //처음 화면 로드
        function OnLoadRecordbox() {

            //클래스 불러오기 and 차트 로드하기 and 학생 명단 출력하기 and 피드백 가져오기
            $.ajax({
                type: 'POST',
                url: "{{url('/groupController/groupsGet')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                //data: { var teacher = 1 },
                data: teacher,
                success: function (data) {

                    var GroupData = data;

                    //사이드바에 클래스 추가
                    for( var i = 0 ; i < GroupData['groups'].length ; i++ ){

                        $('#group_names').append($('<a href="#">')
                            .append($('<div>').attr('class','groups')
                                .attr('name',GroupData['groups'][i]['groupName'])
                                .attr('id',GroupData['groups'][i]['groupId'])
                                .text(GroupData['groups'][i]['groupName'])));
                    }

                    //가장 상단에 위치한 클래스
                    var firstGroup = $('.groups:first-child');

                    //레코드���스네비바 첫부분에 상단 클래스 이름 넣기
                    $('#nav_group_name').text(firstGroup.attr('name'));

                    //그룹아이디값을 상단 클래스 아이디값으로 변경
                    group_id = firstGroup.attr('id');

                    //전체 페이지 출력
                    AllPageLoad(group_id);

                    $('.record_chart').show();
                    $('.record_history').hide();
                    $('.record_student').hide();
                    $('.record_feedback').hide();

                },
                error: function (data) {
                    alert("그룹겟 에러");
                }
            });

        };

        //전체 페이지 로드
        function AllPageLoad(groupid){

            //차트 불러오기
            getChartData_and_loadChart(groupid,defaultStartDate,defaultEndDate);

            //가장 상단에 있는 클래스 ID값으로  최근 기록 불러오기
            getHistory(groupid);

            //가장 상단에 있는 클래스 ID값으로 학생명단 만들기
            getStudents(groupid);

            //피드백 페이지 불러오기

        }

        $(document).ready(function () {

            var raceId = "";
            var userId = "";

            //날짜 타입 라디오 버튼 누를때 마다 차트에 반영
            $(document).on('click','.radio_changeDateToChart',function () {

                var selectedradio = $("input[type=radio][name=optradio]:checked").val();
                var startDate = "";

                function caldate(day){

                    var caledmonth, caledday, caledYear;
                    var v = new Date(Date.parse(loadDt) - day*1000*60*60*24);

                    caledYear = v.getFullYear();

                    if( v.getMonth() < 9 ){
                        caledmonth = '0'+(v.getMonth()+1);
                    }else{
                        caledmonth = v.getMonth()+1;
                    }

                    if( v.getDate() < 9 ){
                        caledday = '0'+v.getDate();
                    }else{
                        caledday = v.getDate();
                    }
                    return caledYear+"-"+caledmonth+'-'+caledday;
                }
                switch (selectedradio) {
                    case "1":
                        startDate = caldate(7);
                        break;
                    case "2":
                        startDate = caldate(30);
                        break;
                    case "3":
                        startDate = caldate(90);
                        break;
                    case "4":
                        startDate = caldate(180);
                        break;
                    case "5":
                        startDate = caldate(365);
                        break;
                }

                //해당되는 날짜를 차트에 보여주기
                getChartData_and_loadChart(group_id,startDate,defaultEndDate);

            });

            //클래스 클릭 할 때 마다 메인 페이지(차트) 로드
            $(document).on('click','.groups',function () {

                var reqGroupId = $(this).attr('id');
                var reqGroupName = $(this).attr('name');

                $('#historyListNumber').empty();
                $('#historyListRaceName').empty();
                $('#history_homework').empty();

                group_id = reqGroupId;

                AllPageLoad(group_id);
            });

            //학생 한명 클릭하면 개인성적 가져오기
            $(document).on('click','.stdList',function () {
                getStudentGrade(this.id);

            });

            //레코드리스트에서 성적표 클릭시 성적표 로드
            $(document).on('click','.history_list_gradeCard button',function () {
                loadGradeCard(this.id);
            });

            //학생 상세정보에서 성적표 클릭시 성적표 로드
            $(document).on('click','.modal_openStudentGradeCard button',function () {
                raceId = $(this).attr('id');
                userId = $(this).attr('name');

                loadStudentGradeCard(userId,raceId);
            });

            //학생 상세정보에서 재시험 클릭시 성적표 로드
            $(document).on('click','.modal_openStudentRetestGradeCard button',function () {
                raceId = $(this).attr('id');
                userId = $(this).attr('name');

                getRetestData(userId,raceId);
            });

            //학생 상세정보에서 학생 클릭시 오��들 로드
            $(document).on('click','.toggle_stdList',function () {
                raceId =$(this).attr('name');
                userId = $(this).attr('id');

                getStudentWrongAnswer(userId,raceId);

            });

            //학생 상세정보에서 오답노트 클릭시 성적표 로드
            $(document).on('click','.modal_openStudentWrongGradeCard button',function () {
                raceId = $(this).attr('id');
                userId = $(this).attr('name');

                getStudentWrongWriting(userId,raceId);
            });

            // 라디오버튼 선택 확인 (문제유형)
            $(document).on('click','#checkbox',function(){

                $('input:checkbox[name="gradeCase"]').each(function() {

                    //checked 처리된 항목의 값
                    if(this.checked) {
                        $('#'+this.value).show();
                    }
                    //check가 아닐때
                    else{
                        $('#'+this.value).hide();
                    }
                });

            });

            $(document).on('click','.feedbackList',function () {
                loadFeedbackModal($(this).attr('id'));

            });

            $(document).on('click','.modal-footer .btn.btn-primary',function () {
                changeCheck($('.request_date').attr('id'));
                insertQuestion();
            });


            //과제 확인하기
            $(document).on('click','.btnHomeworkCheck',function () {
                checkHomework($(this).attr('id'));

            });

            //스크롤 할 때마다 레코드박스 메뉴바 위치 변경
            $(window).scroll(function (event) {

                if($(window).scrollTop() == 0){
                    $('.recordbox_navbar').removeClass('nav-up');
                    $('.recordbox_sidebar').removeClass('sidenav-up');
                    $('.changePages').removeClass('insertMargin');
                }else {
                    $('.recordbox_navbar').addClass('nav-up');
                    $('.recordbox_sidebar').addClass('sidenav-up');
                    $('.changePages').addClass('insertMargin');
                }
            });

        });

        //날짜 조회 눌렀을 때 차트 출력
        function orderChart(){
            var startDate = document.querySelector('input[id="startDate"]').value;
            var endDate = document.querySelector('input[id="endDate"]').value;

            getChartData_and_loadChart(group_id,startDate,endDate);
        }

        //날짜를 가져와서 조회 및 차트 그리기
        function getChartData_and_loadChart(groupId,startDate,endDate){

            $('#'+groupId).css('background-color','#d9edf7');

            var requestData = {"groupId" : groupId , "startDate" : startDate , "endDate" : endDate};
            /*var group_Id = {"groupId" : 1 , "startDate" : "2018-05-01" , "endDate" : "2018-05-08"};*/

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getChart')}}",
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                //data: {_token: CSRF_TOKEN, 'post':params},
                data: requestData,
                success: function (data) {

                    /*
                    * data = { group : {id : 1 , name : "3WDJ"} ,
                               races : { 0 : {  year:2018
                                                month:5
                                                day:11

                                                raceId:2
                                                listName:"테스트용 리스트1"
                                                userCount:5

                                                quizCount:6
                                                rightAnswerCount:4

                                                grammarCount:2
                                                grammarRightAnswerCount:1.4

                                                vocabularyCount:2
                                                vocabularyRightAnswerCount:1.2

                                                wordCount:2
                                                wordRightAnswerCount:1.4
                                              }
                                        }
                    */

                    chartData = data['races'];

                    //레코드 네비바 클래스 이름과 아이디와 내용 바꾸기
                    $('#nav_group_name').text(data['group']['name']);

                    var ChartData = makingChartData(data);
                    makingChart(ChartData);

                },
                error: function (data) {
                    alert("날짜 조회 에러");
                }
            });

        }

        /*        //클래스 가져오기
                //차트 그리기
                //학생 명단 가져오기
                function getGroups_and_loadChart(groupId) {

                    $.ajax({
                        type: 'POST',

                        url: "{ {url('/groupController/groupsGet')} }",
                        //processData: false,
                        //contentType: false,
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        //data: {_token: CSRF_TOKEN, 'post':params},
                        data: groupId,
                        success: function (data) {

                            var GroupData = data;

                            for( var i = 0 ; i < GroupData['groups'].length ; i++ ){

                                $('#group_names').append($('<a href="#">')
                                    .append($('<div class="groups" name="'+GroupData['groups'][i].groupName+'" id="'+ GroupData['groups'][i].groupId +'">')
                                        .text(GroupData['groups'][i].groupName)));

                            }

                            //가장 상단에 위치한 클래스
                            var firstGroup = $('.groups:first-child');

                            //레코드박스네비바 첫부분에 상단 클래스 이름 넣기
                            $('#nav_group_name').text(firstGroup.attr('name'));

                            //가장 상단에 있는 클래스 ID값으로 차트 만들기
                            getChartData_and_loadChart(firstGroup.attr('id'),defaultStartDate,defaultEndDate);
                            getStudents(firstGroup.attr('id'));
                            getHistory(firstGroup.attr('id'));

                            group_id = firstGroup.attr('id');

                        },
                        error: function (data) {
                            alert("그룹겟 에러");
                        }
                    });

                }*/

        //그룹에 속한 학생들 가져오기
        //최근기록 -> 성적표(토글)페이지
        //학생관리 ->
        function getStudents(groupId){

            var reqData ={"groupId" : groupId};

            $.ajax({
                type: 'POST',

                url: "{{url('/groupController/groupDataGet')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                //data: {_token: CSRF_TOKEN, 'post':params},
                data: reqData,
                success: function (data) {

                    $('#student_list').empty();

                    /*
                    data = {group : { id: 1, name: "#WDJ", studentCount : 5}
                            student : { 0: { id: 1300000, name: "김똘똘"}
                                        1: { id: 1300000, name: "최천재"}
                                       }
                            teacher : { id: 123456789, name: "이OO교수"}
                    */

                    var student = data['students'];

                    for(var i = 0 ; i < student.length; i++){
                        $('#student_list').append($('<tr id="student_list_'+i+'">'));

                        for(var j = 0 ; j < 1 ; j++ ) {

                            $('#student_list_' + i).append($('<td>').text(i+1));
                            $('#student_list_' + i).append($('<td>')
                                .append($('<a href="#">')
                                    .text(student[i]['name']))
                                .attr('id',student[i]['id'])
                                .attr('name',student[i]['name'])
                                .attr('class','stdList'));
                        }
                    }

                },
                error: function (data) {
                    alert("그룹에 속한 학생 에러");
                }
            });

        }

        /*        function toggle_detailStudent_and_Wrong(value) {

                    if($("#checkbox_0").is(":checked")){
                        $('#toggle_only_students').attr('class','');
                    }else{
                        $('#toggle_only_students').attr('class','hidden');
                    }

                    if($("#checkbox_1").is(":checked")){
                        $('#toggle_only_wrong_answers').attr('class','');
                    }else{
                        $('#toggle_only_wrong_answers').attr('class','hidden');
                    }
                }*/


        function getHistory(group_id){
            // 요구하는 값
            // $postData = array( 'groupId'   => 1 );

            $('#history_list').empty();

            var reqData = {"groupId" : group_id};
            var raceData = [];

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getRaces')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {

                    /*
                     races : { 0 : { raceId: 7,
                                     listName: "테스트용 리스트1",
                                     teacherName: "이교수",
                                     studentCount: 5,

                                     date: "2018-05-13 19:53:47",
                                     year: 2018,
                                     mont: 5,
                                     day: 13,

                                     wrongClearCount: 0
                                     wrongCount: 0
                                     retestClearCount: 0
                                     retestCount: 0
                     */

                    for( var i = 0 ; i < data['races'].length ; i++ ){
                        $('#history_list').append($('<tr>').attr('id','history_list_tr'+i));
                    }

                    for( var i = 0 ; i < data['races'].length ; i++ ){

                        $('#history_list_tr'+i).append($('<td>').attr('id','history_id_'+data['races'][i]['raceId'])
                            .text(i+1).attr('value',data['races'][i]['raceId']));

                        $('#history_list_tr'+i).append($('<td>').attr('id','history_name_'+data['races'][i]['raceId'])
                            .text(data['races'][i]['listName']).attr('value',data['races'][i]['listName']));

                        $('#history_list_tr'+i).append($('<td>').text(data['races'][i]['date']));

                        $('#history_list_tr'+i).append($('<td>').attr('class','history_list_gradeCard')
                            .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                .attr('id',data['races'][i]['raceId'])
                                .text("성적표")));

                        if(data['races'][i]['retestClearCount'] == data['races'][i]['retestCount'] &&
                            data['races'][i]['wrongClearCount'] == data['races'][i]['wrongCount']){

                            $('#history_list_tr'+i).append($('<td>').append($('<button onclick="checkHomework(this.id)">')
                                .attr('class','btn btn-primary').attr('id',data['races'][i]['raceId']).text("전체완료"))
                                .append($('<button>').text("▶").attr('class','btnHomeworkCheck').attr('id',data['races'][i]['raceId'])));
                        }else{
                            $('#history_list_tr'+i).append($('<td>').append($('<button onclick="checkHomework(this.id)">')
                                .attr('class','btn btn-warning').attr('id',data['races'][i]['raceId']).text("미완료"))
                                .append($('<button>').text("▶").attr('class','btnHomeworkCheck').attr('id',data['races'][i]['raceId'])));
                        }


                    }

                },
                error: function (data) {
                    alert("최근 기록 불러오기 실패");
                }
            });

        }

        //성적표 출력
        function loadGradeCard(raceId){

            //value = {userId : 1300000}
            //value = {raceId : 1}

            var reqData = {'raceId' : raceId};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getStudents')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {
                    /*
                     data = { group : {id : 1 , name : "3WDJ"} ,
                               races : { 0 : {  year:2018,
                                                month:5,
                                                day:11,
                                                date: "2018-05-07 19:53:46",

                                                raceId:1,
                                                listName:"테스트용 리스트1",
                                                userCount:5,
                                                userName:"김똘똘",
                                                userId: 1300000,
                                                teacherName: "이OO교수",

                                                allCount:6,
                                                allRightCount:4,

                                                grammarCount:2,
                                                grammarRightAnswerCount:1.4,

                                                vocabularyCount:2,
                                                vocabularyRightAnswerCount:1.2,

                                                wordCount:2,
                                                wordRightAnswerCount:1.4,

                                                retestState:"not",
                                                wrongState:"not"
                                              }
                                        }
                    */
                    //전체 점수와 ���균 점수들 로드하기
                    //0은 전체 성적표
                    makingModalPage(raceId,data,0);

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }
            });
        }

        //학생 성적표 출력
        function loadStudentGradeCard(userId,raceId){
            //value = {userId : 1300000}
            //value = {raceId : 1}

            var reqData = {'raceId' : raceId , 'userId' : userId};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getStudents')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {
                    /*
                     data = { group : {id : 1 , name : "3WDJ"} ,
                               races : { 0 : {  year:2018,
                                                month:5,
                                                day:11,
                                                date: "2018-05-07 19:53:46",

                                                raceId:1,
                                                listName:"테스트용 리스트1",
                                                userCount:5,
                                                userName:"김똘똘",
                                                userId: 1300000,
                                                teacherName: "이OO교수",

                                                allCount:6,
                                                allRightCount:4,

                                                grammarCount:2,
                                                grammarRightCount:1.4,

                                                vocabularyCount:2,
                                                vocabularyRightCount:1.2,

                                                wordCount:2,
                                                wordRightCount:1.4,
4y4y\
                                                retestState:"not",
                                                wrongState:"not"
                                              }
                                        }
                    */

                    //1은 학생개인 성적표
                    makingModalPage(raceId,data,1);
                    $('.modal-content.studentGrade .modal-title').text("학생 점수");

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }
            });
        }

        //해당 레이스안에서 나온 오답들 가져오기
        function getRaceWrongAnswer(raceId) {

            var reqData ={"raceId" : raceId};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getWrongs')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {
                    /*
                     data = { wrongs: {
                                    0: { number: 1,
                                        id: 1,
                                        question: "苦労してためたお金なのだから、一円（　　）無駄には使いたくない。",
                                        hint:"3",

                                        rightAnswer:1,
                                        example1:"たりとも",
                                        example2:"とはいえ",
                                        example3:"だけさえ",

                                        userCount:5,
                                        rightAnswerCount:0,
                                        wrongCount:5,
                                        example1Count:0,
                                        example2Count:3,
                                        example3Count:2,
                                        }
                                    }
                            }
                    */

                    var wrongsData = data['wrongs'];
                    var leftOrRight = "";

                    $('.wrong_left').empty();
                    $('.wrong_right').empty();

                    if(wrongsData.length == 0){
                        $('.modal_wrong').text("오답 내용이 없습니다.");
                        $('.wrong_left').addClass("noBoardLine");
                        $('.wrong_right').addClass("noBoardLine");

                    }else{

                        for(var i = 0 ; i < wrongsData.length ; i++ ){

                            if(i < 1 ){
                                leftOrRight = "wrong_left";
                                $('.wrong_right').addClass("noBoardLine");
                            }
                            else if(i % 2 == 0 ){
                                leftOrRight = "wrong_left";
                                $('.wrong_right').removeClass("noBoardLine");
                            }
                            else{
                                leftOrRight = "wrong_right";
                                $('.wrong_right').removeClass("noBoardLine");
                            }

                            $('.' + leftOrRight).append($('<table>').attr('class', 'table_wrongList')
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
                                            .append($('<div>').attr('class','wrongExamples')
                                                .append($('<ul>')
                                                    .append($('<li>').text(wrongsData[i]['rightAnswer']+" ("+ wrongsData[i]['rightAnswerCount'] +"명)"))
                                                    .append($('<li>').text(wrongsData[i]['example1']+" ("+ wrongsData[i]['example1Count'] +"명)"))
                                                    .append($('<li>').text(wrongsData[i]['example2']+" ("+ wrongsData[i]['example2Count'] +"명)"))
                                                    .append($('<li>').text(wrongsData[i]['example3']+" ("+ wrongsData[i]['example3Count'] +"명)")
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            );
                        }
                    }

                },
                error: function (data) {
                    alert("해당 학생별 오답 문제 가져오기");
                }
            });

        }

        //학생별 오답 가져오기
        function getStudentWrongAnswer(userId,raceId) {

            var reqData ={"userId" : userId , "raceId" : raceId};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getWrongs')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {
                    /*
                     data = { wrongs: {
                                    0: { number: 1,
                                        id: 1,
                                        question: "苦労してためたお金なのだから、一円（　　）無駄には使いたくない。",
                                        hint:"3",

                                        rightAnswer:1,
                                        example1:"たりとも",
                                        example2:"とはいえ",
                                        example3:"だけさえ",

                                        userCount:1,
                                        rightAnswerCount:0,
                                        wrongCount:1,
                                        example1Count:0,
                                        example2Count:0,
                                        example3Count:1,
                                        }
                                    }
                            }
                    */
                    //오답리스트 로드할 위치(id값)를 변수에 담기
                    var WrongList = "modal_allWrongAnswerList";
                    var wrongsData = data['wrongs'];
                    var leftOrRight = "";

                    $('.wrong_left').empty();
                    $('.wrong_right').empty();

                    if(wrongsData.length == 0){
                        $('.wrong_left').text("오답 내용이 없습니다.");
                        $('.wrong_left').addClass("noBoardLine");
                        $('.wrong_right').addClass("noBoardLine");

                    }else{

                        for(var i = 0 ; i < wrongsData.length ; i++ ){

                            if(i < 5){
                                leftOrRight = "wrong_left";
                                $('.wrong_left').removeClass("noBoardLine");
                                $('.wrong_right').addClass("noBoardLine");
                            }else{
                                leftOrRight = "wrong_right";
                                $('.wrong_right').removeClass("noBoardLine");
                            }

                            $('.' + leftOrRight).append($('<table>').attr('class', 'table_wrongList')
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
                                                    .append($('<li>').text(wrongsData[i]['rightAnswer']))
                                                    .append($('<li>').text(wrongsData[i]['example1']).attr('class', 'example_' + i + '_1'))
                                                    .append($('<li>').text(wrongsData[i]['example2']).attr('class', 'example_' + i + '_2'))
                                                    .append($('<li>').text(wrongsData[i]['example3']).attr('class', 'example_' + i + '_3'))
                                                )
                                            )
                                        )
                                    )
                                )
                            );

                            for (var j = 1; j < 4; j++) {
                                if (wrongsData[i]['example' + j + 'Count'] == 1) {
                                    $('.example_' + i + '_' + j).css('color', 'blue');
                                }
                            }
                        }
                    }
                },
                error: function (data) {
                    alert("해당 학생별 오답 문제 가져오기");
                }
            });

        }

        //오답 노트 작성 메서드
        function getStudentWrongWriting(userId,raceId) {

            var reqData = {'userId' : userId , 'raceId' : raceId};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getWrongs')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {
                    /*
                     data = {
                        group: {id: 1, name: "#WDJ", studentCount: 5},
                        wrongs: {
                            0: { number: 1,
                                id: 1,
                                question: "苦労してためたお金なのだから、一円（　　）無駄には使いたくない。",
                                wrong:"오답 풀이를 해볼까요~",

                                rightAnswerNumber:1,
                                choiceNumber:2,

                                example1:"たりとも",
                                example1Number:2,
                                example2:"とはいえ",
                                example2Number:1,
                                example3:"だけさえ",
                                example3Number:3,
                                example4:"ばかりも",
                                example4Number:4,

                                userCount: 5,
                                rightAnswerCount:1,
                                example1Count:1,
                                example2Count:2,
                                example3Count:1,

                            },

                            1: { number: 2,
                                id: 1,
                                question: "この店は洋食と和食の両方が楽しめる（　　）、お得意さんが多い。",
                                wrong:"오답 풀이를 해볼까요~",

                                rightAnswerNumber:2,
                                choiceNumber:3,

                                example1:"かたがた",
                                example1Number:1,
                                example2:"とあって",
                                example2Number:2,
                                example3:"にあって",
                                example3Number:3,
                                example4:"にしては",
                                example4Number:4,

                                userCount: 5,
                                rightAnswerCount:1,
                                example1Count:1,
                                example2Count:2,
                                example3Count:1,

                            },
                        }
                    };
                    */

                    makingModalPage(raceId,data,2);

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }
            });

        }

        //레이스 이름 클릭시 학생들 과제 상태 체크
        function checkHomework(raceId){
            // 요구하는 값
            /*            $postData = array(
                            'raceId'    => 1
                    );*/
            var reqData = {'raceId' : raceId};

            $.ajax({
                type: 'POST',

                url: "{{url('/recordBoxController/homeworkCheck')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                //data: {_token: CSRF_TOKEN, 'post':params},
                data: reqData,
                success: function (data) {

                    // data = {
                    //     group: {id: 1, name: "3WDJ"},
                    //     students: {
                    //         0: {
                    //             userId: 1300000
                    //             userName: "김똘똘"
                    //
                    //             retestState: "not"
                    //             wrongState: "not"
                    //         }

                    //          1: {
                    //             userId: 1300000
                    //             userName: "김똘똘"
                    //
                    //             retestState: "not"
                    //             wrongState: "not"
                    //         }
                    //      }
                    // }

                    var stdHomework = data['students'];
                    $('#historyListNumber').empty();
                    $('#historyListRaceName').empty();
                    $('#history_homework').empty();

                    for (var i = 0; i < stdHomework.length ; i ++) {

                        if(stdHomework[i]['wrongState'] == "not" && stdHomework[i]['retestState'] == "not"){
                            $('#history_homework').append($('<tr id="history_homework_tr' + i + '">'));

                            $('#historyListNumber').text($('#history_id_'+raceId).attr('value'));
                            $('#historyListRaceName').text($('#history_name_'+raceId).attr('value'));

                            $('#history_homework_tr' + i).append($('<td>').attr('colspan',3).text("해당 학생 없음"));

                        }else{

                            $('#history_homework').append($('<tr id="history_homework_tr' + i + '">'));

                            $('#historyListNumber').text($('#history_id_'+raceId).attr('value'));
                            $('#historyListRaceName').text($('#history_name_'+raceId).attr('value'));

                            $('#history_homework_tr' + i).append($('<td>').text(stdHomework[i]['userName']));

                            switch (stdHomework[i]['retestState']){
                                case "not" :
                                    $('#history_homework_tr' + i).append($('<td>').text("PASS"));

                                    break;
                                case "order" :
                                    $('#history_homework_tr' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("미응시")));

                                    break;
                                case "clear" :
                                    $('#history_homework_tr' + i).append($('<td>').attr('class','modal_openStudentRetestGradeCard')
                                        .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                            .attr('id',raceId).attr('name',stdHomework[i]['userId']).text("응시완료")));

                                    break;
                            }

                            //임의로 값 설정
                            stdHomework[i]['wrongState'] = "clear";

                            switch (stdHomework[i]['wrongState']){
                                case "not" :
                                    $('#history_homework_tr' + i).append($('<td>').text("PASS"));

                                    break;
                                case "order" :
                                    $('#history_homework_tr' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("미제출")));

                                    break;
                                case "clear" :
                                    $('#history_homework_tr' + i).append($('<td>').attr('class','modal_openStudentWrongGradeCard')
                                        .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                            .attr('id',raceId).attr('name',stdHomework[i]['userId']).text("제출완료")));

                                    break;
                            }
                        }
                    }

                },
                error: function (data) {
                    alert("과제 조회 에러2");
                }
            });
        }

        //학생 클릭시 해당 학생 개인성적 조회 및 그래프 로드
        function getStudentGrade(userId) {
            // 요구하는 값
//        $postData = array(
//            'userId'    => 1300000
//        );

            var reqData = {'userId' : userId };

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getStudents')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {

                    /*
                     data = { group : {id : 1 , name : "3WDJ"} ,
                               races : { 0 :ty {  year:2018
                                                month:5
                                                day:11

                                                raceId:2
                                                listName:"테스트용 리스트1"
                                                userCount:5
                                                userName:"김똘똘"

                                                allCount:6
                                                allRightCount:4

                                                grammarCount:2
                                                grammarRightAnswerCount:1.4

                                                vocabularyCount:2
                                                vocabularyRightAnswerCount:1.2

                                                wordCount:2
                                                wordRightAnswerCount:1.4

                                                retestState:not
                                                wrongState:not
                                              }
                                        }
                    */

                    var ChartData = makingStudentChartData(data);
                    makingStudentChart(ChartData);
                    var raceData;

                    $('#studentGradeList').empty();

                    for( var i = 0 ; i < data.races.length ; i++ ) {

                        raceData = JSON.parse(data.races[i]);

                        $('#studentGradeList').append($('<tr>').attr('id','stdGrade_'+i));

                        $('#stdGrade_' + i).append($('<td>').text(i+1));
                        $('#stdGrade_' + i).append($('<td>').text(raceData['year']+"년 "+raceData['month']+"월 "+raceData['day']+"일"));
                        $('#stdGrade_' + i).append($('<td>').text(raceData['listName']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['total_data'][1][i]['y']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['voca_data'][1][i]['y']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['grammer_data'][1][i]['y']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['word_data'][1][i]['y']));

                        switch (raceData['retestState']){
                            case "not" :
                                $('#stdGrade_' + i).append($('<td>').text("PASS"));

                                break;
                            case "order" :
                                $('#stdGrade_' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("미응시")));

                                break;
                            case "clear" :
                                $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentRetestGradeCard')
                                    .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                        .attr('id',raceData['raceId']).attr('name',userId).text("응시완료")));

                                break;
                        }

                        //임의로 값 설정
                        raceData['wrongState'] = "clear";

                        switch (raceData['wrongState']){
                            case "not" :
                                $('#stdGrade_' + i).append($('<td>').text("PASS"));

                                break;
                            case "order" :
                                $('#stdGrade_' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("미제출")));

                                break;
                            case "clear" :
                                $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentWrongGradeCard')
                                    .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                        .attr('id',raceData['raceId']).attr('name',userId).text("제출완료")));

                                break;
                        }

                        $('#stdGrade_'+i).append($('<td>').attr('class','modal_openStudentGradeCard')
                            .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                .attr('id',raceData['raceId']).attr('name',userId).text("성적표")));
                    }

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }
            });
        }

        function makingChartData(raceData) {

            var raceData = raceData['races'];

            /*raceData = {
                            0: {
                                year: 2018,
                                month: 5,
                                day: 9,

                                raceId: 2,
                                listName: "테스트용 리스트1",
                                userCount: 5,

                                quizCount: 6,
                                rightAnswerCount: 4.2,

                                grammarCount: 2,
                                grammarRightAnswerCount: 1.6,

                                vocabularyCount: 2,
                                vocabularyRightAnswerCount: 1.6,

                                wordCount: 2,
                                wordRightAnswerCount: 1,
                            },

                            1: {
                                year: 2018,
                                month: 5,
                                day: 9,

                                raceId: 2,
                                listName: "테스트용 리스트1",
                                userCount: 5,

                                quizCount: 6,
                                rightAnswerCount: 4.2,

                                grammarCount: 2,
                                grammarRightAnswerCount: 1.6,

                                vocabularyCount: 2,
                                vocabularyRightAnswerCount: 1.6,

                                wordCount: 2,
                                wordRightAnswerCount: 1,
                            }
                        };*/

            var total_data_Points = [];
            var grammer_data_Points = [];
            var vocabulary_Points = [];
            var word_data_Points = [];
            var AllChartData = [];

            for(var i = 0 ; i < raceData.length ; i++){

                //총점 구하기
                var total_grade = ((100 / raceData[i]['quizCount']).toFixed(1) *  raceData[i]['rightAnswerCount']).toFixed(0);

                //문법 총점 구하기
                var grammer_grade = ((33 / raceData[i]['grammarCount']).toFixed(1) *  raceData[i]['grammarRightAnswerCount']).toFixed(0);

                //어휘 총점 구하기
                var vocabulary_grade = ((33 / raceData[i]['vocabularyCount']).toFixed(1) *  raceData[i]['vocabularyRightAnswerCount']).toFixed(0);

                //단어 총점 구하기
                var word_grade = ((33 / raceData[i]['wordCount']).toFixed(1) *  raceData[i]['wordRightAnswerCount']).toFixed(0);

                //차트 데이터 배열 만들기
                total_data_Points.push({ x : new Date(raceData[i]['date'].replace('-','/','g')),
                    y : parseInt(total_grade) ,
                    label : raceData[i]['listName']});

                grammer_data_Points.push({ x : new Date(raceData[i]['date'].replace('-','/','g')),
                    y : parseInt(grammer_grade) ,
                    label : raceData[i]['listName']});

                vocabulary_Points.push({ x : new Date(raceData[i]['date'].replace('-','/','g')),
                    y : parseInt(vocabulary_grade) ,
                    label : raceData[i]['listName']});

                word_data_Points.push({ x : new Date(raceData[i]['date'].replace('-','/','g')),
                    y : parseInt(word_grade) ,
                    label : raceData[i]['listName']});
            }

            //차트 데이터 합치기
            AllChartData = { "total_data" : ["전체 평균 점수" , total_data_Points] ,
                "voca_data" : ["어학 점수", vocabulary_Points] ,
                "grammer_data" : ["독해 점수" , grammer_data_Points] ,
                "word_data" : ["단어 점수" , word_data_Points]
            };

            return AllChartData;
        }

        function makingStudentChartData(data){

            /*
                     data = { group : {id : 1 , name : "3WDJ"} ,
                               races : { 0 : {  year:2018
                                                month:5
                                                day:11
                                                date:DateString

                                                raceId:2
                                                listName:"테스트용 리스트1"
                                                userCount:5
                                                userName:"김똘똘"
                                                userId:1300000

                                                allCount:6
                                                allRightCount:4

                                                grammarCount:2
                                                grammarRightCount:0

                                                vocabularyCount:2
                                                vocabularyRightCount:1

                                                wordCount:2
                                                wordRightCount:1

                                                retestState:not
                                                wrongState:not
                                              }
                                        }
                    */
            var total_data_Points = [];
            var grammer_data_Points = [];
            var vocabulary_Points = [];
            var word_data_Points = [];
            var AllChartData = [];
            var categoryCount = 0;
            var gradeByOne = 0;
            var makingStudentData;

            var parseData = JSON.parse(JSON.stringify(data['races']));

            //변수 접근은 .
            //배열 접근은 ['']
            for(var i = 0 ; i < parseData.length ; i++){
                makingStudentData = JSON.parse(parseData[i]);

                gradeByOne = Math.floor(100 / makingStudentData.allCount);

                //문법 총점 구하기
                var grammar_grade = gradeByOne * makingStudentData.grammarRightCount;

                //어휘 총점 구하기
                var vocabulary_grade = gradeByOne * makingStudentData.vocabularyRightCount;

                //단어 총점 구하기
                var word_grade = gradeByOne * makingStudentData.wordRightCount;

                //총점 구하기
                var total_grade = grammar_grade + vocabulary_grade + word_grade;

                //차트 데이터 배열 만들기
                total_data_Points.push({ x      : new Date(makingStudentData['date']),
                    y      : parseInt(total_grade) ,
                    label  : makingStudentData['listName']});

                grammer_data_Points.push({ x    : new Date(makingStudentData['date']),
                    y    : parseInt(grammar_grade) ,
                    label: makingStudentData['listName']});

                vocabulary_Points.push({ x      : new Date(makingStudentData['date']),
                    y      : parseInt(vocabulary_grade) ,
                    label  : makingStudentData['listName']});

                word_data_Points.push({ x       : new Date(makingStudentData['date']),
                    y       : parseInt(word_grade) ,
                    label   : makingStudentData['listName']});
            }

            //차트 데이터 합치기
            AllChartData = { "total_data"   : ["전체 평균 점수" , total_data_Points] ,
                "voca_data"    : ["어학 점수", vocabulary_Points] ,
                "grammer_data" : ["독해 점수" , grammer_data_Points] ,
                "word_data"    : ["단어 점수" , word_data_Points]
            };

            return AllChartData;
        }


        //차트 만들 데이터
        function makingChart(data){

            //data = { "total_data" : [ "전체 평균 점수" , { x: new Date(0,0,0) , y: 80 , label: "문제 : 스쿠스쿠"}]}
            //data['total_data'] , data['voca_data'] , data['grammer_data'] , data['word_data']
            //data['total_data'][0]     ==  "전체 평균 점수"
            //data['total_data'][1]     == {x , y , label}

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title:{},
                axisX:{
                    labelFontSize: 15,
                    valueFormatString: "MM월 DD일 (HH:ss)",
                    crosshair: {
                        enabled: true,
                        snapToDataPoint: true
                    }
                },
                axisY: {
                    maximum: 100,
                    crosshair: {
                        enabled: true,
                    }
                },
                toolTip:{
                    shared: true,
                },
                legend:{
                    cursor:"pointer",
                    verticalAlign: "bottom",
                    horizontalAlign: "center",
                    itemclick: toogleDataSeries
                },
                data: [{
                    type: "line",
                    showInLegend: true,
                    xValueFormatString: "DD, DD MMM, YYYY, HH, mm ,ss",
                    // name: "전체 평균 점수",
                    name: data['total_data'][0],
                    markerType: "square",
                    toolTipContent: "{label}" + "<br>" + "<span class='chart_total'>{name}:</span> {y}",
                    color: "#F08080",
                    dataPoints: data['total_data'][1]
                },
                    {
                        type: "line",
                        showInLegend: true,

                        // name: "어학 점수",
                        name: data['voca_data'][0],

                        lineDashType: "dash",
                        toolTipContent: "<span class='chart_vocabulary'>{name}:</span> {y}",
                        dataPoints: data['voca_data'][1]
                    },
                    {
                        type: "line",
                        showInLegend: true,

                        // name: "독해 점수",
                        name: data['grammer_data'][0],

                        lineDashType: "dash",
                        toolTipContent: "<span class='chart_grammer'>{name}:</span> {y}",
                        dataPoints: data['grammer_data'][1]
                    },
                    {
                        type: "line",
                        showInLegend: true,

                        // name: "단어 점수",
                        name: data['word_data'][0],

                        lineDashType: "dash",
                        toolTipContent: "<span class='chart_word'>{name}:</span> {y}",
                        dataPoints: data['word_data'][1]
                    }
                ]
            });
            chart.render();

            function toogleDataSeries(e){
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else{
                    e.dataSeries.visible = true;
                }
                chart.render();
            }
        }

        //학생 개인 차트 만들 데이터
        function makingStudentChart(data){

            //data = { "total_data" : [ "전체 평균 점수" , { x: new Date(0,0,0) , y: 80 , label: "문제 : 스쿠스쿠"}]}
            //data['total_data'] , data['voca_data'] , data['grammer_data'] , data['word_data']
            //data['total_data'][0]     ==  "전체 평균 점수"
            //data['total_data'][1]     == {x , y , label}

            var chart = new CanvasJS.Chart("chartContainer_privacy_student", {
                animationEnabled: true,
                theme: "light2",
                title:{},
                axisX:{
                    labelFontSize: 15,
                    valueFormatString: "MMM DD (HH:ss)",
                    crosshair: {
                        enabled: true,
                        snapToDataPoint: true
                    }
                },
                axisY: {
                    maximum: 100,
                    crosshair: {
                        enabled: true,
                    }
                },
                toolTip:{
                    shared: true,
                },
                legend:{
                    cursor:"pointer",
                    verticalAlign: "bottom",
                    horizontalAlign: "center",
                    itemclick: toogleDataSeries
                },
                data: [{
                    type: "line",
                    showInLegend: true,
                    xValueFormatString: "DD, DD MMM, YYYY",

                    // name: "전체 평균 점수",
                    name: data['total_data'][0],

                    markerType: "square",
                    toolTipContent: "{label}" + "<br>" + "<span class='chart_total'>{name}:</span> {y}",
                    color: "#F08080",
                    dataPoints: data['total_data'][1]
                },
                    {
                        type: "line",
                        showInLegend: true,

                        // name: "어학 점수",
                        name: data['voca_data'][0],

                        lineDashType: "dash",
                        toolTipContent: "<span class='chart_vocabulary'>{name}:</span> {y}",
                        dataPoints: data['voca_data'][1]
                    },
                    {
                        type: "line",
                        showInLegend: true,

                        // name: "독해 점수",
                        name: data['grammer_data'][0],

                        lineDashType: "dash",
                        toolTipContent: "<span class='chart_grammer'>{name}:</span> {y}",
                        dataPoints: data['grammer_data'][1]
                    },
                    {
                        type: "line",
                        showInLegend: true,

                        // name: "단어 점수",
                        name: data['word_data'][0],

                        lineDashType: "dash",
                        toolTipContent: "<span class='chart_word'>{name}:</span> {y}",
                        dataPoints: data['word_data'][1]
                    }
                ]
            });
            chart.render();

            function toogleDataSeries(e){
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else{
                    e.dataSeries.visible = true;
                }
                chart.render();
            }
        }

        //재시험 점수 가져오기
        function getRetestData(userId,raceId){

//        $postData = array(
//            'userId'        => 1300000
//            'raceId'        => 1
//            'retestState'   => 1
//        );
            var reqData = {"userId" : userId, "raceId" : raceId, "retestState" : 1};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/getStudents')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: reqData,
                success: function (data) {

                    makingModalPage(raceId,data,1);
                    $('.modal-content.studentGrade .modal-title').text("재시험 점수");

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }

            });
        }

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
                    $('.modal-content.detail .modal-title').text('오답 문제');

                    var totalGrade = 0;
                    var totalVoca = 0;
                    var totalGrammer = 0;
                    var totalWord = 0;
                    var totalRight = 0;

                    //data -> 레이스에 관한 모든 데이터(리턴값 그대로)
                    StudentData = JSON.parse(allData['races'][0]);
                    StudentScore = makingStudentChartData(allData);

                    $('.modal-content.studentGrade .modal-title').text("학생 점수");
                    $('#modal_date').text(StudentData['year'] + "년 " + StudentData['month'] + "월 " + StudentData['day'] + "일");
                    $('#modal_raceName_teacher').text(StudentData['listName'] + "  /  " + StudentData['teacherName']);
                    $('.modal #modal_total_students').append($('<a href="#" onclick="getRaceWrongAnswer('+raceId+')">').text('전체 학생'));


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

                    //modal-footer 총 점수들 표시
                    $('#modal_total_grades').text("전체 평균: "+parseInt(totalGrade / allData['races'].length)+
                        " / 어휘: "+parseInt(totalVoca / allData['races'].length)+
                        " / 문법: "+parseInt(totalGrammer / allData['races'].length)+
                        " / 독해: "+parseInt(totalWord / allData['races'].length)+
                        " / 갯수: "+parseInt(totalRight / allData['races'].length));


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
                    $('.modal #modal_total_students').text("전체 학생");
                    $('.modal-content.detail .modal-title').text('오답 문제');

                    //data -> 학생개인에 관한 모든 데이터(리턴값 그대로)
                    StudentScore = makingStudentChartData(allData);
                    StudentData = JSON.parse(allData['races'][0]);

                    $('#modal_date').text(StudentData['year']+"년 "+StudentData['month']+"월 "+StudentData['day']+"일");
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

                    $('.modal-content.detail .modal-title').text('오답 노트');

                    wrongsData = allData['wrongs'];
                    var leftOrRight = "";

                    $('.wrong_left').empty();
                    $('.wrong_right').empty();

                    if (wrongsData.length == 0) {
                        $('.modal_wrong').text("오답 내용이 없습니다.");
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

                            $('.' + leftOrRight).append($('<table>').attr('class', 'table_wrongList')
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
                                                    .append($('<li>').text(wrongsData[i]['rightAnswer'] + " (" + wrongsData[i]['rightAnswerCount'] + "명)"))
                                                    .append($('<li>').text(wrongsData[i]['example1'] + " (" + wrongsData[i]['example1Count'] + "명)"))
                                                    .append($('<li>').text(wrongsData[i]['example2'] + " (" + wrongsData[i]['example2Count'] + "명)"))
                                                    .append($('<li>').text(wrongsData[i]['example3'] + " (" + wrongsData[i]['example3Count'] + "명)")
                                                    )
                                                )
                                            )
                                            .append($('<div>').attr('class','wrongWriting').text(wrongsData[i]['wrong']))
                                        )
                                    )
                                )
                            );

                            for (var j = 1; j < 4; j++) {
                                if (wrongsData[i]['example' + j + 'Count'] == 1) {
                                    $('.example_' + i + '_' + j).css('color', 'blue');
                                }
                            }
                        }

                    }
            }
        }

        function loadFeedback(){

            var reqData = {"groupId" : 1};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/selectQnAs')}}",
                //processData: false,
                //contentType: false,
                data:reqData,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) {

                    /*
                    Data  = { QnAs : {
                                    'QnAId' : 1,
                                    'userName' : 김똘똘,
                                    'teacherName' : 이교수,
                                    'title' : 스쿠스쿠레이스 3번문제 질문입니다.
                                    'question_at' : 제 생각에는 3번이 정답인데 왜 틀린건가요
                                    'answer_at' : 그건 이러이러저러저러 하단다.
                                     },
                              check : false or true
                              }
                    */

                    var instanceData = { QnAs : {
                            0: { QnAId: 1, userName: "김똘똘", techerName: "이교수", title: "스쿠스쿠레이스 3번문제 질문입니다.",
                                question_at: "제 생각에는 3번이 정답인데 왜 틀린건가요", answer_at: "그건 이러이러저러저러 하단다.",date : "2018-05-28"
                            }
                        }
                    };

                    $('#modal_feedbackList').empty();

                    for(var i = 0 ; i < 1;i++){

                        $('#modal_feedbackList')
                            .append($('<tr>').attr('id','qna_'+instanceData['QnAs'][i]['QnAId'])
                                .append($('<td>').text(instanceData['QnAs'][i]['date']))
                                .append($('<td>')
                                    .append($('<a href="#" data-toggle="modal" data-target="#Modal2">')
                                        .attr('class','feedbackList').attr('id',instanceData['QnAs'][i]['QnAId']).text(instanceData['QnAs'][i]['title'])
                                    )
                                )
                            );
                        if(instanceData['QnAs'][i]['answer_at'] == ""){
                            $('#qna_'+instanceData['QnAs'][i]['QnAId']).append($('<td>')
                                .append($('<button>').attr('id','btnQnA_'+instanceData['QnAs'][i]['QnAId']).attr('class','btn btn-warning').text("미확인")));

                        }else{
                            $('#qna_'+instanceData['QnAs'][i]['QnAId']).append($('<td>')
                                .append($('<button">').attr('class','btn btn-primary').text("확인")));
                        }
                    }

                },
                error: function (data) {
                    alert("loadFeedback / 피드백 받아오기 에러");
                }

            });
        }
        loadFeedback();


        function loadFeedbackModal(qnaId){

            var reqData = {"QnAId" : qnaId};

            var instanceData = { QnAs : {
                    0: { QnAId: 1, userName: "김똘똘", techerName: "이교수", title: "스쿠스쿠레이스 3번문제 질문입니다.",
                        question: "제 생각에는 3번이 정답인데 왜 틀린건가요", answer:"그건 이러이러저러저러 하단다",
                        question_at: "2018-05-28",answer_at : "2018-05-29"
                    }
                }
            };

            $('.request_date').empty();
            $('.response_date').empty();
            $('.request_contents').empty();
            $('#teachersFeedback').empty();
            $('.modal-footer').empty();

            for(var i = 0 ; i < 1;i++){

                $('.request_date').text("질문날짜 : "+instanceData['QnAs'][i]['question_at'] +" / 응답날짜 : "+instanceData['QnAs'][i]['answer_at'])
                    .attr('id',qnaId);
                $('.request_contents').text(instanceData['QnAs'][i]['question']);
                $('#teachersFeedback').val(instanceData['QnAs'][i]['answer']);
                $('.modal-footer').append($('<button data-dismiss="modal" onclick="insertQuestion()">').attr('class','btn btn-primary').text('확인'));
                $('.modal-footer').append($('<button data-dismiss="modal" >').attr('class','btn btn-secondary').text('취소'));

            }

        }

        function insertQuestion(){

            var formData = new FormData();
            var imgfiles = document.getElementsByName("feedbackImg")[0].files[0];

            formData.append('questionImg', imgfiles);

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/insertQuestion')}}",
                processData: false,
                contentType: false,
                data:formData,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) {

                },
                error: function (data) {
                    alert("loadFeedback / 피드백 등록하기 에러");
                }

            });
        }

        //레코드 네비바 클릭 할 때 마다 보여줄 페이지를 보여주기 및 숨기기
        function recordControl(id){
            switch (id){
                case "chart" :
                    $('.record_chart').show();
                    $('.record_history').hide();
                    $('.record_student').hide();
                    $('.record_feedback').hide();
                    break;
                case "history" :
                    $('.record_history').show();
                    $('.record_chart').hide();
                    $('.record_student').hide();
                    $('.record_feedback').hide();
                    break;
                case "students" :
                    $('.record_student').show();
                    $('.record_chart').hide();
                    $('.record_history').hide();
                    $('.record_feedback').hide();
                    break;
                case "homework" :
                    $('.record_homework').show();
                    $('.record_chart').hide();
                    $('.record_history').hide();
                    $('.record_feedback').hide();
                    break;
                case "feedback" :
                    $('.record_feedback').show();
                    $('.record_chart').hide();
                    $('.record_student').hide();
                    $('.record_history').hide();
                    break;
            }
        }

    </script>

</head>
<body onload="OnLoadRecordbox();">

{{--메인 네비바 불러오기--}}
@include('Navigation.main_nav')

<div class="recordbox_main">

    {{--사이드바 불러오기--}}
    @include('Recordbox.record_sidebar')

    <div class="changePages">

        <div class="recordbox_navbar">
            {{--레코드 네비바 불러오기--}}
            @include('Recordbox.record_recordnav')
        </div>

        <div class="record_chart">
            {{--레코드 차트페이지 불러오기--}}
            @include('Recordbox.record_chart')
        </div>

        <div class="record_history">
            {{--레코드 최근기록페이지 불러오기--}}
            @include('Recordbox.record_history')
        </div>

        <div class="record_student">
            {{--레코드 학생페이지 불러오기--}}
            @include('Recordbox.record_students')
        </div>

        <div class="record_feedback">
            {{--레코드 피드백페이지 불러오기--}}
            @include('Recordbox.record_feedback')
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
                    <h3 class="modal-title" id="ModalLabel" >학생 점수</h3>

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
                                평균점수
                            </th>
                            <th>
                                어휘
                            </th>
                            <th>
                                문법
                            </th>
                            <th>
                                독해
                            </th>
                            <th>
                                갯수
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
                    <h4 class="modal-title" id="ModalLabel">피드백</h4>
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
                            파일 첨부
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

                    <div class="modal-footer">
                    </div>

                    <script>
                        function changeCheck(qnaId){
                            alert('정상 등록하였습니다.');
                            $('#btnQnA_'+qnaId).attr('class','btn btn-primary').text('확인');
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>