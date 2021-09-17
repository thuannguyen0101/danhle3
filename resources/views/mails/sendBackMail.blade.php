<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HRMS phản hồi</title>
    <style>
        * {
            color: #7e7e7e;
            font-family: sans-serif;
        }
    </style>
</head>
<body>
<div style="width: 550px;border: #cbcbcb 2px solid;margin: auto;text-align: center;padding: 20px">
    <h1>HRMS phản hồi</h1>
    <p>ngày {{\Carbon\Carbon::now()}}</p>
    <p>{{$content}} bởi : {{$user['name']}}</p>
    <p>thắc mắc xin liên hệ : <a style="text-decoration: none;color: #0045ee" href="tel:0987987789">0987987789</a></p>
</div>
</body>
</html>

