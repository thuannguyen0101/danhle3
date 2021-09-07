<h1>HRMS Mail</h1>
<h4>Bạn đã nhận được 1 tin nhắn từ : {{$user}}</h4>
<p>Được gửi lúc : {{\Carbon\Carbon::now()}}</p>
<h4>Vấn đề : {{$request_type}}</h4>
<p>Với nội dung :</p>
{!! $msg !!}
<h4>Trả lời : <a href="mailto:{{$email}}">{{$email}}</a></h4>
