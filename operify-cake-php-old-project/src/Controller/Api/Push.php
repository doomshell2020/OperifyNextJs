<?php

class Push
{
    //notification title
    private $title;

    //notification message 
    private $text;

    // private $image;

    //initializing values in this constructor
    function __construct($title, $text)
    {
        $this->title = $title;
        $this->text = $text;
        // $this->image = $image;

    }

    //getting the push notification
    public function getPush()
    {

        $res = array();
        $res['title'] = $this->title;
        $res['body'] = $this->text;
        // $res['image'] = $this->image;
        $res['android_channel_id'] = "tirupatierp";
        return $res;
    }

}


?>