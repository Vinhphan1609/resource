<?php

namespace App\Exports;

use App\Consts;
use App\Models\Staff;
use App\Models\StaffNote;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Configs;

class StaffsTimeKeepingExport implements  FromView, WithTitle,ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $month;
    public $year;
    public $countDayInMonth;
    public function __construct($month, $year, $countDayInMonth, $typeExport)
    {
        // $this->shop_id = $shop_id;
        $this->month = $month;
        $this->year = $year;
        $this->typeExport = $typeExport;
        $this->countDayInMonth = $countDayInMonth;
        $staff = new Staff();
        // dd($staff);
        // $imageRepository = new ImageRepository();
        $this->staff = $staff;
        // $this->imageReposiroty = $imageRepository;
    }
    
    function lastNameSort($a, $b) {
        $aLast = $this->split_name($a);
        $bLast = $this->split_name($b);
        return strcasecmp($aLast, $bLast);
    }

    public function getStaffTimeKeeping() {
        $staffs = Staff::select('staffs.staff_id','staffs.staff', 'staffs.checkin', 'staffs.checkout', 'users.left_annual_leave', 'users.annual_leave')
        // ->leftJoin('users', 'staffs.staff', '=', $this->stripVN('users.first_name'))
            ->leftJoin('users', 'staffs.staff_id', '=','users.staff_id')
            // ->where('staffs.shop_id', $this->shop_id)
            // ->whereNull('users.staff_id')
            // ->where('users.parent_id','=','8')
            ->whereNotNull('users.staff_id')
            ->whereYear('staffs.checkin', $this->year)
            ->whereMonth('staffs.checkin', $this->month)->get()->toarray();
        $staff_note = (StaffNote::whereYear('date_note', $this->year)->whereMonth('date_note', $this->month)->get()->toArray());
        $configs = Configs::get();
    //    dd($staffs);
        $staffTimeKeeping = [];
        foreach ($staffs as $key => $staff) {
            $datetime = Carbon::parse($staff['checkin']);
            $checkin_day = (Carbon::parse($staff['checkin'])->format('d-m-Y'));
            $day = Carbon::parse($staff['checkin'])->format("d");
            // dd($day);
            if(!empty($staff['checkin'])) {
                $staff['checkin'] = Carbon::parse($staff['checkin'])->format("H:i:s");
            }
            if(!empty($staff['checkout'])) {
                $staff['checkout'] = Carbon::parse($staff['checkout'])->format("H:i:s");
            }
            $timeInCompany = $this->getRealTime($staff['checkin'], $staff['checkout']);
            $staff['lunch_time'] = $timeInCompany['lunch_time'];
            $staff['real_time'] = $timeInCompany['real_time'];

            //set real_time if complain is confirm
            foreach ($staff_note as $note) {
                $day_note = Carbon::parse($note['date_note'])->format('d-m-Y');
                if($checkin_day == $day_note && $note['isConfirm'] == Consts::CONFIRM_STAFF && $note['type_note_id'] == Consts::COMPLAIN) {
                    $staff['real_time'] = $this->setRealTimeComplainIsConfirm($note,$configs)['real_time'];
                }
            }


            $staffName = $this->staff->getStaffByStaffId($staff['staff_id'])->staff;
            if(!empty($staff['checkin'])) {
                $dateStart = Carbon::parse($staff['checkin']);
                $dateEnd   = Carbon::parse($staff['checkout']);
                $staff['total'] = $dateStart->diffInMinutes($dateEnd);

                //count day work and count enought time and not
                if($staff['real_time'] >= Consts::FULL_DAY) {
                    $staff['working_days_full'] = $this->isWorkingDays($datetime) ? 1 : 0;
                    $staff['holiday_full'] = $this->isHolidays($datetime)? 1 : 0;
                    $staff['working_days_part'] = 0;
                    $staff['holiday_part'] =  0;
                    $staff['time_enought'] = 1;
                    $staff['time_not_enought'] = 0;
                    $staff['day_work'] = 1;
                    $staff['day_of'] = 0;
                } else if($staff['real_time'] >= Consts::DIVISION_DAY && $staff['real_time'] < Consts::FULL_DAY ) {
                    $staff['working_days_full'] = 0;
                    $staff['holiday_full'] = 0;
                    $staff['working_days_part'] = $this->isWorkingDays($datetime) ? 1 : 0;
                    $staff['holiday_part'] = $this->isHolidays($datetime)? 1 : 0;
                    $staff['time_enought'] = 0;
                    $staff['time_not_enought'] = 1;
                    $staff['day_work'] = 0.5;   
                    $staff['day_of'] = 0;
                }
                else {
                    $staff['working_days_full'] = 0;
                    $staff['holiday_full'] = 0;
                    $staff['working_days_part'] = 0;
                    $staff['holiday_part'] =  0;
                    $staff['time_enought'] = 0;
                    $staff['time_not_enought'] = 0;
                    $staff['day_work'] = 0;
                    $staff['day_of'] = 0;
                }
            }   else {
                $staff['total'] = 0;
            }
            $staffTimeKeeping[$staffName][$day] = $staff;
        }
        //set day work = 1 if admin confirm day off
        foreach ($staff_note as $staff) {
            $day = Carbon::parse($staff['date_note'])->format("d");
            if($this->staff->getStaffByStaffId($staff['staff_id']) != null) {
                $staffName = $this->staff->getStaffByStaffId($staff['staff_id'])->staff;
                if(empty($staffTimeKeeping[$staff['staff_id']][$day]) && $staff['isConfirm'] == Consts::CONFIRM_STAFF && $staff['type_note_id'] == "2") {
                    $staff['time_enought'] = 0;
                    $staff['time_not_enought'] = 0;
                    $staff['day_work'] = 1;
                    $staff['working_days_full'] = 0;
                    $staff['holiday_full'] = 0;
                    $staff['working_days_part'] = 0;
                    $staff['holiday_part'] = 0;
                    $staff['real_time'] = 0;
                    $staff['day_of'] = 1;
                    $staffTimeKeeping[$staffName][$day] = $staff;
                }
            }
        }
        //set day work if admin confirm complain
        foreach ($staff_note as $staff) {
            if($this->staff->getStaffByStaffId($staff['staff_id']) != null) {
                $datetime = Carbon::parse($staff['date_note']);
                $day = Carbon::parse($staff['date_note'])->format("d");
                $staffName = $this->staff->getStaffByStaffId($staff['staff_id'])->staff;
                if(!isset($staffTimeKeeping[$staff['staff_id']][$day]) && $staff['isConfirm'] == Consts::CONFIRM_STAFF && $staff['type_note_id'] == Consts::COMPLAIN) {
                    if($staff['type_of_complain'] == $configs[Consts::COMPLAIN_DIVISION_DAY-1]['value']) {
                        $staff['time_enought'] = 0;
                        $staff['time_not_enought'] = 1;
                        $staff['day_work'] = 0.5;
                        $staff['working_days_full'] =  0;
                        $staff['holiday_full'] =  0;
                        $staff['working_days_part'] = $this->isWorkingDays($datetime) ? 1 : 0;
                        $staff['holiday_part'] = $this->isHolidays($datetime)? 1 : 0;
                        $staff['real_time'] = 0;
                        $staff['day_of'] = 0;
                        $staffTimeKeeping[$staffName][$day] = $staff;
                    } else if($staff['type_of_complain'] == $configs[Consts::COMPLAIN_FULL_DAY-1]['value']) {
                        $staff['time_enought'] = 1;
                        $staff['time_not_enought'] = 0;
                        $staff['day_work'] = 1;
                        $staff['working_days_full'] = $this->isWorkingDays($datetime) ? 1 : 0;;
                        $staff['holiday_full'] = $this->isHolidays($datetime)? 1 : 0;
                        $staff['working_days_part'] = 0;
                        $staff['holiday_part'] = 0;
                        $staff['real_time'] = 0;
                        $staff['day_of'] = 0;
                        $staffTimeKeeping[$staffName][$day] = $staff;
                    }
                }
            }
        }

//        dd($staffTimeKeeping["Le Cong Chinh"]);
        //set position image
        $position = 3;
        $imageUser = null;
        foreach ($staffTimeKeeping as $key => $timeKeeping) {
            $employee_id = null;
            $total_working_days_full = 0;
            $total_holiday_full = 0;
            $total_working_days_part = 0;
            $total_holiday_part = 0;
            $total_time_enought = 0;
            $total_time_not_enought = 0;
            $total_day_work = 0;
            $total_real_time = 0;
            $holiday = 0;
            $total_day_of = 0;
            foreach ($timeKeeping as $keep) {
                if(!empty($keep['isConfirm']) && ($keep['isConfirm'] == Consts::CONFIRM_STAFF)) {
                    $holiday += 1;
                }
                $employee_id = $keep['staff_id'];
                $total_working_days_full += $keep['working_days_full'];
                $total_holiday_full += $keep['holiday_full'];
                $total_working_days_part += $keep['working_days_part'];
                $total_holiday_part += $keep['holiday_part'];

                $total_time_enought += $keep['time_enought'];
                $total_time_not_enought += $keep['time_not_enought'];
                $total_day_work += $keep['day_work'];
                $total_real_time += $keep['real_time'];
                $total_day_of += $keep['day_of'];
            }
            $staff_id = reset($staffTimeKeeping[$key])['staff_id'];
            $staffTimeKeeping[$key][$this->countDayInMonth+1] = [
                'employee_id' => $employee_id,
                'working_days_full' => $total_working_days_full,
                'holiday_full' => $total_holiday_full,

                'working_days_part' => $total_working_days_part,
                'holiday_part' => $total_holiday_part,

                'total_working_days' => $total_day_work - ($total_holiday_full + $total_holiday_part/2),
                'total_holiday' => $total_holiday_full + $total_holiday_part/2,

                'total_time_enought' =>$total_time_enought,
                'total_time_not_enought' => $total_time_not_enought,
                'total_day_work' => $total_day_work,
                'total_real_time' => $total_real_time,
                'no_of_left_holiday' => reset($staffTimeKeeping[$key])['left_annual_leave'],
                'no_of_current_holiday' => reset($staffTimeKeeping[$key])['annual_leave'],
                'no_of_working_day' => $this->countNoOfWorkingDay($this->year, $this->month, $this->countDayInMonth),
                'day_of' => $total_day_of
//                'avatar' => $avatar'
                
            ];
            $position +=1;
        }

        //Sort last names by alpha
        $arrStaffName = [];
        foreach ($staffTimeKeeping as $key => $value) {
            $arrStaffName[$key] = $key;
        }

        uasort($arrStaffName, array($this, 'lastNameSort'));
        foreach ($arrStaffName as $key => $value) {
            $arrStaffName[$key] = $staffTimeKeeping[$key];
        }

        return $arrStaffName;
    }

//    public function convertImageToBase64IfImagePathInvalid($key) {
//        $image = Staff::where('staff_id', $key)->select('image')->first()->image;
//        $imageUser = null;
//        if($image != null) {
//            $imageUser = $this->imageReposiroty->base64_to_image_for_export($image);
//            if($imageUser != null) {
//                $this->staff->updateWhenRendBase64($key, $imageUser);
//            }
//        }
//    }

    public function view(): View
    {
        if($this->typeExport == "admin") {
            return view('excel.staff_time_keeping', [
                'staffTimeKeeping' => $this->getStaffTimeKeeping(),
                'countDayInMonth' => $this->countDayInMonth,
            ]);
        } else if($this->typeExport == "staff") {
            return view('excel.time_keeping_day_work',[
                'staffTimeKeeping' => $this->getStaffTimeKeeping(),
                'countDayInMonth' => $this->countDayInMonth,
            ]);
        }
    }

//    public function drawings()
//    {
//        $staffTimeKeeping = $this->getStaffTimeKeeping();
//        $avatar = [];
//        foreach ($staffTimeKeeping as $key=>$timeKeeping) {
//            $image = $staffTimeKeeping[$key][$this->countDayInMonth+1]['avatar'];
//            if($image != null) {
//                $avatar[] = $image;
//            }
//        }
//        return $avatar;
//    }

    //get real time work and lunch time
    public function getRealTime($checkin, $checkout) {
        $timeLunch = Carbon::parse("12:00:00");
        $timeEndLunch = Carbon::parse("13:30:00");
        $startWork =  Carbon::parse(Carbon::parse($checkin)->format("H:m:s"));
        $endWork = Carbon::parse(Carbon::parse($checkout)->format("H:m:s"));
        $timeInCompany = $startWork->diffInMinutes($endWork);
        if($startWork < $timeLunch && $endWork > $timeEndLunch) {
            $time['lunch_time'] = Consts::LUNCH_TIME;
            $time['real_time'] = $timeInCompany-$time['lunch_time'];
        } else if($startWork < $timeLunch && $endWork > $timeLunch && $endWork < $timeEndLunch) {
            $time['lunch_time'] = $endWork->diffInMinutes($timeLunch);
            $time['real_time'] = $timeInCompany - $time['lunch_time'];
        } else if($startWork > $timeLunch && $startWork < $timeEndLunch && $endWork > $timeEndLunch) {
            $time['lunch_time'] = $startWork->diffInMinutes($timeEndLunch);
            $time['real_time'] = $timeInCompany - $time['lunch_time'];
        } else if($startWork > $timeEndLunch || $endWork < $timeLunch) {
            $time['real_time'] = $timeInCompany;
            $time['lunch_time'] = 0;
        } else {
            $time['lunch_time'] = 0;
            $time['real_time'] = 0;
        }

        return $time;

    }

    public function title(): string
    {
        return 'Month '.$this->month.'-'.$this->year;
    }

    public function isWorkingDays($date){
        if($date->isWeekday()){
            return true;
        } else {
            return false;
        }
    }

    public function isHolidays($date){
        if($date->isWeekend()){
            return true;
        } else {
            return false;
        }
    }

    public function countNoOfWorkingDay($year, $month, $dayInMonth) {
        $noOfWorkingDay = $dayInMonth;
        for ($day = 1; $day <= $dayInMonth; $day++) {
            $dayDateTime = Carbon::create($year, $month, $day);
            if($dayDateTime->isWeekend()) {
                $noOfWorkingDay -=1;
            }
        }
        return $noOfWorkingDay;
    }

    public function setRealTimeComplainIsConfirm($note, $configs) {
        if($note['type_of_complain'] == $configs[Consts::COMPLAIN_FULL_DAY-1]['value']) {
            $realtime['real_time'] = Consts::FULL_DAY;
        } else if($note['type_of_complain'] == $configs[Consts::COMPLAIN_DIVISION_DAY-1]['value']) {
            $realtime['real_time'] = Consts::DIVISION_DAY;
        } else {
            $realtime['real_time'] = 0;
        }

        return $realtime;
    }    
    //get last name
    function split_name($name) {
        $name = trim($name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        return $last_name;
    }
    function stripVN($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
    
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        return $str;
    }
}
