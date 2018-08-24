<html>
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Nanum+Gothic" rel="stylesheet">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MY CLASS</title>

    <!-- Bootstrap CSS CDN -->
    <link
            rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <style>


        .button1:hover {
            opacity: 0.6 !important;

        }
        .table_color {
            background-image: url("https://i.imgur.com/fFksbHc.png"); !important;
            background-size: 100%;
        }
        body {
            font-family: arial, sans-serif;
            background-color: white;
            font-size: 13px;
            color: #5f5f5f;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
            width: 100%;

        }
        body.disabled {
            overflow: hidden;
        }

        .main-body {
            max-width: 1220px;
            min-width: 955px;
            /*     overflow: hidden; */
            margin: 0 auto;
            position: relative;
            height: 1024px;
        }
        .page-small .main-body {
            max-width: 768px;
            min-width: 320px;
        }

        #wrapper {
            margin: 0 0 0 220px;
            padding: 0;
            transition: all 0.4s ease 0s;
            position: relative;
            /*     min-height: 100% */
            min-height: 705px;
            min-width: 1000px;
        }

        #menu-main {
            width: 220px;
            hegight: 100%;
            left: 0;
            bottom: 0;
            float: left;
            position: fixed;
            /*     min-height: 1000px; */
            top: 0;
            transition: all 0.4s ease 0s;
            background-image: url("https://i.imgur.com/14SXK4U.png");
            background-size: cover;
            border-left: 1px solid #e1e2e3;
            border-right: 1px solid #e1e2e3;
            border-bottom: 1px solid #e1e2e3;
        }

        .btn-primary-outline {
            background-color: transparent;
            border-color: #ccc;
        }
        .btn-round-lg{
            border-radius: 20.5px;
        }
        p, div, th, tr, td, button, a , i {
            font-family: 'Meiryo UI' ; !important;
        }

        ul {
            font-family: 'Meiryo UI'; !important;
        }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script
            src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>


</head>


<body onload="getValue()">



<input type="hidden" name="_token" value="{{csrf_token()}}">



<aside id="menu-main" class="">
    @include('Mygroup.mygroup_sidebar')
</aside>
<nav>
    @include('Navigation.main_nav')
</nav>
{{--사이드바 불러오기--}}


{{--첫 화면 레이스 목록--}}
<div id="wrapper" style="min-height: 1024px;">


    {{--나의 그룹 불러오기--}}
    <div id="myrace">
        @include('Mygroup.mygroup')
        @include('Mygroup.mygroup_modal')
    </div>



</div>
</div>

</body>
<script>
    var groupIds = 0;

    function getAnothergroup(inputGroupIds) {
        //현재 소속학생
        groupIds = inputGroupIds;
        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/groupDataGet')}}",
            //processData: false,
            //contentType: false,
            async:false,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: "groupId=" + inputGroupIds,
            success: function (data) {

                GroupData = data;
//                     alert(JSON.stringify(GroupData['group']['id']));

                teacher = GroupData['teacher']['name'];
                group = GroupData['group']['name'];
//                groupIds = GroupData['group']['id'];
                student = GroupData['students'];

                $('#teacher').html(teacher);
                $('#group').html(group);

//                $('#groupIds').val(groupIds);
                var student_list = '';

                for (var i = 0; i < student.length; i++) {

                    student_list += '<tr><td>'

                        +'<i class="fas fa-user"> </i>'
                        + student[i].name
                        + '</td><td id="delete' + i + '">'
                        + student[i].id
                        + '</td><td>' +
                        ' <button type="button" style="background-color: white" class="btn btn-primary-outline btn-round-lg btn-sm" data-toggle="modal" ' +
                        '   data-target="#studnetchange" onclick="setting(' + i + ');">\n' +
                        ' パスワード変更\n' +
                        ' </button>' +
                        '</td><td>' +
                        '<center><button class="btn btn-round-lg btn-sm " onclick="Delete(' + i + ')"><i class="far fa-trash-alt"></i></button></center>' +
                        '</td></tr>'
                }

                $('#student').html(student_list);

            },
            error: function (data) {
                alert("에러");
            }
        });

        searching_Student(inputGroupIds);

    }




    function setting(settingNumber){
        $('#studentnumbers').val(student[settingNumber].name);
        $('#studentnames').val(student[settingNumber].id);
//

    }

    function Delete(deleteId) {
            //삭제

        var userId = $('#delete'+deleteId).text();
        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/studentGroupExchange')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: "groupId=" + groupIds+"&userId="+userId,
            success: function (data) {
                swal("", " ", "success");
                setTimeout(function(){ window.location.href = "{{url('mygroup')}}"; },1500);
            },
            error: function (data) {
                alert("삭제에러");
            }
        });
    }


    //그룹ID 호출
    //학생 추가
    function add_student(st_made_number){
        searching_Student(groupIds);

        var student_number_zip = [{
            "id":""+st_made_number
        }];

//        student_number_zip.insert("id",student_number);


        //배열을 push 할 경우는 [["13","14","15"],"19","18"] 이런식으로 2차원으로 들어가 처리가 더필요함
//        student_number_zip.push(student_number);


        student_number_zip = JSON.stringify(student_number_zip);

        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/pushInvitation')}}",
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: "groupId="+groupIds+"&students="+student_number_zip,
            success: function (data) {
//                alert(groupIds);
                window.location.href = "{{url('mygroup')}}";
            },
            error: function (data) {
                alert("추가 에러");
            }
        });
    }




    $(document).ready(function () {

        var params = {
            groupId: 1
        };

        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/groupsGet')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            async: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: params,
            success: function (data) {
                GroupData = data;


                Myclass = GroupData['groups'];
                groupIds = Myclass[0].groupId;
//                alert(groupIds);
                console.log(groupIds)



                var class_list = '';

                ///아진짜
                for (var i = 0; i < Myclass.length; i++) {

                    buttonGroupID = Myclass[i].groupId;
//                        class_list +=Myclass[i].groupName
                    class_list
                        += '<tr class="table_color"><td>'
                        + '<button class="button1 btn btn-link" id="' + buttonGroupID + '" onclick="getAnothergroup(this.id)">' + Myclass[i].groupName + '</button>'
                        + '</td><tr>'


                }

                $('#Myclass').html(class_list);

            },
            error: function (data) {
                alert("클래스찾기 에러");
            }
        });


        searching_Student(groupIds);






        $(function(){
            $('#selectBtn').click(function(){
//          console.log(('#contents'));

//          $('#contents').children('p').text(""); P인자식만 해당
                $('input:checkbox').each(function() { //[name=??] 로특정 체크박스만 불러오기가능
                    var add_info ="";
                    if(this.checked) {
                        add_info += "'id' :" + $(this).val() + ",";
                        //                            $('#contents').append(this.value);
                        add_student($(this).val());
                    }
                });
            });
        });





        $('#chkParent').click(function () {
            var isChecked = $(this).prop("checked");
            $('#tblData tr:has(td)')
                .find('input[type="checkbox"]')
                .prop('checked', isChecked);
        });

        $('#tblData tr:has(td)')
            .find('input[type="checkbox"]')
            .click(function () {
                var isChecked = $(this).prop("checked");
                var isHeaderChecked = $("#chkParent").prop("checked");
                if (isChecked == false && isHeaderChecked)
                    $("#chkParent").prop('checked', isChecked);
                else {
                    $('#tblData tr:has(td)')
                        .find('input[type="checkbox"]')
                        .each(function () {
                                if ($(this).prop("checked") == false)
                                    isChecked = false;
                            }
                        );
                    console.log(isChecked);
                    $("#chkParent").prop('checked', isChecked);
                }
            });
    });

    function myFunction() {
        var input,
            filter,
            table,
            tr,
            td,
            i;
        input = document.getElementById("myInput");
        filter = input
            .value
            .toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }

    }
        //소속학생
    function getValue() {
        var groupId = 1;

        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/groupDataGet')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            async:false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: "groupId=" + groupIds,
            success: function (data) {
                GroupData = data;
//                    alert(JSON.stringify(GroupData['group']['id']));

                teacher = GroupData['teacher']['name'];
                group = GroupData['group']['name'];
                groupIds = GroupData['group']['id'];
                student = GroupData['students'];


                $('#teacher').html(teacher);
                $('#group').html(group);
                $('#groupIds').val(groupIds);
                var student_list = '';

                for (var i = 0; i < student.length; i++) {

                    student_list += '<tr><td>'
                        +'<i class="fas fa-user"> </i>'
                        + student[i].name
                        + '</td><td id="delete' + i + '">'
                        + student[i].id
                        + '</td><td>' +
                        ' <button type="button" style="background-color: white" class="btn btn-primary-outline btn-round-lg btn-sm" data-toggle="modal" ' +
                        '   data-target="#studnetchange" onclick="setting(' + i + ');">\n' +
                        ' パスワード変更\n' +
                        ' </button>' +
                        '</td><td>' +
                        '<center><button class="btn btn-round-lg btn-sm " onclick="Delete(' + i + ')"><i class="far fa-trash-alt"></i></button></center>' +
                        '</td></tr>'
                }

                $('#student').html(student_list);
//                alert(groupIds);


            },
            error: function (data) {
                alert("로그인 후 사용 가능");

            }
        });
    }

    function searching_Student(groupIds2){
        //미소속학생

        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/selectUser')}}",
            //processData: false,
            //contentType: false,
            async:false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: "search=&groupId="+ groupIds2,
            success: function (data) {
//                alert(groupIds2)
                GroupData = data;

                search_studentJSON = GroupData['users'];

                var student_list = '';
                for( var i = 0 ; i < search_studentJSON.length; i++){

                    student_list +='<tr><td>'
                        +'<i class="fas fa-user"> </i>'
                        +search_studentJSON[i].name
                        +'</td><td id="st'+i+'">'
                        +search_studentJSON[i].id
                        //                        +'</td><td><button onclick="add_student('+i+')">+</button></td></tr>'
                        +'</td><td><center><input id="checkBox" type="checkbox" value="'+search_studentJSON[i].id+'" ></center></td></tr>'
                }


                $('#myTable').html(student_list);
                return groupIds2;

            },
            error: function (data) {
                alert("검색에러");
            }
        });


        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/groupDataGet')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            async:false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: "groupId=" + groupIds,
            success: function (data) {
                GroupData = data;
//                    alert(JSON.stringify(GroupData['group']['id']));

                teacher = GroupData['teacher']['name'];
                group = GroupData['group']['name'];
                groupIds = GroupData['group']['id'];
                student = GroupData['students'];


                $('#teacher').html(teacher);
                $('#group').html(group);
                $('#groupIds').val(groupIds);
                var student_list = '';

                for (var i = 0; i < student.length; i++) {

                    student_list += '<tr><td>'
                        +'<i class="fas fa-user"> </i>'
                        + student[i].name
                        + '</td><td id="delete' + i + '">'
                        + student[i].id
                        + '</td><td>' +
                        ' <button type="button" style="background-color: white" class="btn btn-primary-outline btn-round-lg btn-sm" data-toggle="modal" ' +
                        '   data-target="#studnetchange" onclick="setting(' + i + ');">\n' +
                        ' パスワード変更\n' +
                        ' </button>' +
                        '</td><td>' +
                        '<center><button class="btn btn-round-lg btn-sm " onclick="Delete(' + i + ')"><i class="far fa-trash-alt"></i></button></center>' +
                        '</td></tr>'
                }

                $('#student').html(student_list);
//                alert(groupIds);


            },
            error: function (data) {
                alert("로그인 후 사용 가능");

            }
        });
    }
    //엑셀 추가
    function enterTabTable(obj,obj2) {
        var i, k, ftag, str="";
        var text = document.getElementById(obj).value;
        var arr = text.split("\n"); // 엔터키로 분리
        if(text.length > 2) {
            str += "<table border='1' cellpadding='3' cellspacing='1'>\n";
            str += "<tbody>\n";
            for(i=0; i < arr.length; i++) {
                ftag = (document.getElementById("firstChk").checked == true) ? (i == 0) ? "No" : i : (i+1);
                str += "<tr>\n";
                str += "<td>"+ftag+"</td>\n";
                var sub_arr = arr[i].split("\t"); // 탭키로 분리
                for(k=0; k < sub_arr.length; k++) {
                    str += "<td>"+sub_arr[k]+"</td>\n";
                }
            }
            str += "</tbody>\n";
            str += "</table>\n";
        }
        document.getElementById(obj2).innerHTML = str;
    }

    $(function(){
        //전체선택 체크박스 클릭
        $("#allCheck").click(function(){
            //만약 전체 선택 체크박스가 체크된상태일경우
            if($("#allCheck").prop("checked")) {
                //해당화면에 전체 checkbox들을 체크해준다
                $("input[type=checkbox]").prop("checked",true);
                // 전체선택 체크박스가 해제된 경우
            } else {
                //해당화면에 모든 checkbox들의 체크를해제시킨다.
                $("input[type=checkbox]").prop("checked",false);
            }
        })
    })

</script>
</html>