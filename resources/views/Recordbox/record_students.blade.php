<style>
    .record_student {
        z-index: 1;
        position: relative;
        display: block;
        clear: both;
    }
    .studentsPage_main{
        width: 100%;
        padding: 10px 0 10px 20px;
        background-color: #f9f9f9;
        height: 50px;
        position: relative;
        display: block;
        font-size: 19px;
        text-align: left;
        font-weight: bold;
        margin-left: 30px;
    }
    .studentsPage_main h4{
        color: #203a8e;
        font-weight: bold;
    }
    .studentContainer {
        width: 100%;
    }
    .studentChart {
        width: 100%;
        height: 500px;
        text-align: center;
        margin-bottom: 50px;
    }

    .stdAllList_scroll {
        width: 250px;
        height: 500px;
        border: 1px solid #e5e6e8;
        float: left;
        position: relative;
        margin: 0;
        padding: 0;
    }
    .stdAllList {
        height: 500px;
        width: 100%;
        overflow-y: scroll;
    }
    .studentList {
        background-color: white;
        width: 100%;
    }
    .studentList thead tr:first-child{
        background-color: #DFDFDF;
    }
    .studentList thead tr th ,.studentList thead tr td{
        width: 50px;
    }
    #std_grade_list_table .studentList tbody tr:nth-child(2n){
        background-color: #e6eaed;
    }
    .chartArea_student{
        float: left;
        position: relative;
        height: 500px;
        width: 70%;
        margin: 0;
        padding: 0;
    }
    .chartWrapper_student {
        width: 90%;
        height: 100%;
        margin-left: 5%;
        margin-right: 5%;
    }
    .chartAreaWrapper_student {
        margin: 0;
        width: 100%;
        height: 100%;
        overflow-x: scroll;
    }
    .canvaschart_student{
        position: relative;
        padding-top: 10px;
        left: 0;
        top: 0;
        width: 100%;
        height: 95%;
        margin: 0;
    }

    .student_grade {
        width: 90%;
        clear: both;
        position: relative;
        margin: 0;
    }
    .student_grade table tr{
        height: 50px;
        text-align: center;
    }


</style>
<script>

    var reqGroupId = "{{$groupId}}";
    var reqWhere = "{{$where}}";

    $(document).ready(function () {

        var raceId = "";
        var userId = "";

        //학생리스트 가져오기
        getStudents(reqGroupId);

        //학생 한명 클릭하면 개인성적 가져오기
        $(document).on('click','.stdList',function () {

            var reqUserId = $(this).attr('id');

            $('#student_list tr').css('background-color','white');
            $('#student_list_'+reqUserId).css('background-color','#d9edf7');

            getStudentGrade(this.id);

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

        //학생 상세정보에서 학생 클릭시 오답노트 로드
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
    });


    //그룹에 속한 학생들 가져오기
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

                var student = data['students'];

                for(var i = 0 ; i < student.length; i++){
                    $('#student_list').append($('<tr id="student_list_'+student[i]['id']+'">'));

                    for(var j = 0 ; j < 1 ; j++ ) {

                        $('#student_list_' + student[i]['id']).append($('<td>').text(i+1));
                        $('#student_list_' + student[i]['id']).append($('<td>')
                            .append($('<a href="#">')
                                .text(student[i]['name']))
                            .attr('id',student[i]['id'])
                            .attr('name',student[i]['name'])
                            .attr('class','stdList'));
                    }
                }
                $('#student_list_' + student[0]['id']).css('background-color','#d9edf7');
                getStudentGrade(student[0]['id']);

            },
            error: function (data) {
                alert("그룹에 속한 학생 에러");
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

                //1은 학생개인 성적표
                makingModalPage(raceId,data,1);
                $('.modal-content.studentGrade .modal-title').text("{{$language['modal']['Title']['allStudentGrade']}}");

            },
            error: function (data) {
                alert("학생별 최근 레이스 값 불러오기 에러");
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

                //오답리스트 로드할 위치(id값)를 변수에 담기
                var WrongList = "modal_allWrongAnswerList";
                var wrongsData = data['wrongs'];
                var leftOrRight = "";

                $('.wrong_left').empty();
                $('.wrong_right').empty();

                if(wrongsData.length == 0){
                    $('.wrong_left').text("{{$language['modal']['Grade']['noWrongData']}}");
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

                        console.log(wrongsData[i]['type']);

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
                                                        .append($('<div>').text("{{$language['history']['answer']}} : " + wrongsData[i]['rightAnswer'])
                                                        )
                                                        .append($('<div>').text("{{$language['history']['hint']}} : " + wrongsData[i]['hint']).css('color', 'blue')
                                                        )
                                                        .append($('<div>').text("{{$language['history']['studentAnswer']}} : " + wrongsData[i]['wrongs'][0]['answer']).css('color', 'black')
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
            },
            error: function (data) {
                alert("해당 학생별 오답 문제 가져오기");
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

                var ChartData = makingStudentChartData(data);
                makingStudentChart(ChartData);
                var raceData;



                $('#studentGradeList').empty();

                for( var i = 0 ; i < data.races.length ; i++ ) {

                    raceData = JSON.parse(data.races[i]);

                    $('#studentGradeList').append($('<tr>').attr('id','stdGrade_'+i));

                    $('#stdGrade_' + i).append($('<td>').text(i+1));
                    $('#stdGrade_' + i).append($('<td>').text(raceData['year']+"{{$language['modal']['Date']['year']}} "
                                                                +raceData['month']+"{{$language['modal']['Date']['month']}} "
                                                                +raceData['day']+"{{$language['modal']['Date']['date']}}"));
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
                            $('#stdGrade_' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("{{$language['history']['notdone']}}")));

                            break;
                        case "clear" :
                            $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentRetestGradeCard')
                                .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                    .attr('id',raceData['raceId']).attr('name',userId).text("{{$language['history']['done']}}")));

                            break;
                    }

                    //임의로 값 설정
                    raceData['wrongState'] = "clear";

                    switch (raceData['wrongState']){
                        case "not" :
                            $('#stdGrade_' + i).append($('<td>').text("PASS"));

                            break;
                        case "order" :
                            $('#stdGrade_' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("{{$language['history']['notsubmit']}}")));

                            break;
                        case "clear" :
                            $('#stdGrade_' + i).append($('<td>').attr('class','modal_openStudentWrongGradeCard')
                                .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                    .attr('id',raceData['raceId']).attr('name',userId).text("{{$language['history']['submit']}}")));

                            break;
                    }

                    $('#stdGrade_'+i).append($('<td>').attr('class','modal_openStudentGradeCard')
                        .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                            .attr('id',raceData['raceId']).attr('name',userId).text("{{$language['history']['grade']}}")));
                }

            },
            error: function (data) {
                alert("학생별 최근 레이스 값 불러오기 에러");
            }
        });
    }

    function makingStudentChartData(data){

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
        AllChartData = { "total_data" : ["{{$language['modal']['Grade']['allGrade']}}" , total_data_Points] ,
                "voca_data" : ["{{$language['modal']['Grade']['totalVoca']}}", vocabulary_Points] ,
                "grammer_data" : ["{{$language['modal']['Grade']['totalGrammer']}}" , grammer_data_Points] ,
                "word_data" : ["{{$language['modal']['Grade']['totalWord']}}" , word_data_Points]
        };

        return AllChartData;
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

                console.log(data);

                makingModalPage(raceId,data,1);
                $('.modal-content.studentGrade .modal-title').text("{{$language['history']['retestGrade']}}");

            },
            error: function (data) {
                alert("학생별 최근 레이스 값 불러오기 에러");
            }

        });
    }

    //오답 노트 작성 메서드
    function getStudentWrongWriting(userId,raceId) {

        var reqData = {'userId': userId, 'raceId': raceId};

        $.ajax({
            type: 'POST',
            url: "{{url('/recordBoxController/getWrongs')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: reqData,
            success: function (data) {

                makingModalPage(raceId, data, 2);

            },
            error: function (data) {
                alert("학생별 최근 레이스 값 불러오기 에러");
            }
        });
    }

</script>


<div class="studentContainer">

    <div class="studentsPage_main">
        <h4>
        {{$language['student']['student']}}
        </h4>
    </div>
    <div class="studentChart">
        <div class="stdAllList_scroll">
            <div class="stdAllList">
                <table class="studentList table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>
        {{$language['modal']['Subtitle']['number']}}
                        </th>
                        <th>
        {{$language['modal']['Subtitle']['name']}}
                        </th>
                    </tr>
                    </thead>

                    {{--getStudent()로 학생들 불러오기--}}
                    <tbody id="student_list">

                    </tbody>

                </table>
            </div>
        </div>

        <div class="chartArea_student">
            <div class="chartWrapper_student">
                <div class="chartAreaWrapper_student">
                    <div class="canvaschart_student" id="chartContainer_privacy_student"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="student_grade">

        <div id="std_grade_list_table" >
            <table class="table table-hover table-bordered studentList" >
                <thead>
                <tr>
                    <th style=" text-align: center;min-width: 50px;">
        {{$language['modal']['Subtitle']['number']}}
                    </th>
                    <th style=" text-align: center;">
        {{$language['modal']['Subtitle']['date']}}
                    </th>
                    <th style="text-align: center;">
        {{$language['modal']['Subtitle']['quizName']}}
                    </th>
                    <th style=" text-align: center;width: 100px;">
        {{$language['modal']['Grade']['allGrade']}}
                    </th>
                    <th style=" text-align: center;min-width: 80px;">
        {{$language['modal']['Grade']['totalVoca']}}
                    </th>
                    <th style=" text-align: center;min-width: 80px;">
        {{$language['modal']['Grade']['totalGrammer']}}
                    </th>
                    <th style=" text-align: center;min-width: 80px;">
        {{$language['modal']['Grade']['totalWord']}}
                    </th>
                    <th style=" text-align: center;width: 100px;">
        {{$language['modal']['Subtitle']['retest']}}
                    </th>
                    <th style=" text-align: center;width: 120px;">
        {{$language['modal']['Subtitle']['omr']}}
                    </th>
                    <th style=" text-align: center;width: 100px;">
        {{$language['modal']['Subtitle']['grade']}}
                    </th>
                </tr>
                </thead>

                <tbody id="studentGradeList" class="">


                </tbody>

            </table>
        </div>
    </div>
</div>
