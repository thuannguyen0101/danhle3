<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<div class="container">

    <div class="container">
        <div class="card  mt-5 " style="">
            <div class="card-header mb-10" style="background: #04aa6d;color: white">
                <h1 class="text-center">HRMS</h1>
            </div>
            <div class="card-body text-center ">
                <div style="font-size: 100%">
                    <i class='far fa-check-circle' style='font-size:500%;color:green;background: white'></i>
                </div>
                @if($choice )
                    <div class="pt-4">
                        <h4>Cảm ơn bạn đã xác nhân yêu cầu xin nghỉ phép</h4>
                        <p>Của: {{$receiver->name}}</p>
                        @switch($choice)
                            @case(2)
                            <p>Với trạng thái là: <a type="button" href="" class="btn btn-danger">Không đồng ý</a></p>
                            @break
                            @case(3)
                            <p>Với trạng thái là: <a type="button" href="" class="btn btn-success">Đồng ý</a></p>
                            @break
                        @endswitch
                        <p>Thời gian: {{$time}}</p>
                    </div>
                @else
                    <div class="pt-4">
                        <h4>Yêu cầu xin nghỉ phép</h4>
                        <p>Của: {{$receiver->name}}</p>
                        <p>đã được bạn xác nhân trước đó</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>
