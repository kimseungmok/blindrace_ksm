<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use App\Http\Controllers\UserController;

class QuizTreeController extends Controller
{

    /****
     * 폴더목록과 선택된 폴더의 리스트 목록을 반납
     *
     * @param Request $request->input()
     *      ['folderId'] 선택된 폴더
     *
     * @return array(
     *      'folders'       => $this->getFolders(세션 아이디)
     *      'lists'         => $this->getLists(선택된 폴더 아이디, 세션 아이디)
     *      'selectFolder'  선택된 폴더
     *      'check'         검색 성공 여부
     *  )
     */
    public function getfolderLists(Request $request)
    {
        $postData = array(
            'folderId' => $request->has('folderId') ? $request->input('folderId') : false
        );

        // 유저의 폴더 정보 가져오기
        $folders = $this->getFolders($request->session()->get('sessionId'));

        // 요구하는 폴더가 없을경우 기본 폴더를 가져옴
        if ($postData['folderId'] == 'base' || ($postData['folderId'] == false)){
            $selectFolderId = $folders[0]['folderId'];
        } else {
            $selectFolderId = $postData['folderId'];
        }

        // 호출된 폴더의 리스트 정보 가져오기
        $lists = $this->getLists($selectFolderId, $request->session()->get('sessionId'));

        // 반납할 데이터 정리
        $returnValue = array(
            'folders'       => $folders,
            'lists'         => $lists,
            'selectFolder'  => $selectFolderId,
            'check'         => true
        );

        return $returnValue;
    }

    /****
     * 폴더목록 가져오기
     *
     * @param $sessionId //세션 아이디
     * @return array(
     *      0 => array(
     *          'folderId' 폴더 아이디
     *          'folderName' 폴더 이름
     *      )
     *  )
     */
    private function getFolders($sessionId){
        // 폴더목록 가져오기
        $folderData = DB::table('folders as f')
            ->select(
                'f.number as folderId',
                'f.name as folderName'
            )
            ->where('s.number', '=', $sessionId)
            ->join('sessionDatas as s', 's.userNumber', '=', 'f.teacherNumber')
            ->orderBy('f.number')
            ->get();

        // 최초 접속시 폴더 정보가 없는 사람의 경우 base 폴더 생성
        if (count($folderData) == 0){
            // 선생 아이디 가져오기
            $teacher = DB::table('users as u')
                ->select(
                    'u.number as userId'
                )
                ->where([
                    'u.classifications' => ['teacher', 'root'],
                    's.number'          => $sessionId
                ])
                ->join('sessionDatas as s', 's.userNumber', '=', 'u.number')
                ->first();

            // base 폴더 만들기
            DB::table('folders')
                ->insert([
                    'teacherNumber' => $teacher->userId,
                    'name'          => 'base'
                ]);

            // 다시 폴더목록 가져오기
            $folderData = DB::table('folders as f')
                ->select(
                    'f.number   as folderId',
                    'f.name     as folderName'
                )
                ->where('s.number', '=', $sessionId)
                ->join('sessionDatas as s', 's.userNumber', '=', 'f.teacherNumber')
                ->orderBy('f.number')
                ->get();
        }

        // 가져온 폴더 정보를 정리하기
        $folders = array();

        // 공개된 리스트 정보를 불러올 폴더 설정
        array_push($folders, array(
                'folderId' => self::OPEN_STATE,
                'folderName' => '公　開')
        );

        foreach ($folderData as $folder){
            array_push($folders, array(
                'folderId' => $folder->folderId,
                'folderName' => $folder->folderName)
            );
        }

        return $folders;
    }

    /****
     * 리스트 목록 가져오기
     *
     * @param $selectFolderId //폴더 아이디
     * @param $sessionId // 세션 아이디
     * @return array(
     *      'listId' 리스트 아이디
     *      'listName' 라스트 이름
     *      'quizCount' 문항수
     *      'createdDate' 만들어진 날짜
     *      'openState' 공유 폴더 여부
     *      // 레이스 출제된 기록
     *      'races' => array(
     *          0 => array(
     *              'date' 출제된 날짜
     *              'type' 출제한 방식
     *              'groupName' 출제된 그룹
     *              'teacherName' 출제한 교사
     *          )
     *      )
     *  )
     */
    private function getLists($selectFolderId, $sessionId){
        // 공개된 리스트 목록 가져오기
        if ($selectFolderId == self::OPEN_STATE){
            $listData = DB::table('lists as l')
                ->select(
                    'l.number                       as listId',
                    'l.name                         as listName',
                    DB::raw('COUNT(lq.quizNumber)   as quizCount'),
                    'l.created_at                   as createdDate',
                    'l.openState                    as openState'
                )
                ->where([
                    'l.openState' => self::OPEN_STATE
                ])
                ->join('listQuizs as lq', 'lq.listNumber', '=', 'l.number')
                ->groupBy('l.number')
                ->orderBy('l.number', 'desc')
                ->get();
        }
        // 선택된 리스트 목록 가져오기
        else {
            $listData = DB::table('lists as l')
                ->select(
                    'l.number                       as listId',
                    'l.name                         as listName',
                    DB::raw('COUNT(lq.quizNumber)   as quizCount'),
                    'l.created_at                   as createdDate',
                    'l.openState                    as openState'
                )
                ->where([
                    's.number' => $sessionId,
                    'l.folderNumber' => $selectFolderId
                ])
                ->join('listQuizs as lq',   'lq.listNumber',    '=', 'l.number')
                ->join('folders as f',      'f.number',         '=', 'l.folderNumber')
                ->join('sessionDatas as s', 's.userNumber',     '=', 'f.teacherNumber')
                ->groupBy('l.number')
                ->orderBy('l.number', 'desc')
                ->get();
        }

        // 반납할 데이터 정리
        $lists = array();
        foreach ($listData as $list){
            // 출제정보 가져오기
            $raceData = DB::table('races as r')
                ->select(
                    'r.created_at   as date',
                    'r.type         as type',
                    'g.name         as groupName',
                    'u.name         as teacherName'
                )
                ->where('r.listNumber', '=', $list->listId)
                ->join('groups as g', 'g.number', '=', 'r.groupNumber')
                ->join('users as u', 'u.number', '=', 'r.teacherNumber')
                ->get();

            $races = array();
            foreach ($raceData as $race){
                array_push($races, array(
                    'date'          => $race->date,
                    'type'          => $race->type,
                    'groupName'     => $race->groupName,
                    'teacherName'   => $race->teacherName
                ));
            }

            array_push($lists, array(
                'listId'        => $list->listId,
                'listName'      => $list->listName,
                'quizCount'     => $list->quizCount,
                'createdDate'   => $list->createdDate,
                'openState'     => $list->openState,
                'races'         => $races
            ));
        }

        return $lists;
    }

    /****
     * 리스트 폴더 만들기
     *
     * @param Request $request->input()
     *      'folderName' 만들 폴더의 이름
     *
     * @return array(
     *      'folders'    => $this->getFolders(세션 아이디),
     *      'lists'      => array() 호완용 변수 레이스가 없음
     *      'selectFolder' 해당 폴더를 선택
     *      'check' 생성 성공여부
     *  )
     */
    public function createFolder(Request $request){
        // 보내진 값 받기
        $postData = array(
            'folderName' => $request->input('folderName')
        );

        // 로그인 되어있는 유저의 정보 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 폴더를 생성
        $folderId = DB::table('folders')
            ->insertGetId([
                    'name'          => $postData['folderName'],
                    'teacherNumber' => $userData['userId']
                ], 'number');

        // 선체 폴더를 다시 받기
        $folders = $this->getFolders($request->session()->get('sessionId'));

        // 반납할 값을 정리
        if (isset($folderId)) {
            $returnValue = array(
                'folders'    => $folders,
                'lists'      => array(),
                'selectFolder'  => $folderId,
                'check'         => true
            );
        }else{
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 리스트 만들기
     *
     * @param Request $request
     *      'folderId' 리스트를 만들 폴더
     *
     * @return view($view)->with('response', $returnValue);
     *      $returnValue => array(
     *          'listId' 호완용 변수 - 수정시 사용되는 변수
     *          'listName' 호완용 변수 - 수정시 사용되는 변수
     *          'folderId' 폴더 아이디
     *          'bookList' => $this->getBookGet();
     *          'quizs' 호완용 변수 - 수정시 사용되는 변수,
     *          'check' 권한 환인 성공 여부
     *      )
     */
    public function createList(Request $request){
        $postData = array(
            'folderId' => $request->input('folderId')
        );

        // 유저정보 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 본인 폴더인지 확인
        $folderData = DB::table('folders')
            ->select(
                'number'
            )
            ->where([
                'teacherNumber' => $userData['userId']
            ])
            ->first();

        // 반납할 값 반납
        if ($folderData) {
            // 저장된 교재 정보 가져오기
            $bookList = $this->getBookGet();

            $returnValue = array(
                'listId'    => 0,
                'listName'  => null,
                'folderId'  => $postData['folderId'],
                'bookList'  => $bookList,
                'quizs'     => array(),
                'check'     => true
            );

            $view = 'QuizTree/quiz_making';
        }else{
            $returnValue = array(
                'check' => false
            );

            $view = 'homepage';
        }

        return view($view)->with('response', $returnValue);
    }

    /****
     * 교재목록 가져오기
     *
     * @return array(
     *  0 => array(
     *          'bookId' 교재 아이디
     *          'bookName' 교재 이름
     *          'pageMax' 교재 최대 페이지
     *          'pageMin' 교재 최소 페이지
     *      )
     *  )
     */
    private function getBookGet(){

        // 교재목록 검색
        $bookData = DB::table('books')
            ->select(
                'number',
                'name',
                'maxPage',
                'minPage'
            )
            ->orderBy('name')
            ->get();

        // 반납할 값 정리
        $bookList = array();
        foreach ($bookData as $book){
            array_push($bookList, array(
                'bookId' => $book->number,
                'bookName' => $book->name,
                'pageMax' => $book->maxPage,
                'pageMin' => $book->minPage
            ));
        }

        return $bookList;
    }

    /****
     * 문제 검색하기
     *
     * @param Request $request->input()
     *      'bookId' 교재 번호
     *      'pageStart' 페이지 시작
     *      'pageEnd' 페이지 끝
     *      'level' 문제 난이도
     *
     * @return array(
     *      'listId' => array(
     *          0 => array(
     *              'quizId' 퀴즈 번호
     *              'bookId' 교재 번호
     *              'page' 페이지 번호
     *              'question' 문제
     *              'hint' 힌트
     *              'right' 정답
     *              'example1' 예문1
     *              'example2' 예문2
     *              'example3' 예문3
     *              'quizType' 문제 종류
     *              'makeType' 주관식, 객관식 구분
     *              'level' 문제 난이도
     *          )
     *      ),
     *      'check' 조회 성공 여부
     *  )
     */
    public function getQuiz(Request $request){
        $postData = array(
            'bookId'    => $request->input('bookId'),
            'pageStart' => $request->input('pageStart'),
            'pageEnd'   => $request->input('pageEnd'),
            'level'     => $request->input('level')
        );

        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        $quizList = array();
        if($userData['classification'] == 'teacher' || $userData['classification'] == 'root') {
            $quizData = DB::table('quizBanks')
                ->select(
                    'number         as quizId',
                    'bookNumber     as bookId',
                    'page           as page',
                    'question       as question',
                    'hint           as hint',
                    'rightAnswer    as right',
                    'example1       as example1',
                    'example2       as example2',
                    'example3       as example3',
                    'type           as type',
                    'level          as level'
                )
                ->where([
                    'bookNumber'    => $postData['bookId'],
                    'level'         => $postData['level']
                ])
                ->where('page', '>=', $postData['pageStart'])
                ->where('page', '<=', $postData['pageEnd'])
                ->get();

            foreach ($quizData as $quiz) {
                $type = explode(' ', $quiz->type);

                array_push($quizList, array(
                    'quizId'    => $quiz->quizId,
                    'bookId'    => $quiz->bookId,
                    'page'      => $quiz->page,
                    'question'  => $quiz->question,
                    'hint'      => $quiz->hint,
                    'right'     => $quiz->right,
                    'example1'  => $quiz->example1,
                    'example2'  => $quiz->example2,
                    'example3'  => $quiz->example3,
                    'quizType'  => $type[0],
                    'makeType'  => $type[1],
                    'level'     => $quiz->level
                ));
            }
        }

        if (count($quizList) > 0) {
            $returnValue = array(
                'listId' => $quizList,
                'check' => true
            );
        }else{
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 만든 리스트 저장하기
     *
     * @param Request $request->input()
     *  'listId' 리스트 아이디 - 수정 용
     *  'listName' 리스트 이름
     *  'quizs' => array(
     *      0 => array(
     *          'question' 문제
     *          'right' 정답
     *          'hint' 힌트
     *          'example1' 예문1
     *          'example2' 예문2
     *          'example3' 예문3
     *          'quizType' 퀴즈 타입
     *          'makeType' 주관식 객관식 구분
     *      )
     *  )
     * @return array(
     *      'check' 입력 성공여부
     *      'errorQuiz' 입력 실패시 입력 실패한 목록 위의 'quizs'와 동일한 양식
     *  )
     */
    public function insertList(Request $request){
        $postData = array(
            'listId' => $request->input('listId'),
            'quizs' => $request->input('quizs'),
            'folderId' => $request->input('folderId'),
            'listName' => $request->input('listName')
        );

        // 교사정보 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));
        
        // 이름이 있는지 확인
        if ($postData['listName']) {

            // 입력 실패한 문제의 배열상 위치를 반납
            $errorQuiz = array();

            // 리스트가 존재하는지, 폴더는 자기 폴더가 맞는지 확인
            $listUserCheck = DB::table('folders as f')
                ->select(
                    DB::raw('count(CASE WHEN l.number = ' . $postData['listId'] . ' THEN 1 END) as listCheck')
                )
                ->where([
                    'f.number' => $postData['folderId'],
                    'f.teacherNumber' => $userData['userId']
                ])
                ->leftJoin('lists as l', 'l.folderNumber', '=', 'f.number')
                ->groupBy('f.number')
                ->first();

            // 해당유저의 리스트인지 확인
            if ($listUserCheck) {
                if ($listUserCheck->listCheck == 0) {
                    $postData['listId'] = DB::table('lists')
                        ->insertGetId([
                            'name' => $postData['listName'],
                            'folderNumber' => $postData['folderId']
                        ], 'number');
                } else {
                    DB::table('lists')
                        ->where([
                            'number' => $postData['listId']
                        ])
                        ->update([
                            'name' => $postData['listName']
                        ]);

                    // 문제들 삭제
                    $this->deleteListQuiz($postData['listId']);
                }

                foreach ($postData['quizs'] as $quiz) {
                    // 문제를 저장
                    // 주관식 객관식 구분
                    if ($quiz['makeType'] == 'obj') {
                        $quizIds = DB::table('quizBanks')
                            ->insertGetId([
                                'question' => $quiz['question'],
                                'rightAnswer' => $quiz['right'],
                                'example1' => $quiz['example1'],
                                'example2' => $quiz['example2'],
                                'example3' => $quiz['example3'],
                                'type' => $quiz['quizType'] . ' ' . $quiz['makeType'],
                                'teacherNumber' => $userData['userId']
                            ], 'number');
                    } else if ($quiz['makeType'] == 'sub') {
                        $quizIds = DB::table('quizBanks')
                            ->insertGetId([
                                'question' => $quiz['question'],
                                'hint' => $quiz['hint'],
                                'rightAnswer' => $quiz['right'],
                                'type' => $quiz['quizType'] . ' ' . $quiz['makeType'],
                                'teacherNumber' => $userData['userId']
                            ], 'number');
                    } else {
                        $quizIds = false;
                    }

                    // 리스트에 문제를 연결
                    if ($quizIds) {
                        $insertCheck = DB::table('listQuizs')
                            ->insert([
                                'listNumber' => $postData['listId'],
                                'quizNumber' => $quizIds
                            ]);
                    } else {
                        $insertCheck = false;
                    }

                    // 입력 실패한 문제를 반납
                    if (!$insertCheck) {
                        array_push($errorQuiz, $quiz);
                    }
                }

                // 반납 값 정리
                // 입력 성공시
                if(count($errorQuiz) == 0){
                    $returnValue = array(
                        'check' => true
                    );
                }
                // 한문제라도 입력이 안되었을 경우
                else{
                    $returnValue = array(
                        'check' => false,
                        'errorQuiz' => $errorQuiz
                    );
                }
            } else {
                // 자기 폴더가 아닐 경우
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            // 레이스 이름이 없을 경우
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 리스트 삭제
     *
     * @param Request $request->input()
     *      'listId' 리스트 번호
     * 
     * @return array(
     *      'check' 삭제 성공 여부
     *  )
     */
    public function deleteList(Request $request){
        $postData = array(
            'listId' => $request->input('listId')
        );

        // 유저정보 받아오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 삭제할 리스트인지 확인
        if($userData){
            $listData = DB::table('lists as l')
                ->select(
                    'l.number                   as listId',
                    DB::raw('COUNT(r.number)    as raceCount')
                )
                ->where([
                    'l.number'          => $postData['listId'],
                    'f.teacherNumber'   => $userData['userId'],
                    'l.openState'       => 1
                ])
                ->join('folders as f',      'f.number',         '=', 'l.folderNumber')
                ->leftJoin('races as r',    'r.listNumber',     '=', 'l.number')
                ->groupBy('l.number')
                ->first();

            // 삭제할 리스트이면 삭제
            if ($listData && ((int)$listData->raceCount == 0)){
                // 문제들 삭제
                $this->deleteListQuiz($listData->listId);

                // 리스트삭제
                DB::table('lists')
                    ->where([
                        'number' => $listData->listId
                    ])
                    ->delete();

                // 반납하는 값
                $returnValue = array(
                    'check' => true
                );
            } else {
                // 반납하는 값
                $returnValue = array(
                    'check' => false
                );
            }
        } else {
            // 반납하는 값
            $returnValue = array(
                'check' => false
            );
        }

        return $returnValue;
    }

    /****
     * 리스트를 삭제하기 전에 문제들 삭제
     *
     * @param $listId // 리스트 아이디
     */
    private function deleteListQuiz($listId){
        // 문제 리스트 받아오기
        $listQuizs = DB::table('listQuizs')
            ->where([
                'listNumber' => $listId
            ])
            ->pluck('quizNumber')
            ->toArray();

        // 문제 리스트 삭제
        DB::table('listQuizs')
            ->where([
                'listNumber' => $listId
            ])
            ->delete();

        // 문제 삭제
        DB::table('quizBanks')
            ->whereIn('number', $listQuizs)
            ->delete();
    }

    /****
     * 리스트 수정 준비하기
     *
     * @param Request $request->input()
     *      'listId' 리스트 번호
     *
     * @return view($view)->with('response', $returnValue);
     *      $returnValue => array(
     *          'listId' 리스트 아이디
     *          'listName' 리스트 이름
     *          'folderId' 폴더 아이디
     *          'bookList' => $this->getBookGet()
     *          'quizs' => $this->getListQuiz(리스트 아이디)
     *          'check' 성공 여부
     *      )
     */
    public function updateList(Request $request){
        $postData = array(
            'listId' => $request->input('listId')
        );

        // 유저 데이터 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 권한확인하기
        $listData = DB::table('lists as l')
            ->select(
                'l.number                   as listId',
                'l.name                     as name',
                'f.number                   as folderId',
                DB::raw('COUNT(r.number)    as raceCount')
            )
            ->where([
                'l.number'          => $postData['listId'],
                'f.teacherNumber'   => $userData['userId'],
                'l.openState'       => 1
            ])
            ->join('folders as f',      'f.number',         '=', 'l.folderNumber')
            ->leftJoin('races as r',    'r.listNumber',     '=', 'l.number')
            ->groupBy('l.number')
            ->first();

        if($listData && ((int)$listData->raceCount == 0)){
            // 저장된 문제들 읽어오기
            $quizs = $this->getListQuiz($listData->listId);

            // 저장된 교재 정보 가져오기
            $bookList = $this->getBookGet();

            // 반납할 값 반납
            $returnValue = array(
                'listId'        => $listData->listId,
                'listName'      => $listData->name,
                'folderId'      => $listData->folderId,
                'bookList'      => $bookList,
                'quizs'         => $quizs,
                'check'         => true
            );
        } else {
            $returnValue = array(
                'check' => false
            );
        }

        if ($returnValue['check']) {
            $view = 'QuizTree/quiz_making';
        } else {
            $view = 'homepage';
        }

        return view($view)->with('response', $returnValue);
    }

    /****
     * 리스트 미리보기
     *
     * @param Request $request->input()
     *      'listId' 리스트 아이디
     *
     * @return array(
     *      'listName' 리스트 이름
     *      'quizs'     => $this->getListQuiz(리스트 아이디)
     *      'check' 조회 성공 여부
     *  )
     */
    public function showList(Request $request){
        $postData = array(
            'listId' => $request->input('listId')
        );

        // 유저 데이터 가져오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        // 권한확인하기
        $listData = DB::table('lists as l')
            ->select(
                'l.number   as listId',
                'l.name     as listName'
            )
            ->where([
                'l.number'          => $postData['listId']
            ])
            ->where(function ($query) use ($userData){
                $query->where('f.teacherNumber', '=', $userData['userId'])
                    ->orWhere('l.openState', '=', self::OPEN_STATE);
            })
            ->join('folders as f', 'f.number', '=', 'l.folderNumber')
            ->first();

        if($listData) {
            // 저장된 문제들 읽어오기
            $quizs = $this->getListQuiz($listData->listId);

            // 반납하는 값
            $returnValue = array(
                'listName'  => $listData->listName,
                'quizs'     => $quizs,
                'check'     => true
            );
        } else {
            $returnValue = array(
                'check'     => false
            );
        }

        return $returnValue;
    }

    /****
     * 리스트 공개 여부 설정
     *
     * @param Request $request->input()
     *      'listId' 리스트 아이디
     *
     * @return array(
     *      'listId' => 리스트 아이디,
     *      'check' => 성공 여부
     *  )
     */
    public function updateOpenState(Request $request){
        $postData = array(
            'listId' => $request->has('listId') ? $request->input('listId') : false
        );

        // 유저정보 읽어오기
        $userData = UserController::sessionDataGet($request->session()->get('sessionId'));

        if ($userData['check'] && $postData['listId']){
            switch ($userData['classification'] == 'teacher'){
                case 'teacher':
                case 'root':
                    $listData = DB::table('lists as l')
                        ->select(
                            'l.openState as openState'
                        )
                        ->where([
                            'l.number' => $postData['listId'],
                            'f.teacherNumber' => $userData['userId']
                        ])
                        ->join('folders as f', 'f.number', '=', 'l.folderNumber')
                        ->first();

                    $updateData = DB::table('lists as l')
                        ->where([
                            'l.number' => $postData['listId'],
                            'f.teacherNumber' => $userData['userId']
                        ])
                        ->join('folders as f', 'f.number', '=', 'l.folderNumber')
                        ->update([
                            'openState' => ($listData->openState == self::OPEN_STATE ? self::OPEN_NOT_STATE : self::OPEN_STATE)
                        ]);
                    break;
                default:
                    $updateData = false;
                    break;
            }

            if ($updateData){
                $returnValue = array(
                    'listId' => $postData['listId'],
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
     * 리스트에 저장된 문제 조회 하기
     * 
     * @param $listId // 리스트 아이디
     * @return array(
     *      0 => array(
     *          'quizId' 퀴즈 아이디
     *          'question' 문제
     *          'hint' 힌트
     *          'right' 정답
     *          'example1' 예문1
     *          'example2' 예문2
     *          'example3' 예문3
     *          'quizType' 문제 타입
     *          'makeType' 주관식 객관식 구분
     *      )
     *  )
     */
    private function getListQuiz($listId){
        // 저장된 문제들 읽어오기
        $quizData = DB::table('quizBanks as qb')
            ->select([
                'qb.number          as number',
                'qb.question        as question',
                'qb.hint            as hint',
                'qb.rightAnswer     as rightAnswer',
                'qb.example1        as example1',
                'qb.example2        as example2',
                'qb.example3        as example3',
                'qb.type            as type'
            ])
            ->where([
                'lq.listNumber' => $listId
            ])
            ->join('listQuizs as lq', 'lq.quizNumber', '=', 'qb.number')
            ->orderBy('qb.number', 'desc')
            ->get();

        // 반납값 정리
        $quizs = array();
        foreach ($quizData as $quiz) {
            $type = explode(' ', $quiz->type);
            array_push($quizs, array(
                'quizId'    => $quiz->number,
                'question'  => $quiz->question,
                'hint'      => $quiz->hint,
                'right'     => $quiz->rightAnswer,
                'example1'  => $quiz->example1,
                'example2'  => $quiz->example2,
                'example3'  => $quiz->example3,
                'quizType'  => $type[0],
                'makeType'  => $type[1]
            ));
        }

        return $quizs;
    }
}