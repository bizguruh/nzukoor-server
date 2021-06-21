<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <style>
    .body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    /* background-color: #badfe7; */
    border-bottom: 1px solid #badfe7;
    border-top: 1px solid #badfe7;
    margin: 0;
    padding: 0;
    width: 100%;
    text-align: center;
}
.inner-body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    background-color: #ffffff;
    border-color: #e8e5ef;
    border-radius: 2px;
    border-width: 1px;
    box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
    margin: 0 auto;
    padding: 0;
    width: 570px;
    line-height: 1.6;
}
    .top_banner{
      min-height: 150px;
      width: 100%;
      background:#75b0b6;
      text-align: center;
      position: relative;
    }
    .button {
    -webkit-text-size-adjust: none;
    border-radius: 4px;
    color: #fff;
    display: inline-block;
    overflow: hidden;
    text-decoration: none;
}
.text-right{
  text-align: right;
}
.button-blue,
.button-primary {
    background-color: #388087;
    border-bottom: 8px solid #388087;
    border-left: 18px solid #388087;
    border-right: 18px solid #388087;
    border-top: 8px solid #388087;
}
  .footer {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    margin: 0 auto;
    padding: 0;
    text-align: center;
    width: 570px;
}

.footer span {
    color: #b0adc5;
    font-size: 12px;
    text-align: center;
}

.footer span a {
    color: #b0adc5;
    text-decoration: underline;
}
  </style>
</head>
<body class="body">

  <div class=" ">
<div class="top_banner">
<img src="{{asset('welcome.png')}}" width="300" height="auto" alt="Welcome">
</div>
<div>
  <h3>Greetings {{$name}}, Here’s your Passport to be more </h3>
  <h4>Welcome to the Social Learning Place.</h4>
  <p>
We believe everybody has the capacity to be more, so get ready to be your very best self.

</p>
<p>Browse our top courses, connect with like-minds, lend a voice to trending discussions and become a catalyst for change.</p>

</div>
<a href="http://skillsguruh.herokuapp.com/explore"><button class="button button-blue">Explore Interest</button></a>

<div>
  <h5>Be part of something that has lasting value: help people around the world experience growth and fulfillment by sharing what you know. </h5>
</div>
<footer class=" text-right">
 <span><a href="http://skillsguruh.herokuapp.com/contact">Contact</a></span> | <span><a href="http://skillsguruh.herokuapp.com/about">About</a></span>
</footer>
  </div>
</body>
</html>