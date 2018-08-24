<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.11.1.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
    <link href="js/bootstrap.min.js" rel="stylesheet">
    <style>
        html{
            width: 100%;
            height: 100%;
        }
        body{
            background-image: url("/img/race_play/re_bg.png") !important;
            min-height: 100%;
            background-position: center;
            background-size: cover;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<script>
    var sessionId = '<?php echo $response['sessionId']; ?>';
    var raceId = '<?php echo $response['raceId']; ?>';
    var testmode ;

    var quizId;
    var quizCount;
    var quiz_JSON;
    var retest_quiz_num =0;

    var quiz_answer_list = [1,2,3,4];
    var rightAnswer;
    var real_A;

    var selected_answer;
    var now_status;
    //quizId , sessionId , answer

    window.onload = function(){
        function shuffle(a) {

            var j, x, i;
            for (i = a.length; i; i -= 1) {
                j = Math.floor(Math.random() * i);
                x = a[i - 1];
                a[i - 1] = a[j];
                a[j] = x;
            }
        }
        function Create2DArray(rows) {
            var arr = [];

            for (var i=0;i<rows;i++) {
                arr[i] = [];
            }

            return arr;
        }

        function shuffle_quiz(){

            real_A = Create2DArray(quiz_JSON.length);

            for(var i = 0; i <quiz_JSON.length; i++){

                if( quiz_JSON[i].makeType == "obj"){

                    shuffle(quiz_answer_list);

                    real_A[i][quiz_answer_list[0]] = quiz_JSON[i].right;
                    real_A[i][quiz_answer_list[1]] = quiz_JSON[i].example1;
                    real_A[i][quiz_answer_list[2]] = quiz_JSON[i].example2;
                    real_A[i][quiz_answer_list[3]] = quiz_JSON[i].example3;

                    for(var j = 0; j<=3; j++){
                        switch(quiz_answer_list[j]){
                            case 1: quiz_JSON[i].right = real_A[i][quiz_answer_list[j]];
                                break;
                            case 2: quiz_JSON[i].example1 = real_A[i][quiz_answer_list[j]];
                                break;
                            case 3: quiz_JSON[i].example2 = real_A[i][quiz_answer_list[j]];
                                break;
                            case 4: quiz_JSON[i].example3 = real_A[i][quiz_answer_list[j]];
                                break;
                        }
                    }
                }
            }
        }

        $.ajax({
            type: 'POST',
            url: "{{url('raceController/retestStart')}}",
            dataType: 'json',
            async:false,
            data:"raceId="+raceId+"&sessionId="+sessionId,

            success: function (result) {
                $('#raceName').text(result['listName']);
                $('#quizCount').text(result['quizCount']+"問");
                $('#passingMark').text("合格点:"+result['passingMark']);
                $('#groupName').text(result['groupName']);
                $('#userName').text(result['userName']);
                $('#now_status').text("1 / "+result['quizCount']);

                quizCount = result['quizCount'];
                quiz_JSON = result['quizs']['quiz'];
                shuffle_quiz();

                quizGet();

            },
            error: function (data) {
                alert("error");
            }
        });
    };
    function nextQuiz(){

        quizId = quiz_JSON[retest_quiz_num-1].quizId;

        if(quiz_JSON[retest_quiz_num-1].makeType == "sub")
            selected_answer = document.getElementById('sub_content').value;

        $.ajax({
            type: 'POST',
            url: "{{url('raceController/retestAnswerIn')}}",
            dataType: 'json',
            data:"quizId="+quizId+"&sessionId="+sessionId+"&answer="+selected_answer,
            success: function (result) {

            },
            error: function (data) {
                alert("학생 재시험정답입력 error");
            }
        });

        //마지막 문제를 풀고난후
        if(retest_quiz_num == quiz_JSON.length)
        {
            $.ajax({
                type: 'POST',
                url: "{{url('raceController/retestEnd')}}",
                dataType: 'json',
                data:"sessionId="+sessionId,
                success: function (result) {
                    if(result['score'] >= result['passingMark'])
                        $('#q_table').html("SUCCESS"+result['score']);
                    else if(result['score'] < result['passingMark']){
                        $('#q_table').html("FAIL"+result['score']);
                    }
                },
                error: function (data) {
                    alert("학생 재시험 엔딩 FAIL");
                }
            });
        }else{
            quizGet();
        }
    }

    function quizGet(){

        switch(quiz_JSON[retest_quiz_num].makeType){

            case "obj":
                selected_answer = quiz_JSON[retest_quiz_num].right;
                $('#quiz_guide').text('()の中に入る言葉として最も良いものを１つ選んでください。');
                $('#answer1_span').text(quiz_JSON[retest_quiz_num].right);
                $('#answer1_radio').val(quiz_JSON[retest_quiz_num].right);

                $('#answer2_span').text(quiz_JSON[retest_quiz_num].example1);
                $('#answer2_radio').val(quiz_JSON[retest_quiz_num].example1);

                $('#answer3_span').text(quiz_JSON[retest_quiz_num].example2);
                $('#answer3_radio').val(quiz_JSON[retest_quiz_num].example2);

                $('#answer4_span').text(quiz_JSON[retest_quiz_num].example3);
                $('#answer4_radio').val(quiz_JSON[retest_quiz_num].example3);

                $('#obj').show();
                $('#sub').hide();
                break;

            case "sub":
                $('#quiz_guide').text('()の中に入る言葉を入力してください。');
                $('#sub').show();
                $('#obj').hide();
                break;
        }
        retest_quiz_num++;

        $('#quiz_number').text("Q"+retest_quiz_num+". ");
        $('#quiz_contents').text(quiz_JSON[retest_quiz_num].question);

        $('#now_status').text(retest_quiz_num+" / "+quizCount);
    }

</script>

<script>

    $(document).on("change","input[type=radio][name=answer]",function(event){
        selected_answer = this.value;
    });

</script>
<body>

    @include('Navigation.main_nav')

    @include('Race.race_test_content')
</body>
</html>