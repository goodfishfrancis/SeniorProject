<?php
    session_start();
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
  <script src="js/map.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdLbIKfOqZpLFI9-X76P81ADhY5nxFNps&callback=initMap"></script>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link rel="stylesheet" type="text/css" href="js/rs-plugin/css/settings.css" media="screen"> -->
  <link rel="stylesheet" type="text/css" href="css/isotope.css" media="screen">
  <link rel="stylesheet" href="css/flexslider.css" type="text/css">
  <link rel="stylesheet" href="js/fancybox/jquery.fancybox.css" type="text/css" media="screen">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Serif:400,400italic,700|Open+Sans:300,400,600,700">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">

  

  <link rel="stylesheet" href="css/style.css">
  <!-- skin -->
  <link rel="stylesheet" href="skin/default.css">
  <!-- =======================================================
    Theme Name: Vlava
    Theme URL: https://bootstrapmade.com/vlava-free-bootstrap-one-page-template/
    Author: BootstrapMade.com
    Author URL: https://bootstrapmade.com
  ======================================================= -->

</head>

<body onload="initMap(); filterData();">
  
  <section id="header" class="appear"></section>
  <div class="navbar navbar-fixed-top" role="navigation" data-0="line-height:100px; height:100px; background-color:rgba(0,0,0,0.3);" data-300="line-height:60px; height:60px; background-color:rgba(5, 42, 62, 1);">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      	  <span class="fa fa-bars color-white"></span>
        </button>
        <div class="navbar-logo">
          <a href="index.php">Austin Safe Routes</a>
        </div>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav" data-0="margin-top:20px;" data-300="margin-top:5px;">
          <li class="active"><a href="index.php">Home</a></li>
          <li><a href="#section-about">About</a></li>
          <?php  
            // if user is not logged in, display 'Log In'
            if (!isset($_SESSION['access_token'])) {
                echo "<li class='active'><a href='login.php'>Log In</a>";
            }
            else { //display 'Log Out'
                echo "<li class='active'><a href='login.php'>Log Out</a>";
            }
          ?>
          <li class="active"><a href="saveRoutes.php">Save Routes</a></li>
          <li class="active"><a href="myRoutes.php">My Routes</a></li>
          <li><a href="#section-contact">Contact</a></li>
          <?php  
            // if user is logged in, display user picture
            if (isset($_SESSION['access_token'])) {
                echo "<li class='active'><img src=". $_SESSION['userPicture']." width='50' height='50'></li>";
            }
          ?>
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
        <a href="#section-services" class="btn-get-started scrollto">Get Started</a>
      </div>
    </div>
  </section>

  <!-- services -->
  
  <section id="section-services" class="section pad-bot30 bg-white">
    
    <div class="container">
      <!--<div class="row">-->
      <!--    <div class="fb-login-button" data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false"></div>-->
      <!--</div>-->
      <div class="row">
        <div class="col-md-4">
          <strong>INCIDENT STATUS: </strong>
          <select id="incidentStatus">
            <option value="ACTIVE">ACTIVE</option>
            <option value="ARCHIVED">ARCHIVED</option>
          </select>
        </div>
        <div class="col-md-5">
          <strong>INCIDENT TYPE: </strong>
          <select id="incidentType">
            <option value="">ALL</option>
            <option value="Traffic Hazard">Traffic Hazard</option>
            <option value="Crash Urgent">Crash Urgent</option>
            <option value="Crash Service">Crash Service</option>
            <option value="Traffic Impediment">Traffic Impediment</option>
            <option value="COLLISION">Collision</option>
            <option value="zSTALLED VEHICLE">Stalled Vehicle</option>
            <option value="COLLISION WITH INJURY">Collision With Injury</option>
            <option value="LOOSE LIVESTOCK">Loose Livestock</option>
            <option value="COLLISN/ LVNG SCN">COLLISN/ LVNG SCN</option>
            <option value="COLLISION/PRIVATE PROPERTY">Collision With Private Property</option>
            <option value="VEHICLE FIRE">Vehicle Fire</option>
            <option value="BLOCKED DRIV/ HWY">Blocked Road</option>
            <option value="ICY ROADWAY">Icy Roadway</option>
            <option value="BOAT ACCIDENT">Boat Accident</option>
            <option value="AUTO/ PED">AUTO/ PED</option>
            <option value="FLEET ACC/ INJURY">FLEET ACC/ INJURY</option>
            <option value="TRAFFIC FATALITY">Traffic Fatality</option>
            <option value="N/ HZRD TRFC VIOL">N/ HZRD TRFC VIOL</option>
            <option value="COLLISN / FTSRA">COLLISION / FTSRA</option>
            <option value="HIGH WATER">High Water</option>
          </select>
        </div>
        <div class="col-md-2">
            <input type= "submit" class ="btn-filter-data" onclick="filterData();"/>
        </div>
      </div>
    </div>
    <script src="js/map.js"></script>
    <div id="map" onload="filterData();"></div>
  </section>
  
  

  <!-- spacer section:testimonial -->
  <section id="testimonials" class="section" data-stellar-background-ratio="0.5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="align-center">
            <div class="flexslider testimonials-slider">
              <ul class="slides">
                <li>
                  <div class="testimonial clearfix">
                    <div class="mar-bot20">
                      <img alt="" src="img/testimonial/mockup1.png" class="img-square">
                    </div>
                    <h5>
												Nunc velit risus, dapibus non interdum quis, suscipit nec dolor. Vivamus tempor tempus mauris vitae fermentum. In vitae nulla lacus. Sed sagittis tortor vel arcu sollicitudin nec tincidunt metus suscipit.Nunc velit risus, dapibus non interdum.
											</h5>
                    <br/>
                  </div>
                </li>
                <li>
                  <div class="testimonial clearfix">
                    <div class="mar-bot20">
                      <img alt="" src="img/testimonial/mockup2.png" class="img-square">
                    </div>
                    <h5>
											Nunc velit risus, dapibus non interdum quis, suscipit nec dolor. Vivamus tempor tempus mauris vitae fermentum. In vitae nulla lacus. Sed sagittis tortor vel arcu sollicitudin nec tincidunt metus suscipit.Nunc velit risus, dapibus non interdum.
											</h5>
                    <br/>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- about -->
  <section id="section-about" class="section appear clearfix">
    <div class="container">

      <div class="row mar-bot40">
        <div class="col-md-offset-3 col-md-6">
          <div class="section-header">
            <h2 class="section-heading animated" data-animation="bounceInUp">Behind Austin SafeRoutes</h2>
            <p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet consectetur, adipisci velit, sed quia non numquam.</p>
          </div>
        </div>
      </div>

      <div class="row align-center mar-bot40">
        <div class="col-md-offset-3 col-md-3">
          <div class="team-member">
            <figure class="member-photo"><img src="img/team/alex.jpeg" alt=""></figure>
            <div class="team-detail">
              <h4>Alexander Solinger</h4>
              <span>St. Edward's University</span>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="team-member">
            <figure class="member-photo"><img src="img/team/nick.jpeg" alt=""></figure>
            <div class="team-detail">
              <h4>Nicholas Scharold</h4>
              <span>St. Edward's University</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /about -->

  <!-- spacer section:stats -->
  <section id="parallax1" class="section" data-stellar-background-ratio="0.5">
    <div class="container">
      <div class="row appear stats">
        <div class="col-md-4">
          <div class="align-center color-white txt-shadow">
            <div class="icon">
              <i class="fas fa-car-crash fa-5x"></i>
            </div>
            <strong id="counter-coffee" class="number"><script type="text/javascript">document.write(25);</script></strong><br>
            <span class="text">Reported Traffic Incidents Today</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="align-center color-white txt-shadow">
            <div class="icon">
              <i class="fa fa-clock-o fa-5x"></i>
            </div>
            <strong id="counter-clock" class="number">20</strong><br>
            <span class="text">Minutes Since Last Reported Incident</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="align-center color-white txt-shadow">
            <div class="icon">
              <i class="fas fa-route fa-5x"></i>
            </div>
            <strong id="counter-heart" class="number">478</strong><br>
            <span class="text">Saved Routes</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- <section id="clients" class="section clearfix bg-white"> -->
    <!-- <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-sm-2 align-center">
              <img alt="logo" src="img/clients/logo1.png">
            </div>

            <div class="col-sm-2 align-center">
              <img alt="logo" src="img/clients/logo2.png">
            </div>

            <div class="col-sm-2 align-center">
              <img alt="logo" src="img/clients/logo3.png">
            </div>

            <div class="col-sm-2 align-center">
              <img alt="logo" src="img/clients/logo4.png">
            </div>

            <div class="col-sm-2 align-center">
              <img alt="logo" src="img/clients/logo5.png">
            </div>
            <div class="col-sm-2 align-center">
              <img alt="logo" src="img/clients/logo6.png">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> -->

  <!-- map -->
  <!-- <section id="section-map" class="clearfix">
    <div id="google-map" data-latitude="40.713417" data-longitude="-74.0092125"></div>
  </section> -->

  <!-- contact -->
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
          <li><a href="#">About us</a></li>
          <li><a href="privacyPolicy.html">Privacy policy</a></li>
          <li><a href="#">Get in touch</a></li>
        </ul>
      </div>
      <div class="row align-center copyright">
        <div class="col-sm-12">
          <p>Copyright &copy; Austin SafeRoutes All rights reserved</p>
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

  <!-- Contact Form JavaScript File -->
  <script src="contactform/contactform.js"></script>

  <!-- Template Main Javascript File -->
  <script src="js/main.js"></script>

</body>

</html>
