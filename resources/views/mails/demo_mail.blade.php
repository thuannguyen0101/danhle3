<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-GB">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>HRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <style type="text/css">
        a[x-apple-data-detectors] {
            color: inherit !important;
        }
        *{
            font-family: sans-serif;
        }
    </style>
</head>

<body style="width: 100%;background: aliceblue">
<div style="margin: auto;width: 50%;background: white;padding: 2%">
    <p style="text-align: center;font-size: 52px">ĐƠN XIN NGHỈ PHÉP</p>
    <p style="padding-left: 2%;font-size: 20px">Chào Anh/Chi {{$user->name}}</p>
    <p style="padding-left: 2%;font-size: 20px">Tôi là: {{$backpackUser['name']}}</p>
    <p style="font-size: 18px">Tôi gừi thông báo này với mong muốn xin nghỉ phép từ ngày {{ $content['start_date']}} đến
        ngày {{$content['start_date']}}
        <br>Do: {{$content['message']}}</p>
    <p style="padding-top:1% ;font-size: 20px">Tôi có thể nhân đươc Mail phản hồi tại: <a
            href="mailto:{{$backpackUser['email']}}">{{$backpackUser['email']}}</a></p>
    <p style="font-size: 20px">Trân trọng</p>

    @if($url)
        <p style="font-size: 20px">Bạn có thể gửi phản hồi cho yêu cầu này tại đây</p>
        <a type="button" href="{{$url}}/2"
           style="padding: 10px 18px; margin: 10px 20px; background-color: #dc3545; color: white; font-size: 1rem; border: none; border-radius: 30px; text-decoration: none; display: inline-block;">
            Không đồng ý</a>

        <a type="button" href="{{$url}}/3"
           style="padding: 10px 18px; margin: 10px 0; background-color: #007bff; color: white; font-size: 1rem; border: none; border-radius: 30px; text-decoration: none; display: inline-block;">
            Đồng ý</a>
    @endif
</div>
</body>
</html>
