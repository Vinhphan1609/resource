<?php
namespace App\Repositories;
use App\Consts;
use App\Models\Staff;
use App\Models\StaffNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class StaffsRepository
{
    /**
     * get model
     * @return string
     */
    // model property on class instances
    // protected $model;

    // // Constructor to bind model to repo
    // public function __construct(Model $model)
    // {
    //     $this->model = $model;
    // }
    public function getStaff($searchKey, $startDate, $endDate, $sort, $direction, $limit, $type = null, $team_id = null, $request)
    {
        $data = Staff::select(
            "users.id as userId",
            "staffs.id",
            "teams.start_time",
            "teams.id as team_id",
            "teams.team_name",
            "staffs.shop_id",
            "staffs.staff_id",
            "staffs.staff",
            "staffs.checkin",
            "staffs.checkout",
            "users.full_name",
            // "users.last_name",
            DB::raw('SUBTIME(TIME_FORMAT(staffs.checkin, "%H:%i:%s"), "'."08:30:00".'")  AS time')
        )   ->leftJoin('users', 'users.staff_id', 'staffs.staff_id')
            ->leftJoin('user_teams', 'user_teams.user_id', 'users.id')
            ->leftJoin('teams', 'teams.id', 'user_teams.team_id')
            ->when(!empty($startDate), function ($query) use ($startDate) {
                return $query->where('staffs.checkin', '>=', $startDate);
            })->when(!empty($endDate), function ($query) use ($endDate) {
                return $query->where('staffs.checkin', '<', $endDate);
            })->when(!empty($searchKey), function ($query) use ($searchKey) {
                return $query->where('staffs.staff', 'like', '%' . $searchKey . '%');
            })->when(!empty($sort), function ($query) use ($sort, $direction) {
                return $query->orderBy($sort, $direction);
            })
            ->where('users.id', '!=', null)
            // ->where('users.is_active', true)
            ->orderBy('staffs.checkin','DESC')
            ->paginate($limit);
            $array = [];
            foreach ($data as $user){
                $array[] = $user->userId;
            }
            if(isset($team_id) && !empty($team_id)) {
                $data = $data->filter(function ($value, $key) use ($team_id) {
                    return $value['team_id'] == $team_id;
                });
            }
            if(isset($type) && !empty($type)){
                if ($type == "up") {
                    $data = $data->filter(function ($value, $key) {
                        return ($value['time'] <= "00:00:00");
                    });
                } else if($type == 'lower') {
                    $data = $data->filter(function ($value, $key) {
                        return ($value['time'] > "00:00:00");
                    });
                }
            }
            if($type == 'no_atten'){
                $staffs = User::select(
                    // "users.role_id",
                    "users.staff_id",
                    // "users.parent_id",
                    "users.name",
                    "users.email",
                    "users.full_name",
                    // "users.last_name",
                    // "user_shops.user_id",
                    // "user_shops.shop_id",
                    "user_teams.team_id",
                    "teams.team_name"
                )
                    // ->Join('user_shops','user_shops.user_id', '=', 'users.id')
                    ->leftJoin('user_teams', 'user_teams.user_id', 'users.id')
                    ->leftJoin('teams', 'teams.id', 'user_teams.team_id')
                    ->where('users.staff_id', '!=', null)
                    // ->where('users.is_active', true)
                    ->whereNotIn('users.id', $array)
                    ->orderBy('users.staff_id')
                    ->paginate($limit);
                return $staffs;
            } else if ($type == '') {
                return $data;
            }

    }
    public function getRealTime($checkin, $checkout) {
        $timeLunch = Carbon::parse("12:00:00");
        $timeEndLunch = Carbon::parse("13:30:00");
        $startWork =  Carbon::parse(Carbon::parse($checkin)->format("H:m:s"));
        $endWork = Carbon::parse(Carbon::parse($checkout)->format("H:m:s"));
        $timeInCompany = $startWork->diffInMinutes($endWork);
//        dd($startWork, $timeLunch);
        if($startWork < $timeLunch && $endWork > $timeEndLunch) {
            $time['lunch_time'] = 90;
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

}


