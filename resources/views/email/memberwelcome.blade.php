<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <style>
    /* Base */

body,
body *:not(html):not(style):not(br):not(tr):not(code) {
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
        Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji",
        "Segoe UI Symbol";
    position: relative;
}

body {
    -webkit-text-size-adjust: none;
    background-color: #ffffff;
    color: #718096;
    height: 100%;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    width: 100% !important;
    font-size: 16px

}
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
    text-align: left;
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
    max-width: 570px;
}
.rounded{
  border-radius: .5rem;
}
.border{
  border:1px solid #ccc;
}
    .top_banner{
      width: 100%;
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
    cursor: pointer;
}
.text-right{
  text-align: right;
}
.text-center{
  text-align: center;
}
.t_header{
  font-weight: 600;
  font-size: 16px;
  color: #e76f51;

}
td{
  width: 50%;

}
.button-blue,
.button-primary {
    background-color: #388087;
    border-bottom: 8px solid #388087;
    border-left: 18px solid #388087;
    border-right: 18px solid #388087;
    border-top: 8px solid #388087;
}
.button-blue-outline,
.button-primary-outline {
    background-color: transparent;
    border: 1px solid #388087;
    padding: 5px 15px;
    color: #388087;
}
.text-green{
  color:#388087;
}
.text-secondary{
  color:#e76f51;
}
.border-green{
  border:2px solid #388087;
}
.mb-0{
  margin-bottom: 0
}
.mb-1{

  margin-bottom: .5rem
}
.mb-2{
  margin-bottom: 1.5rem
}

h1 {

    font-size: 1.4rem;
    font-weight: bold;


}

h2 {

    font-size: 1.2rem;
    font-weight: bold;


}

h3 {

     font-size: 1.1rem;
    font-weight: bold;


}
h4 {

     font-size: 1rem;
    font-weight: bold;


}

.p-2{
  padding:1rem;
}
p {

    font-size: .9rem;
    margin:0;
}

.image{
  width: 50%;
  margin: 0 auto
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
}

.footer span a {
    color: #b0adc5;
    text-decoration: underline;
}
.mytable{
  width: 80%;
  margin: 0 auto;
}
 .p-3{
  padding:1.2rem;
}
.bannerImg{
  width: 100%
}
@media(max-width:600px){
.mytable{
  width: 100%;
}  .image{
  width: 100%
}
  .p-2{
  padding:.6rem;
}
  .rounded .image{
    width: 70px;
  }
  .t_header{
  font-weight: 500;
 font-size: 1rem;
}
  p {

    font-size: 1rem;
    line-height: 1.4em;

}
}
  </style>
</head>
<body class="body">

  <div class=" ">
<div class="top_banner">
<img src="{{asset('welcome.png')}}" class="bannerImg" height="auto" alt="Welcome">
</div>
<div class="text-left"  style="margin-bottom: 16px;padding-top:10px" >
  <h4>Greetings {{$name}}, </h4>
  <div style="line-height:1.5">
    <h1 class="text-green" style="line-height:1.4" >Welcome to Nzukoor!, the Social Learning Place.</h1>
<p>Welcome to Nzùkóór, your meeting point for people, knowledge and opportunities. Connect with your tribe and be inspired to be your most authentic self.
</p>
  </div>

<h2 class="text-center">Start with our 3E-I</h2>

<div class="mytable mb-2">
  <table class="border-green p-3 rounded">
  <tbody>
    <tr>
   <td class="p-2 text-center">
      <div class="rounded ">
    <img src="{{asset('/landing/explore.png')}}"  class="image"  alt="Explore">
     <div class="p-2" style="line-height: 1.4">
        <div class="t_header">Explore</div>
        <p> Explore to discover people, knowledge and opportunities.</p>
     </div>
    </div>

    </td>
     <td class="p-2  text-center">

       <div class="rounded ">
      <img src="{{asset('/landing/engage.png')}}"   class="image" alt="Engage">
       <div class="p-2" style="line-height: 1.4">
        <div  class="t_header">Engage</div>
      <p>Engage by starting or joining conversations of interest.</p>
       </div>
      </div>
     </td>

      </tr>
      <tr>
          <td class="p-2  text-center">
         <div class="rounded ">
        <img src="{{asset('/landing/evolve.png')}}"  class="image"  alt="Evolve">
         <div class="p-2" style="line-height: 1.4">
            <div  class="t_header">Evolve</div>
        <p>Evolve by enrolling to courses and events.</p>
         </div>
        </div>
      </td>
     <td class="p-2 text-center">
         <div class="rounded ">
         <img src="{{asset('/landing/impact.png')}}" class="image" alt="Impact">
          <div class="p-2" style="line-height: 1.4">
           <div  class="t_header">Impact</div>
         <p>Impact others by inviting them to our community.</p>
          </div>
        </div>
       </td>

      </tr>
  </tbody>
</table>

</div>
<p>It is our sincere hope that you feel right at home being your most authentic self. Nnoo!</p>
<div class="mb-2">
  <small>Sincerely, <br>
The Team @ Nzukoor</small>

</div>
<hr>
<div>
  <h4 class="mb-0">Join trending conversations . . .</h4>
  <p><a href="https://nzukoor.com/g/discussion/45" target="_blank"> Does 30-min daily morning meditation really work? </a></p>
</div>
</div>

<footer class=" text-right text-green">
 <small><a href="http://nzukoor.com/contact" class="text-green">Contact</a></small><small> | </small> <small><a class="text-green" href="http://nzukoor.com/about">About</a></small>
</footer>
  </div>
</body>
</html>