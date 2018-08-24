<style>
    .recordbox_navbar {
        margin: 0;
        padding: 0;
        height: 150px;
        min-width: 700px;
        width: 100%;
        transition: top 0.2s ease-in-out;
        position: relative;
        display: block;
        z-index: 1;
        background-image: url("/img/race_recordbox/mainpageIcon.png");
        background-size: 100% 100%;
    }
    .nav-up {
        margin: 0;
        padding: 0;
        top: 0;
        left: 14%;
        height: 60px;
        width: 86%;
        position: fixed;
        z-index: 100;
        background-color: #F36174;
        background-image: none;
    }
    .fakeRecordnav{
    }
    .addFakeToRecordNav{
        margin: 0;
        padding: 0;
        height: 50px;
        min-width: 700px;
        width: 100%;
        transition: top 0.2s ease-in-out;
        position: relative;
        display: block;
        z-index: 1;
    }
    .recordbox.navbar.navbar-default {
        background: #fff;
        border: 1px solid #e5e6e8;
        margin: 0;
    }
    .container-fluid {
        width: 100%;
        height: 100%;
    }
    .targetMenu{
        padding: 15px 10px 9px 10px !important;
        border-bottom: 8px solid #0E76A8;
        pointer-events: none;
    }
    .navbar-brand{
        display: block;
        position: relative;
        width: 100%;
        color: white;
        font-size: 30px;
        font-weight: bold;
        margin-top: 30px;
        margin-left: 30px;
        padding: 0;
    }
    .navbar-body{
        display: block;
        position: relative;
        width: 100%;
        color: white;
    }
    .recordnav_once {
        color: white;
        font-size: 15px;
    }
    .recordnav{
        margin-top: 20px;
    }
    .recordnav li{
        border-right: 2px solid white;
    }
    .recordnav li:first-child{
        padding-left: 0;
    }
    .recordnav li:last-child{
        border-right: 0;
        padding-right: 0;
    }
    .recordnav li a{
        padding: 0 45px 0 45px;
    }
    .recordnav li a:hover,.recordnav li a:focus{
        color: #2a6496;
        background-color: transparent;
    }

    .recordbox-languageRadio{
        display:block;
    }

</style>
<script>

    var reqGroupId = "{{$groupId}}";
    var reqWhere = "{{$where}}";
    var reqCountry = "{{$country}}";

    //record_recordnav.blade.php 로드시 실행될 기능들
    $(document).ready(function () {

        //디자인 추가하기
        //해당 페이지 메뉴 클릭 금지 + 해당 페이지 메뉴 밑에 바 추가하기 ( addClass -> targetMenu )
        $('.recordbox #'+reqWhere).addClass('targetMenu');

        //메뉴 클릭시 ULR로 페이지 이동
        $(document).on('click','.recordnav_once',function () {

            // URL : /recordbox/where/groupId/country
            window.location.href = "{{url('recordbox')}}/" + $(this).attr('id') + "/" + reqGroupId + "/" + reqCountry;
        });
    });
</script>


<div class="recordbox_navbar">
    <div class="container-fluid" >
        <div class="navbar-brand" id="recordnavName">

        </div>
        <div class="navbar-body">
            <ul class="nav navbar-nav recordnav">
                <li>
                <!-- Changing lanuguage / recordnav : 통계 -->
                    <a id="chart" href="#" class="recordnav_once">{{$language['recordnav']['Chart']}}</a>
                </li>
                <li>
                <!-- Changing lanuguage / recordnav : 최근 기록 -->
                    <a id="history" href="#" class="recordnav_once">{{$language['recordnav']['History']}}</a>
                </li>
                <li>
                <!-- Changing lanuguage / recordnav : 학생 관리 -->
                    <a id="students" href="#" class="recordnav_once">{{$language['recordnav']['Student']}}</a>
                </li>
                <li>
                <!-- Changing lanuguage / recordnav : 피드백 -->
                    <a id="feedback" href="#" class="recordnav_once">{{$language['recordnav']['Feedback']}}</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="fakeRecordnav">

</div>