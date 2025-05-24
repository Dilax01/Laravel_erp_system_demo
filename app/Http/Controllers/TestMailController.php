<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    public function testMail()
    {
        Mail::raw('This is a test email from Laravel', function ($message) {
            $message->to('iamsorrywhoareyou.com') 
                    ->subject('Laravel Test Mail');
        });

        return '✅ Mail sent successfully!';
    }
}
