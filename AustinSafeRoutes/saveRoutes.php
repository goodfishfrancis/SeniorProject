<?php
    session_start();
    
    // if user is not logged in, redirect to login page
    if (!isset($_SESSION['access_token'])) {
        //header('Location: login.php');
        echo "<script>alert('Please Login To Continue');</script>";
        echo "<script>location.href='https://asolinge.create.stedwards.edu/AustinSafeRoutes/login.php';</script>";
        exit();
    }

?>

<!DOCTYPE html>
<html>

<head>
  <!-- BASICS -->
  <meta charset="utf-8">
  <title>Austin SafeRoutes - Plan for your commute</title>
  <style>
    #map{
      height:500px;
    }
  </style>
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link rel="stylesheet" type="text/css" href="js/rs-plugin/css/settings.css" media="screen"> -->
  <link rel="stylesheet" type="text/css" href="css/isotope.css" media="screen">
  <link rel="stylesheet" href="css/flexslider.css" type="text/css">
  <link rel="stylesheet" href="js/fancybox/jquery.fancybox.css" type="text/css" media="screen">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Serif:400,400italic,700|Open+Sans:300,400,600,700">

  <link rel="stylesheet" href="css/style.css">
  <!-- skin -->
  <link rel="stylesheet" href="skin/default.css">
  <!-- =======================================================
    Theme Name: Vlava
    Theme URL: https://bootstrapmade.com/vlava-free-bootstrap-one-page-template/
    Author: BootstrapMade.com
    Author URL: https://bootstrapmade.com
  ======================================================= -->
  <script type="text/javascript">
    var map;
    var directionsDisplay;
    var directionsService;
    var stepDisplay;
    var markerArray = [];
    var position;
    var marker = null;
    var polyline = null;
    var poly2 = null;
    var speed = 0.000005, wait = 1;
    var infowindow = null;
    var data = {};
    var myPano;
    var panoClient;
    var nextPanoId;
    var timerHandle = null;
    
    function save_waypoints()
    {
        var w=[],wp;
        var rleg = directionsDisplay.directions.routes[0].legs[0];
        data.start = {'lat': rleg.start_location.lat(), 'lng':rleg.start_location.lng()}
        data.end = {'lat': rleg.end_location.lat(), 'lng':rleg.end_location.lng()}
        var wp = rleg.via_waypoints 
        for(var i=0;i<wp.length;i++)w[i] = [wp[i].lat(),wp[i].lng()] 
        data.waypoints = w;
         
        var str = JSON.stringify(data)
        var routeName = document.getElementById("routeName").value;
        // var userPhone = document.getElementById("userPhone").value;
        var userEmail = "<?php echo $_SESSION['email'] ?>";
        console.log("route name: " + routeName);
        console.log("user Email: " + userEmail);
        
     
        var jax = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        jax.open('POST','./php/process.php');
        jax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        // jax.send('command=save&mapdata='+str)
        jax.send('command=save&mapdata='+str+'&routeName='+routeName+'&userEmail='+userEmail)
        jax.onreadystatechange = function(){ if(jax.readyState==4) {
            if(jax.responseText.indexOf('bien')+1)alert('Updated');
            else alert(jax.responseText)
        }}
    }
    
    function setroute(os)
    {
    	var wp = [];
    	for(var i=0;i<os.waypoints.length;i++)
    		wp[i] = {'location': new google.maps.LatLng(os.waypoints[i][0], os.waypoints[i][1]),'stopover':false }
    		
    	ser.route({'origin':new google.maps.LatLng(os.start.lat,os.start.lng),
    	'destination':new google.maps.LatLng(os.end.lat,os.end.lng),
    	'waypoints': wp,
    	'travelMode': google.maps.DirectionsTravelMode.DRIVING},function(res,sts) {
    		if(sts=='OK')ren.setDirections(res);
    	})	
    }
    
    function displayDirectionsDisplay() {
        //GET THE JSON Object
        var newString = JSON.stringify(directionsDisplay.directions);
        
        //set up area to place drop directionsResponse object string
        var directions_response_panel = document.getElementById("directions_response");
        //dump any contents in directions_response_panel
        directions_response_panel.innerHTML = "";
        //add JSON string to it 
        directions_response_panel.innerHTML = "<pre>" + newString + "</pre>";
    }
    function createMarker(latlng, label, html) {
        // alert("createMarker("+latlng+","+label+","+html+","+color+")");
        var contentString = '<b>'+label+'</b><br>'+html;
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: label,
            zIndex: Math.round(latlng.lat()*-100000)<<5
        });
        marker.myname = label;
        // gmarkers.push(marker);
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.setContent(contentString);
            infowindow.open(map,marker);
        });
        return marker;
    }
    // This function creates and displays google map
    // with vehicle locations displayed as icons
    function initialize() {
        infowindow = new google.maps.InfoWindow(
            {
                size: new google.maps.Size(150,50)
            });
        // Create a map and center it on Austin.
        var myOptions = {
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("map"), myOptions);
        address = 'Austin'
        geocoder = new google.maps.Geocoder();
        geocoder.geocode( { 'address': address}, function(results, status) {
            map.setCenter(results[0].geometry.location);
        });
        // Instantiate a directions service.
        directionsService = new google.maps.DirectionsService();
        // Create a renderer for directions and bind it to the map.
        var rendererOptions = {
            map: map,
            draggable: true
        }
        directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
        
        directionsDisplay.addListener('directions_changed', function() {
            computeTotalDistance(directionsDisplay.getDirections());
        });
        // Instantiate an info window to hold step text.
        stepDisplay = new google.maps.InfoWindow();
        polyline = new google.maps.Polyline({
            path: [],
            strokeColor: '#FF0000',
            strokeWeight: 3
        });
        poly2 = new google.maps.Polyline({
            path: [],
            strokeColor: '#FF0000',
            strokeWeight: 3
        });
        
    }
    var steps = []
    // this function calculates and displays a route
    // between a 'start' and 'end' location and then
    // displays the route
    function calcRoute(){
        if (timerHandle) { clearTimeout(timerHandle); }
        if (marker) { marker.setMap(null);}
        polyline.setMap(null);
        poly2.setMap(null);
        directionsDisplay.setMap(null);
        polyline = new google.maps.Polyline({
            path: [],
            strokeColor: '#FF0000',
            strokeWeight: 3
        });
        // poly2 = new google.maps.Polyline({
        //     path: [],
        //     strokeColor: '#FF0000',
        //     strokeWeight: 3
        // });
        // Create a renderer for directions and bind it to the map.
        var rendererOptions = {
            map: map,
            draggable: true
        }
        directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
        // get 'start' and 'end' locations from user input
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        var travelMode = google.maps.DirectionsTravelMode.DRIVING
        var request = {
            origin: start,
            destination: end,
            travelMode: travelMode
        };
        // Route the directions and pass the response to a
        // function to create markers for each step.
        directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK){
                directionsDisplay.setDirections(response);
                var bounds = new google.maps.LatLngBounds();
                var route = response.routes[0];
                startLocation = new Object();
                endLocation = new Object();
                // For each route, display summary information.
                var path = response.routes[0].overview_path;
                var legs = response.routes[0].legs;
                for (i=0;i<legs.length;i++) {
                    if (i == 0) {
                        startLocation.latlng = legs[i].start_location;
                        startLocation.address = legs[i].start_address;
                        // marker = google.maps.Marker({map:map,position: startLocation.latlng});
                        marker = createMarker(legs[i].start_location,"start",legs[i].start_address,"green");
                    }
                    endLocation.latlng = legs[i].end_location;
                    endLocation.address = legs[i].end_address;
                    var steps = legs[i].steps;
                    for (j=0;j<steps.length;j++) {
                        var nextSegment = steps[j].path;
                        for (k=0;k<nextSegment.length;k++) {
                            polyline.getPath().push(nextSegment[k]);
                            bounds.extend(nextSegment[k]);
                        }
                    }
                }
                // polyline.setMap(map);
                map.fitBounds(bounds);
                // createMarker(endLocation.latlng,"end",endLocation.address,"red");
                map.setZoom(18);
            }
        });
    }
</script>

</head>

<body onload="initialize()">
  <section id="header" class="appear"></section>
  <div class="navbar navbar-fixed-top" role="navigation" data-0="line-height:100px; height:100px; background-color:rgba(0,0,0,0.3);" data-300="line-height:60px; height:60px; background-color:rgba(5, 42, 62, 1);">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      	  <span class="fa fa-bars color-white"></span>
        </button>
        <div class="navbar-logo">
          <a href="index.php">Austin SafeRoutes</a>
        </div>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav" data-0="margin-top:20px;" data-300="margin-top:5px;">
            <li class="active"><a href="index.php">Home</a></li>
            <?php  
                // if user is not logged in, display 'Sign In'
                if (!isset($_SESSION['access_token'])) {
                    echo "<li class='active'><a href='login.php'>Log In</a>";
                }
                else {
                    echo "<li class='active'><a href='login.php'>Log Out</a>";
                }
            ?>
            <li class="active"><a href="saveRoutes.php">Save Routes</a></li>
            <li class="active"><a href="myRoutes.php">My Routes</a></li>
            <li><a href="#section-contact">Contact</a></li>
            
            <!--get picture from facebook user data-->
            <li class="active"><img src="<?php echo $_SESSION['userPicture'] ?>" width="50" height="50"></li>
        </ul>
      </div>
      <!--/.navbar-collapse -->
    </div>
  </div>

  <section id="intro">
    <div class="intro-content">
      <h2>Welcome to Austin SafeRoutes!</h2>
      <h3>Save your routes and plan for your commute</h3>
      <div>
        <a href="#section-services" class="btn-get-started scrollto">Create Route</a>
      </div>
    </div>
  </section>

  <!-- services -->
  <section id="section-services" class="section pad-bot30 bg-white">
    <div id="map"></div>
  </section>
  
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDuunjc6oVSx4bDOGBmQC_1Pi9VngVmNM&callback=initMap"></script>

  <!--create route-->
  <div class="container">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <h4 class="card-header">Create Route</h4>
                <div class="card-body">
                    <p class="card-text">Enter The Start and End Locations to create a route</p>
                    <p class="card-text">Click and drag on route to modify</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <h4 class="card-header">Route Addresses</h4>
                <div class="card-body">
                    <div class="input-group">
                        <!-- <span class="input-group-addon"></span> -->
                        <div id="tools">
                            <input id="start" type="text" class="form-control mb-2" name="Start Address" placeholder="Start Address">
                            <input id="end" type="text" class="form-control mb-2" name="End Address" placeholder="End Address">
                            <input type= "submit" class ="btn-create-route" value="Create Route" onclick="calcRoute();"/>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row"><br><br><br><br></div>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <h4 class="card-header">Save Route</h4>
                <div class="card-body">
                    <p class="card-text">Enter the name of the route and save</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <h4 class="card-header">Route Name</h4>
                <div class="input-group">
                    <div id="tools">
                    
                        <input id="routeName" type="text" class="form-control mb-2" name="Route Name" placeholder="Enter Name of Route">
                        <input type="submit" class="btn-create-route" value="Save Route" onClick="save_waypoints()">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--<div class="row"><br><br><br><br></div>-->
    <!--<div class="row">-->
    <!--    <div class="col-lg-4 mb-4">-->
    <!--        <div class="card h-100">-->
    <!--            <h4 class="card-header">Phone Number</h4>-->
    <!--            <div class="card-body">-->
    <!--                <p class="card-text">Enter the phone number to receive notifications</p>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="col-lg-6 mb-4">-->
    <!--        <div class="card h-100">-->
    <!--            <h4 class="card-header">Phone Number</h4>-->
    <!--            <div class="input-group">-->
    <!--                <div id="tools">-->
                    
    <!--                    <input id="userPhone" type="text" class="form-control mb-2" name="User Phone" placeholder="Enter Phone Number">-->
    <!--                    <input type="submit" class="btn-create-route" value="Save Route" onClick="save_waypoints()">-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    <!--<div class="row">-->
    <!--    <div class="col-lg-6 mb-4">-->
    <!--        <div class="card h-100">-->
    <!--            <div id="directions_response">-->
    <!--                <input type="button" class="btn-create-route" value = "Display JSON" onclick="displayDirectionsDisplay();"/>-->
                    
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    
  </div>

  <section id="section-contact" class="section appear clearfix">
    <div class="container">

      <div class="row mar-bot40">
        <div class="col-md-offset-3 col-md-6">
          <div class="section-header">
            <h2 class="section-heading animated" data-animation="bounceInUp">Contact us</h2>
            <p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet consectetur, adipisci velit, sed quia non numquam.</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="cform" id="contact-form">
            <div id="sendmessage">Your message has been sent. Thank you!</div>
            <div id="errormessage"></div>
            <form action="" method="post" class="contactForm">

              <div class="field your-name form-group">
                <input type="text" name="name" placeholder="Your Name" class="cform-text" size="40" data-rule="minlen:4" data-msg="Please enter at least 4 chars">
                <div class="validation"></div>
              </div>
              <div class="field your-email form-group">
                <input type="text" name="email" placeholder="Your Email" class="cform-text" size="40" data-rule="email" data-msg="Please enter a valid email">
                <div class="validation"></div>
              </div>
              <div class="field subject form-group">
                <input type="text" name="subject" placeholder="Subject" class="cform-text" size="40" data-rule="minlen:4" data-msg="Please enter at least 8 chars of subject">
                <div class="validation"></div>
              </div>

              <div class="field message form-group">
                <textarea name="message" class="cform-textarea" cols="40" rows="10" data-rule="required" data-msg="Please write something for us"></textarea>
                <div class="validation"></div>
              </div>

              <div class="send-btn">
                <input type="submit" value="SEND MESSAGE" class="btn btn-theme">
              </div>

            </form>
          </div>
        </div>
        <!-- ./span12 -->
      </div>

    </div>
  </section>

  <section id="footer" class="section footer">
    <div class="container">
      <div class="row animated opacity mar-bot20" data-andown="fadeIn" data-animation="animation">
        <div class="col-sm-12 align-center">
          <ul class="social-network social-circle">
            <li><a href="#" class="icoRss" title="Rss"><i class="fa fa-rss"></i></a></li>
            <li><a href="#" class="icoFacebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#" class="icoTwitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>
            <li><a href="#" class="icoGoogle" title="Google +"><i class="fa fa-google-plus"></i></a></li>
            <li><a href="#" class="icoLinkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
          </ul>
        </div>
      </div>
      <div class="row align-center mar-bot20">
        <ul class="footer-menu">
          <li><a href="index.php">Home</a></li>
          <li><a href="index.php">About us</a></li>
          <li><a href="privacyPolicy.html">Privacy policy</a></li>
          <li><a href="index.php">Get in touch</a></li>
        </ul>
      </div>
      <div class="row align-center copyright">
        <div class="col-sm-12">
          <p>Copyright &copy; All rights reserved</p>
        </div>
      </div>
      <div class="credits">
        <!--
          All the links in the footer should remain intact.
          You can delete the links only if you purchased the pro version.
          Licensing information: https://bootstrapmade.com/license/
          Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Vlava
        -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade.com</a>
      </div>
    </div>

  </section>
  <a href="#header" class="scrollup"><i class="fa fa-chevron-up"></i></a>

  <!-- Javascript Library Files -->
  <script src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  <script src="js/jquery.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.isotope.min.js"></script>
  <script src="js/jquery.nicescroll.min.js"></script>
  <script src="js/fancybox/jquery.fancybox.pack.js"></script>
  <script src="js/skrollr.min.js"></script>
  <script src="js/jquery.scrollTo.min.js"></script>
  <script src="js/jquery.localScroll.min.js"></script>
  <script src="js/stellar.js"></script>
  <script src="js/jquery.appear.js"></script>
  <script src="js/jquery.flexslider-min.js"></script>
   <!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8HeI8o-c1NppZA-92oYlXakhDPYR7XMY"></script> -->

  <!-- Contact Form JavaScript File -->
  <script src="contactform/contactform.js"></script>

  <!-- Template Main Javascript File -->
  <script src="js/main.js"></script>

</body>

</html>