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
            width: 88%;
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
        .table_wrongList{
            margin-bottom: 30px;
            width: 100%;
            height: 100px;
            padding: 0;
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
        .table_wrongList tbody .wrongWritingArea textarea{
            margin-top: 10px;
            width: 100%;
            min-height: 70px;
        }

        #modal_allWrongAnswerList tr , #details_record tr , #wrongQuestions tr{
            border-bottom: 1px solid #e5e6e8;
        }
        #modal_allWrongAnswerList tr td , #details_record tr td , #wrongQuestions tr td {
            border-left: 1px solid #e5e6e8;
        }


    </style>

    <script type="text/javascript">

        var user_id = 1300000;
        var group_id = 1;

        function OnLoadRecordboxStudent () {
            getGroups_and_loadChart(user_id,group_id);
        };

        $(document).on('click','.modal_openStudentGradeCard button',function () {
            var raceId = $(this).attr('id');
            var userId = $(this).attr('name');

            loadStudentGradeCard(user_id,raceId);
        });

        //학생 상세정보에서 재시험 클릭시 성적표 로드
        $(document).on('click','.modal_openStudentRetestGradeCard button',function () {
            var raceId = $(this).attr('id');
            var userId = $(this).attr('name');

            loadStudentGradeCard(userId,raceId);
        });

        //학생 상세정보에서 오답노트 클릭시 성적표 로드
        $(document).on('click','.modal_openStudentWrongGradeCard button',function () {
            var raceId = $(this).attr('id');
            var userId = $(this).attr('name');

            getStudentWrongWriting(userId,raceId);
        });


        function changeDateToChart(){

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

            getChartData_and_loadChart(user_id,startDate,defaultEndDate);
        }

        function orderChart(){
            var startDate = document.querySelector('input[id="startDate"]').value;
            var endDate = document.querySelector('input[id="endDate"]').value;

            getChartData_and_loadChart(user_id,startDate,endDate);
        }

        //날짜를 가져와서 조회 및 차트 그리기
        function getChartData_and_loadChart(userId,startDate,endDate){

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

        function getGroups_and_loadChart(userId,groupId) {
            $.ajax({
                type: 'POST',

                url: "{{url('/groupController/groupsGet')}}",
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

                },
                error: function (data) {
                    alert("그룹겟 에러");
                }
            });



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
                               races : { 0 : {  year:2018
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

                    console.log(data);
                    var ChartData = makingStudentChartData(data);
                    var raceData = data['races'];
                    makingStudentChart(ChartData);

                    $('#studentGradeList').empty();
                    for( var i = 0 ; i < raceData.length ; i++ ){
                        $('#studentGradeList').append($('<tr>').attr('id','stdGrade_'+i));
                    }

                    for( var i = 0 ; i < raceData.length ; i++ ) {
                        $('#stdGrade_' + i).append($('<td>').text(i+1));
                        $('#stdGrade_' + i).append($('<td>').text(raceData[i]['year']+"년 "+raceData[i]['month']+"월 "+raceData[i]['day']+"일"));
                        $('#stdGrade_' + i).append($('<td>').text(raceData[i]['listName']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['total_data'][1][i]['y']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['voca_data'][1][i]['y']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['grammer_data'][1][i]['y']));
                        $('#stdGrade_' + i).append($('<td>').text(ChartData['word_data'][1][i]['y']));

                        /*if (raceData[i]['retestState'] == 'not') {
                            $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentRetestGradeCard')
                                .append($('<button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal_studentRetestGradeCard">')
                                    .attr('id',raceData[i]['raceId']).attr('name',userId)
                                    .text("시험치기")));
                        } else {
                            $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentRetestGradeCard')
                                .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_studentRetestGradeCard">')
                                    .attr('id',raceData[i]['raceId']).attr('name',userId)
                                    .text("응시")));
                        }*/

                        //일부러 값 바꾸기
                        raceData[i]['wrongState'] = 'not';

                        if (raceData[i]['wrongState'] == 'not') {
                            $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentWrongGradeCard')
                                .append($('<button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal_studentWritingWrons">')
                                    .attr('id',raceData[i]['raceId']).attr('name',userId)
                                    .text("미응시")));
                        } else {
                            $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentWrongGradeCard')
                                .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_studentWrongGradeCard">')
                                    .attr('id',raceData[i]['raceId']).attr('name',userId)
                                    .text("응시")));
                        }
                        $('#stdGrade_'+i).append($('<td>').attr('class','modal_openStudentGradeCard')
                            .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_studentGradeCard">')
                                .attr('id',raceData[i]['raceId']).attr('name',userId)
                                .text("성적표")));
                    }

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }
            });

        }

        //학생 개인 차트 만들 데이터
        function makingStudentChartData(data){
            var raceData = data['races'];

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

            for(var i = 0 ; i < raceData.length ; i++){

                //총점 구하기
                var total_grade = ((100 / raceData[i]['allCount']).toFixed(1) *  raceData[i]['allRightCount']).toFixed(0);

                //문법 총점 구하기
                var grammer_grade = ((33 / raceData[i]['grammarCount']).toFixed(1) *  raceData[i]['grammarRightCount']).toFixed(0);

                //어휘 총점 구하기
                var vocabulary_grade = ((33 / raceData[i]['vocabularyCount']).toFixed(1) *  raceData[i]['vocabularyRightCount']).toFixed(0);

                //단어 총점 구하기
                var word_grade = ((33 / raceData[i]['wordCount']).toFixed(1) *  raceData[i]['wordRightCount']).toFixed(0);

                //차트 데이터 배열 만들기
                total_data_Points.push({ x : new Date(raceData[i]['date']),
                    y : parseInt(total_grade) ,
                    label : raceData[i]['listName']});

                grammer_data_Points.push({ x : new Date(raceData[i]['date']),
                    y : parseInt(grammer_grade) ,
                    label : raceData[i]['listName']});

                vocabulary_Points.push({ x : new Date(raceData[i]['date']),
                    y : parseInt(vocabulary_grade) ,
                    label : raceData[i]['listName']});

                word_data_Points.push({ x : new Date(raceData[i]['date']),
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

        //학생 개인 차트 만들기
        function makingStudentChart(data){

            //data = { "total_data" : [ "전체 평균 점수" , { x: new Date(0,0,0) , y: 80 , label: "문제 : 스쿠스쿠"}]}
            //data['total_data'] , data['voca_data'] , data['grammer_data'] , data['word_data']
            //data['total_data'][0]     ==  "전체 평균 점수"
            //data['total_data'][1]     == {x , y , label}

            var chart = new CanvasJS.Chart("chartContainer_privacy_student", {
                animationEnabled: true,
                theme: "light2",
                title:{},
                width:1000,
                height:450,
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

        function loadStudentGradeCard(userId,raceId){
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
                                                grammarRightCount:1.4,

                                                vocabularyCount:2,
                                                vocabularyRightCount:1.2,

                                                wordCount:2,
                                                wordRightCount:1.4,

                                                retestState:"not",
                                                wrongState:"not"
                                              }
                                        }
                    */

                    var StudentData;

                    for(var i = 0 ; i < data['races'].length ; i++){
                        if (userId == data['races'][i]['userId']){
                            StudentData = data['races'][i];
                        }
                    }

                    $('#modal_student_raceName_teacher').empty();
                    $('.modal_student_date').empty();
                    $('.modal #studentGradeCard').empty();

                    $('#modal_student_raceName_teacher').text(StudentData['listName'] +"  /  " +StudentData['teacherName'] );
                    $('.modal_student_date').text(StudentData['year']+"년 "+StudentData['month']+"월 "+StudentData['day']+"일");


                    for(var i = 0 ; i < 1 ; i++){
                        $('.modal #studentGradeCard').append($('<tr>').attr('id', 'modal_stdGrade_'+i));

                        $('#modal_stdGrade_' + i).append($('<td>').text(StudentData['userName']));
                        $('#modal_stdGrade_' + i).append($('<td>').text(parseInt((100 / StudentData['allCount']) * StudentData['allRightCount'])));
                        $('#modal_stdGrade_' + i).append($('<td>').text(parseInt((33 / StudentData['vocabularyCount']) * StudentData['vocabularyRightCount'])));
                        $('#modal_stdGrade_' + i).append($('<td>').text(parseInt((33 / StudentData['grammarCount']) * StudentData['grammarRightCount'])));
                        $('#modal_stdGrade_' + i).append($('<td>').text(parseInt((33 / StudentData['wordCount']) * StudentData['wordRightCount'])));
                        $('#modal_stdGrade_' + i).append($('<td>').text(StudentData['allRightCount']+"/"+StudentData['allCount']));

                    }

                    getStudentWrongAnswer(userId,raceId);

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }
            });
        }

        function getStudentWrongAnswer(userId,raceId) {

            var reqData ={"userId" : userId , "raceId" : raceId};

            $('#details_record').empty();

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

                    //나중에 페이지 네이션용
                    if (wrongsData.length > 10){
                        wrongsData.length = 9;
                    }

                    for(var i = 0 ; i < wrongsData.length ; i++ ){

                        if (i < 5){
                            leftOrRight = "wrong_left";
                        }else{
                            leftOrRight = "wrong_right";
                        }

                        $('.'+leftOrRight).append($('<table>').attr('class', 'table_wrongList')
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
                                                .append($('<li>').text(wrongsData[i]['rightAnswer']))
                                                .append($('<li>').text(wrongsData[i]['example1']).attr('class','example_'+i+'_1'))
                                                .append($('<li>').text(wrongsData[i]['example2']).attr('class','example_'+i+'_2'))
                                                .append($('<li>').text(wrongsData[i]['example3']).attr('class','example_'+i+'_3'))
                                            )
                                        )
                                    )
                                )
                            )
                        );

                        for(var j = 1 ; j < 4 ; j++){
                            if(wrongsData[i]['example'+j+'Count'] == 1){ $('.example_'+i+'_'+j).css('color','blue'); }
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

            var reqData ={"userId" : userId , "raceId" : raceId};
            var where = "";

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
                    $('.modal-content.studentGrade').hide();

                    //나중에 페이지 네이션용
                    if (wrongsData.length > 10){
                        wrongsData.length = 9;
                    }

                    for(var i = 0 ; i < wrongsData.length ; i++ ){

                        if (i < 5){
                            leftOrRight = "wrong_left";
                        }else{
                            leftOrRight = "wrong_right";
                        }

                        $('.'+leftOrRight).append($('<table>').attr('class', 'table_wrongList')
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
                                                .append($('<li>').text(wrongsData[i]['rightAnswer']))
                                                .append($('<li>').text(wrongsData[i]['example1']).attr('class','example_'+i+'_1'))
                                                .append($('<li>').text(wrongsData[i]['example2']).attr('class','example_'+i+'_2'))
                                                .append($('<li>').text(wrongsData[i]['example3']).attr('class','example_'+i+'_3'))
                                            )
                                        ).append($('<div>').attr('class','wrongWritingArea')
                                            .append('<textarea>').attr('id',wrongsData[i]['number']+i).attr('name',raceId))
                                    )
                                )
                            )
                        );

                        for(var j = 1 ; j < 4 ; j++){
                            if(wrongsData[i]['example'+j+'Count'] == 1){ $('.example_'+i+'_'+j).css('color','blue'); }
                        }
                    }

                },
                error: function (data) {
                    alert("해당 학생별 오답 문제 가져오기");
                }
            });

            var reqData ={"userId" : userId , "raceId" : raceId};

            var data = {
                group: {id: 1, name: "#WDJ", studentCount: 5},
                wrongs: {
                    0: { number: 1,
                        id: 1,
                        question: "苦労してためたお金なのだから、一円（　　）無駄には使いたくない。",

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
                    2: { number: 3,
                        id: 1,
                        question: "姉は市役所に勤める（　　）、ボランティアで日本語を教えています。",

                        rightAnswerNumber:3,
                        choiceNumber:2,

                        example1:"かたがた",
                        example1Number:1,
                        example2:"こととて",
                        example2Number:2,
                        example3:"かたわら",
                        example3Number:3,
                        example4:"うちに",
                        example4Number:4,

                        userCount: 5,
                        rightAnswerCount:1,
                        example1Count:1,
                        example2Count:2,
                        example3Count:1,

                    }
                }
            };

            var wrongsData = data['wrongs'];

            //wrongsData.length == 3
            for(var i = 0 ; i < 3 ; i++ ){

                for(var j = 0 ; j < 4 ; j++) {
                    $('#wrongQuestions').append($('<tr>').attr('id', 'wrong_question_'+wrongsData[i]['number']+"_"+ j));

                    switch (j) {
                        case 0 :
                            $('#wrong_question_'+wrongsData[i]['number']+"_"+ j).append($('<td>').text(wrongsData[i]['number']).attr('rowSpan',3));
                            $('#wrong_question_'+wrongsData[i]['number']+"_"+ j).append($('<td>').text(wrongsData[i]['question']).attr('colSpan',2));

                            break;
                        case 1 :
                            $('#wrong_question_'+wrongsData[i]['number']+"_"+ j).append($('<td>').attr('id','wrongQue_'+wrongsData[i]['number']+"_"+1));
                            $('#wrong_question_'+wrongsData[i]['number']+"_"+ j).append($('<td>').attr('id','wrongQue_'+wrongsData[i]['number']+"_"+2));

                            break;
                        case 2 :
                            $('#wrong_question_'+wrongsData[i]['number']+"_"+ j).append($('<td>').attr('id','wrongQue_'+wrongsData[i]['number']+"_"+3));
                            $('#wrong_question_'+wrongsData[i]['number']+"_"+ j).append($('<td>').attr('id','wrongQue_'+wrongsData[i]['number']+"_"+4));

                            break;
                        case 3 :
                            $('#wrong_question_'+wrongsData[i]['number']+"_"+ j).append($('<td>').attr('colSpan',3)
                                .append($('<textarea>').css({width:"100%",height:"70px"}).attr('id',wrongsData[i]['number']).attr('name',raceId).attr('class','wrong_write')));

                            break;
                    }
                }

                for(var j = 1 ; j <= 4 ; j++){

                    $('#wrongQue_'+wrongsData[i]['number']+"_"+ j).text(wrongsData[i]['example'+j]);

                    switch (j){
                        case wrongsData[i]['rightAnswerNumber']:
                            $('#wrongQue_'+wrongsData[i]['number']+"_"+ j).css('background-color','#ffa500');

                            break;
                        case wrongsData[i]['choiceNumber']:
                            $('#wrongQue_'+wrongsData[i]['number']+"_"+ j).css('background-color','#e5e6e8');

                            break;
                    }
                }
            }

        }

        function recordControl(id){
            switch (id){
                case "nav_group_name" :
                    $('#record_history').attr('class','hidden');
                    $('#record_feedback').attr('class','hidden');
                    break;
                case "history" :
                    $('#record_history').attr('class','');
                    $('#record_feedback').attr('class','hidden');
                    break;
                case "feedback" :
                    $('#record_feedback').attr('class','');
                    $('#record_homework').attr('class','hidden');
                    $('#record_history').attr('class','hidden');

                    $('#feedbackCheck').attr('class','hidden');
                    $('#feedbackCheckIcon').attr('class','hidden');
                    break;
            }
        }

        $(document).on('click','#groupA',function () {
            $('#nav_group_name').text("특강 A반");
            $('#wrapper').show();
            $('#group_chart').attr('class','');
            $('#record_history').attr('class','hidden');
        });

        $(document).on('click','#groupB',function () {
            $('#nav_group_name').text("특강 B반");
            $('#wrapper').hide();
        });


        function WritingCheck(){
            var writingData = [];
            writingData = $('.wrong_write').value;
            alert(writingData);

            writingData = $('.wrong_write').attr('id');
            alert(writingData);

            writingData = $('.wrong_write').attr('class');
            alert(writingData);
        }


        function insertQuestion(){

            var reqData = {'title' : "123" , 'question' : "asd" , "teacherId" : 123456789 ,"groupId" : 1};

            $.ajax({
                type: 'POST',
                url: "{{url('/recordBoxController/insertQuestion')}}",
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

                                                retestState:"not",
                                                wrongState:"not"
                                              }
                                        }
                    */

                },
                error: function (data) {
                    alert("학생별 최근 레이스 값 불러오기 에러");
                }
            });
        }

        insertQuestion();

    </script>

</head>
<body onload="OnLoadRecordboxStudent()">

{{--메인 네비바 불러오기--}}
@include('Navigation.main_nav')

<div class="recordbox_main">

    {{--사이드바 불러오기--}}
    @include('Recordbox.record_sidebar')

    <div class="changePages">

        {{--레코드 네비바 불러오기--}}
        @include('Recordbox.recordbox_student_recordnav')

        {{--레코드 차트페이지 불러오기--}}
        @include('Recordbox.recordbox_student_history')

        {{--레코드 피드백페이지 불러오기--}}
        @include('Recordbox.recordbox_student_feedback')

    </div>

</div>

<div class="modal_page">

    <div class="modal fade" id="modal_studentWritingWrons" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <table class="table_wrongList" id="wrongList">
                                <thead>
                                <tr>
                                    {{--INSERT NEW DATA 01--}}
                                    <th>
                                        <div>
                                            1.
                                        </div>
                                    </th>
                                    {{--INSERT NEW DATA 02--}}
                                    <th>
                                        <div>
                                            <b>
                                                周辺の住民がいくら反対した（　　）、動きだした開発計画は止まらないだろう。
                                            </b>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    {{--INSERT NEW DATA 03--}}
                                    <td colspan="2">
                                        <div class="wrongExamples">
                                            <ul>
                                                <li>
                                                    かたわら (1명)
                                                </li>
                                                <li>
                                                    かたがた (2명)
                                                </li>
                                                <li>
                                                    こととて (1명)
                                                </li>
                                                <li>
                                                    うちに (1명)
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="wrong_right">
                            <table class="table_wrongList" id="wrongList">
                                <thead>
                                <tr>
                                    {{--INSERT NEW DATA 01--}}
                                    <td>
                                        <div>
                                            2.
                                        </div>
                                    </td>
                                    {{--INSERT NEW DATA 02--}}
                                    <td>
                                        <div>
                                            <b>
                                                姉は市役所に勤める（　　）、ボランティアで日本語を教えています。
                                            </b>
                                        </div>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    {{--INSERT NEW DATA 03--}}
                                    <td colspan="2">
                                        <div class="wrongExamples">
                                            <ul>
                                                <li>
                                                    かたわら (1명)
                                                </li>
                                                <li>
                                                    かたがた (1명)
                                                </li>
                                                <li>
                                                    こととて (1명)
                                                </li>
                                                <li>
                                                    うちに (2명)
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div>
                            <table class="table table-hover">
                                <tbody id="modal_allWrongAnswerList">

                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="changeCheck()" id="feedback_modal_confirm">제출하기</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="modal_feedback_cancel">취소</button>
                </div>


            </div>
        </div>
    </div>




    {{--Modal : make quiz--}}
    <div class="modal fade" id="" >
        <div class="modal-dialog" role="document" style="width: 1000px">

            <div class="modal-content" style="padding: 10px 20px 0 20px;">
                <div class="modal-header">
                    <h3 class="modal-title" id="ModalLabel" style="text-align: center;">오답 노트</h3>

                    <div class="modal_student_wrong_date" style="width: 100%;text-align: right;"> </div>

                    <div class="student_race_and_teacher" style="width: 100%;">
                        <h5 style="margin: 0;text-align:center">
                            <div class="" id="modal_student_wrong_raceName_teacher" style="display: inline;margin-right: 10px;"> </div>

                        </h5>
                    </div>

                </div>
                <div class="modal-body" style="text-align: left;margin: 0;">
                    <table class="table table-hover">
                        <thead>
                        <tr id="race_detail_record">
                            <th style="width: 50px">
                                번호
                            </th>
                            <th colspan="2">
                                문제
                            </th>
                        </tr>
                        </thead>

                        <tbody id="wrongQuestions">

                        </tbody>
                    </table>

                </div>

                <input type="hidden" name="hiddenValue" id="hiddenValue" value="" />

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="WritingCheck()" id="feedback_modal_confirm">확인</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="modal_feedback_cancel">취소</button>
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
                    <br><br>
                    <div class="request_date" style="float: right;">
                        날짜 : 2018-04-17
                    </div>
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
                        <input type="file" accept="image/*" onchange="loadFile(event)" id="ex_file">

                        <img id="output" style="max-width: 300px;max-height: 300px;"/>

                        {{--사진 불러오는 스크립트--}}
                        <script type="text/javascript">
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
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="changeCheck()" id="feedback_modal_confirm">확인</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="modal_feedback_cancel">취소</button>
                    </div>

                    <script>
                        function changeCheck(){
                            alert('정상 등록하였습니다.');
                            $('#1check').attr('class','btn btn-primary').text('확인');
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>