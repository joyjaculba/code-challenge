<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" type="text/css" rel="stylesheet"/>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <style type="text/css">
            body {
                background: #f3f2f291 !important;
            }
        </style>
    </head>
    <body>
        <div class="p-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">Calendar</div>
                        <div class="card-body">
                            <form id="event_form" name="event_form" class="form-horizontal">
                                {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" align="left">
                                        <label for="event_name">Event</label>
                                            <input type="text" class="form-control" id="event_name" name="event_name" required v-model="newEvent.event_name">
                                    </div>
                                    <div class="form-group">
                                        <div class="row" align="left">
                                            <div class="col-md-6">
                                                <label for="event_name">From</label>
                                                    <input type="date" class="form-control" id="from" name="from" required v-model="newEvent.from">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="event_name">To</label>
                                                    <input type="date" class="form-control" id="to" name="from" required v-model="newEvent.to">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        @php $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat','Sun'); @endphp
                                        @foreach($days as $d)
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input"  name="week" value="{{$d}}">{{$d}}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="form-group">
                                        <button class="btn btn-primary" @click.prevent="createEvent()" id="save_event" name="save_event" type="button">Save</button>
                                        
                                    </div>
                                </div>
                                @php
                                    $date = '2020-03-01';
                                    $end_date = '2020-03-31';
                                @endphp

                                <div class="col-md-8">
                                    <h5 class="card-title" id="month-year">{{date('M Y')}}</h5>
                                      <table class="table table-md" id="days_events">
                                        <tbody >
                                            @while(strtotime($date) <= strtotime($end_date))
                                            <tr>
                                                <td>{{date('d D',strtotime($date))}}</td>
                                                <td></td>
                                            </tr>
                                            @php
                                            $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                                            @endphp
                                            @endwhile
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </body>
    <script src="{{ url('js/app.js') }}" type="text/javascript"></script>
     <!-- Include jQuery -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="{{ url('js/notify.min.js') }}" type="text/javascript"></script>

    <script>
         $(document).ready(function(){
            $('#save_event').click(function(e){
               e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
               var weeks = [];
                $.each($("input[name='week']:checked"), function(){
                    weeks.push($(this).val());
                });
               $.ajax({
                  url: "{{ route('events.add') }}",
                  type: 'POST',
                  data: {
                    _token : $('meta[name="csrf-token"]').attr('content'),
                     event_name: $('#event_name').val(),
                     from: $('#from').val(),
                     to: $('#to').val(),
                     weeks_arr: weeks,
                  },
                  success: function(result){
                    console.log(result);
                    var from = new Date(result.from);
                    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    $('#month-year').html(months[from.getMonth()] + " "+from.getFullYear());
                    $("#days_events tbody").html("");
                    
                    var to = new Date(result.to);

                    var getDateArray = function(start, end) {
                        var arr = new Array();
                        var dt = new Date(start);
                        while (dt <= end) {
                            arr.push(new Date(dt));
                            dt.setDate(dt.getDate() + 1);
                        }
                        return arr;
                    }

                    var dateArr = getDateArray(from, to);

                    var weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
                    var w = result.weeks;
                    for (var i = 0; i < dateArr.length; i++) {
                        if(w.indexOf(weekday[dateArr[i].getDay()]) !== -1){
                            $("#days_events tbody").append("<tr class='table-success'><td>"+dateArr[i].getDate()+" "+weekday[dateArr[i].getDay()]+"</td><td>"+result.event_name+"</td></tr>"); 
                            console.log(dateArr[i].getDate()+" "+weekday[dateArr[i].getDay()]+ " COLORED");
                        }
                        else {
                            $("#days_events tbody").append("<tr><td>"+dateArr[i].getDate()+" "+weekday[dateArr[i].getDay()]+"</td><td></td></tr>"); 
                            console.log(dateArr[i].getDate()+" "+weekday[dateArr[i].getDay()]);
                        } 
                        
                    }
                    $.notify("Events successfully added.", "success");
                  }});
               });
            });
      </script>
</html>
