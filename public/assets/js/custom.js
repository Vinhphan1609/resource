$(document).ready(function () {
    // Initialize card flip
    // $('.card.hover').hover(function () {
    //     $(this).addClass('flip');
    // }, function () {
    //     $(this).removeClass('flip');
    // });
    $(document).on('click', '.fullscreen-btn', function (e) {
        if (!document.fullscreenElement &&    // alternative standard method
            !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {  // current working methods
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }

    });

    $(".play-audio").click(function () {
        let sound = $(this).attr('source')
        let audio = new Audio(sound);
        audio.play();
    })

    if ($(".alert")[0]){
        setTimeout(function(){ $(".alert").fadeOut() }, 5000);
    }

    $('#datepicker-from, #datepicker-to ').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        }
    });

    $('#datepicker-birthday').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $('#only-time').datetimepicker({
        pickDate: false,
        format: 'HH:mm:ss',
    });

    $('input[name="startDate"]').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    $('#daterangepicker').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "alwaysShowCalendars": true,
        "startDate": $("#daterangepicker").attr('startDate'),
        "endDate": $("#daterangepicker").attr('endDate'),
        locale: {
            format: 'DD/MM/YYYY'
        }
    }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

    $("<div id='tooltip'></div>").css({
        position: "absolute",
        //display: "none",
        padding: "10px 20px",
        "background-color": "#ffffff",
        "z-index": "99999"
    }).appendTo("body");

    $('#my-select').multiSelect();
    $('.multiselect').multiSelect();
    // $('#lightgallery').lightGallery();
    $('.clockpicker').clockpicker({
        autoclose: true
    });

    if($("#userShop"). length){
        var select = document.getElementById('userShop');
        select.addEventListener('change', function(){
            this.form.submit();
        }, false);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("select[name='pic_id']").on('change', function () {
        $("#my-shop").html("<option value=''>-- Choose One --</option>");
        var shop_id = $(this).val();
        $.ajax({
            url: '/admin/contracts/getCustomerShop',
            type: "GET",
            data: { shop_id: shop_id },
            dataType: "html",
            success:function(data){
                JSON.parse(data).map(function (value, key) {
                    $("#my-shop").append("<option value='"+value.id+"'>"+value.shopname+"</option>")
                });
            }
        });
    });

    $("select#my-shop").on('change', function () {
        $("#my-camera").html("<option value=''>-- Choose One --</option>");
        var shop_id = $(this).val();
        $.ajax({
            url: '/admin/contracts/getShopCamera',
            type: "GET",
            data: { shop_id: shop_id },
            dataType: "html",
            success:function(data){
                JSON.parse(data).map(function (value, key) {
                    $("#my-camera").append("<option value='"+value.id+"'>"+value.name+"</option>")
                });
            }
        });
    });

    $('.formAddCustomer').submit(function(e) {
        e.preventDefault();
        $("#my-shop").html("<option value=''>-- Choose One --</option>");
        $('#error-email, #error-name').html("");
        // get all the inputs into an array.
        var $inputs = $('.formAddCustomer :input');
        // not sure if you wanted this, but I thought I'd add it.
        // get an associative array of just the values.
        var data = {};
        $inputs.each(function() {
            data[this.name] = $(this).val();
        });
        $.ajax({
            url: '/admin/contracts/add-customers',
            type: "POST",
            data: data,
            dataType: "json",
            success:function(data){
                $('#pic-id').append($('<option>', {value:data.value, text:data.text})).val(data.value);
                $('#addCustomerModal').modal('toggle');
            },
            error: function(jqXhr, json, errorThrown){
                var errors = jqXhr.responseJSON;
                if(errors && errors.name){
                    $('#error-name').append(errors.name);
                }
                if (errors && errors.email) {
                    $('#error-email').append(errors.email);
                }
            }
        });
    });

    $('#formAddShop').submit(function(e) {
        e.preventDefault();
        $('#error-address-shop, #error-shopname').html('');
        let pic_id = ($('#pic-id').val());
        // get all the inputs into an array.
        var $inputs = $('#formAddShop :input');
        // not sure if you wanted this, but I thought I'd add it.
        // get an associative array of just the values.
        var data = {};
        $inputs.each(function() {
            data[this.name] = $(this).val();
        });
        data['user_id'] = pic_id;
        $.ajax({
            url: '/admin/contracts/add-shop',
            type: "POST",
            data: data,
            dataType: "json",
            success:function(data){
                $('#my-shop').val(data.text);
                $('#my-shop').data('id', data.value);
                $('.multi-select-menuitems').append($('<option>', {value:data.value, text:data.text, selected:'true'}));
                // $('.multi-select-menuitems').append("<label class=\"multi-select-menuitem\" for=\"shop[]_1\" role=\"menuitem\">" + data.text + "</label>");
                // $('#ms-my-select ul li:eq(0)').before("<li class='ms-elem-selectable' id='"+data.value+"-selectable'><span>"+data.text+"</span></li>");
                $('#addShopModal').modal('toggle');
            },
            error: function(jqXhr, json, errorThrown){
                var errors = jqXhr.responseJSON;
                if(errors && errors.shopname){
                    $('#error-shopname').append(errors.shopname);
                }
                if (errors && errors.address) {
                    $('#error-address-shop').append(errors.address);
                }
            }
        });
    });

    $('#formAddCamera').submit(function(e) {
        e.preventDefault();
        // get all the inputs into an array.
        var $inputs = $('#formAddCamera :input');
        // not sure if you wanted this, but I thought I'd add it.
        // get an associative array of just the values.
        var data = {};
        $inputs.each(function() {
            data[this.name] = $(this).val();
        });
        $.ajax({
            url: '/admin/contracts/add-camera',
            type: "POST",
            data: data,
            dataType: "json",
            success:function(data){
                $('#my-camera').append($('<option>', {value:data.value, text:data.text})).val(data.value);
                $('#addCameraModal').modal('toggle');
            },
            error: function(jqXhr, json, errorThrown){
                var errors = jqXhr.responseJSON;
                if(errors && errors.name){
                    $('#error-name').append(errors.name);
                }
                if (errors && errors.email) {
                    $('#error-email').append(errors.email);
                }
            }
        });
    });

    $( "#emotion" ).change(function() {
        window.location.href = "/admin/home?shop_id=" + $('#shopid').val() + "&&date=" + $('#emotion').val() + "&&type=" + $('#xemotion').val();
    });
    $( "#customer" ).change(function() {
        window.location.href = "/admin/home?shop_id=" + $('#shopid').val() + "&&date=" + $('#customer').val() + "&&type=" + $('#xcustomer').val();
    });
    $("#gender" ).change(function() {
        window.location.href = "/admin/home?shop_id=" + $('#shopid').val() + "&&date=" + $('#gender').val() + "&&type=" + $('#xgender').val();
    });
    $("#age" ).change(function() {
        window.location.href = "/admin/home?shop_id=" + $('#shopid').val() + "&&date=" + $(this).val() + "&&type=" + $('#xage').val();
    });
    $("#list" ).change(function() {
        window.location.href = "/admin/home?shop_id=" + $('#shopid').val() + "&&date=" + $(this).val() + "&&type=" + $('#xlist').val();
    });
    $(".repeater-top").change(function () {
        window.location.href = "/admin/home?shop_id=" + $('#shopid').val() + "&&date=" + $(this).val() + "&&type=" + $(this).attr("data-type");
    })

    $('#color-schemes li a').click(function(){
        var lastClass = $('body').attr('class').split(' ').pop();
        $.ajax({
            url: '/admin/set-bg',
            type: "POST",
            data: {lastClass: lastClass},
            dataType: "json",
            success:function(data){
                console.log(data)
            }
        });
    });

    $("#search-date").click(function () {
        var start = $("#datepicker-from").val();
        var end = $("#datepicker-to").val();
        window.location.href = "/admin/home?shop_id=" + $('#shop_id').val() + "&start=" + start  + "&end=" + end;
    })

    $('#smartwizard').smartWizard({
        onLeaveStep:leaveAStepCallback,
        selected: 0,  // Initial selected step, 0 = first step
        keyNavigation:true, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
        autoAdjustHeight:true, // Automatically adjust content height
        cycleSteps: false, // Allows to cycle the navigation of steps
        backButtonSupport: true, // Enable the back button support
        useURLhash: true, // Enable selection of the step based on url hash
        lang: {  // Language variables
            next: 'Next',
            previous: 'Previous'
        },enableFinishButton: false,
        labelNext:'Next',
        labelPrevious:'',
        labelFinish:'',
        toolbarSettings: {
            toolbarPosition: 'bottom', // none, top, bottom, both
            toolbarButtonPosition: 'right', // left, right
            showNextButton: true, // show/hide a Next button
            showPreviousButton: true, // show/hide a Previous button
        },
        anchorSettings: {
            anchorClickable: true, // Enable/Disable anchor navigation
            enableAllAnchors: false, // Activates all anchors clickable all times
            markDoneStep: true, // add done css
            enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
        },
        contentURL: null, // content url, Enables Ajax content loading. can set as data data-content-url on anchor
        disabledSteps: [],    // Array Steps disabled
        errorSteps: [],    // Highlight step with errors
        theme: 'dots',
        transitionEffect: 'fade', // Effect on navigation, none/slide/fade
        transitionSpeed: '400'
    });
    $(".buttonNext").addClass("btn btn-success");
    function leaveAStepCallback(obj){
        var step_num= obj.attr('rel');
        return validateSteps(step_num);
    }
    function validateSteps(step){
        var isStepValid = true;
        if(step == 1) {
            $("#step-2").removeClass("hidden");
        }
        if(step == 2){
            $("#step-3").removeClass("hidden");
            var pic_id = $("#pic-id").val();
            let shopname = $('#my-shop').val();
            let service = $('.service-' +$('#service-id').val() +'').text();
            console.log(service);
            if(pic_id == ""){
                alert("Choose customers. Pls!");
                return false;
            }
            $.ajax({
                url: '/admin/contracts/getCustomerAddress',
                type: "GET",
                data: { pic_id: pic_id },
                dataType: "json",
                success:function(data){
                    console.log(data);
                    $("#customer-name").html(data.name);
                    $("#customer-address").html(data.address);
                    $("#customer-shop").html(shopname);
                    $('#customer-service').html(service);
                }
            });
        }
        //** validate step3 **//
        // if(step == 3){
        // if(validateStep3() == false ){
        //     isStepValid = false;
        //     $('#wizard').smartWizard('showMessage','Please correct the errors in step'+step+ ' and click next.');
        //     $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});
        // }else{
        //     $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
        // }
        // }
        return isStepValid;
    }

    $(".load-notification").click(function () {
        $('.wapper-noti').html("");
        $.ajax({
            url: '/admin/load-notification',
            type: "GET",
            dataType: "json",
            success:function(res){
                $('.wapper-noti').prepend('<li> <h1>You have <strong class="noti-number"> '+res.length+' </strong> new notifications</h1></li>');
                if(res.length > 0){
                    res.map(function (item, key) {
                        $('.wapper-noti').append('<li><a href="#" id="'+item.id+'" class="read-notification"><span class="label label-green"></span>'+item.title+'<span class="small"></span></a></li>');
                    })
                }
                $('.read-notification').click(function(){
                    $.ajax({
                        url: '/admin/read-notification',
                        type: "GET",
                        dataType: "json",
                        success:function(response){
                            if(response){
                                $('.label-transparent-black').addClass( "hidden" );
                                $('.noti-number').html("0");
                                window.location.href = "/admin/notifications";
                            }
                        }
                    });
                });
                $('.wapper-noti').append('<li><a href="/admin/notifications" class="text-center">Read All</a></li>');
            }
        });
    });

    $('.shop-list').on('click', function(e) {
        let shop_id = $('.shop-list').val();
        $('.staff-list').find('option').remove().end();
       $.ajax({
           method: 'get',
           url: '/admin/staffs/get-by-shop/' + shop_id,
           success: function (data) {
               $.each(data.staffs, function (key, value) {
                   $('.staff-list').append('<option value="'+key+'">'+value+'</option>');
               });

           }
       })
    });

    //delete shop
    $('.deleteShop').on('click', function (event) {
        let position = $(this).data('position');
        let id = $(this).data('id');
        $('#modalDeleteShop .id').val(id);
        $('#modalDeleteShop .position').val(position);
        $('#modalDeleteShop').modal('show');

    });
    $('#modalDeleteShop .btn-primary').on('click', function() {
        let id = $('#modalDeleteShop .id').val();
        let position = $('#modalDeleteShop .position').val();
        console.log(position);
        $('#modalDeleteShop').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/shops/shop-destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.position_' + position).remove();
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
        })
    })

    //delete school
    $('.deleteSchool').on('click', function () {
        let position = $(this).data('position');
        let id = $(this).data('id');
        $('#modalDeleteSchool .id').val(id);
        $('#modalDeleteSchool .position').val(position);
        $('#modalDeleteSchool').modal('show');
    })
    $('#modalDeleteSchool .btn-primary').on('click', function() {
        let id = $('#modalDeleteSchool .id').val();
        let position = $('#modalDeleteSchool .position').val();
        $('#modalDeleteSchool').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/schools/school-destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.position_' + position).remove();
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
        })
    })

    //delete camera
    $('.deleteCamera').on('click', function() {
        let position = $(this).data('position');
        let id = $(this).data('id');
        $('#modalDeleteCamera .id').val(id);
        $('#modalDeleteCamera .position').val(position);
        $('#modalDeleteCamera').modal('show');
    })
    $('#modalDeleteCamera .btn-primary').on('click', function() {
        let id = $('#modalDeleteCamera .id').val();
        let position = $('#modalDeleteCamera .position').val();
        $('#modalDeleteCamera').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/shops/cameras/destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.position_' + position).remove();
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
            error: function (data) {
            }
        })
    })

    //deleteCamera school

    $('.deleteCameraSchool').on('click', function() {
        let position = $(this).data('position');
        let id = $(this).data('id');
        $('#modalDeleteCameraSchool .id').val(id);
        $('#modalDeleteCameraSchool .position').val(position);
        $('#modalDeleteCameraSchool').modal('show');
    })
    $('#modalDeleteCameraSchool .btn-primary').on('click', function() {
        let id = $('#modalDeleteCameraSchool .id').val();
        let position = $('#modalDeleteCameraSchool .position').val();
        $('#modalDeleteCameraSchool').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/schools/cameras/destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.position_' + position).remove();
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
            error: function (data) {
            }
        })
    })

    //delete roles
    $('.delete-role').on('click', function () {
        let id = $(this).data('id');
        $('#modalDeleteRole .id').val(id);
        $('#modalDeleteRole').modal('show');
    })
    $('#modalDeleteRole .btn-primary').on('click', function() {
        let id = $('#modalDeleteRole .id').val();
        $('#modalDeleteRole').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/roles/destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.roles-' + id).remove();
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
            error: function (data) {
            }
        })
    })

    //delete user
    $('.delete-user').on('click', function() {
       let id = $(this).data('id');
       let position = $(this).data('position');
       $('#modalDeleteUser').modal('show');
       $('#modalDeleteUser .id').val(id);
       $('#modalDeleteUser .position').val(position);
    });
    $('#modalDeleteUser .btn-primary').on('click', function () {
        let id = $('#modalDeleteUser .id').val();
        let position = $('#modalDeleteUser .position').val();
        $('#modalDeleteUser').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/users/destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.position-' + position).remove();
                    console.log(position);
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
            error: function (data) {
            }
        })
    });
    if(($('.shop_id_hidden').val())!='' && ($('.shop_id_hidden').val() != undefined)) {
        let shop_id = $('.shop_id_hidden').val();
        let staff_id = $('.shop_id_hidden').data('staff');
        $.ajax({
            method: 'get',
            url: '/admin/staffs/get-staffs-and-shop/' + shop_id +'/' + staff_id,
            success: function (data) {
                $.each(data[0].staffs, function (key, value) {
                    $('.staff-list').append('<option value="'+key+'">'+value+'</option>');
                    if(key == data.staff.staff_id) {
                        $("select option[value="+key+"]").attr('selected', 'selected');
                    }
                });

            }
        })
    }

    $('#role_id').change( function () {
        let type = $(this).val();
        $.ajax({
            method: 'GET',
            url: '/admin/users/get-shop/' + type,
            success: function(data) {
                $('.shop-list').find('option').remove();
                $.each(data, function (key, value) {
                    $('.shop-list').append("<option value="+key+">"+value+"</option>");
                })
            }
        })
    })

    //delete user school
    $('.delete-user-school').on('click', function() {
        let id = $(this).data('id');
        let position = $(this).data('position');
        $('#modalDeleteUserSchool').modal('show');
        $('#modalDeleteUserSchool .id').val(id);
        $('#modalDeleteUserSchool .position').val(position);
    });
    $('#modalDeleteUserSchool .btn-primary').on('click', function () {
        let id = $('#modalDeleteUserSchool .id').val();
        let position = $('#modalDeleteUserSchool .position').val();
        $('#modalDeleteUserSchool').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/users/destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.position-' + position).remove();
                    console.log(position);
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
            error: function (data) {
            }
        })
    });

    //delete student
    $('.delete-student').on('click', function() {
        let id = $(this).data('id');
        let position = $(this).data('position');
        $('#modalDeleteStudent').modal('show');
        $('#modalDeleteStudent .id').val(id);
        $('#modalDeleteStudent .position').val(position);
    });
    $('#modalDeleteStudent .btn-primary').on('click', function () {
        let id = $('#modalDeleteStudent .id').val();
        let position = $('#modalDeleteStudent .position').val();
        console.log(position);
        $('#modalDeleteStudent').modal('hide');
        $.ajax({
            type: 'DELETE',
            url: '/admin/schools/students/destroy/' + id,
            success: function(res) {
                if(res.status == 200) {
                    $('.status-success').text(res.message).show();
                    $('.position-' + position).remove();
                    console.log(position);
                } else {
                    $('.status-error').text(res.message).show();
                }
            },
            error: function (data) {
            }
        })
    });


    $('.type_attendance').on('change', function () {
        let type = $('.type_attendance').val();
        let shop_id = $('.type_attendance').data('shop-id');
        if(type == "attendance") {
            window.location.assign('/admin/staffs/attendance?shop_id=' + shop_id);
        } else if(type == "no_attendance") {
            window.location.assign('/admin/staffs/no-attendance?shop_id=' + shop_id);
        }
    });

});

