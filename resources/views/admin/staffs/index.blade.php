<table class="table table-bordered">
    <thead>
    {{-- <tr>
        <th class="text-center">@sortablelink('staffs.staff', __('action.Staff'))</th>
        <th class="text-center">{!! __('action.teams') !!}</th>
        @if($type == 'no_atten')
            <th class="text-center">Ask for leave</th>
            <th class="text-center">Date</th>
        @else
            <th class="text-center">@sortablelink('staffs.checkin', __('action.Time Late'))</th>
            <th class="text-center">Working day</th>
            <th class="text-center">@sortablelink('staffs.checkin', __('Check-in'))</th>
            <th class="text-center">@sortablelink('staffs.checkin', __('Check-out'))</th>
        @endif
    </tr> --}}
    <tr>
        <th>Name</th>
        <th>teams</th>
        <th>late</th>
        <th>Working day</th>
        <th>Check-in </th>
        <th>Check-out </th>
        <th><a href="{{ url('export-excel?shop_id='.'&startDate='.$startDate.'&endDate='.$endDate.'&typeExport=admin') }}"  class="btn btn-success btn_x" style="margin-top: 25px; width: 50px; height: 36px;">
            excel
        </a></th>

    </tr>
    </thead>
    <tbody>
    @if($type == 'no_atten')
        @foreach($staffsNoAttendance as $staff)
            <tr class="odd gradeX text-center {{$staff->id}} ___ ">
                <td><a href="{{url('admin/staffs/calendar/'.$staff->staff_id)}}">{{ $staff->full_name}}</a></td>
                <td>{{ $staff->team_name }}</td>
                @if($staff->ask_for_leave == 1)
                    <td class="text-center" style="color: yellow"><i class="fa fa-star"></i></td>
                @else
                    <td class="text-center"></td>
                @endif
                <td class="text-center">{{$staff->day_leave}}</td>
            </tr>
        @endforeach
    @else
        @foreach ($staffs as $staff)
        
            <tr class="odd gradeX text-center ">
                <td><a href="{{url('admin/staffs/calendar/'.$staff->staff_id)}}">{{ $staff->full_name }}</a></td>
                <td>{{ $staff->team_name }}</td>
                <td>{{ $staff->time }}</td>
                @if(!empty($staff['checkin']))
                    <td>
                        @if($staff['time_in_company']['real_time'] >= 300)
                            Full
                        @elseif($staff['time_in_company']['real_time'] >= 150 && $staff['time_in_company']['real_time'] < 300)
                            Part
                        @elseif($staff['time_in_company']['real_time'] >= 0 && $staff['time_in_company']['real_time'] < 150)

                        @endif
                    </td>
                @else
                    <td>Absent</td>
                @endif
                @if(!empty($staff->checkin))
                    <td>{{ ($staff->checkin) }}</td>
                @else
                    <td>{{$staff->day_leave}}</td>
                @endif
                <td>{{ ($staff->checkout) }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>