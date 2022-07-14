<?php

namespace App\Http\Controllers;

use App\Exports\Checktime;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Staff;
use App\Repositories\StaffsRepository;
use App\Utils;
use Illuminate\Support\Facades\DB;
use DatePeriod;
use DateTime;
use DateInterval;
use App\Repositories\UserRepository;
use App\Exports\StaffsTimeKeepingExport;
use App\Exports\Test;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class StaffsController extends Controller
{
    public function __construct(StaffsRepository $staffsRepository,UserRepository $userRepository)
    {
        // $user = new User();
        $this->staffRepository = $staffsRepository;
        $this->userRepository = $userRepository;
        // $this->model = new BaseRepository($staff);
        // $this->staffRepository = new StaffsRepositories();
        // $this->userRepository = new UserRepository($user);
    }
    public function index( Request $request ){
        $teams = [null => " -- All --"];
        $sort = $request->input('sort', "checkin");
        $direction = $request->input('direction', "desc");
        $team_id = $request->input('team_id', key($teams));
        $limit = $request->input('limit', 25);
        $type = $request->input('type', null);
        $searchKey = $request->input('searchKey', null);
        if(!empty($request->endDate)){
            $startDate = $request->input('startDate', 10);
            $endDate = $request->input('endDate', 10);
        } else {
            $startDate = Utils::formatStartDateForStaff($request->startDate);
            $endDate = Utils::formatEndDateForStaff($request->startDate);
        }
        
        $typeExport = $request->input('typeExport', 'admin');
        //get all day in period time
        $periodTime = $this->getPeriodTime($startDate, $endDate);
        //get staff no attendance
        $allStaffsNoAttendance = $this->getStaffNoAttendanceInPeriodTime($periodTime, $searchKey, $sort, $direction, $limit, $type, $team_id, $request);
        
        // dd($StaffsNoAttendance);
        $staffs = $this->staffRepository->getStaff($searchKey, $startDate, $endDate, $sort, $direction, $limit, $type, $team_id, $request);
        if($type == '') {
            foreach ($allStaffsNoAttendance as $staff) {
                $staffs[] = $staff;
            }
        }
        foreach ($staffs as $staff) {
            if(!empty($staff->checkin)) {
                $staff['time_in_company'] = $this->staffRepository->getRealTime($staff->checkin, $staff->checkout);
            }
        }
        $user = DB::table('staffs')->get();

        // dd($staffs);
        return view('admin.staffs.index',compact('staffs',
        'typeExport',
        'searchKey',
        'startDate',
        'endDate',
        'type',
        'sort',
        'direction',
        'teams',
        'team_id',
        'allStaffsNoAttendance',
        'user'
    ));
    }
    public function getPeriodTime($startDate, $endDate) {
        $periodTime = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            new DateTime($endDate)
        );

        return $periodTime;
    }
    public function getStaffNoAttendanceInPeriodTime($periodTime, $searchKey, $sort, $direction, $limit, $type, $team_id, $request) {
        $type = 'no_atten';
        $notes = [];
        $staffsNoAttendance = [];
        $staffs = [];
    //    dd($startDate);
        foreach ($periodTime as $keyTime => $value) {
            $valueTimeStart = $value->format('Y-m-d');
            $valueTimeEnd = $value->format('Y-m-d 23:59:59');
            $notes[$valueTimeStart] = $this->userRepository->getUserNoteInDayIsConfirmed($valueTimeStart);
            // $this->staffRepository->getStaff($searchKey, $startDate, $endDate, $sort, $direction, $limit, $type, $team_id, $request);
            $staffsNoAttendance[$valueTimeStart] =$this->staffRepository->getStaff( $searchKey, $valueTimeStart, $valueTimeEnd, $sort, $direction, $limit, $type, $team_id, $request);
        //    dd($staffsNoAttendance[$valueTimeStart]);
            foreach ($staffsNoAttendance[$valueTimeStart] as $keyStaff => $staff) {
                $staffs[] = $staff;
                $staff['day_leave'] = $valueTimeStart;
                foreach ($notes[$valueTimeStart] as $note) {
                    if($note->staff_id == $staff->staff_id) {
                        $staff['ask_for_leave'] = 1 ;
                    } else {
                        $staff['ask_for_leave'] = 0;
                    }
                }
            }
        }
        // dd($staffs);
        return $staffs;
    }
    public function exportExcel(Request $request) {
        // dd($request);
        // $shop_id = $request->shop_id;
        $month = Carbon::parse($request->startDate)->format('m');
        $year = Carbon::parse($request->startDate)->format('Y');
        $countDayInMonth = Carbon::parse($request->startDate)->daysInMonth;
        $typeExport = $request->typeExport;
        return Excel::download(new StaffsTimeKeepingExport( $month, $year, $countDayInMonth, $typeExport), 'staff_time_keeping_export.xlsx');

        // return Excel::download(new Checktime, 'users.xlsx');
        // return Excel::download(new Checktime,'x.xlxs');
    }
    // public function test(){
    //     return Excel::download(new Test,'text.xlsx');
    // }
}
