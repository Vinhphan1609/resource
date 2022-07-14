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
class UserRepository 
{
    /**
     * get model
     * @return string
     */
    public function getUserNoteInDayIsConfirmed($day) {
        return User::leftjoin('staff_note', 'users.staff_id', 'staff_note.staff_id')
            ->where('users.staff_id', '!=', null)
            // ->where('users.role_id', 5)
            ->where('staff_note.date_note', Carbon::parse($day)->toDateString())
            ->where('staff_note.type_note_id', 2)
            ->where('staff_note.isConfirm', 1)
            ->get();
    }
}