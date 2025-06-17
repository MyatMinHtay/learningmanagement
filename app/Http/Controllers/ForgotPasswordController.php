<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ForgotPasswordController extends Controller
{

    public function index(){
        return view('auth.pwreset');
        // if(Auth::check()){
          
        //     return view('auth.pwreset');
        // }else{
        //     return redirect('/')->with('warning','you must login to go this page');
        // }
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['resetemail' => 'required|email']);

        $email = $request->input('resetemail');
        $token = rand(100000, 999999) + 100000; // Generate a 6-digit code
        // $username = auth()->user()->username;
        $username = 'aung aung';

        // dd($request->all());

        // Store the token in the password_resets table
        try{
            $data = [];
            $data['email'] = $email;
            $data['token'] = $token;
            
            PasswordReset::create($data);
        }catch(Exception $e){
            return back()->withErrors('errors',$e->getMessage());
        }

        // Send the email with the token


        $postemail = $this->sendTestEmail($email,$username,$token);

        if($postemail){
            return redirect()->back()->with('success','we send reset code to your email plz check!');
        }else{
            return back()->withErrors('errors',$postemail);
        }

        
    }

    public function sendTestEmail($receiveemail,$receivename,$token){

        // Render the email content using a Blade view
        $emailContent = View::make('emails.password_reset', ['token' => $token])->render();
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'morningstartranslationmm.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@morningstartranslationmm.com';
            $mail->Password   = 'Jerry075087Hello';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('noreply@morningstartranslationmm.com', 'MorningStar'); //ဘယ်က ပို့တယ်ဆိုတဲ့ email address
            $mail->addAddress($receiveemail, $receivename); //လက်ခံမယ့် Email address

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Test Email';
            $mail->Body    = $emailContent;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
        
    }

}
