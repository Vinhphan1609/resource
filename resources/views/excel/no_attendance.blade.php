<html>
    <table border="1" width="100%">
        <tr height="30">
            <td style="text-align: center">#</td>
            <td style="width: 30px">Full name</td>
            <td style="width: 30px">Email</td>
            <td style="width: 30px">Team</td>
            <td style="width: 30px">Ask for leave</td>
            <td style="width: 30px">Date</td>
        </tr>
        <?php $i = 1;?>
        @foreach($staffs_no_attendance as$staff)
            <tr>
                <td style="text-align: center">{{$i}}</td>
                <td>{{$staff->first_name}}</td>
                <td>{{$staff->email}}</td>
                <td>{{$staff->team_name}}</td>
                @if($staff->ask_for_leave == \App\Consts::ASK_FOR_LEAVE)
                    <td>Yes</td>
                @elseif($staff->ask_for_leave == \App\Consts::NO_ASK_FOR_LEAVE)
                    <td>No</td>
                @endif
                <td>{{$staff->day_leave}}</td>
            </tr>
            <?php $i++ ?>
        @endforeach
    </table>
</html>