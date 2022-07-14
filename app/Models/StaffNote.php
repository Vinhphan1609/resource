<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffNote extends Model
{
    use HasFactory;
    protected $table = 'staff_note';
    public function getNotes($searchKey, $startDate, $endDate, $shop_id,$sort, $direction, $limit) {
        $notes = StaffNote::where('isConfirm', 0)->select('staff_note.id', 'staff_note.staff_id', 'staff_note.type_note_id', 'staff_note.reason_note', 'staff_note.date_note')
            ->LeftJoin('staffs', 'staff_note.staff_id', '=', 'staffs.staff_id')
            ->LeftJoin('shops', 'staffs.shop_id', '=', 'shops.id')
            ->groupBy('staff_note.id')
            ->when(!empty($searchKey), function ($query) use ($searchKey) {
                return $query->where('staffs.staff', 'like', '%' . $searchKey . '%');
            })->when(!empty($startDate), function ($query) use ($startDate) {
                return $query->where('staff_note.date_note', '>=', $startDate);
            })->when(!empty($endDate), function ($query) use ($endDate) {
                return $query->where('staff_note.date_note', '<', $endDate);
            })->when(!empty($sort), function ($query) use ($sort, $direction) {
                return $query->orderBy($sort, $direction);
            })->when(!empty($shop_id), function ($query) use ($shop_id) {
                return $query->where('staffs.shop_id', $shop_id);
            })
            ->paginate($limit);
        return $notes;
    }

    public function getStaffByStaffId($staff_id) {
        $staffNote = Staff::where('staff_id', $staff_id)->first();
        return $staffNote;
    }

    public function updateIsConfirm($id, $isConfirm) {
        $note = StaffNote::where('id', $id)->first();
        $note->isConfirm = $isConfirm;
        $note->save();
    }

    public function store($request) {
        $staffNote = new StaffNote;
        $staffNote->staff_id = $request->staff_id;
        $staffNote->type_note_id = $request->type_note_id;
        $staffNote->reason_note = $request->reason_note;
        $staffNote->date_note = $request->date_note;
        $staffNote->type_of_complain = $request->note_complain;
        $staffNote->code = Str::random(10);
        if($staffNote->save() == true){
            return $staffNote;
        }
        return null;
    }

    public function show($id) {
        $note = StaffNote::where('id', $id)->first();
        return $note;
    }

    public function updateNote($request, $id) {
        $note = StaffNote::where('id', $id)->update([
            'reason_note' => $request->reason_note,
        ]);
        return $note;
    }

    public function destroyNote($id) {
        return StaffNote::where('id', $id)->delete();
    }


    //get complain
    public function getComplain($searchKey, $startDate, $endDate, $shop_id,$sort, $direction, $limit, $type_note_id) {
        $complains = StaffNote::where('isConfirm', 0)->select('staff_note.id', 'staff_note.type_of_complain', 'staff_note.staff_id', 'staff_note.type_note_id', 'staff_note.reason_note', 'staff_note.date_note')
            ->LeftJoin('staffs', 'staff_note.staff_id', '=', 'staffs.staff_id')
            ->LeftJoin('shops', 'staffs.shop_id', '=', 'shops.id')
            ->groupBy('staff_note.id')
            ->when(!empty($searchKey), function ($query) use ($searchKey) {
                return $query->where('staffs.staff', 'like', '%' . $searchKey . '%');
            })->when(!empty($startDate), function ($query) use ($startDate) {
                return $query->where('staff_note.date_note', '>=', $startDate);
            })->when(!empty($endDate), function ($query) use ($endDate) {
                return $query->where('staff_note.date_note', '<', $endDate);
            })->when(!empty($sort), function ($query) use ($sort, $direction) {
                return $query->orderBy($sort, $direction);
            })->when(!empty($shop_id), function ($query) use ($shop_id) {
                return $query->where('staffs.shop_id', $shop_id);
            })->when(!empty($type_note_id), function ($query) use ($type_note_id) {
                return $query->where('staff_note.type_note_id', $type_note_id);
            })
            ->paginate($limit);
//        dd($complains);
        return $complains;
    }
}
