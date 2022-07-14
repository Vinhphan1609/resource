<html>
    <?php $month = \Carbon\Carbon::now()->format("Y-m");?>
    <table border="1" width="100%">
        <tr height="30">
            <td style="text-align: center">#</td>
            <td style="width: 30px">Name</td>
            <td></td>
            <td colspan="5" style="font-weight: bold; text-align: center;" >Total</td>
            <td colspan="9" style="font-weight: bold; text-align: center;" ></td>
            @for($day = 1; $day <= $countDayInMonth; $day++)
                @if(\Carbon\Carbon::parse($month.'-'.$day)->isWeekend() == true)
                    <td colspan="4" style="font-weight: bold; text-align: center; background-color: #d8d8d8" >{{$day}}</td>
                @else
                    <td colspan="4" style="font-weight: bold; text-align: center;" >{{$day}}</td>
                @endif
            @endfor
        </tr>
        <tr height="30">
            <td style="text-align: center"></td>
            <td style="width: 30px"></td>
            <td style="width: 30px"></td>
            <td></td>
            <td colspan="2" style="font-weight: bold; text-align: center;" >Full</td>
            <td colspan="2" style="font-weight: bold; text-align: center;" >Part</td>
            <td colspan="3" style="font-weight: bold; text-align: center;" >Total</td>
            <td style="width: 30px"></td>
            <td style="width: 30px"></td>
            <td style="width: 30px"></td>
            <td style="width: 30px"></td>
            <td style="width: 30px"></td>
            <td style="width: 30px"></td>
            @for($day = 1; $day <= $countDayInMonth; $day++)
            <td colspan="4" style="font-weight: bold; text-align: center;" ></td>
            @endfor
        </tr>
        <tr>
            <td></td>
            <td></td>

            <td style="text-align: center; width: 15px;">Employee ID</td>
            <td style="text-align: center; width: 15px;">No Of Current Holiday</td>

            <td style="text-align: center; width: 15px;">Working days</td>
            <td style="text-align: center; width: 15px;">Holiday</td>
            <td style="text-align: center; width: 15px;">Working days</td>
            <td style="text-align: center; width: 15px;">Holiday</td>
            <td style="text-align: center; width: 15px;">Working days</td>
            <td style="text-align: center; width: 15px;">Holiday</td>
            <td style="text-align: center; width: 15px;">Total</td>
            <td style="text-align: center; width: 15px;">No of working days</td>
            <td style="text-align: center; width: 15px;">Day of in month</td>
            <td style="text-align: center; width: 15px;">No of left holiday</td>
            <td style="text-align: center; width: 15px">Working Time</td>
            <td style="text-align: center; width: 20px">No of 8 hour days</td>
            <td style="text-align: center; width: 20px">Difference</td>
            @for($day = 1; $day <= $countDayInMonth; $day++)
                <td style="text-align: center; width: 15px">Checkin</td>
                <td style="text-align: center; width: 15px">Checkout</td>
                <td style="text-align: center; width: 15px">Total</td>
                <td style="text-align: center; width: 15px">Real Time</td>
            @endfor

        </tr>
        <?php $i = 1;?>
        @foreach($staffTimeKeeping as $name => $timeKeeping)
            <tr>
                <td style="text-align: center;"> {{ $i }}</td>
                <td>{{ $name }}</td>
                <td style="text-align: center; ">{{$timeKeeping[$countDayInMonth+1]['employee_id']}}</td>
                <td style="text-align: center; ">{{$timeKeeping[$countDayInMonth+1]['no_of_current_holiday']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['working_days_full']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['holiday_full']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['working_days_part']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['holiday_part']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['total_working_days']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['total_holiday']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['total_day_work']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['no_of_working_day']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['day_of']}}</td>
                <td style="text-align: center;">{{$timeKeeping[$countDayInMonth+1]['no_of_left_holiday']}}</td>
                <td style="text-align: center;">
                    {{--  Working Time--}}
                    {{App\Helpers\Helper::convertMinuteToTime($timeKeeping[$countDayInMonth+1]['total_real_time']) }}
                </td>
                <td style="text-align: center;">
                    {{--  No of 8 hour days--}}
                    {{App\Helpers\Helper::convertMinuteToDay($timeKeeping[$countDayInMonth+1]['total_real_time'] / 8) }}
                </td>
                <td style="text-align: center;">
                    {{--  Difference--}}
                    {{App\Helpers\Helper::convertMinuteToDay($timeKeeping[$countDayInMonth+1]['total_real_time'] / 8) - $timeKeeping[$countDayInMonth+1]['total_day_work']}}
                </td>
                @for($day = 1; $day <= $countDayInMonth; $day++)
                    <?php $day = $day <= 9 ? "0".$day : $day;?>
                    @if(isset($timeKeeping[$day]) && !empty($timeKeeping[$day]['checkin']))
                        <td style="text-align: right;">{{$timeKeeping[$day]['checkin']}}</td>
                        <td style="text-align: right;">{{$timeKeeping[$day]['checkout']}}</td>
                        <td style="text-align: right;">
                            {{ App\Helpers\Helper::convertMinuteToTime($timeKeeping[$day]['total']) }}
                        </td>
                        <td style="text-align: right;">{{ App\Helpers\Helper::convertMinuteToTime($timeKeeping[$day]['real_time']) }}</td>
                    @elseif(isset($timeKeeping[$day]) && empty($timeKeeping[$day]['checkin']))
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;">NP</td>
                    @else
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                    @endif
                @endfor
            </tr>
            <?php $i++ ?>
        @endforeach
    </table>
</html>
