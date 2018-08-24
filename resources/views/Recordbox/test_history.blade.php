

<style>
    .record_history {
        z-index: 1;
        position: relative;
        display: block;
        clear: both;
    }
    .recordbox-history {
        margin-top: 40px;
        margin-left: 20px;
        margin-right: 20px;
        padding: 0;
    }
    .historyContainer {
        width: 100%;
    }
    .historyContainer .historyList {
        display: block;
        float: left;
        width: 65%;
    }
    .historyContainer .raceListDetail {
        display: block;
        float: left;
        width: 30%;
        text-align: center;
        height: 40%;
    }
    .historyContainer .raceListDetail .raceListDetailScroll {
        width: 100%;
        height: 100%;
        overflow-y: scroll;
        border: 1px solid #e5e6e8;
    }
    .raceListDetail table thead tr th ,.raceListDetail table tbody {
        text-align: center;
    }

</style>

<div class="recordbox-history">
    <div class="historyContainer">

        <div class="historyList">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>번호</th>
                    <th>퀴즈 제목</th>
                    <th>날짜</th>
                    <th>총점</th>
                    <th>어휘</th>
                    <th>문법</th>
                    <th>독해</th>
                    <th>문항수</th>
                    <th>과제 확인하기</th>
                </tr>
                </thead>
                <tbody id="history_list">
                </tbody>
            </table>

            <div class="panel-footer" style="height: 80px;">
                <div class="row">
                    <div class="col col-xs-4">Page 1 of 5
                    </div>
                    <div class="col col-xs-8">
                        <ul class="pagination hidden-xs pull-right">
                            <li><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                        </ul>
                        <ul class="pagination visible-xs pull-right">
                            <li><a href="#">«</a></li>
                            <li><a href="#">»</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: block;float: left;margin: 2%;">
        </div>

        {{--과제 목록 보기--}}
        <div class="raceListDetail">
            <div class="raceListDetailScroll">
                <table class="table table-hover table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th id="historyListNumber">
                            번호
                        </th>
                        <th id="historyListRaceName" colspan="2">
                            퀴즈제목
                        </th>
                    </tr>
                    <tr>
                        <th>
                            학생
                        </th>
                        <th>
                            재시험
                        </th>
                        <th>
                            오답노트
                        </th>
                    </tr>
                    </thead>

                    {{--getStudent()로 학생들 불러오기--}}
                    <tbody id="history_homework">

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

