<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable {

    use Queueable,
        SerializesModels;

        public $data;

        public function __construct($data)
        {
            $this->data =$data;
        }
    //build the message.

    public function build() {
      // 
      return $this->from(env('MAIL_FROM_ADDRESS','no-reply@transflow.io'))->subject('New Customer Equiry')->view('email')->with('data', $this->data);
    }


}
