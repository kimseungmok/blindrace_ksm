<style>
    .recordbox_sidebar {
        margin: 0;
        padding: 0;
        position: relative;
        width: 14%;
        height:95%;
        float: left;
        border: 1px solid #e5e6e8;
        background-image: url("https://i.imgur.com/HSrLDSe.png");
        background-size: 100% 100%;

    }
    .sidenav-up {
        margin: 0;
        padding: 0;
        top: 0;
        left: 0;
        width: 14%;
        height:100%;
        position: fixed;
        background-color: #F2F2F2;
        background-size: 100% 100%;
        z-index: 100;
    }
    .fake_sidebar{
    }
    .addToFake{
        margin: 0;
        padding: 0;
        position: relative;
        width: 14%;
        height:95%;
        float: left;
    }

    .page-small {
        display: none !important;
    }
    .page-small {
        width: 100% !important;
    }
    .main-left-menu {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    #side-menu2 {

    }
    #side-menu li .nav-second-level li a, #side-menu2 li .nav-second-level li a, #side-menu2 li .nav-second-level a {
        padding: 15px 0 10px 15px;
        color: #2a6496;
        text-transform: none;
        position: relative;
        display: block;
        font-size: 15px;
        height: 50px;

    }
    .class-myclass{
        height: 200px;
        padding: 30px 0 0 25px;
        font-size: 30px;
        color: #203a8e;
    }
    .class_list {
        background-image: url("/img/race_recordbox/sidebarIcon.png");
        background-size: 100% 100%;
        width: 100%;
    }
    .checking-class_list{
        background-color:#d9edf7;
    }
    @media (max-width: 768px) {
        .page-small .content, .page-small #wrapper-class .content, .page-small .content-main {
            padding: 15px 5px;
            min-width: 320px
        }
    }
</style>
<script>

    //보여줄 클래스아이디
    var reqGroupId = "{{$groupId}}";

    //sidebar에서 실행할 메소드들
    $(document).ready(function () {

        //클래스 리스트 불러오기
        loadClasses();

        //사이드바 클래스이름 클릭했을 경우
        $(document).on('click','.groups',function () {
            //요구하는 클래스를 URL에 집어넣어 이동하기
            var groupId = $(this).attr('id');
            window.location.href = "{{url('recordbox/'.$where)}}/" + groupId;
        });
    });


    //해당하는 교수님의 클래스 목록 불러오기
    //URL에서 조회하려는 클래스가 없을 경우 -> alert("없는 페이지입니다.");
    function loadClasses() {

        var nonCount = 0;
        //클래스 불러오기 and 차트 로드하기 and 학생 명단 출력하기 and 피드백 가져오기

        $.ajax({
            type: 'POST',
            url: "{{url('/groupController/groupsGet')}}",
            //processData: false,
            //contentType: false,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: null,
            success: function (data) {

                var GroupData = data.groups;
                //사이드바에 클래스 추가

                for( var i = 0 ; i < GroupData.length ; i++ ){
                    $('.class-toggle').append($('<div>').attr('class','nav-second-level class_list')
                        .append($('<a href="#">')
                            .attr('class','groups')
                            .attr('name',GroupData[i]['groupName'])
                            .attr('id',GroupData[i]['groupId'])
                            .text(GroupData[i]['groupName'])));

                    //찾고자 하는 클래스가 없을 경우 -> nonCount + 1
                    if (GroupData[i]['groupId'] != reqGroupId){ nonCount++ }
                }

                //조회하려는 클래스가 없을 경우 -> (nonCount == GroupData.length)
                if(nonCount == GroupData.length){

                    //체인지페이지 부분 안보이게 하기
                    $('.changePages').hide();
                    alert("없는 클래스입니다.");

                }else{

                    //체인지페이지 부분 보이게 하기
                    $('.changePages').show();
                    //레코드박스 네비바 첫부분에 상단 클래스 이름 넣기
                    $('#recordnavName').attr('class','navbar-brand').text($('.class-toggle #'+reqGroupId).attr('name'));
                }
            },
            error: function (data) {
                alert("로그인부터 해주시기 바랍니다.");
            }
        });
    }


</script>

<div class="recordbox_sidebar" >

    <div class="innerContents">
        <!--네비바 위부분 공백-->
        <div class="page-small" style="text-align: center; margin-top: 10px; margin-bottom:10px;">
        </div>

        <div class="m-t-lg">

            <div class="class-myclass" id="side-menu3_li">
                    My Class
            </div>
            <ul class="main-left-menu" id="side-menu2">

                {{--클래스 이름 리스트 들어갈 자리--}}
                <li class="class-toggle">

                </li>
            </ul>
        </div>
    </div>
</div>

<div class="fake_sidebar" >

</div>