<style>
    .record_history {
        z-index: 1;
        position: relative;
        display: block;
        clear: both;
    }
    .recordbox-history {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        background-color: white;
    }
    .historyContainer {
        width: 100%;
        height: 100%;
    }
    .historyContainer .historyList {
        display: block;
        float: left;
        width: 60%;
        text-align: left;
    }
    .historyList_container{
        padding: 10px 0 10px 20px;
        background-color: #f9f9f9;
        height: 50px;
    }
    .historyList-name{
        font-size: 19px;
        position: relative;
        float: left;
        margin-left: 30px;
    }
    .historyList-name h4{
        font-weight: bold;
        color: #203a8e;
    }
    .historyList-sorting{
        position: relative;
        float: right;
    }
    .history{
        margin: 0;
        padding: 0;
    }
    .history li{
        padding: 5px 10px 5px 10px;
    }
    .historyList-history{
        height: 100%;
    }
    .history-outline{
        height: 100%;
        background-color: #f9f9f9;
    }
    .historyList-table{
        background-color: white;
    }
    .historyList-table thead tr ,.historyList-table tbody tr{
        height: 20px;
    }
    .historyList-table thead tr:first-child{
        background-color: #DFDFDF;
    }
    .historyList-table tbody tr:nth-child(2n),.raceListDetail table tbody tr:nth-child(2n) {
        background-color: #e6eaed;
    }
    .raceListDetail {
        display: block;
        float: right;
        width: 30%;
        height: 82%;
        background-color: #f9f9f9;
    }
    .historyContainer .raceListDetail .raceListDetailScroll {
        width: 100%;
        height: 100%;
        overflow-y: scroll;
        border: 1px solid #e5e6e8;
    }
    .raceListDetail table{
        background-color: white;
    }
    .raceListDetail table thead tr:last-child{
        background-color: #DFDFDF;
    }
    .raceListDetail table thead tr th ,.raceListDetail table tbody {
        text-align: center;
    }
    .raceListDetail-up{
        margin: 0;
        padding: 0;
        top: 60px;
        right: 0;
        height: 91%;!important;
        position: fixed;
        z-index: 100;
        width: 26%; !important;
    }
    .empty-striped{
        float: left;
        width: 10%;
        height: 100%;
        background-image: url("https://i.imgur.com/NYAOWGv.png");
        background-size: 100% 100%;
    }

</style>

<script>

    var reqGroupId = "{{$groupId}}";
    var reqWhere = "{{$where}}";

    $(document).ready(function () {
        var raceId = "";
        var userId = "";

        //최근기록 불러오기
        getHistory(reqGroupId);

        //레코드리스트에서 성적표 클릭시 성적표 로드
        $(document).on('click','.history_list_gradeCard button',function () {
            loadGradeCard(this.id);
        });

        //과제 확인하기
        $(document).on('click','.btnHomeworkCheck',function () {
            checkHomework($(this).attr('id'));

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

        //학생 상세정보에서 오답노트 클릭시 성적표 로드
        $(document).on('click','.modal_openStudentWrongGradeCard button',function () {
            raceId = $(this).attr('id');
            userId = $(this).attr('name');

            getStudentWrongWriting(userId,raceId);
        });

        //학생 상세정보에서 학생 클릭시 오답노트 로드
        $(document).on('click','.toggle_stdList',function () {
            raceId =$(this).attr('name');
            userId = $(this).attr('id');

            getStudentWrongAnswer(userId,raceId);

        });
    });


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
                            // CL. 성적표
                            .text("{{$language['history']['grade']}}")));

                    if(data['races'][i]['retestClearCount'] == data['races'][i]['retestCount'] &&
                        data['races'][i]['wrongClearCount'] == data['races'][i]['wrongCount']){

                        $('#history_list_tr'+i).append($('<td>').append($('<button onclick="checkHomework(this.id)">')
                            // CL. 전체완료
                            .attr('class','btn btn-primary').attr('id',data['races'][i]['raceId']).text("{{$language['history']['alldone']}}")))
                    }else{
                        $('#history_list_tr'+i).append($('<td>').append($('<button onclick="checkHomework(this.id)">')
                            // CL. 미완료
                            .attr('class','btn btn-warning').attr('id',data['races'][i]['raceId']).text("{{$language['history']['allnotyet']}}")))
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

                //전체 점수와 ���균 점수들 로드하기
                //0은 전체 성적표
                makingModalPage(raceId,data,0);

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

                var wrongsData = data['wrongs'];
                var leftOrRight = "";
                var allPage = Math.floor(wrongsData.length / 10);

                $('.wrong_left').empty();
                $('.wrong_right').empty();
                $('.modal_pagenation').empty();

                if(wrongsData.length == 0){
                    //CL. 오답 내용이 없습니다.
                    $('.modal_wrong').text("{{$language['modal']['Grade']['noWrongData']}}");
                    $('.wrong_left').addClass("noBoardLine");
                    $('.wrong_right').addClass("noBoardLine");
                }else{

                    for(var i = 0 ; i < wrongsData.length ; i++ ){

                        // if(i < 1 ){
                        //     leftOrRight = "wrong_left";
                        //     $('.wrong_right').addClass("noBoardLine");
                        // }
                        // else if(i % 2 == 0 ){
                        //     leftOrRight = "wrong_left";
                        //     $('.wrong_right').removeClass("noBoardLine");
                        // }
                        // else{
                        //     leftOrRight = "wrong_right";
                        //     $('.wrong_right').removeClass("noBoardLine");
                        // }

                        if(i < 5){
                            leftOrRight = "wrong_left";
                            $('.wrong_left').removeClass("noBoardLine");
                            $('.wrong_right').addClass("noBoardLine");
                        }else{
                            leftOrRight = "wrong_right";
                            $('.wrong_right').removeClass("noBoardLine");
                        }

                        //임의로 값 설정
                        //wrongsData[i]['type'] = "sub";


                        switch(wrongsData[i]['type']){
                            case "obj" :

                                /***************************************************************************/
                                // wrongsData[0]['question'] = "苦労してためたお金なのだから、一円（　　）無駄には使いたくない。";
                                // wrongsData[0]['rightAnswer'] = "とはいえ";
                                // wrongsData[0]['example1'] = "たりとも";
                                // wrongsData[0]['example2'] = "ばかりも";
                                // wrongsData[0]['example3'] = "だけさえ";
                                // wrongsData[2]['question'] = "この店は洋食と和食の両方が楽しめる（　　）、お得意さんが多い。";
                                // wrongsData[2]['rightAnswer'] = "とあって";
                                // wrongsData[2]['example1'] = "からして";
                                // wrongsData[2]['example2'] = "にあって";
                                // wrongsData[2]['example3'] = "にしては";
                                // wrongsData[4]['question'] = "姉は市役所に勤める（　　）、ボランティアで日本語を教えています。";
                                // wrongsData[4]['rightAnswer'] = "かたわら";
                                // wrongsData[4]['example1'] = "かたがた";
                                // wrongsData[4]['example2'] = "こととて";
                                // wrongsData[4]['example3'] = "うちに";
                                /***************************************************************************/

                                $('.' + leftOrRight).append($('<div>').attr('class','objWrong')
                                    .append($('<table>').attr('class', 'table_wrongList')
                                        .append($('<thead>')
                                            .append($('<tr>')
                                                .append($('<th>')
                                                    .append($('<div>').text(wrongsData[i]['number'])
                                                    )
                                                )
                                                .append($('<th>')
                                                    .append($('<div>')
                                                        .append($('<b>').text(wrongsData[i]['question'])
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                        .append($('<tbody>')
                                            .append($('<tr>')
                                                .append($('<td colspan="2">')
                                                    .append($('<div>').attr('class','wrongExamples')
                                                        .append($('<ul>')
                                                        //CL. 명
                                                            .append($('<li>').text(wrongsData[i]['rightAnswer']+" ("+ wrongsData[i]['rightAnswerCount'] +"{{$language['modal']['Grade']['people']}})"))
                                                            .append($('<li>').text(wrongsData[i]['example1']+" ("+ wrongsData[i]['example1Count'] +"{{$language['modal']['Grade']['people']}})"))
                                                            .append($('<li>').text(wrongsData[i]['example2']+" ("+ wrongsData[i]['example2Count'] +"{{$language['modal']['Grade']['people']}})"))
                                                            .append($('<li>').text(wrongsData[i]['example3']+" ("+ wrongsData[i]['example3Count'] +"{{$language['modal']['Grade']['people']}})")
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                );

                                break;
                            case "sub" :
                                /***************************************************************************/
                                // wrongsData[1]['question'] = "周辺の住民がいくら反対した（　　）、動きだした開発計画は止まらないだろう。";
                                // wrongsData[1]['rightAnswer'] = "ところで";
                                // wrongsData[1]['hint'] = "とこ@で";
                                // wrongsData[3]['question'] = "苦労してためたお金なのだから、一円（　　）無駄には使いたくない。";
                                // wrongsData[3]['rightAnswer'] = "たりとも";
                                // wrongsData[3]['hint'] = "@@とも";
                                // wrongsData[5]['question'] = "姉は市役所に勤める（　　）、ボランティアで日本語を教えています。";
                                // wrongsData[5]['rightAnswer'] = "かたわら";
                                // wrongsData[5]['hint'] = "か@@@";
                                /***************************************************************************/
                                $('.' + leftOrRight).append($('<div>').attr('class','subWrong')
                                    .append($('<table>').attr('class', 'table_wrongList')
                                        .append($('<thead>')
                                            .append($('<tr>')
                                                .append($('<th>')
                                                    .append($('<div>').text(wrongsData[i]['number'])
                                                    )
                                                )
                                                .append($('<th>')
                                                    .append($('<div>')
                                                        .append($('<b>').text(wrongsData[i]['question'])
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                        .append($('<tbody>')
                                            .append($('<tr>')
                                                .append($('<td colspan="2">')
                                                    .append($('<div>').attr('class','wrongExamples')
                                                    //CL. history : 정답
                                                    //CL. history : 힌트
                                                        .append($('<div>').text("{{$language['history']['answer']}} : "+wrongsData[i]['rightAnswer']+" ("+ wrongsData[i]['rightAnswerCount'] +"{{$language['modal']['Grade']['people']}})")
                                                        )
                                                        .append($('<div>').text("{{$language['history']['hint']}} : "+wrongsData[i]['hint']).css('color','blue')
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                );
                                break;
                        }

                    }

                    allPage = 3;
                    $('#modalPagenation').append($('<div>').attr('class','wrong_page'));
                    for(var i = 0 ; i <= allPage ; i++){
                        $('.wrong_page').append($('<a href="#">').val(i+1).text(i+1));
                    }
                }

            },
            error: function (data) {
                alert("해당 레이스별 오답 문제 가져오기");
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

                var stdHomework = data['students'];
                $('#historyListNumber').empty();
                $('#historyListRaceName').empty();
                $('#history_homework').empty();

                for (var i = 0; i < stdHomework.length ; i ++) {

                    if(stdHomework[i]['wrongState'] == "not" && stdHomework[i]['retestState'] == "not"){

                    }else{

                        $('#history_homework').append($('<tr id="history_homework_tr' + i + '">'));

                        $('#historyListNumber').text($('#history_id_'+raceId).attr('value'));
                        $('#historyListRaceName').text($('#history_name_'+raceId).attr('value'));

                        $('#history_homework_tr' + i).append($('<td>').text(stdHomework[i]['userName']));

                        switch (stdHomework[i]['retestState']){
                            case "not" :
                            //CL. history : PASS
                                $('#history_homework_tr' + i).append($('<td>').text("{{$language['history']['pass']}}"));

                                break;
                            case "order" :
                            //CL. history : 미응시
                                $('#history_homework_tr' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("{{$language['history']['notdone']}}")));

                                break;
                            case "clear" :
                                $('#history_homework_tr' + i).append($('<td>').attr('class','modal_openStudentRetestGradeCard')
                                    .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                        .attr('id',raceId).attr('name',stdHomework[i]['userId']).text("{{$language['history']['done']}}")));

                                break;
                        }

                        //임의로 값 설정
                        //stdHomework[i]['wrongState'] = "clear";

                        switch (stdHomework[i]['wrongState']){
                            case "not" :
                                $('#history_homework_tr' + i).append($('<td>').text("{{$language['history']['pass']}}"));

                                break;
                            case "order" :
                                $('#history_homework_tr' + i).append($('<td>').append($('<button>').attr('class', 'btn btn-warning').text("{{$language['history']['notsubmit']}}")));

                                break;
                            case "clear" :
                                $('#history_homework_tr' + i).append($('<td>').attr('class','modal_openStudentWrongGradeCard')
                                    .append($('<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal_RaceGradeCard">')
                                        .attr('id',raceId).attr('name',stdHomework[i]['userId']).text("{{$language['history']['submit']}}")));

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

    function makingChartData(raceData) {

        var raceData = raceData['races'];


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
        AllChartData = { "total_data" : ["{{$language['modal']['Grade']['allGrade']}}" , total_data_Points] ,
                "voca_data" : ["{{$language['modal']['Grade']['totalVoca']}}", vocabulary_Points] ,
                "grammer_data" : ["{{$language['modal']['Grade']['totalGrammer']}}" , grammer_data_Points] ,
                "word_data" : ["{{$language['modal']['Grade']['totalWord']}}" , word_data_Points]
        };

        return AllChartData;
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
                valueFormatString: "MM{{$language['modal']['Date']['month']}} DD{{$language['modal']['Date']['date']}} (HH:ss)",
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


    //재시험 점수 가져오기
    function getRetestData(userId,raceId){

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


                        switch(wrongsData[i]['type']){
                            case "obj" :

                                $('.' + leftOrRight).append($('<div>').attr('class','objWrong')
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
                                                    .append($('<div>').attr('class','wrongExamples')
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

                                $('.' + leftOrRight).append($('<div>').attr('class','subWrong')
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
                                                    .append($('<div>').attr('class','wrongExamples')
                                                        .append($('<div>').text("{{$language['history']['answer']}} : "+wrongsData[i]['rightAnswer']+" ("+ wrongsData[i]['rightAnswerCount'])
                                                        )
                                                        .append($('<div>').text("{{$language['history']['hint']}} : "+wrongsData[i]['hint']).css('color','blue')
                                                        )
                                                        .append($('<div>').text("{{$language['history']['studentAnswer']}} : "+wrongsData[i]['wrongs'][0]['answer']).css('color','black')
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

<div class="recordbox-history">
    <div class="historyContainer">

        <div class="historyList">
            <div class="historyList_container">
                <div class="historyList-name">
                    <h4>
                    {{$language['history']['history']}}
                    </h4>
                </div>
                <div class="historyList-sorting">
                    <ul class="nav navbar-nav history">
                        <li>
                        {{$language['history']['lastDate']}}▼
                        </li>
                        <li>
                        {{$language['history']['highGrade']}}▼
                        </li>
                        <li>
                        {{$language['history']['rowGrade']}}▼
                        </li>
                        <li>
                        {{$language['history']['homeworkRefer']}}▼
                        </li>
                    </ul>
                </div>
                <script>
                    $('.historyList-sorting').hide();
                </script>
            </div>
            <div class="historyList-history">
                <div class="history-outline">
                    <table class="table table-hover historyList-table">
                        <thead>
                        <tr>
                            <th>{{$language['modal']['Subtitle']['number']}}</th>
                            <th>{{$language['modal']['Subtitle']['quizName']}}</th>
                            <th>{{$language['modal']['Subtitle']['date']}}</th>
                            <th>{{$language['modal']['Subtitle']['grade']}}</th>
                            <th>{{$language['modal']['Subtitle']['homework']}}</th>
                        </tr>
                        </thead>
                        <tbody id="history_list">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="empty-striped">

        </div>

        {{--과제 목록 보기--}}
        <div class="raceListDetail">
            <div class="raceListDetailScroll">
                <table class="table table-hover table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th id="historyListNumber">
                        {{$language['modal']['Subtitle']['number']}}
                        </th>
                        <th id="historyListRaceName" colspan="2">
                        {{$language['modal']['Subtitle']['quizName']}}
                        </th>
                    </tr>
                    <tr>
                        <th>
                        {{$language['modal']['Subtitle']['student']}}
                        </th>
                        <th>
                        {{$language['modal']['Subtitle']['retest']}}
                        </th>
                        <th>
                        {{$language['modal']['Subtitle']['omr']}}
                        </th>
                    </tr>
                    </thead>

                    {{--getStudent()로 학생들 불러오기--}}
                    <tbody id="history_homework">

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
