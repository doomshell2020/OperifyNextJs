<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operify</title>
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="<?php echo SITE_URL ?>image/favicon-32x32.png"
        type="image/ico">
  
    <link rel="stylesheet" href="<?php echo SITE_URL ?>css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo SITE_URL ?>css/owl.carousel.min-ope.css">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>css/owl.theme.default.min-ope.css">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>css/style.css">
        <link rel="stylesheet" href="<?php echo SITE_URL ?>css/responsive-operi.css">
</head>

<body>


    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo SITE_URL ?>homes/index">
                    <img src="<?php echo SITE_URL ?>image/logo.png" alt="logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mb-2 mb-lg-0">


                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"
                                href="<?php echo SITE_URL ?>homes/about">About Us</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"
                                href="<?php echo SITE_URL ?>homes/product">Product</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page"
                                href="<?php echo SITE_URL ?>homes/pricing">Pricing</a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL ?>homes/faq">FAQ</a>
                        </li>


                    </ul>

                </div>
                <form class="d-flex align-items-center nav-rgt" role="search">
                    <!-- <button class="btn  me-2 btn-1" type="button">Login</button>
            <button class="btn  me-2 btn-2" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">Book a Demo</button> -->
                    <a href="<?php echo SITE_URL ?>logins" class="btn  me-2 btn-1">Login</a>
                    <a href="#" class="btn  me-2 btn-2" data-bs-toggle="modal" data-bs-target="#exampleModal">Get
                        Started For Free</a>
                </form>
            </div>
        </nav>
    </header>


    
<div class="form-design">
    <!-- Button trigger modal -->
    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
  </button> -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row g-0 w-100">
                        <div class="col-md-4">
                            <div class="mdl-hd-left">

                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mdl-hd-rgt">
                                <div>
                                    <h2 class="modal-title " id="exampleModalLabel">Sign Up </h2>
                                    <div class="d-flex flex-wrap justify-content-between mt-2">
                                        <p class="mb-1"> Start your 14-days Free Trial </p>
                                        <p class="mb-1"> No cards and no commitments </p>
                                        <p> 100% Safe and Secure </p>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">

                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="frm-left">
                                <img src="<?php echo SITE_URL ?>image/form-img.png" alt="">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="frm-right">
                                <!-- <form class=" row g-3" action="homes/contactus" id="form1" method="post"> -->

                                <? echo $this->Form->create('contactus', array('url' => array('controller' => 'homes', 'action' => 'contactus'), 'type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'class' => 'row g-3', 'id' => 'form1', 'autocomplete' => 'off', 'required', 'validate')); ?>


                                    <div class="col-lg-6">
                                        <div class="form-floating">
                                            <input type="text" name="name" class="form-control" id="inputEmail4"
                                                placeholder="Enter Your Name">
                                            <label for="inputEmail4" class="form-label">Name</label>
                                        </div>
                                    </div>


                                    <div class="col-lg-6">
                                        <div class="form-floating">
                                            <input type="text" name="company_name" class="form-control" id="inputEmail4"
                                                placeholder="Enter Company Name">
                                            <label for="inputEmail4" class="form-label">Company Name</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-floating">
                                            <input type="text" name="title" class="form-control" id="inputfName"
                                                placeholder="Enter Your Title">
                                            <label for="inputfName" class="form-label ">Title</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-floating">
                                            <input type="email" name="email" class="form-control" id="inputEmail4"
                                                placeholder="Email">
                                            <label for="inputEmail4" class="form-label">Email</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-floating">
                                            <input type="text" name="phone" class="form-control" id="inputPhone" placeholder="Mobile" maxlength="12" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                            <label for="inputPhone" class="form-label">Mobile</label>

                                        </div>
                                    </div>




                                    <div class="col-lg-6">
                                        <div class="form-floating">
                                            <select name="day" class="form-control " id="validationCustom06">
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                                <option value="Saturday">Saturday</option>
                                            </select>
                                            <label for="day" class="form-label">Day</label>
                                        </div>
                                    </div>



                                    <div class="col-lg-6">
                                        <div class="form-floating">

                                            <select name="time" class="form-control" id="validationCustom07">
                                                <option value="Morning (9AM – 12PM)">Morning (9AM – 12PM)</option>
                                                <option value="Afternoon (12PM - 2PM)">Afternoon (12PM - 2PM)</option>
                                                <option value="Evening (4PM - 7PM)">Evening (4PM - 7PM)</option>
                                            </select>

                                            <label for="phonenumber" class="form-label">Time</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-floating">
                                            <textarea name="message" class="form-control mb-3" placeholder="Message"></textarea>
                                            <label for="companyName" class="form-label">Message</label>
                                        </div>
                                    </div>



                                    <div class="mt-5">
                                        <p class="mb-0">Create a name for your Operify account,
                                            e.g. short name of your company.</p>
                                        <p>It cannot be changed after the account is created.</p>
                                    </div>

                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="g-recaptcha" id="recaptcha2" data-sitekey="6LdSrYQqAAAAAKv0a1Fbxhh_ec9cvtT9Bhk-ChaC"></div>



                                    <div class="col-12 mt-4 mb-2">
                                        <button type="submit" class="btn "> Start a free 14-days trial</button>
                                    </div>


                                    <span>By creating an account, you agree to the <a href="<?php echo SITE_URL ?>homes/terms"> Terms of
                                            Service</a> and<a href="<?php echo SITE_URL ?>homes/privacy"> Privacy Policy.</a></span>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>