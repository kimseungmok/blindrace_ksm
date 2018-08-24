<!DOCTYPE html>
<html>
<title>My group</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Nanum+Gothic" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<body style="background-color: #f9f9f9"  >
<!--그룹이 아무것도 없을때의 경우를 생각하지않았음 -->

<!-- Sidebar -->
{{--<div class="w3-sidebar w3-bar-block w3-light-grey w3-card"--}}
{{--style="width:20%"> <form> <input type="text" name="search" placeholder="학생 찿기"--}}
{{--class="input"> </form> <button type="button" class="w3-bar-item w3-button"--}}
{{--data-toggle="modal" data-target="#create"> 클래스 생성 </button> <p></p> <div--}}
{{--class="w3-dropdown-hover"> <h2>나의 클래스</h2> <button class="w3-button">클래스 <i--}}
{{--class="fa fa-caret-down"></i> </button> <a href="#" class="w3-bar-item--}}
{{--w3-button">A반</a> <a href="#" class="w3-bar-item w3-button">B반</a> <a href="#"--}}
{{--class="w3-bar-item w3-button">C반</a> <a href="#" class="w3-bar-item--}}
{{--w3-button">D반</a> </div> </div>--}}

<!-- Page Content -->

<div >
    <div class="jumbotrons">

        <!-- Button trigger modal -->


        <!-- Modal -->
        <div class="modal fade" id="studnetchange" tabindex="-1" role="dialog" aria-labelledby="studnetchange1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-body">
                        {{--<label for="studentnumber"><b>이름</b> </label>--}}
                        {{--<input type="text"  name="studentnumber"  id="studentnumbers"  required>--}}



                        {{--<label for="name"><b>이름</b></label>--}}
                        {{--<input type="text"  name="name" id="studentnames" required>--}}
                        {{--<input type="hidden">--}}
                        {{--<p></p>--}}

                        {{--<input id="checkBox" type="checkbox">--}}

                        <input type="text" size="40px" placeholder="新しいパスワードを入力してください" id="psw" required>
                        <button class="btn btn-primary-outline btn-round-lg " style="border: 1px solid white ; margin-left: 7px " type="button" onclick="update('#')">変更</button>
                    </div>

                </div>
            </div>
        </div>

        <form class="form-inline">
            <div class="form-group"></div>
            <div class="form-group"></div>
        </form>
        <!-- <div class="fa-3x">
            <i class="fas fa-cog fa-spin light" data-toggle="modal" data-target="#teacher"></i>
        </div> -->


        <i><p style="margin-left : 15px ; font-size: 40px; color:white;" id ="teacher"></p></i>
        <i><p style="margin-left : 15px ; font-size: 80px ; color:white; " id ="group" class="in" ></p></i>
        <button type="button" style="margin-left: 15px ; color:white;" class="btn btn-primary-outline btn-round-lg btn-lg " data-toggle="modal";  data-target="#exampleModal">
            <img src="/img/exel.png" style="display:inline-block; width:50px; height:50px;" alt="">
            + 学生登録</button>
        <img style="position: absolute; top: 0% ; right: 0% " src="https://i.imgur.com/J8qUt6P.png">
        <!-- Button trigger modal -->


        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div >
                        <textarea id="cmemo" cols="55" rows="10"></textarea>
                        <label for="firstChk"><input type="hidden" id="firstChk" value="1" onclick="enterTabTable('cmemo','cview')"></label>
                        {{--<button type="button" onclick="expBasicData('cmemo','cview')">예시 보기</button>--}}
                        {{--<button style="border: 1px solid white; margin-top: 10px;" class="btn btn-primary-outline btn-round-lg btn-sm " type="button" onclick="enterTabTable('cmemo','cview')">確認</button>--}}
                        <button style="border: 1px solid white; margin-left: 10px; margin-top: 10px" class="btn btn-primary-outline btn-round-lg btn-sm " type="button" onclick="excel('cview')">確認</button>
                        <div id="cview"></div>

                    </div>


                </div>

            </div>
        </div>
    </div>
    <div style="width : 100% ; height: 20px ; background-color: #9fcdff"></div>

</div>

</div>



<div class="row">
    <div class="col col-lg-5" style="padding-left: 15px;">

        <div>
            <p class="in pen" style="margin-right: 10px">所属していない学生</p>
            <input
                    class="margins"
                    type="text"
                    id="myInput"
                    onkeyup="myFunction()"
                    placeholder="学生検索"
                    title="Type in a name"

                    value="">
            <input type="button"
                   style="margin-left: 10px"
                   class="btn btn-primary-outline btn-round-lg btn-sm in" id="selectBtn" value="登録">

            <table>
                <tr class="header">
                    <th style="width:42%"><i class="fas fa-user-circle"></i>  名前</th>
                    <th style="width:23%"><i class="fas fa-clipboard-list"></i> 学籍番号</th>
                    <th style="width:8%"><i class="fas fa-user-plus"></i> <input type="checkbox" id="allCheck"/></th>
                </tr>
            </table>
            <table id="myTable">
            </table>

            {{--<input type="button" id="selectBtn" value="추가">--}}
        </div>
    </div>
    <div class="col col-lg-1" >
        <button class="centerbutton"></button>
    </div>
    <div class="col col-lg-5">

        <p class="pen">現在のクラス学生</p>
        <table>
            <tr class="header" style="border-top: 1px solid gray;">
                <th style="width:34%"><i class="fas fa-user-circle"></i>  名前</th>
                <th style="width:25%"><i class="fas fa-clipboard-list"></i> 学籍番号</th>
                <th style="width:35%;"><i class="fas fa-info"></i> パスワード</th>
                <th style="width:25%;"></th>
            </tr>
        </table>

        <table id="student">
            {{--<tr>--}}
            {{--<th>--}}
            {{--<input type="checkbox"/>クラス</th>--}}
            {{--<th>名前</th>--}}
            {{--<th>学番</th>--}}
            {{--<th>레코드 박스</th>--}}
            {{--</tr>--}}
            {{--<tr>--}}
            {{--<td><input type="checkbox"/>B반--}}
            {{--<button>X</button>--}}
            {{--</td>--}}
            {{--<td  data-toggle="modal" data-target="#studnetsetting">안준휘</td>--}}
            {{--<td>1401036</td>--}}
            {{--<td>확인</td>--}}
            {{--</tr>--}}

        </table>

    </div>
</div>







<style>
    p ,div ,th ,tr  {
        font-family: 'Nanum Gothic', sans-serif;
    }
    .centerbutton{
        width: 100% ;
        height: 100% ;
        background-size: contain;
        background-image: url("https://i.imgur.com/NYAOWGv.png");
        border: 0px;
    }
    table {
        font-family: arial, sans-serif;
        width: 100%;
        border-spacing: 0px 6px !important;
    }
    .fade:not(.show) {
        opacity: 10;
    }

    .container {

        padding-right: 0px;
        padding-left: 0px;

    }
    .col-lg-5{
        padding-right: 0px;
        padding-left: 0px;
    }
    .col-lg-1 {


        background-image: url("https://i.imgur.com/NYAOWGv.png");
        background-size: auto;
        padding-right: 0px;
        padding-left: 0px;
    }
    .row {



    }
    .pen {
        margin-left: 20px;
        margin-top: 10px;
        font-size: 20px;
        color : #203a8e; !important;
    }
    th {
        background-color: #DFDFDF ; !important;
    }

    .in {
        display: inline-block;
    }
    .ma {
        margin-left: 80%;
        margin-top: 10px;
    }
    .jumbotrons{
        padding-right: 0px;
        padding-left: 0px;
        height: 260px; !important;
        width: 100%; !important;
        background-color: #9E7AC2 ;
        background-size: contain;

    }
    .btn-primary-outline {
        background-color: transparent;
        border-color: #ccc;
    }
    .btn-round-lg{
        border-radius: 20.5px;

    }

    button {
        display: inline-block;
    }



    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }


    tr:nth-child(even) {
        background-color: #e6eaed;
    }

    tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .input[type=text] {
        width: 130px;
        box-sizing: border-box;
        border: 2px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        background-color: white;
        background-image: url("https://i.imgur.com/LCkVIxO.png");
        background-position: 10px 10px;
        background-repeat: no-repeat;
        padding: 12px 20px 12px 40px;
        -webkit-transition: width 0.4s ease-in-out;
        transition: width 0.4s ease-in-out;
    }

    .input[type=text]:focus {
        width: 100%;
    }

    /* The Modal (background) */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        padding-top: 100px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0,0,0);
        /* Fallback color */
        background-color: rgba(0,0,0,0.4);
        /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        position: relative;
        background-color: #fefefe;
        margin: auto;
        padding: 0;
        border: 1px solid #888;
        width: 80%;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
        -webkit-animation-name: animatetop;
        -webkit-animation-duration: 0.4s;
        animation-name: animatetop;
        animation-duration: 0.4s;
    }

    /* Add Animation */
    @-webkit-keyframes animatetop {
        from {
            top: -300px;
            opacity: 0;
        }
        to {
            top: 0;
            opacity: 1;
        }
    }

    @keyframes animatetop {
        from {
            top: -300px;
            opacity: 0;
        }
        to {
            top: 0;
            opacity: 1;
        }
    }

    /* The Close Button */
    .close {
        color: white;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:focus,
    .close:hover {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-header {
        padding: 2px 16px;
        background-color: #5cb85c;
        color: white;
    }

    .modal-body {
        padding: 2px 16px;
    }

    .modal-footer {
        padding: 2px 16px;
        background-color: #5cb85c;
        color: white;
    }
    .modal-backdrop {
        position: relative !important;
    }
    .light {
        margin-left: 840px;
    }
    .inline {
        display: inline-block;
    }
    p, div, th, tr, td, button, a {
        font-family: 'Meiryo UI' ; !important;
    }

    ul {
        font-family: 'Meiryo UI'; !important;
    }
</style>

<script>

    //학생 정보 수정
    function update() {


        var userName = document.getElementById("studentnumbers").value;
        var password = document.getElementById("psw").value;




        var postData =
            {
                userId :    userName,
                password :  password

            }



//            alert(JSON.stringify(postData));

//            studentnumbers studentnames checkBox psw
        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/studentModify')}}",
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: postData ,
            success: function (data) {
//                    alert(JSON.stringify(postData))
                window.location.href = "{{url('mygroup')}}";
            },
            error: function (data) {
                alert("수정 실패");
            }
        });
    }






    function expBasicData(obj,obj2) {
        var sampleData = "1301036\t 김민수\n";
        sampleData += "1301032\t 박민수\n";
        sampleData += "1301033\t 최민수\n";
        sampleData += "1301030\t 공민수\n";
        document.getElementById(obj).value = sampleData;

        enterTabTable(obj,obj2);
    }

    function enterTabTable(obj,obj2) {
        var i, k, ftag, str="";
        var text = document.getElementById(obj).value;
        var arr = text.split("\n"); // 엔터키로 분리
        if(text.length > 2) {
            str += "<table border='1' cellpadding='3' cellspacing='1'>\n";
            str += "<tbody>\n";
            for(i=0; i < arr.length; i++) {
                var sub_arr = arr[i].split("\t"); // 탭키로 분리
                if(sub_arr.length==2) {
                    ftag = (document.getElementById("firstChk").checked == true) ? (i == 0) ? "No" : i : (i + 1);
                    str += "<tr>\n";
                    str += "</td>\n";


                    for (k = 0; k < sub_arr.length; k++) {

                        str += "<td>" + sub_arr[k] + "</td>\n";

                    }
                }
            }

            str += "</tbody>\n";
            str += "</table>\n";

        }
        document.getElementById(obj2).innerHTML = str;

    }

    function excel() {

        var text = document.getElementById('cmemo').value;
        var arr = text.split("\n"); // 엔터키로 분리
        var studentlist =new Array();

        if(text.length > 0) {
            for (var i = 0; i < arr.length; i++) {
                var sub_arr = arr[i].split("\t"); // 탭키로 분리
                if (sub_arr.length == 2) {
                    studentlist.push({
                        id:sub_arr[0],
                        name:sub_arr[1]
                    }) ;
                }
            }
        }


        var excelstudent = document.getElementById("cview").value;

        var params = {
            groupId : groupIds,
            students :JSON.stringify(studentlist)
        };
//            alert(JSON.stringify(studentlist))
        swal("登録完了", "success");
        setTimeout(function(){ window.location.href = "{{url('mygroup')}}"; },1500);

        jQuery.ajaxSettings.traditional = true;


        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/pushInvitation')}}",
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data:params,
            success: function (data) {

//                    alert(JSON.stringify(data));

            },
            error: function (data) {
                alert("엑셀등록 에러! 올바르게 입력하세요");
            }
        });
    }

</script>
</body>
</html>