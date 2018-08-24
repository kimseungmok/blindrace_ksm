<script>
    var testmode ;

    var quizId;
    var quizCount;
    var quiz_JSON;
    var test_quiz_num =0;

    var quiz_answer_list = [1,2,3,4];
    var rightAnswer;
    var real_A;

    var selected_answer;


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


                // $('#raceName').text(listName);
                // $('#quizCount').text(quizCount);
                // $('#passingMark').text("합격점:"+passingMark);
                // $('#groupName').text(groupName);
                // $('#userName').text(userName);


        socket.on('pop_quiz_start',function(quizData,listName){
            quiz_JSON = JSON.parse(quizData);
            shuffle_quiz();
            quizGet();
        });
    };
    function nextQuiz(){

        quizId = quiz_JSON[test_quiz_num-1].quizId;

        if(quiz_JSON[test_quiz_num-1].makeType == "sub")
            selected_answer = document.getElementById('sub_content').value;

       socket.emit('answer',roomPin,selected_answer,sessionId,"닉네임대신",quizId);
       

        //마지막 문제를 풀고난후
        if(test_quiz_num == quiz_JSON.length)
        {
            $.ajax({
                type: 'POST',
                url: "{{url('raceController/raceEnd')}}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: 'json',
                success: function (result) {
                    r_result = result['students'];
                    
                    
                    for(var i=0; i<r_result.length; i++ ){
                        if(r_result[i].sessionId == sessionId){
                            if(r_result[i].retestState == true)
                              $('#q_table').html("FAIL "+"点数");
                            else
                              $('#q_table').html("PASS "+"点数");
                        }
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
        $('#quiz_number').text(test_quiz_num+1);
        $('#quiz_contents').text(quiz_JSON[test_quiz_num].question);

        switch(quiz_JSON[test_quiz_num].makeType){

            case "obj":
                selected_answer = quiz_JSON[test_quiz_num].right;
                $('#quiz_guide').text('()の中に入る言葉として最も良いものを１つ選んでください。');
                $('#answer1_span').text(quiz_JSON[test_quiz_num].right);
                $('#answer1_radio').val(quiz_JSON[test_quiz_num].right);

                $('#answer2_span').text(quiz_JSON[test_quiz_num].example1);
                $('#answer2_radio').val(quiz_JSON[test_quiz_num].example1);

                $('#answer3_span').text(quiz_JSON[test_quiz_num].example2);
                $('#answer3_radio').val(quiz_JSON[test_quiz_num].example2);

                $('#answer4_span').text(quiz_JSON[test_quiz_num].example3);
                $('#answer4_radio').val(quiz_JSON[test_quiz_num].example3);

                $('#obj').show();
                $('#sub').hide();
                break;

            case "sub":
                $('#quiz_guide').text('()の中に入る言葉を入力してください。');
                $('#sub').show();
                $('#obj').hide();
                break;
        }
        test_quiz_num++;
    }

</script>

<script>

    $(document).on("change","input[type=radio][name=answer]",function(event){
        selected_answer = this.value;
    });

</script>
<div>
@include('Race.race_test_content')
</div>