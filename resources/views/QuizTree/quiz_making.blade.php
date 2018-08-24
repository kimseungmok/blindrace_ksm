<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz_making</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="js/bootstrap.min.js" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="css/magic-check.css" rel="stylesheet">

    <style type="text/css">

        p,  th, tr, td, button {
            font-family: 'Meiryo UI';
            font-size: 20px;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        .contents {
            margin-left: 20%;
            margin-top : -20px;

        }

        #quizFont {
            font-size: 25px;
            color: white;
        }

        #buttonDesign {
            border: 2px solid white;
            padding: 10px;
            border-radius: 25px;
            background-color: transparent;
        }

        #deletebuttonDesign {
            border: 1px solid #E4E4E4;
            padding: 10px;
            border-radius: 16px;
            background-color: transparent;
        }

        .threebuttonDesign {
            border: 1px solid #E4E4E4;
            padding: 10px;
            border-radius: 16px;
            background-color: transparent;
        }

        .threebuttonDesignT {
            margin: 10px 20px 10px 0px;
            text-align: right;
        }

        .selectCall {
            width: 80%;
            padding-top: 1%;
        }

    </style>
</head>

<!--1. 퀴즈를 새로 생성할 경우-->
@if(count($response['quizs']) == 0)
    <body onload="document.getElementById('add').click();">

<!--2. 퀴즈를 수정할 경우-->
@elseif(count($response['quizs']) > 0)
    <body onload="updateQuiz()">

@endif

<nav>
    @include('Navigation.main_nav')
</nav>

<aside style="display:inline-block; vertical-align:top;">
    @include('QuizTree.quiz_making_sidebar')
</aside>

<script>

    var idNum = 0;

    // 문항 번호(id) 저장용 배열
    var idArray = new Array();

    // 출제 유형 저장용 배열
    var makeTypeRadio = new Array();
    // 문제 유형 저장용 배열
    var quizTypeRadio = new Array();

    $(document).ready(function () {
        $('#listName').change(function () {
            var getListName = $('#listName').val();
            var listNameObj = document.getElementById("listName");
            listNameObj.value = getListName;
        });
    });


    // 메인 -> 문제 테이블 추가 : empty, update, call
    function quizAdd(addArr) {

        // id 값 부여 + 배열에 저장
        idNum++;
        idArray.push(idNum);

        // quizBox Div에 table 추가
        $(".quizBox").append(
            "<div class='quiz' style='margin: 20px'>" +
            "<table class='table table-bordered' id='tableNum"+ idNum +"'>" +
                "<thead>" +
                    "<td style='background-color: #BEE4F4; width: 5%; height: 60px; color: #31639D; vertical-align: middle''>No</td>" +
                    "<td id='quizNum" + idNum + "' style='width: 5%; vertical-align: middle''>" + idArray.length +"</td>" +
                    "<td style='background-color: #BEE4F4; width: 10%; color: #31639D; vertical-align: middle''>出題タイプ</td>" +
                    "<td style='width: 25%; vertical-align: middle''>" +
                        "<form>" +
                        "<label class='radio-inline'><input type='radio' id='obj" + idNum + "' name='makeType" + idNum + "' value='obj'>選択肢</label>" +
                        "<label class='radio-inline'><input type='radio' id='sub" + idNum + "' name='makeType" + idNum + "' value='sub'>記述問題</label>" +
                        "</form>" +
                    "</td>" +
                    "<td style='background-color: #BEE4F4; width: 10%; color: #31639D; vertical-align: middle''>問題タイプ</td>" +
                    "<td style='width: 25%; vertical-align: middle''>" +
                        "<form>" +
                        "<label class='radio-inline'><input type='radio' id='voc" + idNum + "' name='quizType" + idNum + "' value='vocabulary'>vocabulary</label>" +
                        "<label class='radio-inline'><input type='radio' id='wor" + idNum + "' name='quizType" + idNum + "' value='word'>word</label>" +
                        "<label class='radio-inline'><input type='radio' id='gra" + idNum + "' name='quizType" + idNum + "' value='grammar'>grammar</label>" +
                        "</form>" +
                    "</td>" +
                    "<td style='width: 5%; vertical-align: middle'' id='deleteNum"+ idNum +"'><button id='deletebuttonDesign' class='btn' style='color: #31639D'>&nbsp;削除&nbsp;</button></td>" +
                "</thead>" +
                "<tbody>" +
                "<tr>" +
                    "<td style='background-color: #BEE4F4; height: 100px; color: #31639D; vertical-align: middle''>問題</td>" +
                    "<td colspan='6'><textarea id='question" + idNum + "' placeholder='ここに問題を書いてください'style='width: 100%; border: 0'>" +
                    addArr.question +
                    "</textarea></td>" +
                "</tr>" +
                "</tbody>" +
                "<tfoot id='addTr" + idNum + "'>" +
                /*"<tr>" +
                    "<td rowspan='2' style='background-color: #d9edf7'>정답</td>" +
                    "<td colspan='3' style='background-color: #EAEAEA'>" +
                    "<input id='right" + idNum + "' type='text' style='width: 100%; background-color: #EAEAEA; border: 0' value='" +
                    addArr.right+ "'></td>" +
                    "<td colspan='3'>" +
                    "<input id='example1" + idNum + "' type='text' style='width: 100%; border: 0' value='" +
                    addArr.example1 +"'></td>" +
                "</tr>" +
                "<tr>" +
                    "<td colspan='3'>" +
                    "<input id='example2" + idNum + "' type='text' style='width: 100%; border: 0' value='" +
                    addArr.example2 +"'></td>" +
                    "<td colspan='3'>" +
                    "<input id='example3" + idNum + "' type='text' style='width: 100%; border: 0' value='" +
                    addArr.example3 +"'></td>" +
                "</tr>" +*/
                "</tfoot>" +
            "</table>" +
            "</div>");


        // 받아온 값(교재 정보 or 수정 문제)으로 라디오버튼 표시
        if(addArr.makeType == "obj") {
            $("#obj" + idNum).attr("checked", true);
            makeTypeRadio[idNum] = addArr.makeType;
            addObj(idNum, addArr);
        }

        else if(addArr.makeType == "sub") {
            $("#sub" + idNum).attr("checked", true);
            makeTypeRadio[idNum] = addArr.makeType;
            addSub(idNum, addArr);
        }

        if(addArr.quizType == "vocabulary") {
            $("#voc" + idNum).attr("checked", true);
            quizTypeRadio[idNum] = addArr.quizType;
        }

        else if(addArr.quizType == "word") {
            $("#wor" + idNum).attr("checked", true);
            quizTypeRadio[idNum] = addArr.quizType;
        }

        else if(addArr.quizType == "grammar") {
            $("#gra" + idNum).attr("checked", true);
            quizTypeRadio[idNum] = addArr.quizType;
        }


        // 라디오버튼 선택 확인 (출제유형)
        $("input[name='makeType" + idNum + "']:radio").change(function () {

            // 테이블 변형 : 주관식일 경우 addSub() 메서드 호출
            if(this.id.slice(0,3) == "sub") {
                makeTypeRadio[this.id.slice(3)] = "sub";
                addSub(this.id.slice(3), addArr);
            }

            // 테이블 변형 : 객관식일 경우 addObj() 메서드 호출
            else if (this.id.slice(0,3) == "obj") {
                makeTypeRadio[this.id.slice(3)] = "obj";
                addObj(this.id.slice(3), addArr);
            }

        });

        // 라디오버튼 선택 확인 (문제유형)
        $("input[name='quizType" + idNum + "']:radio").change(function () {

            if(this.id.slice(0,3) == "voc")
                quizTypeRadio[this.id.slice(3)] = "vocabulary";

            else if(this.id.slice(0,3) == "wor")
                quizTypeRadio[this.id.slice(3)] = "word";

            else if(this.id.slice(0,3) == "gra")
                quizTypeRadio[this.id.slice(3)] = "grammar";
        });


        // 문항 삭제 버튼 클릭 시
        $(document).on('click', '#deleteNum'+idNum, function (e) {
            e.preventDefault();

            $('#tableNum'+ this.id.slice(9)).remove();

            var index = idArray.indexOf(Number(this.id.slice(9)));
            idArray.splice(index, 1);

            var count = 0;

            for(var i in idArray) {

                count++;
                var quizId = $('#quizNum'+ idArray[i]);

                quizId.html(count);
            }
        });
    }

    // 테이블 : 객관식 용 (칸 4개)
    function addObj(idNum, addArr) {
        $('#addTr' + idNum).empty();

        if(addArr.example1 == null) addArr.example1 = "";
        if(addArr.example2 == null) addArr.example2 = "";
        if(addArr.example3 == null) addArr.example3 = "";

        $('#addTr' + idNum).append(
            "<tr>" +
            "<td rowspan='2' style='background-color: #BEE4F4; color: #31639D; vertical-align: middle''>正答</td>" +
            "<td colspan='3' style='background-color: #FFECB8'>" +
            "<input id='right" + idNum + "' type='text' placeholder='ここに正答を書いてください' style='width: 100%; background-color: #FFECB8; border: 0' value='" +
            addArr.right+ "'></td>" +
            "<td colspan='3'>" +
            "<input id='example1" + idNum + "' type='text' placeholder='例1' style='width: 100%; border: 0' value='" +
            addArr.example1 +"'></td>" +
            "</tr>" +
            "<tr>" +
            "<td colspan='3'>" +
            "<input id='example2" + idNum + "' type='text' placeholder='例2' style='width: 100%; border: 0' value='" +
            addArr.example2 +"'></td>" +
            "<td colspan='3'>" +
            "<input id='example3" + idNum + "' type='text' placeholder='例3' style='width: 100%; border: 0' value='" +
            addArr.example3 +"'></td>" +
            "</tr>"
        );
    }

    // 테이블 : 주관식 용 (칸 2개)
    function addSub(idNum, addArr) {
        $('#addTr' + idNum).empty();

        if(addArr.hint == null) addArr.hint = "";

        $('#addTr' + idNum).append(
            "<tr>" +
            "<td rowspan='2' style='background-color: #BEE4F4; color: #31639D; vertical-align: middle'>正答</td>" +
            "<td colspan='3' style='background-color: #FFECB8'>" +
            "<input id='right" + idNum + "' type='text' placeholder='複数正答の場合、、(コンマ)で表示してください' style='width: 100%; background-color: #FFECB8; border: 0' value='" +
            addArr.right+ "'></td>" +
            "<td colspan='3'>" +
            "<input id='hint" + idNum + "' type='text' placeholder='ここにヒントを書いてください' style='width: 100%; border: 0' value='" +
            addArr.hint +"'></td>" +
            "</tr>"
        );
    }

    // ----->  ★퀴즈 불러오기 파트★

    // 폴더, 퀴즈 리스트 저장용 배열
    var folderData = new Array();
    var quizData = new Array();

    // 미리보기 문제 저장용 배열
    var showListData = new Array();

    // 퀴즈 만들기에 추가될 문제 저장용 배열
    var callQuizData = new Array();

    // AJAX : 폴더 리스트 호출
    $.ajax({
        type: 'POST',
        url: "{{url('quizTreeController/getfolderLists')}}",
        //processData: false,
        //contentType: false,
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        //data: {_token: CSRF_TOKEN, 'post':params},
        data: {folderId: '{{$response['folderId']}}'},
        success: function (data) {

            folderData = data;

            for(var i = 0; folderData['folders'].length; i++) {
                $("#folderSelect").append(
                    "<option value='" + folderData['folders'][i]['folderId'] + "'>" + folderData['folders'][i]['folderName'] + "</option>"
                );
            }
        },
        error: function (data) {
            swal("エラー");
        }
    });

    // 선택된 폴더에 있는 퀴즈리스트 호출
    $(document).ready(function () {
        $("#folderSelect").change(function () {
            var selectedFolder = $("#folderSelect :selected").val();

            $.ajax({
                type: 'POST',
                url: "{{url('quizTreeController/getfolderLists')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                //data: {_token: CSRF_TOKEN, 'post':params},
                data: {folderId: selectedFolder},
                success: function (data) {

                    quizData = data;

                    // 퀴즈 선택란 쌓이는 것 방지 : 비워주기
                    $("#quizSelect").empty();
                    $("#quizSelect").append("<option>クイズ名</option>");

                    for(var i = quizData['lists'].length - 1; i >= 0; i--) {
                        $("#quizSelect").append(
                            "<option value='" + quizData['lists'][i]['listId'] + "'>" + quizData['lists'][i]['listName'] + "</option>"
                        );
                    }
                },
                error: function (data) {
                    swal("エラー");
                }
            });
        });
    });

    // 선택된 퀴즈 -> 미리보기
    var selectedQuiz;

    $(document).ready(function () {
        $("#quizSelect").change(function () {
            selectedQuiz = $("#quizSelect :selected").val();

            $.ajax({
                type: 'POST',
                url: "{{url('quizTreeController/showList')}}",
                //processData: false,
                //contentType: false,
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                //data: {_token: CSRF_TOKEN, 'post':params},
                data: {listId: selectedQuiz},
                success: function (data) {

                    showListData = data;

                    // 쌓이는 문제 데이터 비우기
                    $("#quizShow").empty();
                    $("#quizShow").append("<h4 align='center'>▼　プレビュー　▼</h4>");

                    var str = "";
                    var questionId = 0;

                    for(var i = showListData['quizs'].length-1 ; i >= 0 ; i--) {

                        questionId++;

                        if (showListData['quizs'][i]['hint'] == null) showListData['quizs'][i]['hint'] = "";
                        if (showListData['quizs'][i]['example1'] == null) showListData['quizs'][i]['example1'] = "";
                        if (showListData['quizs'][i]['example2'] == null) showListData['quizs'][i]['example2'] = "";
                        if (showListData['quizs'][i]['example3'] == null) showListData['quizs'][i]['example3'] = "";
                        if (showListData['quizs'][i]['quizType'] == "vocabulary") showListData['quizs'][i]['quizType'] = "vocabulary";
                        if (showListData['quizs'][i]['quizType'] == "word") showListData['quizs'][i]['quizType'] = "word";
                        if (showListData['quizs'][i]['quizType'] == "grammar") showListData['quizs'][i]['quizType'] = "grammar";

                        if (showListData['quizs'][i]['makeType'] == "obj") {
                            str += "<table class='table table-bordered'>";
                            str += "<tr>";
                            str += "<td style='text-align: center;'>" + questionId + "</td>";
                            str += "<td style='background-color: #d9edf7; width: 22.5%; text-align: center'>出題タイプ</td>";
                            str += "<td style='width: 22.5%; text-align: center'>選択肢</td>";
                            str += "<td style='background-color: #d9edf7; width: 22.5%; text-align: center'>問題タイプ</td>";
                            str += "<td style='width: 22.5%; text-align: center'>" + showListData['quizs'][i]['quizType'] + "</td>";
                            str += "</tr>";
                            str += "<tr>";
                            str += "<td style='background-color: #d9edf7; text-align: center'>問題</td>";
                            str += "<td colspan='5'>" + showListData['quizs'][i]['question'] + "</td>";
                            str += "</tr>";
                            str += "<tr>";
                            str += "<td style='background-color: #d9edf7; text-align: center'>正答</td>";
                            str += "<td style='background-color: #EAEAEA'>" + showListData['quizs'][i]['right'] + "</td>";
                            str += "<td>" + showListData['quizs'][i]['example1'] + "</td>";
                            str += "<td>" + showListData['quizs'][i]['example2'] + "</td>";
                            str += "<td>" + showListData['quizs'][i]['example3'] + "</td>";
                            str += "</tr>";
                            str += "</table>";
                        }

                        else if (showListData['quizs'][i]['makeType'] == "sub") {
                            str += "<table class='table table-bordered'>";
                            str += "<tr>";
                            str += "<td style='text-align: center;'>" + questionId + "</td>";
                            str += "<td style='background-color: #d9edf7; width: 22.5%; text-align: center'>出題タイプ</td>";
                            str += "<td style='width: 22.5%; text-align: center'>記述問題</td>";
                            str += "<td style='background-color: #d9edf7; width: 22.5%; text-align: center'>問題タイプ</td>";
                            str += "<td style='width: 22.5%; text-align: center'>" + showListData['quizs'][i]['quizType'] + "</td>";
                            str += "</tr>";
                            str += "<tr>";
                            str += "<td style='background-color: #d9edf7; text-align: center'>問題</td>";
                            str += "<td colspan='5'>" + showListData['quizs'][i]['question'] + "</td>";
                            str += "</tr>";
                            str += "<tr>";
                            str += "<td style='background-color: #d9edf7; text-align: center'>正答</td>";
                            str += "<td colspan='2' style='background-color: #EAEAEA;'>" + showListData['quizs'][i]['right'] + "</td>";
                            str += "<td colspan='2'><ヒント> " + showListData['quizs'][i]['hint'] + "</td>";
                            str += "</tr>";
                            str += "</table>";
                        }
                    }

                    $("#quizShow").append(str);

                },
                error: function (data) {
                    swal("エラー");
                }
            });
        })
    });

    // 모달 : 불러오기 버튼 클릭 시
    function callQuiz() {
        $.ajax({
            type: 'POST',
            url: "{{url('quizTreeController/showList')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: {listId: selectedQuiz},
            success: function (data) {

                callQuizData = data;

                for(var i = callQuizData['quizs'].length - 1; i >= 0; i--) {

                    var callQuizArray = {
                        question: callQuizData['quizs'][i]['question'],
                        right: callQuizData['quizs'][i]['right'],
                        example1: callQuizData['quizs'][i]['example1'],
                        example2: callQuizData['quizs'][i]['example2'],
                        example3: callQuizData['quizs'][i]['example3'],
                        makeType: callQuizData['quizs'][i]['makeType'],
                        quizType: callQuizData['quizs'][i]['quizType'],
                        hint: callQuizData['quizs'][i]['hint']
                    };

                    quizAdd(callQuizArray);
                }
            },
            error: function (data) {
                swal("エラー");
            }
        });
    }

    // <----- ★퀴즈 불러오기 파트 끝★

    // 문항 추가 버튼 클릭 시 + 퀴즈 생성(BODY ONLOAD)
    $(document).on('click', '#add', function (e) {
        e.preventDefault();

        // 새로운 퀴즈
        var emptyArray = {
            question: "",
            right: "",
            example1: "",
            example2: "",
            example3: "",
            makeType: "obj",
            quizType: "",
            hint: ""
        };

        quizAdd(emptyArray);

    });

    // 퀴즈 저장 버튼 클릭 시
    $(document).on('click', '#save', function (e) {
        e.preventDefault();

        // list 아이디
        var listId = "{{$response['listId']}}";

        // list 이름
        var listName = $('#listName').val();

        // folder 아이디
        var folderId = "{{$response['folderId']}}";

        var quizs = new Array();

        for (var i in idArray) {
            quizs.push({
                question: $('#question' + idArray[i]).val(),
                right: $('#right' + idArray[i]).val(),
                example1: $('#example1' + idArray[i]).val(),
                example2: $('#example2' + idArray[i]).val(),
                example3: $('#example3' + idArray[i]).val(),
                makeType: makeTypeRadio[idArray[i]],
                quizType: quizTypeRadio[idArray[i]],
                hint: $('#hint' + idArray[i]).val()
            });
        }

        var params = {
            listId: listId,
            listName: listName,
            folderId: folderId,
            quizs: quizs
        };

        // controller로 data send
        $.ajax({
            type: 'POST',
            url: "{{url('quizTreeController/insertList')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: params,
            success: function (data) {
                if(data.check == true) {
                    swal("保存完了");
                    window.location.href = "{{url('quiz_list')}}";
                }
                else {
                    swal("空いている部分を埋めてください");
                    //alert(JSON.stringify(data));
                }
            },
            error: function (data) {
                swal("空いている部分を埋めてください");
            }
        });

    });

    // 수정 퀴즈(BODY ONLOAD) : 기존에 저장된 문제 목록 불러오기
    function updateQuiz() {

        @for($i = count($response['quizs']) - 1; $i >= 0; $i--)

        var updateArray = {
            question: "{{$response['quizs'][$i]['question']}}",
            right: "{{$response['quizs'][$i]['right']}}",
            example1: "{{$response['quizs'][$i]['example1']}}",
            example2: "{{$response['quizs'][$i]['example2']}}",
            example3: "{{$response['quizs'][$i]['example3']}}",
            makeType: "{{$response['quizs'][$i]['makeType']}}",
            quizType: "{{$response['quizs'][$i]['quizType']}}",
            hint: "{{$response['quizs'][$i]['hint']}}"
        };

        quizAdd(updateArray);

        @endfor
    };

    // 목록으로 버튼 클릭 시 : quiz_list로 돌아가기
    function backToList() {
        //모달창으로 확인하기
        window.location.href = "{{url('quiz_list')}}";
    }

</script>

<!-- 본문 -->
<div class="contents">

    <!-- 노랑 타이틀 -->
    <div id="titleImg">

        <!-- Quiz Tree -->
        <div style="padding-top: 10px">
            <p style="margin-left: 5%; font-size: 50px; color: white">Quiz Tree</p>
        </div>

        <div class="form-inline" style="margin-left: 5%">
            <div style="float: left">
                <!--1. 퀴즈를 새로 생성할 경우-->
                @if(count($response['quizs']) == 0)
                    <p id="quizFont">クイズ名 : <input type="text" id="listName" class="form-control" style="width: 40em; height: 40px; border-radius: 12px; font-size: 20px"></p>

                    <!--2. 퀴즈를 수정할 경우-->
                @elseif(count($response['quizs']) > 0)
                    <p id="quizFont">クイズ名 : <input type="text" id="listName" class="form-control" style="width: 40em; height: 40px; border-radius: 12px" value="{{$response['listName']}}"></p>

                @endif
            </div>
            <div>&nbsp;
                <button id="buttonDesign" class="btn" style="color: white;" data-toggle="modal" data-target="#callQuizModal">+ クイズ呼び出す</button>
            </div>
        </div>
    </div>



    <!--문제 박스 : div-->
    <div class="quizBox">

    </div>

    <div class="threebuttonDesignT">
        <button type="button" class="threebuttonDesign" data-toggle="modal" data-target="#backToList" style="color: #31639D">&nbsp;戻る&nbsp;</button>
        <button type="button" class="threebuttonDesign" id="save" style="color: #31639D">&nbsp;保存&nbsp;</button>
        <button type="button" class="threebuttonDesign" id="add" style="color: #31639D">&nbsp;追加&nbsp;</button>
    </div>

</div>

<!-- Modal : call Quiz -->
<div class="modal fade" id="callQuizModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center">
                <h5 class="modal-title" id="ModalLabel" style="font-size: 20px">過去のクイズ</h5>
            </div>
            <div class="modal-body">
                {{--Dropdowns--}}
                <div style="text-align: center">
                    <div class="selectCall" style="margin: 0 auto; width: 50%">
                        <select id="folderSelect" class="form-control" style="height:40px; font-size: 20px">
                            <option>フォルダ名</option>
                            <!-- 폴더 리스트 -->
                        </select>
                    </div>
                    <div class="selectCall" style="margin: 0 auto; margin-top: 1%; width: 50%">
                        <select id="quizSelect" class="form-control" style="height:40px; font-size: 20px">
                            <option>クイズ名</option>
                            <!-- 퀴즈 리스트 -->
                        </select>
                    </div>
                </div>

                <div id="quizShow" style="margin-top: 2%">
                    <h4 align="center" style="font-size: 20px">▼プリビュー▼</h4>
                    <!-- 퀴즈 미리보기-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="callQuiz()" data-dismiss="modal">確認</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">戻る</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal : back to List -->
<div class="modal fade" id="backToList">
    <div class="modal-dialog">
        <input type="hidden" name="folderName" id="folderName" value="">
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body" style="text-align: center">
                クイズが保存していません. リストに戻りますか？
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" onclick="backToList()">確認</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
            </div>
        </div>
    </div>
</div>
