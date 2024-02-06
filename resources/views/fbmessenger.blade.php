<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Facebook Messenger - By James Harris</title>


    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/normalize.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>

<body>



    <div class="wrapper-mobile">


        <div class="mobile"><img src="{{ asset('assets/img/lone-logo.svg') }}">Not available on Tablet or Mobile devices.</div>


    </div>



    <div class="wrapper">

        <header>

            <div class="container">

                <div class="left"><img src="{{ asset('assets/img/lone-logo.svg') }}"></div>

                <div class="middle">
                    <h3>{{ $page_name }}</h3>
                    <p>Messenger</p>
                </div>




                <div class="right">

                    <div class="username">
                        <div class="settings"><img src="{{ asset('assets/img/settings.svg') }}"></div>{{ $page_name }}</div>

                    <div class="avatar"><img src="{{ $profile_pic }}"></div>

                </div>

            </div>

        </header>

        <main>

            <div class="col-left">

                <div class="col-content">

                    <div class="messages ">
                        <ul class="list-group">

                        @foreach($all_conversations as $key=>$conversation)
                            
                            <li class="list_item @if($key==0) {{'active'}} @endif" id="{{ $conversation['id'] }}">
                                <a style="cursor: pointer;" onclick='showConversationMessage("{{ $conversation['id'] }}","{{ $conversation['participant_id'] }}")'>
                                    <div class="avatar">
                                        <div class="avatar-image">
                                            <div class="status online"></div>
                                            <img src="{{ asset('assets/img/avatar-2.png') }}">
                                        </div>
                                    </div>
                                    <h3>{{$conversation['participant_name']}}</h3>
                                    <!-- <p>Be there soon.</p> -->
                                    <!-- <a style="cursor: pointer;" onclick='showConversationMessage("{{ $conversation['id'] }}")'>{{ $conversation['id'] }}</a> -->
                                </a>
                            </li>

                        @endforeach

                        </ul>
                    </div>

                </div>

            </div>

            <div class="col">

                <div class="col-content">

                    <section class="message">
                        <div class="grid-message">
                            
                            @foreach($all_default_messages as $message)

                                @if($message['from']['name'] != 'My Demo Page')
                                    <div class="col-message-received">
                                        <div class="message-received">
                                            <p>{{ $message['message'] }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($message['from']['name'] == 'My Demo Page')
                                    <div class="col-message-sent">
                                        <div class="message-sent">
                                            <p>{{ $message['message'] }}</p>
                                        </div>
                                    </div> 
                                @endif

                            @endforeach
                        </div>
                    </section>

                </div>


                <div class="col-foot">

                    <div class="compose">

                        <input placeholder="Type a message" id="facebook_message">
                        <div class="compose-dock">
                            <div class="dock">
                                <!-- <img src="{{ asset('assets/img/picture.svg') }}">
                                <img src="{{ asset('assets/img/smile.svg') }}"> -->
                                <button type="button" id="send_message" recipent="{{$current_recipent}}" conversion="{{$current_conversion}}" class="btn btn-primary">Send >></button>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-right">

                <div class="col-content">

                    <div class="user-panel">

                        <div class="avatar">
                            <div class="avatar-image">
                                <div class="status online"></div><img src="{{ $profile_pic }}"></div>

                            <h3>{{ $page_name }}</h3>
                           <!--  <p>London, United Kingdom</p> -->

                        </div>

                    </div>   

                </div>

            </div>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript">

        //Ajax function to fetch messeges
        function showConversationMessage(conversation_id,participant_id)
        {
            $('.list_item').removeClass('active');
            
            var parentElement = $("#"+conversation_id).addClass('active');
            $("#send_message").attr('recipent',participant_id);
            $("#send_message").attr('conversion',conversation_id);
            
            jQuery.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: '/ajax_messages/'+conversation_id,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    // container.html('');
                    var html = '';
                    $.each(data, function(index, item) {
                       // alert(item.from.name)

                       
                        if (item.from.name == 'My Demo Page') {
                            html += '<div class="col-message-sent"><div class="message-sent"><p>'+item.message+'</p></div></div>';
                        } 

                        if (item.from.name != 'My Demo Page') {
                            html += '<div class="col-message-received"><div class="message-received"><p>'+item.message+'</p></div></div>';
                        } 
                    });
                    
                    //ADD HTML 
                    $('.grid-message').html(html);
                },error:function(){ 
                     console.log(data);
                }
            });
        }//end of the function

        $(document).on('click','#send_message',function(){
            var message = $("#facebook_message").val();
            var user_id = $(this).attr('recipent');
            var conversation_id = $(this).attr('conversion');

            if(message != ""  && user_id != ""){
                jQuery.ajax({
                    type: 'POST', //THIS NEEDS TO BE GET
                    url: '/ajax_send_message',
                    data: jQuery.param({ message: message, user_id : user_id, _token : "{{ csrf_token() }}"}) ,
                    dataType: 'json',
                    success: function (data) {
                        
                        $("#facebook_message").val("");
                        showConversationMessage(conversation_id,user_id);
                        
                        
                    },error:function(){ 
                        console.log(data);
                    }
                });
            }

        })
    </script>


</body>

</html>
