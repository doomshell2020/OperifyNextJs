<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <?php 
        // Program to display URL of current page. 
        //pr($_SERVER);
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
            $link = "https"; 
        else
            $link = "http"; 
        // Here append the common URL characters. 
        $link .= "://"; 
        // Append the host(domain name, ip) to the URL. 
        $link .= $_SERVER['HTTP_HOST'];
        $link .= $_SERVER['REQUEST_URI'];
        $met=$this->Comman->meta($link);
        $met2=$this->Comman->meta("https://www.idsprime.com/");
        if($met['title']!="")
    { ?>
    <!-- Google Links -->
    <link rel="preconnect" href="https://www.google.com">
    <link rel="preconnect" href="https://www.googleapis.com">
    <link rel="preconnect" href="https://cse.google.com">
    <link rel="preconnect" href="https://stats.g.doubleclick.net">
    <link rel="preconnect" href="https://www.google.co.in">
    <link rel="preconnect" href="https://clients1.google.com">
    <link rel="preconnect" href="https://cse.google.com">
    <!-- Open Graph -->
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $met['title']; ?>" />
    <meta property="og:description" name="description" content="<?php echo $met['description'];?>" />
    <meta property="og:url" content="<?php echo SITE_URL; ?>" />
    <meta property="og:site_name" content="idsPrime" />
    <meta property="og:updated_time" content="<?php echo date('Y-m-d H:i:s'); ?>" />
    <meta property="og:image" content="<?php echo SITE_URL; ?>images/idsprime-logo.webp" />
    <meta property="og:image:secure_url" content="<?php echo SITE_URL; ?>images/idsprime-logo.webp" />
    <meta property="og:image:width" content="712" />
    <meta property="og:image:height" content="364" />
    <meta property="og:image:alt" content="idsPrime-logo" />
    <meta property="og:image:type" content="images/webp" />
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL; ?>">
    <meta property="twitter:title" content="<?php echo $met['title']; ?>">
    <meta property="twitter:description" content="<?php echo $met['description'];?>">
    <meta property="twitter:image" content="<?php echo SITE_URL; ?>images/idsprime-logo.webp">
    <!-- FB Admins -->
    <meta name="fb:admins" content="">
    <meta name="google-site-verification" content="" />
    <meta name="p:domain_verify" content="" />
    <meta name="yandex-verification" content="" />
    <title><?php echo $met['title']; ?></title>
    <meta name="keywords" content="<?php echo $met['keyword'];?>" />
    <meta name="description" content="<?php echo $met['description'];?>" />
    <?php }else{ ?>
    <title><?php echo $met2['title']; ?></title>
    <meta name="keywords" content="<?php echo $met2['keyword'];?>" />
    <meta name="description" content="<?php echo $met2['description'];?>" />
    <?php } ?>
    <link rel="icon" type="image/ico" href="favicon.ico">
    <link href="http://www.idsprime.com" rel="canonical">
    <link href="<?php echo SITE_URL; ?>" rel="alternate" hreflang="en-US">
    <!----------------------------Google recaptcha--------------------->
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
    <!------------font------------------>
    <!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800" rel="stylesheet"> -->
    <!---------------owl carousel---------------------->
    <link href="<?php echo SITE_URL; ?>css/owl.carousel.min.css" rel="stylesheet" type="text/css">
    <!---------bootstrap-------------------->
    <link href="<?php echo SITE_URL; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"> -->

    <!------------wow-effect------------------>
    <link href="<?php echo SITE_URL; ?>css/animate.css" rel="stylesheet" type="text/css">
    <!------------font-awesome------------------>
    <link href="<?php echo SITE_URL; ?>css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo SITE_URL; ?>css/scrolling-nav.css" rel="stylesheet">
    <!---------------genral---------------------->
    <link href="<?php echo SITE_URL; ?>css/style2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo SITE_URL; ?>css/responsive2.css" rel="stylesheet" type="text/css">
    <?= $this->element('scriptheader') ?>
</head>

<body>
    <div id="page-wrapper">
  
        <style>
        #header .nav li a:last-child {
            padding: 10px 0px 9px 26px;
        }

        #header nav.navbar {
            background: #fff !important;
        }

        #why .tg-sectiontitle,
        #whatSMS .tg-sectiontitle {
            margin-bottom: 30px;
        }

        #whatSMS {
            padding: 30px 0px 50px
        }

        #whatSMS p {
            text-align: justify;
            text-align-last: center;
            font-size: 16px;
            margin-bottom: 10px;
        }

        #why .tg-sectionhead,
        #whatSMS .tg-sectionhead {
            float: none;
            padding-bottom: 0px;
        }

        #login div.tab button {
            height: 163px;
        }

        .tabcontent p {
            margin-bottom: 15px;
            font-size: 16px;
            text-align: justify;
        }

        #why {
            padding: 60px 0px;
        }

        #why h2,
        h3 {
            text-align: left;
            margin-bottom: 30px;
            font-size: 32px;
            color: #414042;
            font-weight: 500;
        }

        #why p {
            text-align: justify;
            text-align-last: left;
            font-size: 16px;
            margin-bottom: 10px;
        }

        #why .whyContent {
            padding-left: 30px;
            border-left: 1px solid #999;
            margin-left: 10px;
        }

        #why .whyContent p {
            position: relative;
        }

        #why .whyContent p:before {
            content: '';
            height: 15px;
            width: 15px;
            position: absolute;
            left: -38px;
            top: 5px;
            background: #fff;
            border-radius: 50%;
            border: 2px solid #1790cf;
        }

        .tg-sectiontitle {
            margin: 0px;
            margin-bottom: 30px
        }

        #services {
            padding-bottom: 60px;
        }

        /* Accordion styles */
        .tabs {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
        }

        .tab {
            width: 100%;
            color: white;
            overflow: hidden;
        }

        .tab-label {
            display: flex;
            justify-content: space-between;
            padding: 1em;
            background: #2c3e50;
            font-weight: bold;
            cursor: pointer;
            /* Icon */
        }

        .tab-label:hover {
            background: #1a252f;
        }

        .tab-label::after {
            content: "❯";
            width: 1em;
            height: 1em;
            text-align: center;
            transition: all 0.35s;
        }

        .tab-content {
            max-height: 0;
            padding: 0 1em;
            color: #2c3e50;
            background: white;
            transition: all 0.35s;
        }

        .tab-close {
            display: flex;
            justify-content: flex-end;
            padding: 1em;
            font-size: 0.75em;
            background: #2c3e50;
            cursor: pointer;
        }

        .tab-close:hover {
            background: #1a252f;
        }

        input:checked+.tab-label {
            background: #1a252f;
        }

        input:checked+.tab-label::after {
            transform: rotate(90deg);
        }

        input:checked~.tab-content {
            max-height: 100vh;
            padding: 1em;
        }

        #benefits input[type=checkbox],
        input[type=radio] {
            display: none;
        }

        .tab-label {
            background: #1790cf !important;
            border-radius: 0px !important;
            font-size: 16px;
        }


        .tabs {
            box-shadow: none;
            border-radius: 0px;
            /* display: flex;
            flex-wrap: wrap;
            justify-content: space-between; */
        }

        .tab {
            margin-bottom: 10px;
            /* width: 49%; */
        }

        #benefits {
            margin-bottom: 40px;
        }

        .tab-content {
            max-height: 0;
            padding: 0 1em;
            color: #2c3e50;
            background: white;
            transition: all 0.35s;
            text-align: justify;
            color: #333;
            font-weight: 400;
            font-size: 16px;
        }
        </style>
        <header id="header">

            <!-- <nav id="mainNav" class="navbar navbar-expand-lg navbar-light">
                <div class="container w-100">
                    <a class="navbar-brand js-scroll-trigger" href="#header">
                    <img src="<?php echo SITE_URL; ?>images/idsprime-logo.webp" alt="idsPrime-logo">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-link js-scroll-trigger"><a href="#services">Our Services</a></li>
                            <li class="nav-link js-scroll-trigger"><a href="#client">Our Clients</a></li>
                            <li class="nav-link js-scroll-trigger"><a href="#features">Features</a></li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="<? echo SITE_URL; ?>homes/aboutus">About us</a>
                            </li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="<? echo SITE_URL; ?>homes/pricing">Pricing Policy</a>
                            </li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="<? echo SITE_URL; ?>homes/privacy">Privacy Policy</a>
                            </li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="#contact">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav> -->

            <nav id="mainNav" class="navbar navbar-inverse navbar-expand-lg navbar-dark bg-dark fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand js-scroll-trigger" href="#header">
                            <img src="<?php echo SITE_URL; ?>images/idsprime-logo.webp" alt="idsPrime-logo">
                        </a>
                    </div>
                    <div class="collapse navbar-collapse" id="myNavbar">
                        <ul class="nav navbar-nav ml-auto">
                            <li class="nav-link js-scroll-trigger"><a href="#services">Our Services</a></li>
                            <li class="nav-link js-scroll-trigger"><a href="#client">Our Clients</a></li>
                            <li class="nav-link js-scroll-trigger"><a href="#features">Features</a></li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="<? echo SITE_URL; ?>homes/aboutus">About us</a>
                            </li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="<? echo SITE_URL; ?>homes/pricing">Pricing Policy</a>
                            </li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="<? echo SITE_URL; ?>homes/privacy">Privacy Policy</a>
                            </li>
                            <li class="nav-link js-scroll-trigger">
                                <a href="#contact">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
<style>

.scroll_lg {
    display: none !important;
}
.no_scroll_lg {
    display: inline-block !important;
}

 .menu a span {
    display: block !important;
    height: 2px !important;
    background-color: #000 !important;
    float: right !important;
    -webkit-transition: .5s !important;
    -moz-transition: .5s !important;
    -o-transition: .5s !important;
    transition: .5s !important;
}

   .pagee {
       position: absolute;
       top: 354px;
       left: 0px;
       right: 0px;
       text-align: center;
       margin: auto;
       background: #ff6300;
       width: max-content;
       padding: 5px 30px;
       color: #fff;
   }   
   </style>

<img src="https://www.doomshell.com/images/template404.jpg" style="width:100% height:100%" >

<a href="javascript:history.back()"><P class="pagee">GO TO HOMEPAGE</P></a>
<div class="f_bar">
            <div class="container">
                <div class="row">
                    <div class="col-md-2 col-xs-12"></div>
                    <div class="col-md-8 col-xs-12">
                        <p>© <?php echo date('Y'); ?> <a href="https://www.idsprime.com/">idsprime.com</a>. | All Rights Reserved.</p>
                    </div>
                    <div class="col-md-2 col-xs-12">

                    </div>
                </div>
            </div>  
        </div>
    </div>
<?php

//echo $this->element('footer');
?>