<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FbmessengerController extends Controller
{
    private $access_token ;
    private $page_id ;

    public function __construct()
    {
        $this->access_token = env('FACEBOOK_ACCESS_TOKEN');
        $this->page_id = env('FACEBOOK_PAGE_ID');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    
    public function index() 
    {
        $all_conversations = $this->getAllConversations();
        $default_message_id = $all_conversations['data'][0]['id']; //die;
        $all_default_messages = $this->getAllMessages($default_message_id);

        $my_details = $this->myDetails();
        $page_name = $my_details['name'];
        $profile_pic = $my_details['picture']['data']['url'];

        foreach($all_conversations['data'] as $key=>$item){
            $r = $this->getAllRecepents($item['id']);
            $all_conversations['data'][$key]['participant_name'] = $r['participants']['data'][0]['name'];
            $all_conversations['data'][$key]['participant_id'] = $r['participants']['data'][0]['id'];

            if($key==0){
                $current_recipent = $r['participants']['data'][0]['id'];
            }
        }
    	
    	return view('fbmessenger', [
            'all_conversations' => $all_conversations['data'],
            'all_default_messages' => $all_default_messages,
            'current_recipent' => $current_recipent ,
            'current_conversion' => $default_message_id ,
            'page_name' => $page_name,
            'profile_pic' => $profile_pic,
        ]);
    }

    public function getAllConversations()
    {

        $ch = curl_init();

        //curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v19.0/211553705379200/conversations?platform=MESSENGER&access_token=EAAM4KbKWdToBO2XkK7WUuG3d3k34AKddxvHRDgOAPuggZC5335jzdAl5JfmdRRYdKUg6dbZCLVTuFavrSUKFNcxSACMfp6ZB1nkQvAbJZCdUCSU0phhT6wW6XWqZCyOjm0vzLUSH56XV72gtWAXMZAAFsrvjPjGNHdElz2xYGP6KUnVZCzzUfQQKmZAHM9lQrQjp');
        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v19.0/'.$this->page_id.'/conversations?platform=MESSENGER&access_token='.$this->access_token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);

    }//End of the function


    //GET ALL RECIEPENT
    public function getAllRecepents($id)
    {

        //GET ALL RECEPENT ID'S
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v19.0/'.$id.'?fields=participants&access_token='.$this->access_token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);

    }//End of the function


    //GET ALL MESSAGES
    public function getAllMessages($id)
    {
        //GET ALL MESSAGES ID'S
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v19.0/'.$id.'?fields=messages&access_token='.$this->access_token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $message_ids = json_decode($result, true);

        $allMessages = array();
        foreach ($message_ids['messages']['data'] as $message_id) {
            $allMessages[] = $this->getAllMessageDetails($message_id['id']);
        }

        return array_reverse($allMessages,true);

    }//End of the function


    //GET ALL MESSAGES DETAILS
    public function getAllMessageDetails($id)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v19.0/'.$id.'?fields=id,created_time,from,to,message&access_token='.$this->access_token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($result, true);

    }//End of the function

    
    //GET PAGE DETAILS
    public function myDetails()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v19.0/me?fields=id,name,picture&access_token='.$this->access_token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);
    }//End of the function

    
    //SEND MESSAGE
    public function sendMessage()
    {
        
        $ch = curl_init();

        $user_id = $_POST['user_id'];
        $message = $_POST['message'];

        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v19.0/'.$this->page_id.'/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "recipient={\"id\":$user_id}&messaging_type=RESPONSE&message={\"text\":\"$message\"}&access_token=".$this->access_token);

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);

    }//End of the function

}
