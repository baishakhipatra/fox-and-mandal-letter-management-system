<?php
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

if (!function_exists('generateUniqueAlphaNumericValue')) {
    function generateUniqueAlphaNumericValue($length = 10) {
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $number = random_int(0, 36);
            $character = base_convert($number, 10, 36);
            $random_string .= $character;
        }
        return strtoupper($random_string);
    }
}

if (!function_exists('getInitials')) {
     function getInitials($fullName) {
            return collect(explode(' ', $fullName))
                ->map(fn($name) => Str::upper(Str::substr($name, 0, 1)))
                ->join('');
        }
}



// function SendMail($data)
// {
// 	if(isset($data['from']) || !empty($data['from'])) {
// 		$mail_from = $data['from'];
// 	} else {
// 		$mail_from = 'admin@foxandmandal.co.in';
// 	}
// 	// $mail_from = $data['from'] ? $data['from'] : 'support@onninternational.com';



//     // send mail
//     Mail::send($data['blade_file'], $data, function ($message) use ($data) {
// 		if(isset($data['from']) || !empty($data['from'])) {
// 			$mail_from = $data['from'];
// 		} else {
// 			$mail_from = 'admin@foxandmandal.co.in';
// 		}

// 		// $mail_from = $data['from'] ? $data['from'] : 'support@onninternational.com';
//         $message->to($data['email'], $data['name'])->subject($data['subject'])->from($mail_from, env('APP_NAME'));
//     });
// }

function SendMail($data)
{
    $mail_from = (isset($data['from']) && !empty($data['from']))
        ? $data['from']
        : env('MAIL_FROM_ADDRESS');

    try {
        Mail::send($data['blade_file'], $data, function ($message) use ($data, $mail_from) {
            $message->to($data['email'], $data['name'] ?? null)
                ->subject($data['subject'])
                ->from($mail_from, env('APP_NAME'));
        });
        return true;
    } catch (\Exception $e) {
        Log::error('Mail sending failed: ' . $e->getMessage());
        return false;
    }
}
