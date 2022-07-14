<?php

namespace App;

class Consts
{
    const PASSWORD_DEFAULT = '1';
    const URL_API_CHAINOS = 'http://118.70.67.17:8850/api/smartkios/';
    const UNKNOWN = 'Unknown';
    const SLASH = '/';
    const DOT = '.';
    const SOUND_PATH = 'sound';
    // Role
    const ADMIN = 1;
    const SHOP_MANAGER = 2;
    const SHOP_USER = 3;
    const ATTENDANCE_MANAGER = 4;
    const ATTENDANCE_USER = 5;
    const LICENSE = 6;
    const SCHOOL_MANAGER = 7;
    const SCHOOL_USER = 8;
    const SCHOOL_ADMIN = 11;
    const MARKETING = 9;
    const SALE = 10;
    //
    const FULL_DAY = 300; // 5H
    const DIVISION_DAY = 150; // 2H30
    // User
    const SCHOOL_MANAGER_ID = 9;
    // Type
    const VISITED = 0;
    const AGE = 1;
    const EMOTION = 2;
    const TIME_RATE = 3;
    Const TYPE_BEING_LATE = 1;
    const TYPE_LEAVE_APPLICATION = 2;
    const TYPE_COMPLAIN = 3;
    // Email Default
    const COMPANY_NAME = "Chainos Solution";

    // Pagination
    const LIMIT = 10;
    const DEVICE_TYPE_IN_OUT = 'in_out';
    const DEVICE_TYPE_FACE_DETECTOR = 'face_detector';
    const DEVICE_TYPE_HEAD_MAP = 'head_map';
    // User Status
    const USER_STATUS_ACTIVE = 'active';
    const USER_STATUS_UNACTIVE = 'unactive';
    const USER_STATUS_FORGOT = 'fotgot';

    const DEVICE_TYPE = [
        NULL => '',
        Consts::DEVICE_TYPE_IN_OUT => 'In/Out',
        Consts::DEVICE_TYPE_FACE_DETECTOR => 'Face Detector',
        Consts::DEVICE_TYPE_HEAD_MAP => 'Head Map',
    ];

    const DEVICE_STATUS_ACTIVITY = 'activity';
    const DEVICE_STATUS_LOST_OF_SIGN = 'lost_of_sign';
    const DEVICE_STATUS_NOT_USED = 'not_used';
    const DEVICE_STATUS_UNCLEAR = 'unclear';

    const DEVICE_STATUS = [
        NULL => '',
        Consts::DEVICE_STATUS_ACTIVITY => 'Activity',
        Consts::DEVICE_STATUS_LOST_OF_SIGN => 'Lost Of Sign',
        Consts::DEVICE_STATUS_NOT_USED => 'Not Used',
        Consts::DEVICE_STATUS_UNCLEAR => 'Unclear',
    ];

    const USER_STATUS_NORMAL = 'normal';
    const USER_STATUS_LOCKED = 'locked';
    const USER_STATUS_BLOCKED = 'blocked';

    const USER_STATUS = [
        NULL => '',
        Consts::USER_STATUS_NORMAL => 'Normal',
        Consts::USER_STATUS_LOCKED => 'Locked',
        Consts::USER_STATUS_BLOCKED => 'Blocked',
    ];

    //color
    const COLOR_BEING_LATE = '#FFB6C1';
    const COLOR_LEAVE_APPLICATION = '#800000';
    const COLOR_COMPLAIN = '#9932CC';
    const COLOR_CONFIRMED = '#5bc0de';
    const COLOR_REJECT = '#777';
    const COLOR_EARLIER = '#00a65a';
    const COLOR_LATER = '#f56954';


    //
    const LUNCH_TIME = 90;

    //type shop
    const TYPE_SHOP = 1;
    const TYPE_SCHOOL = 2;
    const TYPE_SHOP_FASHION = 3;


    //gender
    const GENDER_MALE = "Male";
    const GENDER_FEMALE = "Female";

    const STRING_SCHOOL = "School";
    //Staff Note
    const CONFIRM_STAFF = 1;
    const CANCEL_STAFF = 2;

    //type_note
    const BEING_LATE = 1;
    const LEAVE_APPLICATION =2;
    const COMPLAIN =3;

    //configs
    const COMPLAIN_FULL_DAY = 7;
    const COMPLAIN_DIVISION_DAY = 8;

    //leave application
    const ASK_FOR_LEAVE = 1;
    const NO_ASK_FOR_LEAVE = 0;

    //Type attendance
    const STAFF_ATTENDANCE = "attendance";
    const STAFF_NO_ATTENDANCE = "no_attendance";

    //ID
    const ID_SHOP = 100;

    //time start, time end
    const TIME_START = "08:30:00";
    const TIME_END = "18:00:00";

}
