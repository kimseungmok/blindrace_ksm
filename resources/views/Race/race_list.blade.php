<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
</head>

<style type="text/css">

    p, div, th, tr, td, button {
        font-family: 'a뉴고딕M';
    }

    #Race{
        background:#00008B;
        border: solid 1px #1976D2;

        box-shadow: 1px 0px 0px #1976D2,0px 1px 0px #1976D2,
        2px 1px 0px #1976D2,1px 2px 0px #1976D2,
        3px 2px 0px #1976D2,2px 3px 0px #1976D2,
        4px 3px 0px #1976D2,3px 4px 0px #1976D2,
        5px 4px 0px #1976D2,4px 5px 0px #1976D2,
        6px 5px 0px #1976D2,5px 6px 0px #1976D2,
        7px 6px 0px #1976D2,6px 7px 0px #1976D2,
        8px 7px 0px #1976D2,7px 8px 0px #1976D2,
        9px 8px 0px #1976D2,8px 9px 0px #1976D2;
    }
    #Race:hover, #Race:active, #Race:active:focus{
        background:#00008B;
    }
    #Exam{
        background:#9b59b6;
        border: solid 1px #8e44ad;

        box-shadow: 1px 0px 0px #8e44ad,0px 1px 0px #8e44ad,
        2px 1px 0px #8e44ad,1px 2px 0px #8e44ad,
        3px 2px 0px #8e44ad,2px 3px 0px #8e44ad,
        4px 3px 0px #8e44ad,3px 4px 0px #8e44ad,
        5px 4px 0px #8e44ad,4px 5px 0px #8e44ad,
        6px 5px 0px #8e44ad,5px 6px 0px #8e44ad,
        7px 6px 0px #8e44ad,6px 7px 0px #8e44ad,
        8px 7px 0px #8e44ad,7px 8px 0px #8e44ad,
        9px 8px 0px #8e44ad,8px 9px 0px #8e44ad;
    }
    #Exam:hover , #Exam:active, #Exam:active:focus{
        background: #800080;
    }

    .race_menu_button{
        width:130px;
        height:130px;
        display:inline-block;

        transition: margin-top 0.3s ease,
        margin-left 0.3s ease,
        box-shadow 0.3s ease;

        margin-top:5%;
        margin-right:1%;
    }
    .race_menu_button:active{
        transition: margin-top 0.3s ease;

        /*margin-left:9px;*/
        margin-right:9px;
        margin-top:9px;
        box-shadow: 0px 0px 0px #1976D2;
    }


    .menu_time_img{
        width:50px;
        heihgt:50px;
    }
    .race_menu_img{
        width:70px;
        height:70px;
    }
    .race_menu_span{
        font-size:20px;
        color:white;
    }

    #wrapper {
        margin: 0 0 0 220px;
        /*padding: 0;*/
        /*position: relative;*/
        /*min-height: 705px;*/
        /*min-width: 1000px;*/
    }

    .folderButton {
        background-image: url("https://i.imgur.com/Wf95GDk.png'");
        background-size: 100%;
        border:1px solid transparent !important;
        padding: 8px;
        margin-top: -1px;
        width: 100%;
        font-family: arial, sans-serif;
        border-collapse: collapse; !important;
        background-size: cover;
        border-spacing: 0px 0px !important;
    }

    #titleImg {
        background-image: url("https://i.imgur.com/G7fmmiJ.png");
        background-size: 100%;
        background-repeat: no-repeat;
        height: 9.5vw;
    }

    #theadFont {
        font-family: 'a뉴고딕M';
        font-size: 22px;
        text-align: center;
    }

    #tbodyFont {
        font-family: 'a뉴고딕M';
        font-size: 20px;
        text-align: center;
        vertical-align: middle;
    }
</style>

<script>

    // 그룹 데이터 저장용 배열
    var groupData = new Array();

    function groupGet(){
        $.ajax({
            type: 'POST',
            url: "{{url('groupController/groupsGet')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: "",
            success: function (data) {
                groupData = data;

                for(var i =  groupData['groups'].length-1; i >= 0; i--) {
                    $("#groupSelect").append(
                        "<option value='" + groupData['groups'][i]['groupId'] + "'>" + groupData['groups'][i]['groupName'] + "</option>"
                    );
                }
            },
            error: function (data) {
                alert("error");
            }
        });
    }
    // 모달로 넘기는 그룹 선택 파트
    $(document).ready(function () {

        // 선택된 그룹 id 값 넘기기
        $('#groupSelect').change(function () {
            var selectedText = $("#groupSelect :selected").val();

            var groupIdObj = document.getElementById("groupId");
            groupIdObj.value = selectedText;
        });

        //모드에서 레이스 클릭
        $('#Race').click(function(){
            $('#raceType').val("race");
            //다른 버튼들은 원래 색으로 돌려놓는 부분
            $('#Re-Test').css("background","#ff69b4");
            $('#Exam').css("background","#9b59b6");

            //눌렸을떄의 색상
            $('#Race').css("background","#00008B");
        });

        //모드에서 쪽지시험 클릭
        $('#Exam').click(function(){
            $('#raceType').val("popQuiz");
            //다른 버튼들은 원래 색으로 돌려놓는 부분
            $('#Re-Test').css("background","#ff69b4");
            $('#Race').css("background","#03A9F4");
            //눌렸을떄의 색상
            $('#Exam').css("background","#800080");
        });


    });

    // 레이스 시작할 때 해당 리스트 id값 전달
    function sendId(listId) {
        var listIdObj = document.getElementById("listId");
        listIdObj.value = listId;
    }

    // folder, list 정보 저장용 배열
    var folderListData = new Array();
    var quizlistData = new Array();

    // BODY ONLOAD : 컨트롤러로부터 폴더 정보를 불러오기 위한 AJAX
    function getFolderValue() {

        // 폴더 리스트만 띠우기 위한거기 때문에 folderId 값은 상관없음
        var params = {
            folderId: 0
        };

        $.ajax({
            type: 'POST',
            url: "{{url('quizTreeController/getfolderLists')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: params,
            success: function (data) {

                // 최신 폴더 id값
                var index =  data['folders'].length-1;

                //최신 폴더 & 최신 리스트 불러오기
                getFolderListValue(data['folders'][index]['folderId']);
            },
            error: function (data) {
                alert("error");
            }
        });
    }

    // 컨트롤러로부터 폴더 & 리스트 정보를 불러오기 위한 AJAX
    function getFolderListValue(idNum) {

        // 폴더 데이터 초기화
        folderListData = [];

        // 퀴즈 데이터 초기와
        quizlistData = [];

        var params = {
            folderId: idNum
        };

        $.ajax({
            type: 'POST',
            url: "{{url('quizTreeController/getfolderLists')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: params,
            success: function (data) {

                folderListData = data;
                quizlistData = data;

                $("#folderTitle").empty();

                for(var i = 0; i < folderListData['folders'].length; i++) {
                    if(folderListData['folders'][i]['folderId'] == folderListData['selectFolder']) {
                        $("#folderTitle").append(
                            "<p style='margin-left: 2%; font-size: 50px; color: white'>" + folderListData['folders'][i]['folderName'] + "</p>"
                        );
                    }
                }

                listValue();
            },
            error: function (data) {
                alert("error");
            }
        });

    }

    // AJAX 통신 성공시 호출되는 메서드 : 리스트 정보를 보여줌
    function listValue() {

        // 폴더 & 퀴즈 값이 쌓이지 않게 초기화
        $("#folderList").empty();
        $("#list").empty();

        // <----- 폴더 리스트 ----->
        for(var i = folderListData['folders'].length - 1; i >= 0; i--) {

            if(folderListData['folders'][i]['folderId'] == 0) {
                $("#folderList").append(
                    "<li><a href='#' class='folderButton' onclick='getFolderListValue(" + folderListData['folders'][i]['folderId'] + ")'><span class='fa fa-users'></span>" + " " + folderListData['folders'][i]['folderName'] + "</a></li>"
                );
            }
            else {
                $("#folderList").append(
                    "<li><a href='#' class='folderButton' onclick='getFolderListValue(" + folderListData['folders'][i]['folderId'] + ")'>" + folderListData['folders'][i]['folderName'] + "</a></li>"
                );
            }
        }

        // <----- 퀴즈 리스트 ----->
        for(var i = 0; i < quizlistData['lists'].length; i++) {
            $("#list").append(
                "<tr>" +
                "<td id='tbodyFont' class='hidden-xs'>"+ quizlistData['lists'][i]['listId'] + "</td>" +
                "<td id='tbodyFont'>"+ quizlistData['lists'][i]['listName'] + "</td>" +
                "<td id='tbodyFont'>"+ quizlistData['lists'][i]['quizCount'] + "</td>" +
                "<td id='tbodyFont'>" +
                "<button type='submit' class='btn btn-primary' data-toggle='modal' data-target='#Modal' " +
                "onclick='sendId("+ quizlistData['lists'][i]['listId'] +")'>Play</button>" +
                "</td>" +
                "</tr>");

        }
    }

</script>

<body onload="getFolderValue(); groupGet();">

<nav>
    @include('Navigation.main_nav')
</nav>

<div>
    <!-- 사이드 바 -->
    <div class="side-menu">
        <aside class="navbar navbar-default" role="navigation">
            @include('Race.race_sidebar')
        </aside>
    </div>

    <!-- 본문 -->
    <div id="wrapper">
        <!-- 하늘색 타이틀 -->
        <div id ="titleImg">

            <!-- 현재 폴더 이름 -->
            <div style="padding-top: 40px" id="folderTitle">
            </div>

        </div>

        <div style="width : 100% ; height: 20px ; background-color: #f8efc0"></div>

        <!-- 퀴즈 리스트 -->
        <div class="">
            <table class="table">
                <thead>
                <tr>
                    <th id="theadFont" class="hidden-xs">#</th>
                    <th id="theadFont">クイズ名前</th>
                    <th id="theadFont">問題数</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="list">
                <!--list 공간-->
                </tbody>
            </table>
        </div>
    </div>
</div>

{{--Modal : select group--}}
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{url('raceController/createRace')}}"  method="Post" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="groupId" id="groupId" value="">
            <input type="hidden" name="raceType" id="raceType" value="race">
            <input type="hidden" name="listId" id="listId" value="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Group Select</h5>
                </div>
                <div class="modal-body" style="text-align: center" >
                    <!--Dropdowns-->
                    <div class="select" style="margin: 0 auto; width: 50%">
                        <select id="groupSelect" class="form-control">
                            <option>グル-プ名前</option>
                            <!-- 그룹 목록 넣을 공간 -->
                            {{--<option value="1">특강 A반</option>--}}
                            {{--<option value="2">특강 B반</option>--}}
                        </select>
                    </div>
                    <div class="form-inline" style="margin: 0 auto; width: 50%; margin-top: 1em; margin-bottom: 1em;">
                        <input id="cutLineScore" name="passingMark" type="text" placeholder="カットライン点数" class="form-control" style="width: 100%;">
                    </div>

                    <div id="race_menu">
                        <span style="text-align: center; display:block;"><b>Mode Select</b></span>
                        <span style="text-align: center; display:block;">(プレイするモードをクリックしてください。) </span>

                        <label class="race_menu_button" id="Race">
                            <img class="menu_time_img" src="/img/race_student/Realtime.png" alt=""><br>
                            <img  class="race_menu_img" src="/img/race_student/blind_race.png" alt=""><br>
                            <span class="race_menu_span">Race</span>
                            <input class="magic-radio" type="radio" name="race_mode"/>
                        </label>


                        <label class="race_menu_button" id="Exam" >
                            <img class="menu_time_img" src="/img/race_student/Realtime.png" alt=""><br>
                            <img  class="race_menu_img" src="/img/race_student/exam.png" alt=""><br>
                            <span class="race_menu_span">PopQuiz</span>
                            <input class="magic-radio" type="radio" name="race_mode"/>
                        </label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">確認</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">戻る</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

</body>
</html>