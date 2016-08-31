<?php

//error_reporting(0);
require('api/database_connection.php');
$captcha;$error_msg="";
if(isset($_POST['g-recaptcha-response']))
  $captcha=$_POST['g-recaptcha-response'];

if(!$captcha){
        $error_msg .= "reCAPTCHA Invalid.  Please try again.<br/>";
  exit;
}
$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfaiiQTAAAAAF-qr9OlLtaRt5QAj2zGRskre1U9&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
if($response['success'] == false)
{
    $error_msg .= "reCAPTCHA Invalid.  Please try again<br/>";
}
if (isset($_POST['submit'])) {
    if (empty($_POST['taoaddress'])) {
        $error_msg .= "Please provide a Tao address<br/>";
    } else {
        $taoaddress = $_POST['taoaddress'];
    }
    if (empty($_POST['referralid'])) {
        $referralid = "";
    } else {
        $referralid = $_POST['referralid'];
    }
    $uri = "http://tao.network/api/sale.php/".$taoaddress."/".$referralid;
    $json = file_get_contents($uri);
    if(empty($json)){
        die('fatal error');
    }

    $data = json_decode($json, true);
} else {
        $error_msg .= "Invalid.  Please try again<br/>";
}
?>
<!DOCTYPE html>
<html dir="rtl">
    <head>
        <title>Tao: Bringing Balance to the Blockchain</title>
        <meta name="description" content="">
        <meta name="keywords" content="blockchain cryptocurrency ICO bitcoin exchange initial coin offering">
        <meta charset="utf-8">
        <meta name="author" content="Taoron">
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        
        <!-- Favicons -->
        <link rel="shortcut icon" href="images/favicon.png">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
        
        <!-- CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-rtl.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/style-responsive.css">
        <link rel="stylesheet" href="css/animate.min.css">
        <link rel="stylesheet" href="css/vertical-rhythm.min.css">
        <link rel="stylesheet" href="css/owl.carousel.css">
        <link rel="stylesheet" href="css/magnific-popup.css">        
    </head>
    <body class="appear-animate">
        <!-- Page Loader -->        
       <!-- End Page Loader -->
        
        <!-- Page Wrap -->
        <div class="page" id="top">
            <!-- Navigation panel -->
            <nav class="main-nav dark transparent stick-fixed">
                <div class="full-wrapper relative clearfix">
                    <!-- Logo ( * your text or image into link tag *) -->
                    <div class="nav-logo-wrap local-scroll">
                        <a href="#top" class="logo">
                            <img src="images/logo-white.png" alt="" />
                        </a>
                    </div>
                    <div class="mobile-nav">
                        <i class="fa fa-bars"></i>
                    </div>
                    <div class="inner-nav desktop-nav">
                        <ul class="clearlist scroll-nav local-scroll">
                            <!--
                            <li class="active"><a href="http://explorer.tao.network">Explorer</a></li>
                            -->
                            <li class="active"><a href="/wallet/#wallet">Web Wallet</a></li>
                            <li class="active"><a href="Roadmap.pdf">Roadmap</a></li>
                            <li class="active"><a href="crowdsale.html">Crowdsale</a></li>
                            <!--
                            <li class="active"><a href="#download">Download</a></li>
                            -->
                            <li class="active"><a href="/TaoOfMusic.pdf">Tao of Music</a></li>
                            <li class="active"><a href="/InvestorsHandbook.pdf">Investors' Handbook</a></li>
                            <li class="active"><a href="/WelcometoTao.pdf">White Paper</a></li>
                            <li class="active"><a href="index.html#about">About</a></li>
                            <li class="active"><a href="index.html#home">Home</a></li>
                            <!-- Social Links -->
                            <li>
                                <a href="https://tao11.typeform.com/to/ws8MgM"><span class="mn-soc-link tooltip-bot" title="Slack"><i class="fa fa-slack"></i></span></a>
                                <a href="https://twitter.com/taoblockchain"><span class="mn-soc-link tooltip-bot" title="Twitter"><i class="fa fa-twitter"></i></span></a>
                                <a href="https://github.com/taoblockchain"><span class="mn-soc-link tooltip-bot" title="Github"><i class="fa fa-github"></i></span></a>
                            </li>
                            <!-- End Social Links  -->
                            
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navigation panel -->
<?php
    if (!empty($data["error"]) || !empty($error_msg))
    {
        echo "<script>alert('".$error_msg."');</script>";
        echo "<script>alert('".$data["error"]."');</script>";

        ?>
            <section class="page-section bg-dark" data-background="images/full-width-images/tao3.png" id="error">
                <div class="container text-left">
                    <h1>Error In Transaction</h1>
                </div>
                <div class="container relative">
                    <div class="container">
                        <div class="text-left">
                        <h2>Please see the information below to complete your sale.</h2>
                        </div>
                        <div id="error_msg" class="section-text align-left mb-70 mb-xs-40" style="color:#ddd !important;">
                            <ul>
                                <?php if (len($data) > 0) echo "<li>".$data["error"]."</li>"; ?>
                                <?php echo "<li>".$error_msg."</li>"; ?>
                            </ul>
                        </div>
                        <div class="section-text align-left mb-70 mb-xs-40" style="color:#ddd !important;">
                            Please use your browser's Back Button correct the items listed above and resubmit your request
                        </div>
                    </div>
                </div>
            </section>
        <?php
    } else {

        ?>
            <section class="page-section bg-dark" data-background="images/full-width-images/tao3.png" id="sale_complete" style="height:70px;padding-bottom:75px;"> 
                <div class="container text-left">
                    <h1>Almost done...</h1>
                </div>
            </section>
            <section class="page-section bg-light" style="top:-50px;">
                <div class="container relative">
                        <div class="section-text align-left mb-70 mb-xs-40" style="color:#000 !important;"><h3>Please send funds to the Bitcoin address provided</h3><br />Your account will be credited with the amount sent to the address provided once the payment has been fully confirmed, usually between 60-90 minutes<br />
                            Tao is a proportional crowdsale, which means your share of the network is a direct proprtion of your funds contributed
                        </div>
                </div>
                <div class="container relative">
                        <div class="text-center">
                            <div id="clickme">
                                <a  href="https://www.coinbase.com/join/575854acc9e2825342000b97" target="_blank" class="btn btn-primary">&#63;Need to buy Bitcoin</a>
                            </div>
                            <div id="clickme">
                                <a  href="https://info.shapeshift.io/tools/shapeshift-lens" target="_blank" class="btn btn-primary">&#33;Pay With Altcoins</a>
                            </div>

                        </div>
                    <div class="container">
                        <div class="alt-service-grid">
                            <div class="row multi-columns-row">                          
                                <!-- Alt Service Item -->
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="alt-service-item">
                                        <div class="form-group">
                                            <h3>Your Referral Rewards Link</h3>
                                            <span>Have your friends sign up with this link and earn special rewards<br/></span>
                                            <div class="col-md-6" id="reward_code" style="color:#000;">
                                                <?php echo $data["referralLink"]; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Service Item -->           
                                <!-- Alt Service Item -->
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="alt-service-item">
                                        <div class="form-group required-control">               
                                            <h3>Bitcoin Deposit Address</h3><span id="bitcoin_address"  style="color:#000;>
                                            <div id="qr_code">
                                                <?php echo $data["btcAddress"]; ?><br />
                                                <?php echo $data["qrCode"]; ?>
                                            </div></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Service Item -->
                            </div>
                        </div>                  
                    </div>
                </div>
            </section>
        <?php        
    }

?>
            <!-- Foter -->
            <footer class="page-section bg-gray-lighter footer pb-60">
                <div class="container">
                    
                    <!-- Footer Logo -->
                    <div class="local-scroll mb-30 wow fadeInUp" data-wow-duration="1.2s">
                        <a href="#top"><img src="images/logo-footer.png" width="78" height="36" alt="" /></a>
                    </div>
                    <!-- End Footer Logo -->
                    <div class="container">
                        <div class="alt-service-grid">
                            <div class="row multi-columns-row">                          
                                
                                <!-- Alt Service Item -->
                                <div class="col-sm-8 col-md-4 col-lg-4">
                                    <div class="alt-service-item">
                                        <h3 class="hs-line-4 font-alt" style="color:#000">&nbsp;</h3>
                                    </div>
                                </div>
                                <!-- End Service Item -->
                                <!-- Alt Service Item -->
                                <div class="col-sm-8 col-md-4 col-lg-4">
                                    <div class="alt-service-item">
                                        <div class="alt-service-icon">
                                            <i class="fa fa-usd"></i>
                                        </div>
                                        <h3 class="hs-line-4 font-alt" style="color:#000">Total Raised</h3>
                                        <span id="currentvalue"></span>
                                    </div>
                                </div>
                                <!-- End Service Item -->
                                <!-- Alt Service Item -->
                                <div class="col-sm-8 col-md-4 col-lg-4">
                                    <div class="alt-service-item">
                                        <div class="alt-service-icon">
                                            <i class="fa fa-power-off"></i>
                                        </div>
                                        <h3 class="hs-line-4 font-alt" style="color:#000">Total Tokens</h3>
                                        <span id="totaltokens"></span>
                                    </div>
                                </div>
                                <!-- End Service Item -->
                              
                                <!-- Alt Service Item -->
                                <div class="col-sm-8 col-md-4 col-lg-4">
                                    <div class="alt-service-item">
                                        <h3 class="hs-line-4 font-alt" style="color:#000">&nbsp;</h3>
                                    </div>
                                </div>
                                <!-- End Service Item -->
                            </div>
                        </div>                  
                    </div>
                    
                    <!-- Social Links -->
                    <div class="footer-social-links mb-110 mb-xs-60">
                        <a href="https://tao11.typeform.com/to/ws8MgM" title="Slack" target="_blank"><i class="fa fa-slack"></i></a>
                        <a href="https://twitter.com/taoblockchain" title="Twitter" target="_blank"><i class="fa fa-twitter"></i></a>
                        <a href="https://github.com/taoblockchain" title="Github" target="_blank"><i class="fa fa-github"></i></a>
                        </div>
                    <!-- End Social Links -->  
                    
                    <!-- Footer Text -->
                    <div class="footer-text">
                        Copyright 2016 - All rights reserved.                        
                    </div>
                    <!-- End Footer Text --> 
                    
                 </div>
                 
                 
                 <!-- Top Link -->
                 <div class="local-scroll">
                     <a href="#top" class="link-to-top"><i class="fa fa-caret-up"></i></a>
                 </div>
                 <!-- End Top Link -->
                 
            </footer>
            <!-- End Foter -->
        
        </div>
        <!-- End Page Wrap -->
        <!-- JS -->
        <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>        
        <script type="text/javascript" src="js/SmoothScroll.js"></script>
        <script type="text/javascript" src="js/jquery.scrollTo.min.js"></script>
        <script type="text/javascript" src="js/jquery.localScroll.min.js"></script>
        <script type="text/javascript" src="js/jquery.viewport.mini.js"></script>
        <script type="text/javascript" src="js/jquery.countTo.js"></script>
        <script type="text/javascript" src="js/jquery.appear.js"></script>
        <script type="text/javascript" src="js/jquery.sticky.js"></script>
        <script type="text/javascript" src="js/jquery.parallax-1.1.3.js"></script>
        <script type="text/javascript" src="js/jquery.fitvids.js"></script>
        <script type="text/javascript" src="js/owl.carousel.min.js"></script>
        <script type="text/javascript" src="js/isotope.pkgd.min.js"></script>
        <script type="text/javascript" src="js/imagesloaded.pkgd.min.js"></script>
        <script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language=en"></script>
        <script type="text/javascript" src="js/gmap3.min.js"></script>
        <script type="text/javascript" src="js/wow.min.js"></script>
        <script type="text/javascript" src="js/masonry.pkgd.min.js"></script>
        <script type="text/javascript" src="js/jquery.simple-text-rotator.min.js"></script>
        <script type="text/javascript" src="js/all.js"></script>
        <script type="text/javascript" src="js/contact-form.js"></script>
        <script type="text/javascript" src="js/jquery.ajaxchimp.min.js"></script> 
        <script type="text/javascript" src="js/jquery.downCount.js"></script>        
        <!--[if lt IE 10]><script type="text/javascript" src="js/placeholder.js"></script><![endif]-->
        <script>
        /**
         * Get the value of a querystring
         * @param  {String} field The field to get the value of
         * @param  {String} url   The URL to get the value from (optional)
         * @return {String}       The field value
         */
        var getQueryString = function ( field, url ) {
            var href = url ? url : window.location.href;
            var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
            var string = reg.exec(href);
            return string ? string[1] : null;
        };

        $(function() { 
            $(".btn").click(function(){
                $(this).button('loading').delay(1000).queue(function() {
                    $(this).button('reset');
                    $(this).dequeue();
                });        
            });
        });

        var ref_id = getQueryString('ref_id');
        $('#referralid:text').val(ref_id);
        $.ajax({
            url: "http://tao.network/api/basics.php",
            dataType: 'json'
        }).then(function(data) {
           $('#currentvalue').text(data['currentUSDValue']);
           $('#totaltokens').text(data['totalCoins']);
           $('#tokenprice').text(data['currentPrice']);
        });
        $(document).ready(function() {
        });
        </script>

</body>
</html>