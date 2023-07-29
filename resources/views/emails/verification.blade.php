<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h1>Email Verification</h1>
    <p>Hello {{ $user->name }},</p>
    <p>Your OTP for email verification is: {{ $user->otp }}</p>
    <p>Please use this OTP to verify your email.</p>
</body>
</html>
