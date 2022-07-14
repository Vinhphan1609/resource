<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    protected $table = 'staffs';
    protected $fillable = [
        'id',
        'staff_id',
        'staff',
        'created_at',
        'updated_at'
    ];
    public $timestamp = true;

    public function user_shop() {
        return $this->hasMany('App\Models\UserShop','shop_id','shop_id') ;
    }
    public function updateWhenRendBase64($staff, $imageUser) {
        Staff::where('staff', $staff)->update([
            'image_path' => $imageUser
        ]);
    }
    public function getStaffByStaffId($staff_id) {
        return Staff::where('staff_id', $staff_id)->first();
    }
}
