<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;

class RecordBoxController extends Controller{
    /****
     * 차트정보 가져오기
     *
     * @param Request $request->input()
     *          'groupId'   해당 그룹 아이디
     *          ['startDate'] 차트 검색 시작 날짜
     *          ['endDate']   차트 검색 종료 날짜
     *
     * @return array(
     *              'group' => array(
     *                  'id'    그룹 아이디
     *                  'name'  그룹 이름
     *                  'teacherId' 해당 그룹의 선생 아이디
     *              ),
     *              'races'     $this->selectGroupRecords('groupId', 'startDate', 'endDate');
     *              'startDate' 검색된 값의 시작 날짜
     *              'endDate'   검색된 값의 종료 날짜
     *              'check'     검색 성공여부
     *          );
     */
    public function getChart(Request $request){
        // 현재 시간가져오기 기본값 가져오기
        $time = time();
        $endDate = date('Y-m-d', $time);
        $startDate = date('Y-m-d', $time - 7 * 24 * 60 * 60);

        $postData = array(
            'groupId'   => $request->has('groupId') ? $request->input('groupId') : false,
            'startDate' => $request->has('startDate') ? $request->input('startDate') : $startDate,
            'endDate'   => $request->has('endDate') ? $request->input('endDate') : $endDate
        );

        // 유저정보가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));
        if ($userData['check'] /*&& (count($errors) == 0)*/){

            // 그룹권한 확인
            $where = array();
            switch ($userData['classification']){
                case 'teacher':
                    $where = array('teacherNumber' => $userData['userId']);
                case 'root':
                    $groupData = DB::table('groups')
                        ->select(
                            'number as groupId',
                            'name   as groupName',
                            'teacherNumber as teacherId'
                        )
                        ->where([
                            'number' => $postData['groupId']
                        ])
                        ->where($where)
                        ->first();

                    if($groupData){
                        $races = $this->selectGroupRecords($groupData->groupId, $postData['startDate'], $postData['endDate']);

                        // 반납하는값
                        $returnValue = array(
                            'group' => array(
                                'id'    => $groupData->groupId,
                                'name'  => $groupData->groupName,
                                'teacherId'  => $groupData->teacherId
                            ),
                            'races'     => $races,
                            'startDate' => $postData['startDate'],
                            'endDate'   => $postData['endDate'],
                            'check'     => true
                        );
                    } else {
                        $returnValue = array(
                            'check' => false
                        );
                    }
                    break;
                default:
                    $returnValue = array(
                        'check' => false
                    );
                    break;
            }
        } else {
            $returnValue = array(
//                'errors' => $errors,
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 최근출제된 레이스 목록 받아오기
     *
     * @param Request $request->input()
     *      'groupId' 해당 그룹 아이디
     *      ['startDate'] 차트 검색 시작 날짜
     *      ['endDate'] 차트 검색 종료 날짜
     *
     * @return array(
     *          'races'  => array(
     *              0 => array(
     *                  'raceId'            레이스 아이디
     *                  'listName'          리스트 이름
     *                  'teacherName'       선생 이름
     *                  'date'              레이스 출제 날짜
     *                  'year'              레이스 출제 년도
     *                  'month'             레이스 출제 달
     *                  'day'               레이스 출제 일
     *                  'studentCount'      레이스 참가 학생 수
     *                  'retestClearCount'  재시험 응시완료자 수
     *                  'retestCount'       재시험 총 대상자 수
     *                  'wrongClearCount'   오답노트 응시완료자 수
     *                  'wrongCount'        오답노트 총 대상자 수
     *              )
     *          ),
     *          'check' 권한확인, 검색 성공여부
     *      );
     */
    public function getRaces(Request $request){
        $postData = array(
            'groupId'   => $request->input('groupId'),
            'startDate' => $request->has('startDate') ? $request->input('startDate') : false,
            'endDate'   => $request->has('endDate') ? $request->input('endDate') : false
        );

        // 유저정보가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));
        if ($userData['check']) {
            $dateWhere = array();
            if ($postData['startDate']){
                array_push($dateWhere, array(
                    DB::raw('date(r.created_at)'), '>=', $postData['startDate']
                ));
            }
            if ($postData['endDate']){
                $dateWhere = array(
                    [DB::raw('date(r.created_at)'), '<=', $postData['endDate']]
                );
            }

            // 그룹권한 확인
            $where = array();
            switch ($userData['classification']) {
                case 'teacher':
                    $where = array('teacherNumber' => $userData['userId']);
                case 'root':
                    $groupData = DB::table('groups')
                        ->select(
                            'number as groupId'
                        )
                        ->where([
                            'number' => $postData['groupId']
                        ])
                        ->where($where)
                        ->first();

                    if($groupData){
                        // 레이스 정보 읽어오기
                        $raceData = DB::table('races as r')
                            ->select(
                                'r.number as raceId',
                                'l.name as listName',
                                'u.name as teacherName',
                                'r.created_at as date',
                                DB::raw('year(r.created_at) as year'),
                                DB::raw('month(r.created_at) as month'),
                                DB::raw('dayofmonth(r.created_at) as day'),
                                DB::raw('count(distinct ru.userNumber) as studentCount'),
                                DB::raw('count(CASE WHEN ru.retestState = "order" THEN 1 END) as retestOrderCount'),
                                DB::raw('count(CASE WHEN ru.retestState = "clear" THEN 1 END) as retestClearCount'),
                                DB::raw('count(CASE WHEN ru.wrongState = "order" THEN 1 END) as wrongOrderCount'),
                                DB::raw('count(CASE WHEN ru.wrongState = "clear" THEN 1 END) as wrongClearCount')
                            )
                            ->where('r.groupNumber', '=', $groupData->groupId)
                            ->where($dateWhere)
                            ->join('raceUsers as ru', 'ru.raceNumber', '=', 'r.number')
                            ->join('lists as l', 'l.number', '=', 'r.listNumber')
                            ->join('folders as f', 'f.number', '=', 'l.folderNumber')
                            ->join('users as u', 'u.number', '=', 'f.teacherNumber')
                            ->groupBy('r.number')
                            ->orderBy('r.created_at', 'desc')
                            ->get();

                        // 레이스 정보 정리
                        $races = array();
                        foreach ($raceData as $race){
                            array_push($races, array(
                                'raceId' => $race->raceId,
                                'listName' => $race->listName,
                                'teacherName' => $race->teacherName,
                                'date' => $race->date,
                                'year' => $race->year,
                                'month' => $race->month,
                                'day' => $race->day,
                                'studentCount' => $race->studentCount,
                                'retestClearCount' => $race->retestClearCount,
                                'retestCount' => $race->retestOrderCount + $race->retestClearCount,
                                'wrongClearCount' => $race->wrongClearCount,
                                'wrongCount' => $race->wrongOrderCount + $race->wrongClearCount
                            ));
                        }
                        
                        // 반납하는값
                        $returnValue = array(
                            'races'  => $races,
                            'check' => true
                        );
                    } else {
                        $returnValue = array(
                            'check' => false
                        );
                    }

                    break;
                default:
                    $returnValue = array(
                        'check' => false
                    );
                    break;
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 과제 미출제 학생 조회
     *
     * @param Request $request->input()
     *      'raceId' 해당 레이스 아이디
     *
     * @return array(
     *      'students'  => array(
     *              0 => array(
     *                  'userId'        유저 아이디
     *                  'userName'      유저 이름
     *                  'retestState'   재시험 응시 상태
     *                  'wrongState'    오답노트 응시 상태
     *              )
     *          ),
     *          'check' 성공 여부
    *      );
     */
    public function homeworkCheck(Request $request){
        $postData = array(
            'raceId'    => $request->input('raceId')
        );

        // 유저정보가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));
        if ($userData['check'] && $postData['raceId']) {

            // 그룹권한 확인
            $where = array();
            switch ($userData['classification']) {
                case 'teacher':
                case 'root':

                    // 레이스 정보 읽어오기
                    $studentData = DB::table('raceUsers as ru')
                        ->select(
                            'ru.userNumber as userId',
                            'u.name as userName',
                            'ru.retestState as retestState',
                            'ru.wrongState as wrongState'
                        )
                        ->where('ru.raceNumber', '=', $postData['raceId'])
                        ->join('users as u', 'u.number', '=', 'ru.userNumber')
                        ->groupBy(['ru.userNumber', 'ru.raceNumber'])
                        ->orderBy('ru.userNumber', 'desc')
                        ->get();

                    // 레이스 정보 정리
                    $students = array();
                    foreach ($studentData as $student) {
                        array_push($students, array(
                            'userId' => $student->userId,
                            'userName' => $student->userName,
                            'retestState' => $student->retestState,
                            'wrongState' => $student->wrongState
                        ));
                    }

                    // 반납하는값
                    $returnValue = array(
                        'students'  => $students,
                        'check' => true
                    );
                    break;
                default:
                    $returnValue = array(
                        'check' => false
                    );
                    break;
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 학생의 최근기록 조회 - 'userId'
     * 레이스를 친 학생들 정보 조회 - 'raceId'
     * 재시험 한 결과를 조회하기 위해서 사용 - 'userId', 'raceId', 'retestState' = 1
     *
     * 학생용 자기 성적조회 - 'groupId'
     * 학생용 자기 재시험 성적 조회 - 'raceId', 'retestState' = 1
     *
     * @param Request $request->input()
     *      ['userId']
     *      ['raceId']
     *      ['retestState']     재시험 정보 조회용 변수
     *      ['groupId']         학생 전용 변수
     *      ['sessionId']       모바일 전용 변수
     *
     * @return array(
     *      'races' => array(
     *          0 => array(
     *              'raceId'                레이스 아이디
     *              'listName'              리스트 이름
     *              'teacherName'           선생 이름
     *              'userId'                유저 아이디
     *              'userName'              유저 이름
     *              'date'                  레이스 실시 날짜
     *              'year'                  레이스 실시 년도
     *              'month'                 레이스 실시 달
     *              'day'                   레이스 실시 일
     *              'allCount'              문항 수
     *              'allRightCount'         정답 수
     *              'vocabularyCount'       어휘 문항 수
     *              'vocabularyRightCount'  어휘 정답 수
     *              'wordCount'             단어 문항 수
     *              'wordRightCount'        단어 정답 수
     *              'grammarCount'          문법 문항 수
     *              'grammarRightCount'     문법 정답 수
     *              'retestState'           재시험 응시 상태
     *              'wrongState'            오답노트 응시 상태
     *              'wrongDate'             오답노트 응시 날짜
     *          )
     *      ),
     *      'check' 검색 성공 여부
     *  );
     */
    public function getStudents(Request $request){
        $postData = array(
            'userId'        => $request->has('userId') ? $request->input('userId') : false,
            'raceId'        => $request->has('raceId') ? $request->input('raceId') : false,
            'retestState'   => $request->has('retestState') ? $request->input('retestState') : self::RETEST_NOT_STATE,
            'groupId'       => $request->has('groupId') ? $request->input('groupId') : false,
            'sessionId'     => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저정보가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 모바일용, 세션 아이디 값을 보낼 경우 세션의 유저아이디 값을 사용
        if ($userData['classification'] == 'student'){
            $postData['userId'] = $userData['userId'];
        }

        if ($userData['check']) {
            // 조회 구분
            if($postData['userId'] && $postData['raceId']){
                $typeWhere = array(
                    'ru.userNumber' => $postData['userId'],
                    'ru.raceNumber' => $postData['raceId'],
                    're.retest' => $postData['retestState']
                );
            } else if($postData['userId'] && $postData['groupId']){
                $typeWhere = array(
                    'ru.userNumber' => $postData['userId'],
                    'r.groupNumber' => $postData['groupId'],
                    're.retest' => self::RETEST_NOT_STATE
                );
            } else if($postData['userId']){
                $typeWhere = array(
                    'ru.userNumber' => $postData['userId'],
                    're.retest' => self::RETEST_NOT_STATE
                );
            } else if ($postData['raceId']){
                $typeWhere = array(
                    'ru.raceNumber' => $postData['raceId'],
                    're.retest' => self::RETEST_NOT_STATE
                );
            } else {
                $typeWhere = false;
            }

            // 그룹권한 확인
            $where = array();
            if ($typeWhere) {
                switch ($userData['classification']) {
                    // 학생은 자기것만 볼 수 있음.
                    case 'student':
                    case 'sleepStudent':
                        $where = array('ru.userNumber' => $userData['userId']);
                    // 선생은 모든 학생을 볼 수 있음.
                    case 'teacher':
                    case 'root':
                        // 학생 정보 조회
                        $raceData = DB::table('raceUsers as ru')
                            ->select(
                                'r.number as raceId',
                                'l.name as listName',
                                'ru.userNumber as userId',
                                'u.name as userName',
                                'r.created_at as date',
                                'ut.name as teacherName',
                                DB::raw('year(r.created_at) as year'),
                                DB::raw('month(r.created_at) as month'),
                                DB::raw('dayofmonth(r.created_at) as day'),
                                DB::raw('count(re.quizNo) as allCount'),
                                DB::raw('count(CASE WHEN re.answerCheck = "O" THEN 1 END) as allRightAnswerCount'),
                                DB::raw('count(CASE WHEN qb.type = "vocabulary obj" THEN 1 END) as vocabularyObjCount'),
                                DB::raw('count(CASE WHEN qb.type = "vocabulary sub" THEN 1 END) as vocabularySubCount'),
                                DB::raw('count(CASE WHEN qb.type like "vocabulary%" AND re.answerCheck = "O" THEN 1 END) as vocabularyRightAnswerCount'),
                                DB::raw('count(CASE WHEN qb.type = "word obj" THEN 1 END) as wordObjCount'),
                                DB::raw('count(CASE WHEN qb.type = "word sub" THEN 1 END) as wordSubCount'),
                                DB::raw('count(CASE WHEN qb.type like "word%" AND re.answerCheck = "O" THEN 1 END) as wordRightAnswerCount'),
                                DB::raw('count(CASE WHEN qb.type = "grammar obj" THEN 1 END) as grammarObjCount'),
                                DB::raw('count(CASE WHEN qb.type = "grammar sub" THEN 1 END) as grammarSubCount'),
                                DB::raw('count(CASE WHEN qb.type like "grammar%" AND re.answerCheck = "O" THEN 1 END) as grammarRightAnswerCount'),
                                'ru.retestState as retestState',
                                'ru.wrongState as wrongState',
                                'ru.wrong_at as wrongDate'
                            )
                            ->where($typeWhere)
                            ->where($where)
                            ->join('races as r', 'r.number', '=', 'ru.raceNumber')
                            ->join('lists as l', 'l.number', '=', 'r.listNumber')
                            ->join('users as ut', 'ut.number', '=', 'r.teacherNumber')
                            ->join('users as u', 'u.number', '=', 'ru.userNumber')
                            ->join('records as re', function ($join) {
                                $join->on('re.raceNo', '=', 'ru.raceNumber');
                                $join->on('re.userNo', '=', 'ru.userNumber');
                            })
                            ->join('quizBanks as qb', 'qb.number', '=', 're.quizNo')
                            ->groupBy(['ru.userNumber', 'ru.raceNumber'])
                            ->orderBy('ru.raceNumber', 'desc')
                            ->get();

                        // 반납할 정보 정리
                        $races = array();
                        foreach ($raceData as $race) {
                            array_push($races, json_encode(array(
                                'raceId' => $race->raceId,
                                'listName' => $race->listName,
                                'teacherName' => $race->teacherName,
                                'userId' => $race->userId,
                                'userName' => $race->userName,
                                'date' => $race->date,
                                'year' => $race->year,
                                'month' => $race->month,
                                'day' => $race->day,
                                'allCount' => (int)($race->allCount),
                                'allRightCount' => (int)($race->allRightAnswerCount),
                                'vocabularyCount' => (int)($race->vocabularyObjCount + $race->vocabularySubCount),
                                'vocabularyRightCount' => (int)($race->vocabularyRightAnswerCount),
                                'wordCount' => (int)($race->wordObjCount + $race->wordSubCount),
                                'wordRightCount' => (int)($race->wordRightAnswerCount),
                                'grammarCount' => (int)($race->grammarObjCount + $race->grammarSubCount),
                                'grammarRightCount' => (int)($race->grammarRightAnswerCount),
                                'retestState' => $race->retestState,
                                'wrongState' => $race->wrongState,
                                'wrongDate' => $race->wrongDate
                            )));
                        }

                        $returnValue = array(
                            'races' => $races,
                            'check' => true
                        );
                        break;
                    default:
                        $returnValue = array(
                            'check' => false
                        );
                        break;
                }
            } else {
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * getStudents 의 모바일용
     * 학생의 최근기록 조회 - 'userId'
     * 레이스를 친 학생들 정보 조회 - 'raceId'
     * 재시험 한 결과를 조회하기 위해서 사용 - 'userId', 'raceId', 'retestState' = 1
     *
     * 학생용 자기 성적조회 - 'groupId'
     * 학생용 자기 재시험 성적 조회 - 'raceId', 'retestState' = 1
     *
     * @param Request $request->input()
     *      ['userId']
     *      ['raceId']
     *      ['retestState']     재시험 정보 조회용 변수
     *      ['groupId']         학생 전용 변수
     *      ['sessionId']       모바일 전용 변수
     *
     * @return $this->>getStudents(Request $request)
     */
    public function mobileGetStudents(Request $request){
        return $this->getStudents($request);
    }

    /****
     * 오답문제 조회하기 학생별 'userId', 'raceId'
     * 오답문제 조회하기 레이스 전체 'raceId'
     * 
     * 학생용 오답문제 조회하기 'raceId'
     * 
     * + 재시험 한 결과를 조회하기 위해서 사용 'retestState' => 1
     * 
     * @param Request $request->input()
     *      ['userId']
     *      ['raceId']
     *      ['retestState'] - 재시험 정보 조회용 변수
     *      ['sessionId'] - 모바일 전용 변수
     *
     * @return array(
     *      'raceId' 레이스 아이디
     *      'wrongs' => array(
     *          // 객관식
     *          0 -> array(
     *              'number'            문제 번호
     *              'id'                문제 아이디
     *              'question'          문제
     *              'hint'              힌트
     *              'rightAnswer'       정답
     *              'rightAnswerCount'  정답자 수
     *              'example1'          예문 1번
     *              'example1Count'     오답자1 수
     *              'example2'          예문 2번
     *              'example2Count'     오답자2 수
     *              'example3'          예문 3번
     *              'example3Count'     오답자3 수
     *              'wrongCount'        전체 오답자 수
     *              'userCount'         전체 유저 수
     *              'wrong'             한명의 유저를 검색할 경우 유저가 입력한 오답
     *          ),
     *          // 주관식
     *          1 => array(
     *              'number'            문제 번호
     *              'id'                문제 아이디
     *              'question'          문제
     *              'hint'              힌트
     *              'rightAnswer'       정답
     *              'rightAnswerCount'  정답자 수
     *              'wrongs'            오답 들
     *              'wrongCount'        오답자 수
     *              'userCount'         전체 유저 수
     *              'wrong'             한명의 유저를 검색할 경우 유저가 입력한 오답
     *          )
     *      ),
     *      'check'  성공 여부
     *  );
     */
    public function getWrongs(Request $request){
        $postData = array(
            'userId'    => $request->has('userId') ? $request->input('userId') : false,
            'raceId'    => $request->has('raceId') ? $request->input('raceId') : false,
            'retestState'   => $request->has('retestState') ? $request->input('retestState') : self::RETEST_NOT_STATE,
            'sessionId'   => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 유저권한 확인
        if ($userData['check'] && $postData['raceId']){
            // 메서드 호출 타입 설정
            if ($userData['classification'] == 'student'){
                $typeWhere = array(
                    're.userNo' => $userData['userId'],
                    're.raceNo' => $postData['raceId'],
                    're.retest' => $postData['retestState']
                );
                $typeGroupBy = array('re.raceNo', 're.userNo', 're.quizNo', 're.retest');
            } else if ($postData['userId']){
                $typeWhere = array(
                    're.userNo' => $postData['userId'],
                    're.raceNo' => $postData['raceId'],
                    're.retest' => $postData['retestState']
                );
                $typeGroupBy = array('re.raceNo', 're.userNo', 're.quizNo', 're.retest');
            } else {
                $typeWhere = array(
                    're.raceNo' => $postData['raceId'],
                    're.retest' => $postData['retestState']
                );
                $typeGroupBy = array('re.raceNo', 're.quizNo', 're.retest');
            }

            // 문제 리스트 뽑아오기
            $raceQuizs = DB::table('records as re')
                ->select(
                    'qb.number as quizId',
                    'qb.question as question',
                    'qb.hint as hint',
                    'qb.rightAnswer as rightAnswer',
                    'qb.example1 as example1',
                    'qb.example2 as example2',
                    'qb.example3 as example3',
                    'qb.type as type',
                    DB::raw('count(distinct re.userNo) as userCount'),
                    DB::raw('count(CASE WHEN re.answerCheck = "O" THEN 1 END) as rightAnswerCount')
                )
                ->where($typeWhere)
                ->join('quizBanks as qb', 'qb.number', '=', 're.quizNo')
                ->groupBy($typeGroupBy)
                ->orderBy('re.quizNo')
                ->get();

            // 문제 확인
            $wrongs = array();
            for ($i = 0 ; $i < count($raceQuizs) ; $i++) {
                // 오답노트 불러오기용 변수
                $wrongText = false;

                // 오답 확인 정답자 수가 총 유저 수보다 작을 경우
                if ($raceQuizs[$i]->userCount > $raceQuizs[$i]->rightAnswerCount) {
                    // 타입 구분용
                    $type = explode(' ', $raceQuizs[$i]->type);

                    // 객관식 처리
                    if ($type[1] == 'obj') {
                        $studentAnswerData = DB::table('records as re')
                            ->select(
                                're.answer as answer'
                            )
                            ->where($typeWhere)
                            ->where(['re.quizNo' => $raceQuizs[$i]->quizId])
                            ->get();

                        $example1Count = 0;
                        $example2Count = 0;
                        $example3Count = 0;
                        foreach ($studentAnswerData as $studentAnswer){
                            if ($studentAnswer->answer == $raceQuizs[$i]->example1){
                                $example1Count++;
                            } else if ($studentAnswer->answer == $raceQuizs[$i]->example2){
                                $example2Count++;
                            } else if ($studentAnswer->answer == $raceQuizs[$i]->example3){
                                $example3Count++;
                            }
                        }

                        // 학생 조회일 경우 오답노트도 출력
                        if ($postData['userId']){
                            $wrongText = DB::table('records as re')
                                ->select(
                                    're.wrongAnswerNote as wrongAnswerNote'
                                )
                                ->where([
                                    're.quizNo' => $raceQuizs[$i]->quizId
                                ])
                                ->where($typeWhere)
                                ->first();
                        }

                        array_push($wrongs, array(
                            'number' => $i + 1,
                            'id' => $raceQuizs[$i]->quizId,
                            'type' => $type[1],
                            'question' => $raceQuizs[$i]->question,
                            'hint' => $raceQuizs[$i]->hint,
                            'rightAnswer' => $raceQuizs[$i]->rightAnswer,
                            'rightAnswerCount' => $raceQuizs[$i]->rightAnswerCount,
                            'example1' => $raceQuizs[$i]->example1,
                            'example1Count' => $example1Count,
                            'example2' => $raceQuizs[$i]->example2,
                            'example2Count' => $example2Count,
                            'example3' => $raceQuizs[$i]->example3,
                            'example3Count' => $example3Count,
                            'wrongCount' => $raceQuizs[$i]->userCount - $raceQuizs[$i]->rightAnswerCount,
                            'userCount' => $raceQuizs[$i]->userCount,
                            'wrong' => $wrongText ? $wrongText->wrongAnswerNote : false
                        ));
                    }
                    // 주관식 처리
                    else if ($type[1] == 'sub') {
                        $quizData = DB::table('records as re')
                            ->select(
                                're.answer as answer',
                                'u.number as userId',
                                'u.name as userName'
                            )
                            ->where($typeWhere)
                            ->where(['qb.number' => $raceQuizs[$i]->quizId])
                            ->join('quizBanks as qb', 'qb.number', '=', 're.quizNo')
                            ->join('users as u', 'u.number', '=', 're.userNo')
                            ->get();

                        $wrongData = array();
                        $rights = explode(',', $raceQuizs[$i]->rightAnswer);
                        foreach ($quizData as $quiz) {
                            $rightCheck = true;
                            foreach ($rights as $right){
                                if ($quiz->answer == $right) {
                                    $rightCheck = false;
                                    break;
                                }
                            }
                            if ($rightCheck) {
                                array_push($wrongData, array(
                                    'userId' => $quiz->userId,
                                    'userName' => $quiz->userName,
                                    'answer' => $quiz->answer
                                ));
                            }
                        }

                        // 학생 조회일 경우 오답노트도 출력
                        if ($postData['userId']){
                            $wrongText = DB::table('records as re')
                                ->select(
                                    're.wrongAnswerNote as wrongAnswerNote'
                                )
                                ->where([
                                    're.quizNo' => $raceQuizs[$i]->quizId
                                ])
                                ->where($typeWhere)
                                ->first();
                        }

                        array_push($wrongs, array(
                            'number' => $i + 1,
                            'id' => $raceQuizs[$i]->quizId,
                            'type' => $type[1],
                            'question' => $raceQuizs[$i]->question,
                            'hint' => $raceQuizs[$i]->hint,
                            'rightAnswer' => $raceQuizs[$i]->rightAnswer,
                            'rightAnswerCount' => $raceQuizs[$i]->userCount - count($wrongData),
                            'wrongs' => $wrongData,
                            'wrongCount' => count($wrongData),
                            'userCount' => $raceQuizs[$i]->userCount,
                            'wrong' => $wrongText ? $wrongText->wrongAnswerNote : false
                        ));
                    }
                }
            }

            // 반납값 정리2
            $returnValue = array(
                'raceId' => $postData['raceId'],
                'wrongs' => $wrongs,
                'check' => true
            );
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * getWrongs 의 모바일용
     *
     * 오답문제 조회하기 학생별 'userId', 'raceId'
     * 오답문제 조회하기 레이스 전체 'raceId'
     *
     * 학생용 오답문제 조회하기 'raceId'
     *
     * + 재시험 한 결과를 조회하기 위해서 사용 'retestState' => 1
     *
     * @param Request $request->input()
     *      ['userId']
     *      ['raceId']
     *      ['retestState'] - 재시험 정보 조회용 변수
     *      ['sessionId'] - 모바일 전용 변수
     *
     * @return $this->getWrongs(Request $request)
     */
    public function mobileGetWrongs(Request $request){
        return $this->getWrongs($request);
    }

    /****
     * 오답풀이 입력하기
     *
     * @param Request $request->input()
     *      'raceId' 레이스 아이디
     *      'wrongs' => array(
     *          0 => array(
     *              'quizId'    문제 아이디(int:1~)
     *              'text'      오답 풀이(string)
     *          )
     *      )
     *      ['sessionId'] 모바일 전용 변수
     * 
     * @return array(
     *          'wrongCheck'    오답노트 완료 여부
     *          'check'         입력 성공 여부
     *      )
     */
    public function insertWrongs(Request $request){
        $postData = array(
            'raceId' => $request->input('raceId'),
            'wrongs' => $request->input('wrongs'),
            'sessionId'   => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 로그인 확인
        if ($userData['check']){
            // 오답풀이 대상자인지 확인
            $recordData1 = DB::table('raceUsers as ru')
                ->select(
                    DB::raw('count(CASE WHEN re.answerCheck = "X" THEN 1 END) as wrongAnswerCount')
                )
                ->where([
                    'ru.userNumber' => $userData['userId'],
                    'ru.raceNumber' => $postData['raceId'],
                    'ru.wrongState' => 'order',
                    're.retest' => self::RETEST_NOT_STATE
                ])
                ->join('records as re', function ($join){
                    $join->on('re.userNo', '=', 'ru.userNumber');
                    $join->on('re.raceNo', '=', 'ru.raceNumber');
                })
                ->groupBy('ru.userNumber')
                ->first();

            if ($recordData1) {
                // 오답풀이 입력
                foreach ($postData['wrongs'] as $wrong) {
                    if ($wrong['text'] != '') {
                        $update = array(
                            'wrongAnswerNote' => $wrong['text']
                        );
                    } else {
                        $update = array(
                            'wrongAnswerNote' => null
                        );
                    }

                    DB::table('records')
                        ->where([
                            'raceNo' => $postData['raceId'],
                            'quizNo' => $wrong['quizId'],
                            'userNo' => $userData['userId'],
                            'retest' => self::RETEST_NOT_STATE,
                            'answerCheck' => 'X'
                        ])
                        ->update($update);
                }

                // 오답풀이가 전부 입력되었는지 확인
                $recordData2 = DB::table('raceUsers as ru')
                    ->select(
                        DB::raw('count(CASE WHEN re.wrongAnswerNote IS NOT NULL THEN 1 END) as wrongAnswerCount')
                    )
                    ->where([
                        'ru.userNumber' => $userData['userId'],
                        'ru.raceNumber' => $postData['raceId'],
                        'ru.wrongState' => 'order',
                        're.retest' => self::RETEST_NOT_STATE
                    ])
                    ->join('records as re', function ($join){
                        $join->on('re.userNo', '=', 'ru.userNumber');
                        $join->on('re.raceNo', '=', 'ru.raceNumber');
                    })
                    ->groupBy('ru.userNumber')
                    ->first();

                if ($recordData1->wrongAnswerCount == $recordData2->wrongAnswerCount){
                    DB::table('raceUsers')
                        ->where([
                            'ru.userNumber' => $userData['userId'],
                            'ru.raceNumber' => $postData['raceId'],
                            'ru.wrongState' => 'order',
                        ])
                        ->update([
                            'ru.wrongState'  => 'clear',
                            'ru.wrong_at'  => DB::raw('now()')
                        ]);

                    $returnValue = array(
                        'wrongCheck' => true,
                        'check' => true
                    );
                } else {
                    $returnValue = array(
                        'wrongCheck' => false,
                        'check' => true
                    );
                }
            } else {
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 모바일 버전 오답풀이 입력하기
     *
     * @param Request $request->input()
     *      'raceId' 레이스 아이디
     *      'wrongs' => array(
     *          0 => array(
     *              'quizId'    문제 아이디(int:1~)
     *              'text'      오답 풀이(string)
     *          )
     *      )
     *      ['sessionId'] 모바일 전용 변수
     *
     * @return $this->insertWrongs(Request $request)
     */
    public function mobileInsertWrongs(Request $request){
        return $this->insertWrongs($request);
    }

    /****
     * 웹 용 피드백 혹은 질문하기
     *
     * @param Request $request->input()
     *      'title'     재목
     *      'question'  질문
     *      'teacherId' 질문할 선생의 아이디
     *      'groupId'   질문한 그룹 아이디
     *      ['sessionId'] 모바일용 변수
     *
     * @return array(
     *      'check' 질문 저장 성공여부
     *  );
     */
    public function insertQuestion(Request $request){
        $postData = array(
            'title' => $request->has('title') ? $request->input('title') : false,
            'question' => $request->has('question') ? $request->input('question') : false,
            'teacherId' => $request->has('teacherId') ? $request->input('teacherId') : false,
            'groupId' => $request->has('groupId') ? $request->input('groupId') : false,
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 로그인 확인
        if ($userData['check'] && $postData['title'] && $postData['question'] && $postData['teacherId']){
            switch ($userData['classification']){
                case 'student':
                    // 자기 그룹인지 확인
                    $groupCheck = DB::table('groups as g')
                        ->select(DB::raw('count(*) as check'))
                        ->where([
                            'g.number' => $postData['groupId'],
                            'g.teacherNumber' => $postData['teacherId'],
                            'gs.userNumber' => $userData['userId']
                        ])
                        ->join('groupStudents as gs', 'gs.groupNumber', '=', 'g.number')
                        ->first();

                    if ($groupCheck && ($groupCheck->check == 1)) {
                        $fileNumber = null;
                        if ($request->hasFile('questionImg')) {
                            $file = $request->file('questionImg');
                            $fileName=date("Y_m_d_His").$file->getClientOriginalName();
                            $url=Storage::url('imgFile/'.$fileName);
                            $file->storeAs('imgFile',$fileName);
                            $url = str_replace('/storage/', '/storage/app/', $url);

                            $fileNumber = DB::table('files')
                                ->insertGetId([
                                    'userNumber' => $userData['userId'],
                                    'name' => $fileName,
                                    'url' => $url,
                                    'type' => 'jpg'
//                                    'type' => $file->getMimeType()
                                ], 'number');
                        }

                        DB::table('QnAs')
                            ->insert([
                                'userNumber' => $userData['userId'],
                                'teacherNumber' => $postData['teacherId'],
                                'groupNumber' => $postData['groupId'],
                                'title' => $postData['title'],
                                'question' => substr($postData['question'], 7),
                                'questionFileNumber' => $fileNumber
                            ]);

                        // 반납값 정리
                        $returnValue = array(
                            'check' => true
                        );
                    } else {
                        $returnValue = array(
                            'check' => false,
                            'mark' => 1
                        );
                    }
                    break;
                default:

                    // 반납값 정리
                    $returnValue = array(
                        'check' => false,
                        'mark' => 2
                    );
                    break;
            }
        } else {
            $returnValue = array(
                'check' => false,
                'mark' => 3
            );
        }

        return $returnValue;
    }

    /****
     * 웹 용 QnA 목록 가져오기
     *
     * @param Request $request->input
     *      'groupId'   질문한 그룹 아이디
     *      ['sessionId'] 모바일용 변수
     *
     * @return array(
     *      'QnAs' => array(
     *              'QnAId'         질문 아이디
     *              'userName'      질문한 학생 이름
     *              'teacherName'   질문받은 교사 이름
     *              'title'         질문 제목
     *              'question_at'   질문한 날짜
     *              'answer_at'     답변한 날짜
     *          ),
     *          'check' 조회 성공 여부
     *      )
     */
    public function selectQnAs(Request $request){
        $postData = array(
            'groupId' => $request->input('groupId'),
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저 정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        if ($userData['check']){
            // 유저 권한별 정리
            switch ($userData['classification']){
                case 'student':
                    $where = array(
                        'QnAs.userNumber' => $userData['userId']
                    );
                    break;
                case 'teacher':
                    $where = array(
                        'QnAs.teacherNumber' => $userData['userId']
                    );
                    break;
                case 'root':
                    $where = array(
                        'QnAs.teacherNumber' => $userData['userId']
                    );
                    break;
                default:
                    $where = array(
                        1 => 2
                    );
                    break;
            }

            // QnA정보 가져오기
            $QnAData = DB::table('QnAs')
                ->select(
                    'QnAs.number as QnAId',
                    'u.name as userName',
                    'tu.name as teacherName',
                    'QnAs.title as title',
                    'QnAs.question_at as question_at',
                    'QnAs.answer_at as answer_at'
                )
                ->where($where)
                ->where([
                    'QnAs.groupNumber' => $postData['groupId']
                ])
                ->join('users as u', 'u.number', '=', 'QnAs.userNumber')
                ->join('users as tu', 'tu.number', '=', 'QnAs.teacherNumber')
                ->get();

            // 반납값 정리
            $QnAs = array();
            foreach($QnAData as $QnA){
                array_push($QnAs, array(
                    'QnAId' => $QnA->QnAId,
                    'userName' => $QnA->userName,
                    'teacherName' => $QnA->teacherName,
                    'title' => $QnA->title,
                    'question_at' => $QnA->question_at,
                    'answer_at' => $QnA->answer_at
                ));
            }

            $returnValue = array(
                'QnAs' => $QnAs,
                'check' => true
            );
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 웹 용 QnA 조회하기
     *
     * @param Request $request->input()
     *      'QnAId' 질문 아이디
     *      ['sessionId'] 모바일용 변수
     *
     * @return array(
     *      'QnA' => array(
     *          'QnAId'         질문 번호
     *          'userName'      질문한 학생 이름
     *          'teacherName'   질문받은 교사 이름
     *          'title'         질문 제목
     *          'question'      질문
     *          'answer'        대답
     *          'question_at'   질문한 날짜
     *          'answer_at'     대답한 날짜
     *      ),
     *      'check' 조회 성공 여부확인
     * )
     */
    public function selectQnA(Request $request){
        $postData = array(
            'QnAId' => $request->input('QnAId'),
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저 정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        if ($userData['check']){
            // 유저 권한별 정리
            switch ($userData['classification']){
                case 'student':
                    $where = array(
                        'QnAs.userNumber' => $userData['userId']
                    );
                    break;
                case 'teacher':
                    $where = array(
                        'QnAs.teacherNumber' => $userData['userId']
                    );
                    break;
                case 'root':
                    $where = array(
                        'QnAs.teacherNumber' => $userData['userId']
                    );
                    break;
                default:
                    $where = array(
                        1 => 2
                    );
                    break;
            }

            // QnA정보 가져오기
            $QnAData = DB::table('QnAs')
                ->select(
                    'QnAs.number as QnAId',
                    'u.name as userName',
                    'tu.name as teacherName',
                    'QnAs.title as title',
                    'QnAs.question as question',
                    'QnAs.answer as answer',
                    'qf.name as questionFileName',
                    'qf.url as questionFileUrl',
                    'qf.type as questionFileType',
                    'af.name as answerFileName',
                    'af.url as answerFileUrl',
                    'af.type as answerFileType',
                    'QnAs.question_at as question_at',
                    'QnAs.answer_at as answer_at'
                )
                ->join('users as u', 'u.number', '=', 'QnAs.userNumber')
                ->join('users as tu', 'tu.number', '=', 'QnAs.teacherNumber')
                ->leftJoin('files as qf', 'qf.number', '=', 'QnAs.questionFileNumber')
                ->leftJoin('files as af', 'af.number', '=', 'QnAs.answerFileNumber')
                ->where($where)
                ->where([
                    'QnAs.number' => $postData['QnAId']
                ])
                ->orderBy('QnAs.number', 'DESC')
                ->first();

            // 반납값 정리
            if ($QnAData) {
                $returnValue = array(
                    'QnA' => array(
                        'QnAId' => $QnAData->QnAId,
                        'userName' => $QnAData->userName,
                        'teacherName' => $QnAData->teacherName,
                        'title' => $QnAData->title,
                        'question' => $QnAData->question,
                        'answer' => $QnAData->answer,
                        'questionFileName' => $QnAData->questionFileName,
                        'questionFileUrl' => $QnAData->questionFileUrl,
                        'questionFileType' => $QnAData->questionFileType,
                        'answerFileName' => $QnAData->answerFileName,
                        'answerFileUrl' => $QnAData->answerFileUrl,
                        'answerFileType' => $QnAData->answerFileType,
                        'question_at' => $QnAData->question_at,
                        'answer_at' => $QnAData->answer_at
                    ),
                    'check' => true
                );
            } else {
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 웹 용 QnA 답변하기
     *
     * @param Request $request->input()
     *      'QnAId' 질문 아이디
     *      'answer' QnA 답변
     *      ['sessionId'] 모바일용 변수
     *
     * @return array(
     *      'check' 답변 성공 여부확인
     * )
     */
    public function updateAnswer(Request $request){
        $postData = array(
            'QnAId' => $request->has('QnAId') ? $request->input('QnAId') : false,
            'answer' => $request->has('answer') ? $request->input('answer') : false,
            'sessionId' => $request->has('sessionId') ? $request->input('sessionId') : $request->session()->get('sessionId')
        );

        // 유저 정보 가져오기
        $userData = UserController::sessionDataGet($postData['sessionId']);

        // 확인
        if ($userData['check'] && $postData['QnAId'] && $postData['answer']){
            switch ($userData['classification']){
                case 'teacher':
                    $where = array(
                        'teacherNumber' => $userData['userId']
                    );
                    break;
                default:
                    $where = array(
                        1 => 2
                    );
                    break;
            }

            $fileNumber = null;
            if ($request->hasFile('answerImg')) {
                $file = $request->file('answerImg');
                $fileName=date("Y_m_d_His").$file->getClientOriginalName();
                $url=Storage::url('imgFile/'.$fileName);
                $file->storeAs('imgFile',$fileName);
                $url = str_replace('/storage/', '/storage/app/', $url);

                $fileNumber = DB::table('files')
                    ->insertGetId([
                        'userNumber' => $userData['userId'],
                        'name' => $fileName,
                        'url' => $url,
                        'type' => 'jpg'
//                        'type' => $file->getMimeType()
                    ], 'number');
            }

            // 업데이트
            DB::table('QnAs')
                ->where($where)
                ->where([
                    'number' => $postData['QnAId']
                ])
                ->update([
                    'answer' => substr($postData['answer'], 7),
                    'answer_at' => DB::raw('now()'),
                    'answerFileNumber' => $fileNumber
                ]);

            $returnValue = array(
                'check' => true
            );
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 모바일 용 QnA 질문하기
     *
     * @param Request $request->input()
     *      'title' 재목
     *      'question' 질문
     *      'teacherId' 질문할 선생의 아이디
     *      'groupId'   질문한 그룹 아이디
     *      ['sessionId'] 모바일용 변수
     *
     * @return $this->insertQuestion(Request $request);
     */
    public function mobileInsertQuestion(Request $request){
        return $this->insertQuestion($request);
    }

    /****
     * 모바일 용 QnA 목록 가져오기
     *
     * @param Request $request->input
     *      'groupId'   질문한 그룹 아이디
     *      ['sessionId'] 모바일용 변수
     *
     * @return $this->selectQnAs(Request $request);
     */
    public function mobileSelectQnAs(Request $request){
        return $this->selectQnAs($request);
    }

    /****
     * 모바일 용 QnA 조회하기
     *
     * @param Request $request->input()
     *      'QnAId' 질문 아이디
     *      ['sessionId'] 모바일용 변수
     *
     * @return $this->selectQnA(Request $request);
     */
    public function mobileSelectQnA(Request $request){
        return $this->selectQnA($request);
    }

    /****
     * 모바일 용 QnA 답변하기
     *
     * @param Request $request->input()
     *      'QnAId' 질문 아이디
     *      'answer' QnA 답변
     *      ['sessionId'] 모바일용 변수
     *
     * @return $this->updateAnswer(Request $request);
     */
    public function mobileUpdateAnswer(Request $request){
        return $this->updateAnswer($request);
    }

    /****
     * 기간내의 차트 읽어오기
     *
     * @param $groupId   그룹 아이디
     * @param $startDate 차트 검색 시작 날짜
     * @param $endDate   차트 검색 종료 날짜
     * @return array(
     *      'listName' 리스트 이름
     *      'raceId' 레이스 아이디
     *      'date' 레이스 출제 날짜
     *      'year' 레이스 출제 년도
     *      'month' 레이스 출제 달
     *      'day' 레이스 출제 일
     *      'userCount' 시험친 학생 수
     *      'quizCount' 레이스 출제 문항 수
     *      'rightAnswerCount' 정답 수
     *      'vocabularyCount' 어휘 수
     *      'vocabularyRightAnswerCount' 어휘 정답 수
     *      'wordCount' 단어 수
     *      'wordRightAnswerCount' 단어 정답 수
     *      'grammarCount' 문법 수
     *      'grammarRightAnswerCount' 문법 정답 수
     *  )
     */
    private function selectGroupRecords($groupId, $startDate, $endDate){
        $recordDatas = DB::table('races as r')
            ->select(
                'l.name as listName',
                'r.number as raceId',
                'r.created_at as date',
                DB::raw('year(r.created_at) as year'),
                DB::raw('month(r.created_at) as month'),
                DB::raw('dayofmonth(r.created_at) as day'),
                DB::raw('count(distinct ru.userNumber) as userCount'),
                DB::raw('count(distinct re.quizNo) as quizCount'),
                DB::raw('count(CASE WHEN re.answerCheck = "O" THEN 1 END) as rightAnswerCount'),
                DB::raw('count(CASE WHEN qb.type like "vocabulary%" THEN 1 END) as vocabularyCount'),
                DB::raw('count(CASE WHEN qb.type like "vocabulary%" AND re.answerCheck = "O"  THEN 1 END) as vocabularyRightAnswerCount'),
                DB::raw('count(CASE WHEN qb.type like "word%" THEN 1 END) as wordCount'),
                DB::raw('count(CASE WHEN qb.type like "word%" AND re.answerCheck = "O"  THEN 1 END) as wordRightAnswerCount'),
                DB::raw('count(CASE WHEN qb.type like "grammar%" THEN 1 END) as grammarCount'),
                DB::raw('count(CASE WHEN qb.type like "grammar%" AND re.answerCheck = "O"  THEN 1 END) as grammarRightAnswerCount')
            )
            ->where([
                're.retest' => self::RETEST_NOT_STATE,
                'r.groupNumber' => $groupId
            ])
            ->where(DB::raw('date(r.created_at)'), '>=', $startDate)
            ->where(DB::raw('date(r.created_at)'), '<=', $endDate)
            ->join('lists as l', 'l.number', '=', 'r.listNumber')
            ->join('raceUsers as ru', 'ru.raceNumber', '=', 'r.number')
            ->join('records as re', function ($join){
                $join->on('re.raceNo', '=', 'ru.raceNumber');
                $join->on('re.userNo', '=', 'ru.userNumber');
            })
            ->join('quizBanks as qb', 'qb.number', '=', 're.quizNo')
            ->groupBy('r.number')
            ->orderBy('r.number')
            ->get();

        // 반납할 값 정리
        $records = array();
        foreach ($recordDatas as $record){
            array_push($records, array(
                'listName'                      => $record->listName,
                'raceId'                        => $record->raceId,
                'date'                          => $record->date,
                'year'                          => $record->year,
                'month'                         => $record->month,
                'day'                           => $record->day,
                'userCount'                     => $record->userCount,
                'quizCount'                     => $record->quizCount,
                'rightAnswerCount'              => $record->rightAnswerCount            / $record->userCount,
                'vocabularyCount'               => $record->vocabularyCount             / $record->userCount,
                'vocabularyRightAnswerCount'    => $record->vocabularyRightAnswerCount  / $record->userCount,
                'wordCount'                     => $record->wordCount                   / $record->userCount,
                'wordRightAnswerCount'          => $record->wordRightAnswerCount        / $record->userCount,
                'grammarCount'                  => $record->grammarCount                / $record->userCount,
                'grammarRightAnswerCount'       => $record->grammarRightAnswerCount     / $record->userCount
            ));
        }

        return $records;
    }

    // 제약조건
    public function store(Request $request){
        $this->validate($request,[
            'questionImg' => 'image,max:4096',
            'answerImg' => 'image,max:4096'
        ]);

//        // 예외처리
//        $errors = array();
//        if(preg_match(self::GROUP_ID_FORMAT, $postData['groupId'])){
//            array_push($errors, 'RecordBoxController : groupId wrong');
//        }
//        if(preg_match(self::DATE_FORMAT, $postData['startDate'])){
//            array_push($errors, 'RecordBoxController : startDate wrong');
//        }
//        if(preg_match(self::DATE_FORMAT, $postData['endDate'])){
//            array_push($errors, 'RecordBoxController : endDate wrong');
//        }
    }
    public function messages(){
        return [
            'questionImg.required' => 'A questionImg is required'
        ];
    }
}

?>