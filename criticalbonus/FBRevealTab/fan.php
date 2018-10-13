<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
<head>
</head>
<body style="overflow:hidden;">
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script type="text/javascript">
   window.fbAsyncInit = function(){
   FB.init({
     appId:'xxxxxxxxxxxxxx',
         session:{},
         status:true,
         cookie:true,
         xfbml:true
         });
   FB.Event.subscribe('edge.create',
                      function(response){
                        top.location.href = 'http://www.facebook.com/your-fan-page';
                      }
                      );
   FB.Canvas.setAutoResize();

 };
(function() {
  var e = document.createElement('script');
  e.type = 'text/javascript';
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
 }());
</script>

Congrats, you're a fan!
</body>
</html>