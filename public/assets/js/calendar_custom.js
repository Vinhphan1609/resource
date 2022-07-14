$(function () {
    function init_events(data) {
        $('#calendar').fullCalendar({
            header: false,
            events: data,
            editable: true,
            dayClick: function (date) {
                $('.err_reason_note').hide();
                $('.err_reason_note').text('');
                $('#addNoteModal').modal('show');
                $('#date').text(date.format());
                $('#date_note').val(date.format());
            },
            eventClick: function (infos) {
                if (infos.isConfirm == 0) {
                    if (confirm("Want to delete?")) {
                        $.ajax({
                            method: 'delete',
                            url: '/admin/staffs/calendar/delete/note/' + infos.id,
                            success: function (data) {
                                location.reload();
                            }
                        })
                    }
                } else {
                    alert(infos.title);
                }
            }
        })
    }

    //save note to DB
    $('.save-note').on('click', function () {
        let formData = $('#form_staff_note').serializeArray();
        formData.push({name: 'staff_id', value: staffId});
        $.ajax({
            method: 'post',
            url: '/admin/staffs/calendar/'+staffId+'/create',
            data: formData,
            beforeSend: function(){
                $('#loading1').css("visibility", "visible");
            },
            success: function (data) {
                $('#notesModal').modal('hide');
                $('#reason_note').val('');
                $('#loading1').css("visibility", "hidden");
                location.reload();
            },
            error: function (errors) {
                let err = errors.responseJSON.errors;
                $.each(err, function (key, value) {
                    $('.err_' + key).show();
                    $('.err_' + key).text(value);
                    $('#loading1').css("visibility", "hidden");
                })
            }
        });
    });

    function change_events(data) {
        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar('addEventSource', data);
        $('#calendar').fullCalendar('rerenderEvents');
    }

    init_events(JSON.parse(staffData));

    function getAjax(currentDate) {
        var staff_id = $(".content").attr('staff_id');
        $.ajax({
            url: '/admin/staffs/calendar-month',
            type: "get",
            data: {
                staff_id: staff_id,
                'month': moment(currentDate).format("MM"),
                'year': moment(currentDate).format("YYYY")
            },
            dataType: "json",
            success: function (res) {
                var enought = res.infos.enought ? parseInt(res.infos.enought) : 0;
                var not_enought = res.infos.not_enought ? parseInt(res.infos.not_enought) : 0;
                $(".total_day").html(enought + not_enought);
                $(".enought").html(enought);
                $(".not_enought").html(not_enought);
                $('.total_working_time').html(res.infos.total_working_time);
                change_events(res.staff)
            }
        });
    }

    var currentDate = $('#calendar').fullCalendar('getDate')._d;
    $('#cal-current-day').html(moment(currentDate).format("dddd"));
    $('#cal-current-date').html(moment(currentDate).format("MMMM YYYY"));
    // Previous month action
    $('#cal-prev').click(function () {
        $('#calendar').fullCalendar('prev');
        var currentDate = $('#calendar').fullCalendar('getDate')._d;
        $('#cal-current-day').html(moment(currentDate).format("dddd"));
        $('#cal-current-date').html(moment(currentDate).format("MMMM YYYY"));
        getAjax(currentDate);
    });

    // Next month action
    $('#cal-next').click(function () {
        $('#calendar').fullCalendar('next');
        var currentDate = $('#calendar').fullCalendar('getDate')._d;
        $('#cal-current-day').html(moment(currentDate).format("dddd"));
        $('#cal-current-date').html(moment(currentDate).format("MMMM YYYY"));
        getAjax(currentDate);
    });

    //staff complain
    $('#type_note').on('change', function() {
        if($('#type_note').val() == 3) {
            $('.content_reason').show();
        } else {
            $('.content_reason').hide().disable;
            $('#complain_type_none').prop('checked', true);
            $('#complain_full_day').prop('checked', false);
            $('#complain_division_day').prop('checked', false);
        }
    })
})