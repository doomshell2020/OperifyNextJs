<footer>
        <div class="container-fluid">


            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <!-- <div class="col-md-4 order-md-1  order-1 col-sm-6 text-sm-start text-center">
                    <div class="socil-icons">
                        <a href="#"> <i class="ri-twitter-x-line"></i></a>
                        <a href="#"> <i class="ri-facebook-fill"></i></a>
                        <a href="#"> <i class="ri-linkedin-fill"></i></a>
                        <a href="#"> <i class="ri-youtube-line"></i></a>
                        <a href="#"> <i class="ri-pinterest-line"></i></a>
                    </div>
                </div> -->



                <div class=" order-md-2 order-0 ">
                    <div class="btm-logo">
                        <a href="<?php echo SITE_URL ?>homes/index">
                            <img src="<?php echo SITE_URL ?>image/logo.png" alt="logo">
                        </a>
                    </div>
                </div>



                <div class=" order-md-3 order-2   text-sm-end text-center">
                <div class="download-app">
                            <a href="https://apps.apple.com/in/app/the-operify/id6670391874" target="_blank">
                                <img src="<?php echo SITE_URL ?>image/ios-download.png" alt="ios-download">
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=com.doomshell.the_operify&pcampaignid=web_share" target="_blank">
                                <img src="<?php echo SITE_URL ?>image/android-download.png" alt="android-download">
                            </a>
                        </div>
                </div>





                <div class="col-12 order-md-4 order-3 mt-4">
                    <div class="btm-links btm-line">
                        <ul>
                            <li>
                                <a href="<?php echo SITE_URL ?>homes/privacy">Privacy Policy</a>
                            </li>
                            <li>
                                <a href="<?php echo SITE_URL ?>homes/terms">Terms of Service</a>
                            </li>
                            <li>
                                <a href="<?php echo SITE_URL ?>homes/refundpolicy">Refund Policy</a>
                            </li>
                            <li>
                                <a href="<?php echo SITE_URL ?>homes/faq">FAQ</a>
                            </li>
                            <li>
                                <a href="<?php echo SITE_URL ?>homes/pricing">Pricing</a>
                            </li>
                        </ul>
                    </div>
                </div>


            </div>

            <div class="btm-line">
                <p>© Copyright 2024, All Rights Reserved by Doomshell</p>
            </div>
        </div>
    </footer>


    <script src="<?php echo SITE_URL ?>js/jquery.min-ope.js"></script>
    <script src="<?php echo SITE_URL ?>js/owl.carousel.min-ope.js"></script>
    <script src="<?php echo SITE_URL ?>js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>


    <script>
        $('.testimonial-carousel').owlCarousel({
            loop: true,
            nav: true,
            dots: false,
            autoplay: true,
            navText: [],
            margin: 50,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },

                1000: {
                    items: 2,
                    nav: true,
                    loop: true
                }
            }
        })
    </script>




    <script>
        $('.client-carousel').owlCarousel({
            loop: true,
            nav: true,
            dots: false,
            navText: [],
            autoplay: true,
            margin: 30,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: true
                },

                576: {
                    items: 4,
                    nav: true,
                    loop: true
                },
                1000: {
                    items: 5,
                    nav: true,
                    loop: true
                }
            }
        })
    </script>


</body>

</html>


<script>
    $("#form1").submit(function(event) {
        alert();
        var recaptcha = $("#g-recaptcha-response").val();
        if (recaptcha === "") {
            event.preventDefault();
            alert("Please check the recaptcha");
            return false;
        }
    });
</script>