<!DOCTYPE html>
<html lang="en">
<head>
<style>
   .button{
      background: #388087;
      padding: 8px 20px;
      font-size: 13px;
      border-radius: 5px;
      outline: none;
      margin: 8px 0;
      color: white;
   }
</style>
</head>
<body>
   <h3>  Dear {{$username}},</h3>
<p>You have been invited by {{$organization}} to be a {{$role}} on SkillsGuruh.
  </p>
  <p> Welcome to The Social Learning Place, we hope you like it here.</p>
<p>Please log in to get started.</p>
<a href="http://skillsguruh.com/login"><button class="button">Click to login</button></a>
<p>Sincerely,</p>
<p>The Team @ SkillsGuruh</p>


</body>
</html>