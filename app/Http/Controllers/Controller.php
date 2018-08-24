<?php

namespace app\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    // 공유 레이스 번호
    const OPEN_STATE = 0;
    const OPEN_NOT_STATE = 1;

    // 재시험 구분
    const RETEST_NOT_STATE  = 0;
    const RETEST_STATE      = 1;

    // 정규식 확인용
    const DATE_FORMAT = '/^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))$/g';
    const GROUP_ID_FORMAT = '/^[0-9]{0}[1-9]$/';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
