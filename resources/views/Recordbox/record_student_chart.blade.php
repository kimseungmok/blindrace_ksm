
<head>
    <style>
        .recordbox-chartContainer h4{
            color: #203a8e;
            font-weight: bold;
        }
        .chartAttribute {
            margin-top: 20px;
            height : 150px;
            width: 100%;
            text-align: left;
        }
        .attributeContainer {
            margin-left: 50px;
            width: 80%;
        }
        .recordbox-radio{
            display: block;
            margin-top: 20px;
        }
        .chooseDate {
            display: block;
            margin-top: 20px;
            margin-left: 20px;
        }
        .recordbox-radio h4,.chooseDate h4{
        }
        .recordbox-radioButtons {
            vertical-align: middle;
        }
        .recordbox-radioButtons label{
            margin-left: 20px;
            margin-right: 20px;
        }
        .chart_total {
            color: #f08080;
        }
        .chart_vocabulary {
            color: #51cda0;
        }
        .chart_grammer {
            color: #df7970;
        }
        .chart_word {
            color: #4c9ca0;
        }
        .chartArea{
            height: 400px;
            width: 100%;
            margin: 0;
            border-top: 6px solid #e5e6e8;
            text-align: left;
            padding: 20px 0 0 50px;
        }
        .chartWrapper {
            margin-top: 30px;
            margin-right: 5%;
            width: 80%;
            height: 100%;
        }
        .chartAreaWrapper {
            margin: 0;
            width: 100%;
            height: 100%;
            overflow-x: scroll;
        }
        .canvaschart{
            position: relative;
            padding-top: 10px;
            left: 0;
            top: 0;
            width: 100%;
            height: 95%;
            margin: 0;
        }

        .student_race_chart{

        }
    </style>


    <script type="text/javascript">

        /***********************************날짜 구하기**************************************************************************************************/
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

        /***********************************본격적인 함수들**************************************************************************************************/

        var reqGroupId = "{{$groupId}}";
        var reqWhere = "{{$where}}";
        var chartData = "";

        $(document).ready(function () {

            //차트 출력하기
            getChartData_and_loadChart(reqGroupId,defaultStartDate,defaultEndDate);

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
                getChartData_and_loadChart(reqGroupId,startDate,defaultEndDate);

            });
        });

        //날짜 조회 눌렀을 때 차트 출력
        function orderChart(){
            var startDate = document.querySelector('input[id="startDate"]').value;
            var endDate = document.querySelector('input[id="endDate"]').value;

            //해당되는 날짜를 차트에 보여주기
            getChartData_and_loadChart(reqGroupId,startDate,endDate);
        }

        //날짜를 가져와서 조회 및 차트 그리기
        function getChartData_and_loadChart(groupId,startDate,endDate){

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

                    var ChartData = makingChartData(data);
                    makingChart(ChartData);

                },
                error: function (data) {
                    alert("날짜 조회 에러");
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

            //Changing language : chart / 총점수,어휘점수
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

                        // name: "어휘 점수",
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

    </script>

</head>

    <div class="recordbox-chartContainer">

        <div class="chartAttribute">

            <div class="attributeContainer">
                <!-- Changing language : chart / 시간 정하기 -->
                <h4>{{$language['chart']['setTime']}}</h4>

                <div class="recordbox-radio">
                    <div class="recordbox-radioButtons">
                        <label><input type="radio" class="radio_changeDateToChart" name="optradio" onclick="changeDateToChart()" value='1' checked="checked">{{$language['chart']['week']}}</label>
                        <label><input type="radio" class="radio_changeDateToChart" name="optradio" onclick="changeDateToChart()" value='2' >{{$language['chart']['month']}}</label>
                        <label><input type="radio" class="radio_changeDateToChart" name="optradio" onclick="changeDateToChart()" value='3' >{{$language['chart']['3month']}}</label>
                        <label><input type="radio" class="radio_changeDateToChart" name="optradio" onclick="changeDateToChart()" value='4' >{{$language['chart']['6month']}}</label>
                        <label><input type="radio" class="radio_changeDateToChart" name="optradio" onclick="changeDateToChart()" value='5' >{{$language['chart']['12month']}}</label>
                    </div>
                </div>

                <div class="chooseDate" >
                    <input type="date" name="chooseday" id="startDate"></input>

                    <input type="date" name="chooseday" id="endDate"></input>

                    <button class="btn btn-primary" style="margin-left: 30px;" onclick="orderChart()">
                        <!-- Changing language : chart / 조회 -->
                        {{$language['chart']['refer']}}
                    </button>
                </div>
            </div>
        </div>


        <div class="chartArea">
            <h4>
            <!-- Changing language : chart / 조회 -->
            {{$language['chart']['result']}}
            </h4>
            <div class="chartWrapper">
                <div class="chartAreaWrapper">
                    <div class="canvaschart" id="chartContainer"></div>
                </div>
            </div>
        </div>

    </div>
