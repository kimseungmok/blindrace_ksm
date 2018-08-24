<style type="text/css">
    .record_feedback {
        z-index: 1;
        position: relative;
        display: block;
        clear: both;
    }
    .feedbackPage_main{
        width: 100%;
        padding: 10px 0 10px 20px;
        background-color: #f9f9f9;
        height: 50px;
        position: relative;
        display: block;
        font-size: 20px;
        text-align: left;
        margin-left: 30px;
    }
    .feedbackPage_main h4{
        color: #203a8e;
        font-weight: bold;
    }
f
    .feedback_page table{
        background-color: white;
    }
    .feedback_page table thead tr:first-child{
        text-align: center;
        background-color: #D7D7D7;
    }
    .feedback_page table tbody tr:nth-child(2n){
        background-color: #e6eaed;
    }
    .panel-table .panel-body{
        padding:0;
    }

    .panel-table .panel-body .table-bordered{
        border-style: none;
        margin:0;
        text-align: center;
    }

    .panel-table .panel-body .table-bordered > thead > tr > th:first-of-type {
        text-align:center;
        width: 150px;
    }

    .panel-table .panel-body .table-bordered > thead > tr > th:last-of-type,
    .panel-table .panel-body .table-bordered > tbody > tr > td:last-of-type {
        border-right: 0px;
    }

    .panel-table .panel-body .table-bordered > thead > tr > th:first-of-type,
    .panel-table .panel-body .table-bordered > tbody > tr > td:first-of-type {
        border-left: 0px;
    }

    .panel-table .panel-body .table-bordered > tbody > tr:first-of-type > td{
        border-bottom: 0px;
    }

    .panel-table .panel-body .table-bordered > thead > tr:first-of-type > th{
        border-top: 0px;
    }

    .panel-table .panel-footer .pagination{
        margin:0;
    }

    /*
    used to vertically center elements, may need modification if you're not using default sizes.
    */
    .panel-table .panel-footer .col{
        line-height: 34px;
        height: 34px;
    }

    .panel-table .panel-heading .col h3{
        line-height: 30px;
        height: 30px;
    }

    .panel-table .panel-body .table-bordered > tbody > tr > td{
        line-height: 34px;
    }
</style>
<script>

    $(document).ready(function () {

        loadFeedback();

        $(document).on('click','.feedbackList',function () {
            loadFeedbackModal($(this).attr('id'));

        });

        $(document).on('click','.modal-footer .btn.btn-primary',function () {
            updateAnswer($('.request_date').attr('id'));
            console.log( $(this).attr('id'));

        });

        //과제 확인하기
        $(document).on('click','.btnHomeworkCheck',function () {
            checkHomework($(this).attr('id'));

        });

        //cancel눌렀을 때
        $(document).on('click','.modal-footer .btn-secondary',function(){
        });

    });

    function loadFeedback(){

        var reqData = {"groupId" : 1};

        $.ajax({
            type: 'POST',
            url: "{{url('/recordBoxController/selectQnAs')}}",
            //processData: false,
            //contentType: false,
            data:reqData,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {

                $('#modal_feedbackList').empty();

                for(var i = 0 ; i < 1;i++){

                    $('#modal_feedbackList')
                        .append($('<tr>').attr('id','qna_'+data['QnAs'][i]['QnAId'])
                            .append($('<td>').text(data['QnAs'][i]['question_at']))
                            .append($('<td>')
                                .append($('<a href="#" data-toggle="modal" data-target="#Modal2">')
                                    .attr('class','feedbackList').attr('id',data['QnAs'][i]['QnAId']).text(data['QnAs'][i]['title'])
                                )
                            )
                        );

                    if(data['QnAs'][i]['answer_at'] == null){
                        $('#qna_'+data['QnAs'][i]['QnAId']).append($('<td>')
                            .append($('<button>').attr('id','btnQnA_'+data['QnAs'][i]['QnAId']).attr('class','btn btn-warning')
                            //Change language : feedback
                                //.text("미확인")));
                                .text("{{ $language['feedback']['notcheck']}}")));

                    }else{
                        $('#qna_'+data['QnAs'][i]['QnAId']).append($('<td>')
                            .append($('<button">').attr('class','btn btn-primary')
                            //Change language : feedback
                            //.text("확인")));
                            .text("{{ $language['feedback']['check']}}")));
                    }
                }

            },
            error: function (data) {
                alert("loadFeedback / 피드백 받아오기 에러");
            }

        });
    }

    function loadFeedbackModal(qnaId){

        var reqData = {"QnAId" : qnaId};

        $.ajax({
            type: 'POST',
            url: "{{url('/recordBoxController/selectQnA')}}",
            //processData: false,
            //contentType: false,
            data:reqData,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {

                console.log(data);

                $('.request_date').empty();
                $('.response_date').empty();
                $('.request_contents').empty();
                $('#teachersFeedback').empty();
                $('.modal-footer.feedback').empty();

                $('.modal-footer.feedback').append($('<button data-dismiss="modal">').attr('class','btn btn-primary').text("{{$language['modal']['Feedback']['ok']}}"));
                $('.modal-footer.feedback').append($('<button data-dismiss="modal">').attr('class','btn btn-secondary').text("{{$language['modal']['Feedback']['cancel']}}"));

                if(data['QnA']['answerFileUrl'] != null){
                    var outputImg = document.getElementById('output');
                    outputImg.src = 
                }

                if(data['QnA']['answer_at'] != null){
                    $('.request_date').text("{{$language['modal']['Feedback']['questionDate']}} : "+data['QnA']['question_at']
                                    +" / {{$language['modal']['Feedback']['feedbackDate']}} : "+data['QnA']['answer_at'])
                    .attr('id',qnaId);
                }else{
                    $('.request_date').text("{{$language['modal']['Feedback']['questionDate']}} : "+data['QnA']['question_at'])
                    .attr('id',qnaId);
                }

                $('.request_contents').text(data['QnA']['question']);
                $('#teachersFeedback').val(data['QnA']['answer']);
                
            },
            error: function (data) {
                alert("loadFeedback / 피드백 받아오기 에러");
            }
        });

    }

    function updateAnswer(QnAId){

        var formData = new FormData();
        var imgfiles = document.getElementsByName("feedbackImg")[0].files[0];
        var answerText = "text = "+$('#teachersFeedback').val();

        formData.append('QnAId', QnAId);
        formData.append('answer', answerText);
        formData.append('answerImg', imgfiles);

        $.ajax({
            type: 'POST',
            url: "{{url('/recordBoxController/updateAnswer')}}",
            processData: false,
            contentType: false,
            data:formData,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {

                swal("{{$language['modal']['Feedback']['alert']}}");
            },
            error: function (data) {
                alert("loadFeedback / 피드백 등록하기 에러");
            }
        });
    }


</script>

<div class="feedbackPage_main">
    <h4>
        <!-- Change language : feedback / 피드백-->
        {{$language['feedback']['Feedback']}}
    </h4>
</div>

    <div class="feedback_page" style="margin: 10px;">
        <table class="table table-bordered table-list" style="margin: 0; text-align: center;" >
            <thead>
            <tr>
                <td>
        {{$language['modal']['Subtitle']['date']}}
                </td>
                <td>
        {{$language['modal']['Subtitle']['title']}}
                </td>
                <td id="feedbackCheck" class="feedback_check">
        {{$language['modal']['Subtitle']['state']}}
                </td>
            </tr>
            </thead>
            <tbody id="modal_feedbackList">
            </tbody>
        </table>
    </div>

