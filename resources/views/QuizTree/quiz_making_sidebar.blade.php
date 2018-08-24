<!DOCTYPE html>
<html xmlns:height="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <style>
        div {
            font-family: 'Meiryo UI';
        }

        .sidenav {
            height: 100%;
            width: 20%;
            position: absolute;
            z-index: 1;
            left: 0;
            background-color: white;
        }

        .select {
            width: 80%;
            padding-top: 5%;
            margin-left: 10%;
        }

        .sample_quiz {
            text-align: center;
            overflow-y: scroll;
            width: 100%;
            height: 70%;
            background-color: white;
        }

        .searchButton {
            border: 2px solid white;
            padding: 10px;
            border-radius: 25px;
            background-color: transparent;
            margin-left: 10%;
        }

        #sideImg {
            background-image: url("https://i.imgur.com/oDRk68B.png");
            background-size: 100%;
            background-repeat: no-repeat;
            height: 12vw;
        }

        #titleImg {
            background-image: url("https://i.imgur.com/3BaJJlL.png");
            background-size: 100%;
            background-repeat: no-repeat;
            height: 9.5vw;
        }

    </style>
</head>

<script>

    // example quiz data 저장용
    var quizData;

    // 검색 버튼 클릭 시
    $(document).on('click', '#btn', function (e) {

        e.preventDefault();

        var params = {
            bookId: $('#bookId').val(),
            pageStart: $('#pageStart').val(),
            pageEnd: $('#pageEnd').val(),
            level: $('#level').val()
        };

        //alert(JSON.stringify(params));

        $.ajax({
            type: 'POST',
            url: "{{url('quizTreeController/getQuiz')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //data: {_token: CSRF_TOKEN, 'post':params},
            data: params,
            success: function (data) {

                // 테이블 비우기
                //$('#example').empty();
                $('#example *').remove();

                quizData = data.listId;

                for(var i = 0; i < quizData.length; i++) {
                    var $tr = $('<tr />').appendTo('#example');
                    $tr.attr({id : i+1});
                    $('<td />').text(i+1).appendTo($tr);
                    $('<td style="font-family: Meiryo" />').text(quizData[i].question).appendTo($tr);

                    // tr에 onclick method 추가
                    $('#'+(i+1)).unbind('click').bind('click', function () {

                        /*alert(quizData[this.id-1].question +
                         quizData[this.id-1].right +
                         quizData[this.id-1].example1 +
                         quizData[this.id-1].example2 +
                         quizData[this.id-1].example3 +
                         quizData[this.id-1].makeType +
                         quizData[this.id-1].quizType
                         )*/

                        quizAdd(quizData[this.id-1]);
                    });
                }
            },
            error: function (data) {
                swal("空いている部分を埋めてください");
            }
        });
    });

    /*<input type="hidden" name="bookId" value="">
     <input type="hidden" name="level" value="">
     <input type="hidden" name="pageStart" value="">
     <input type="hidden" name="pageEnd" value="">
     <input type="hidden" name="type" value="o">*/

    $(document).ready(function () {
        $('#bookSelect').change(function () {
            var selectedBook = $('#bookSelect :selected').val();
            var bookIdObj = document.getElementById("bookId");
            bookIdObj.value = selectedBook;
        });

        $('#levelSelect').change(function () {
            var selectedLevel = $('#levelSelect :selected').val();
            var levelObj = document.getElementById("level");
            levelObj.value = selectedLevel;
        });

        $('#pageS').change(function () {
            var pageS = $('#pageS').val();
            var pageStartObj = document.getElementById("pageStart");
            pageStartObj.value = pageS;
        });

        $('#pageE').change(function () {
            var pageE = $('#pageE').val();
            var pageEndObj = document.getElementById("pageEnd");
            pageEndObj.value = pageE;
        });
    });

</script>

<body>

<div class="sidenav">
    <div id="sideImg">

        <div class="select">
            <select id="bookSelect" class="form-control" style="height: 40px; border-radius: 12px; font-size: 20px">
                <option>教材選択</option>
                @for($i = count($response['bookList']) - 1; $i >= 0; $i--)
                    <option value="{{$response['bookList'][$i]['bookId']}}">{{$response['bookList'][$i]['bookName']}}</option>
            @endfor
            <!--<option value="1">test</option>
            <option value="2">급소공략</option>-->
            </select>
        </div>

        <div class="select">
            <select id="levelSelect" class="form-control" style="height: 40px; border-radius: 12px; font-size: 20px">
                <option>レベル選択</option>
                <option value="1">N1</option>
                <option value="2">N2</option>
                <option value="3">N3</option>
                <option value="4">N4</option>
                <option value="5">N5</option>
            </select>
        </div>

        <div class="form-inline" style="margin-left: 10%; padding-top: 5%">
            <input id="pageS" class="form-control" type="text" placeholder="ページ" style="width: 20%; height: 40px; border-radius: 12px; font-size: 15px">
            &nbsp;~&nbsp;
            <input id="pageE" class="form-control" type="text" placeholder="ページ" style="width: 20%; height: 40px; border-radius: 12px; font-size: 15px">
            <button id="btn" type="button" class="searchButton" style="width: 20%; color: white">検索</button>
        </div>

        <input type="hidden" name="bookId" id="bookId" value="">
        <input type="hidden" name="level" id="level" value="">
        <input type="hidden" name="pageStart" id="pageStart" value="">
        <input type="hidden" name="pageEnd" id="pageEnd" value="">

    </div>


    <!--예문-->
    <div class="sample_quiz">
        <table class="table table-bordered table-striped">
            <thead id="theadStyle">
            <tr>
                <th style="text-align: center; width: 10%;">#</th>
                <th style="text-align: center; width: 90%;">例文</th>
            </tr>
            </thead>

            <tbody id="example">
            {{--<tr>
                <td><a href="#">1</a></td>
                <td><a href="#">生活習慣病は40代を（　　）?え始める</a></td>
                <td>50%</td>
            </tr>

            <tr>
                <td><a href="#">2</a></td>
                <td><a href="#">食中毒を起こしたら店にはさすがに誰も（　）しない.</a></td>
                <td>70%</td>
            </tr>--}}
            </tbody>
        </table>
    </div>


</div>
</body>
</html>