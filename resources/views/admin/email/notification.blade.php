<!DOCTYPE html>
<html>
<head>
    <title>New Incoming Letter {{ $letter->letter_id }}</title>
</head>
    <body>
        <p>Dear Recipient,</p>

        <p>You have received new correspondence in your dashboard. Here are the details:</p>

        <p>Please <a href="{{ route('admin.delivery.download', $letter->id) }}">click here</a> to view the complete details.</p>
        
        <p>Regards,<br>
            Front Desk
        </p>
    </body>
</html>