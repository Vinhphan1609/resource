<html>
<?php $month = \Carbon\Carbon::now()->format("Y-m");?>
<table border="1" width="100%">
    <tr height="30">
        <td style="text-align: center">#</td>
        <td style="width: 30px">Name</td>
        <td style="font-weight: bold; text-align: center;" >Total</td>
        @for($day = 1; $day <= $countDayInMonth; $day++)
            @for($day = 1; $day <= $countDayInMonth; $day++)
                @if(\Carbon\Carbon::parse($month.'-'.$day)->isWeekend() == true)
                    <td style="font-weight: bold; text-align: center; background-color: #d8d8d8" >{{$day}}</td>
                @else
                    <td style="font-weight: bold; text-align: center;" >{{$day}}</td>
                @endif
            @endfor
        @endfor
    </tr>
    <?php $i = 1;?>
    @foreach($staffTimeKeeping as $name => $timeKeeping)
        <tr>
            <td style="text-align: center;"> {{ $i }}</td>
            <td>{{ $name }}</td>
            <td style="text-align: right;">{{$timeKeeping[$countDayInMonth+1]['total_day_work']}}</td>
            @for($day = 1; $day <= $countDayInMonth; $day++)
                <?php $day = $day <= 9 ? "0".$day : $day;?>
                @if(isset($timeKeeping[$day]) && !empty($timeKeeping[$day]['day_work']))
                    <td style="text-align: right;">{{ $timeKeeping[$day]['day_work'] }}</td>
                @elseif(isset($timeKeeping[$day]) && empty($timeKeeping[$day]['checkin']))
                    <td style="text-align: right;">NP</td>
                @else
                    <td style="text-align: right;"></td>
                @endif
            @endfor
        </tr>
        <?php $i++ ?>
    @endforeach

</table>
</html>