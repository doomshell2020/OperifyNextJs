<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Overview
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Production/Overview">
                    Overview
                </a></li>
        </ol>
    </section>
    <!-- content header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box ovrviw">

                    <div class="row">

                        <div class="col-sm-4">
                            <h6>Sales</h6>
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-secondary">Week</button>
                                <button type="button" class="btn btn-secondary">Month</button>
                                <button type="button" class="btn btn-secondary">Year</button>
                            </div>



                            <div class="inr-ovrviw">
                                <div class="row">
                                    <p for="">Today, Saturday 9 September</p>
                                    <div class="col-sm-4">
                                        <h4>0</h4>
                                        <p>Retail Sale</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4>0</h4>
                                        <p>Rs</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4>0</h4>
                                        <p>Comparing with friday</p>
                                    </div>
                                </div>
                            </div>

                            <div class="inr-ovrviw">
                                <div class="row">
                                    <p for="">Today, Saturday 9 September</p>
                                    <div class="col-sm-4">
                                        <h4>0</h4>
                                        <p>Retail Sale</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4>0</h4>
                                        <p>Rs</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4>0</h4>
                                        <p>Comparing with friday</p>
                                    </div>
                                </div>
                            </div>




                        </div>
                        <div class="col-sm-8">
                            <div class="ovrviw_graph">

                                <canvas id='ovrgrphf'></canvas>


                            </div>
                        </div>
                    </div>
                    <!-- /.col -->

                    <div class="ord_inv">
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="row">
                                    <h6>Overdue Orders</h6>
                                    <div class="col-sm-2">
                                        <h4>0</h4>
                                        <p>Orders</p>
                                    </div>
                                    <div class="col-sm-2">
                                        <h4>0</h4>
                                        <p>Rs</p>
                                    </div>

                                </div>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Company</th>
                                            <th>Sales Order</th>
                                            <th>Total</th>
                                            <th>Term</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                        </tr>
                                    </tbody>

                                </table>


                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <h6>Unpaid Invoices</h6>
                                    <div class="col-sm-2">
                                        <h4>0</h4>
                                        <p>Invoices</p>
                                    </div>
                                    <div class="col-sm-2">
                                        <h4>0</h4>
                                        <p>Rs</p>
                                    </div>

                                </div>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Company</th>
                                            <th>Invoice</th>
                                            <th>Total</th>
                                            <th>Term</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <h6>Money</h6>
                                        <div class="col-sm-2">
                                            <h4>0</h4>
                                            <p>Rs</p>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th></th>
                                                <th></th>
                                                <th>Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6">

                                    <canvas id="myChart" style="width:100%;max-width:700px"></canvas>

                                </div>
                            </div>

                            <div class="col-sm-12">
                                <h6>Money</h6>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Operation</th>
                                            <th>Appr.</th>
                                            <th>Moment</th>
                                            <th>Countryparty</th>
                                            <th>Company</th>
                                            <th>Total</th>
                                            <th>Currency</th>
                                            <th>Date Modified</th>
                                            <th>Modified by</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.   content-wrapper -->
<div class="modal fade" id="myModal" style="width:51% !important;overflow-y: auto !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:100% !important;">
        <div class="modal-content personal">
            <div class="loader">
                <div class="es-spinner">
                    <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".globalModals").click(function(event) {
            // alert($(this).attr("href"));
            $('.modal-content').load($(this).attr("href")); //load content from href of link
        });
    });
</script>








































<script>
    const ANIMATION_DURATION = 300;

    const SIDEBAR_EL = document.getElementById("sidebar");

    const SUB_MENU_ELS = document.querySelectorAll(
        ".menu > ul > .menu-item.sub-menu"
    );

    const FIRST_SUB_MENUS_BTN = document.querySelectorAll(
        ".menu > ul > .menu-item.sub-menu > a"
    );

    const INNER_SUB_MENUS_BTN = document.querySelectorAll(
        ".menu > ul > .menu-item.sub-menu .menu-item.sub-menu > a"
    );

    class PopperObject {
        instance = null;
        reference = null;
        popperTarget = null;

        constructor(reference, popperTarget) {
            this.init(reference, popperTarget);
        }

        init(reference, popperTarget) {
            this.reference = reference;
            this.popperTarget = popperTarget;
            this.instance = Popper.createPopper(this.reference, this.popperTarget, {
                placement: "right",
                strategy: "fixed",
                resize: true,
                modifiers: [{
                        name: "computeStyles",
                        options: {
                            adaptive: false
                        }
                    },
                    {
                        name: "flip",
                        options: {
                            fallbackPlacements: ["left", "right"]
                        }
                    }
                ]
            });

            document.addEventListener(
                "click",
                (e) => this.clicker(e, this.popperTarget, this.reference),
                false
            );

            const ro = new ResizeObserver(() => {
                this.instance.update();
            });

            ro.observe(this.popperTarget);
            ro.observe(this.reference);
        }

        clicker(event, popperTarget, reference) {
            if (
                SIDEBAR_EL.classList.contains("collapsed") &&
                !popperTarget.contains(event.target) &&
                !reference.contains(event.target)
            ) {
                this.hide();
            }
        }

        hide() {
            this.instance.state.elements.popper.style.visibility = "hidden";
        }
    }

    class Poppers {
        subMenuPoppers = [];

        constructor() {
            this.init();
        }

        init() {
            SUB_MENU_ELS.forEach((element) => {
                this.subMenuPoppers.push(
                    new PopperObject(element, element.lastElementChild)
                );
                this.closePoppers();
            });
        }

        togglePopper(target) {
            if (window.getComputedStyle(target).visibility === "hidden")
                target.style.visibility = "visible";
            else target.style.visibility = "hidden";
        }

        updatePoppers() {
            this.subMenuPoppers.forEach((element) => {
                element.instance.state.elements.popper.style.display = "none";
                element.instance.update();
            });
        }

        closePoppers() {
            this.subMenuPoppers.forEach((element) => {
                element.hide();
            });
        }
    }

    const slideUp = (target, duration = ANIMATION_DURATION) => {
        const {
            parentElement
        } = target;
        parentElement.classList.remove("open");
        target.style.transitionProperty = "height, margin, padding";
        target.style.transitionDuration = `${duration}ms`;
        target.style.boxSizing = "border-box";
        target.style.height = `${target.offsetHeight}px`;
        target.offsetHeight;
        target.style.overflow = "hidden";
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        window.setTimeout(() => {
            target.style.display = "none";
            target.style.removeProperty("height");
            target.style.removeProperty("padding-top");
            target.style.removeProperty("padding-bottom");
            target.style.removeProperty("margin-top");
            target.style.removeProperty("margin-bottom");
            target.style.removeProperty("overflow");
            target.style.removeProperty("transition-duration");
            target.style.removeProperty("transition-property");
        }, duration);
    };
    const slideDown = (target, duration = ANIMATION_DURATION) => {
        const {
            parentElement
        } = target;
        parentElement.classList.add("open");
        target.style.removeProperty("display");
        let {
            display
        } = window.getComputedStyle(target);
        if (display === "none") display = "block";
        target.style.display = display;
        const height = target.offsetHeight;
        target.style.overflow = "hidden";
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        target.offsetHeight;
        target.style.boxSizing = "border-box";
        target.style.transitionProperty = "height, margin, padding";
        target.style.transitionDuration = `${duration}ms`;
        target.style.height = `${height}px`;
        target.style.removeProperty("padding-top");
        target.style.removeProperty("padding-bottom");
        target.style.removeProperty("margin-top");
        target.style.removeProperty("margin-bottom");
        window.setTimeout(() => {
            target.style.removeProperty("height");
            target.style.removeProperty("overflow");
            target.style.removeProperty("transition-duration");
            target.style.removeProperty("transition-property");
        }, duration);
    };

    const slideToggle = (target, duration = ANIMATION_DURATION) => {
        if (window.getComputedStyle(target).display === "none")
            return slideDown(target, duration);
        return slideUp(target, duration);
    };

    const PoppersInstance = new Poppers();

    /**
     * wait for the current animation to finish and update poppers position
     */
    const updatePoppersTimeout = () => {
        setTimeout(() => {
            PoppersInstance.updatePoppers();
        }, ANIMATION_DURATION);
    };

    /**
     * sidebar collapse handler
     */
    document.getElementById("btn-collapse").addEventListener("click", () => {
        SIDEBAR_EL.classList.toggle("collapsed");
        PoppersInstance.closePoppers();
        if (SIDEBAR_EL.classList.contains("collapsed"))
            FIRST_SUB_MENUS_BTN.forEach((element) => {
                element.parentElement.classList.remove("open");
            });

        updatePoppersTimeout();
    });

    /**
     * sidebar toggle handler (on break point )
     */
    document.getElementById("btn-toggle").addEventListener("click", () => {
        SIDEBAR_EL.classList.toggle("toggled");

        updatePoppersTimeout();
    });

    /**
     * toggle sidebar on overlay click
     */
    document.getElementById("overlay").addEventListener("click", () => {
        SIDEBAR_EL.classList.toggle("toggled");
    });

    const defaultOpenMenus = document.querySelectorAll(".menu-item.sub-menu.open");

    defaultOpenMenus.forEach((element) => {
        element.lastElementChild.style.display = "block";
    });

    /**
     * handle top level submenu click
     */
    FIRST_SUB_MENUS_BTN.forEach((element) => {
        element.addEventListener("click", () => {
            if (SIDEBAR_EL.classList.contains("collapsed"))
                PoppersInstance.togglePopper(element.nextElementSibling);
            else {
                const parentMenu = element.closest(".menu.open-current-submenu");
                if (parentMenu)
                    parentMenu
                    .querySelectorAll(":scope > ul > .menu-item.sub-menu > a")
                    .forEach(
                        (el) =>
                        window.getComputedStyle(el.nextElementSibling).display !==
                        "none" && slideUp(el.nextElementSibling)
                    );
                slideToggle(element.nextElementSibling);
            }
        });
    });

    /**
     * handle inner submenu click
     */
    INNER_SUB_MENUS_BTN.forEach((element) => {
        element.addEventListener("click", () => {
            slideToggle(element.nextElementSibling);
        });
    });
</script>






<script>
    const toastTrigger = document.getElementById('liveToastBtn')
    const toastLiveExample = document.getElementById('liveToast')
    if (toastTrigger) {
        toastTrigger.addEventListener('click', () => {
            const toast = new bootstrap.Toast(toastLiveExample)

            toast.show()
        })
    }
</script>















<script>
    var toastElList = [].slice.call(document.querySelectorAll('.toast'))
    var toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, option)
    })
</script>



<script>
    var label = document.querySelector(".label");
    var c = document.getElementById("ovrgrphf");
    var ctx = c.getContext("2d");
    var cw = c.width = 700;
    var ch = c.height = 350;
    var cx = cw / 2,
        cy = ch / 2;
    var rad = Math.PI / 180;
    var frames = 0;

    ctx.lineWidth = 1;
    ctx.strokeStyle = "#999";
    ctx.fillStyle = "#ccc";
    ctx.font = "14px monospace";

    var grd = ctx.createLinearGradient(0, 0, 0, cy);
    grd.addColorStop(0, "hsla(167,72%,60%,1)");
    grd.addColorStop(1, "hsla(167,72%,60%,0)");

    var oData = {
        "Mon": 0.5,
        "Tue": 0.2,
        "Wed": 0.0,
        "Thu": 0.7,
        "Fri": 0.2,
        "Sat": 1,
        "Sun": 0.1,

    };

    var valuesRy = [];
    var propsRy = [];
    for (var prop in oData) {

        valuesRy.push(oData[prop]);
        propsRy.push(prop);
    }


    var vData = 4;
    var hData = valuesRy.length;
    var offset = 50.5; //offset chart axis
    var chartHeight = ch - 2 * offset;
    var chartWidth = cw - 2 * offset;
    var t = 1 / 7; // curvature : 0 = no curvature 
    var speed = 2; // for the animation

    var A = {
        x: offset,
        y: offset
    }
    var B = {
        x: offset,
        y: offset + chartHeight
    }
    var C = {
        x: offset + chartWidth,
        y: offset + chartHeight
    }

    /*
          A  ^
            |  |  
            + 25
            |
            |
            |
            + 25  
          |__|_________________________________  C
          B
    */

    // CHART AXIS -------------------------
    ctx.beginPath();
    ctx.moveTo(A.x, A.y);
    ctx.lineTo(B.x, B.y);
    ctx.lineTo(C.x, C.y);
    ctx.stroke();

    // vertical ( A - B )
    var aStep = (chartHeight - 50) / (vData);

    var Max = Math.ceil(arrayMax(valuesRy) / 1.0) * 0.5;
    var Min = Math.floor(arrayMin(valuesRy) / -1.0) * 0.5;
    var aStepValue = (Max - Min) / (vData);
    console.log("aStepValue: " + aStepValue); //8 units
    var verticalUnit = aStep / aStepValue;

    var a = [];
    ctx.textAlign = "right";
    ctx.textBaseline = "middle";
    for (var i = 0; i <= vData; i++) {

        if (i == 0) {
            a[i] = {
                x: A.x,
                y: A.y + 25,
                val: Max
            }
        } else {
            a[i] = {}
            a[i].x = a[i - 1].x;
            a[i].y = a[i - 1].y + aStep;
            a[i].val = a[i - 1].val - aStepValue;
        }
        drawCoords(a[i], 3, 0);
    }

    //horizontal ( B - C )
    var b = [];
    ctx.textAlign = "center";
    ctx.textBaseline = "hanging";
    var bStep = chartWidth / (hData + 1);

    for (var i = 0; i < hData; i++) {
        if (i == 0) {
            b[i] = {
                x: B.x + bStep,
                y: B.y,
                val: propsRy[0]
            };
        } else {
            b[i] = {}
            b[i].x = b[i - 1].x + bStep;
            b[i].y = b[i - 1].y;
            b[i].val = propsRy[i]
        }
        drawCoords(b[i], 0, 3)
    }

    function drawCoords(o, offX, offY) {
        ctx.beginPath();
        ctx.moveTo(o.x - offX, o.y - offY);
        ctx.lineTo(o.x + offX, o.y + offY);
        ctx.stroke();

        ctx.fillText(o.val, o.x - 2 * offX, o.y + 2 * offY);
    }
    //----------------------------------------------------------

    // DATA
    var oDots = [];
    var oFlat = [];
    var i = 0;

    for (var prop in oData) {
        oDots[i] = {}
        oFlat[i] = {}

        oDots[i].x = b[i].x;
        oFlat[i].x = b[i].x;

        oDots[i].y = b[i].y - oData[prop] * verticalUnit - 25;
        oFlat[i].y = b[i].y - 25;

        oDots[i].val = oData[b[i].val];

        i++
    }



    ///// Animation Chart ///////////////////////////
    //var speed = 3;
    function animateChart() {
        requestId = window.requestAnimationFrame(animateChart);
        frames += speed; //console.log(frames)
        ctx.clearRect(60, 0, cw, ch - 60);

        for (var i = 0; i < oFlat.length; i++) {
            if (oFlat[i].y > oDots[i].y) {
                oFlat[i].y -= speed;
            }
        }
        drawCurve(oFlat);
        for (var i = 0; i < oFlat.length; i++) {
            ctx.fillText(oDots[i].val, oFlat[i].x, oFlat[i].y - 25);
            ctx.beginPath();
            ctx.arc(oFlat[i].x, oFlat[i].y, 3, 0, 2 * Math.PI);
            ctx.fill();
        }

        if (frames >= Max * verticalUnit) {
            window.cancelAnimationFrame(requestId);

        }
    }
    requestId = window.requestAnimationFrame(animateChart);

    /////// EVENTS //////////////////////
    c.addEventListener("mousemove", function(e) {
        label.innerHTML = "";
        label.style.display = "none";
        this.style.cursor = "default";

        var m = oMousePos(this, e);
        for (var i = 0; i < oDots.length; i++) {

            output(m, i);
        }

    }, false);

    function output(m, i) {
        ctx.beginPath();
        ctx.arc(oDots[i].x, oDots[i].y, 20, 0, 2 * Math.PI);
        if (ctx.isPointInPath(m.x, m.y)) {
            //console.log(i);
            label.style.display = "block";
            label.style.top = (m.y + 10) + "px";
            label.style.left = (m.x + 10) + "px";
            label.innerHTML = "<strong>" + propsRy[i] + "</strong>: " + valuesRy[i] + "%";
            c.style.cursor = "pointer";
        }
    }

    // CURVATURE
    function controlPoints(p) {
        // given the points array p calculate the control points
        var pc = [];
        for (var i = 1; i < p.length - 1; i++) {
            var dx = p[i - 1].x - p[i + 1].x; // difference x
            var dy = p[i - 1].y - p[i + 1].y; // difference y
            // the first control point
            var x1 = p[i].x - dx * t;
            var y1 = p[i].y - dy * t;
            var o1 = {
                x: x1,
                y: y1
            };

            // the second control point
            var x2 = p[i].x + dx * t;
            var y2 = p[i].y + dy * t;
            var o2 = {
                x: x2,
                y: y2
            };

            // building the control points array
            pc[i] = [];
            pc[i].push(o1);
            pc[i].push(o2);
        }
        return pc;
    }

    function drawCurve(p) {

        var pc = controlPoints(p); // the control points array

        ctx.beginPath();
        //ctx.moveTo(p[0].x, B.y- 25);
        ctx.lineTo(p[0].x, p[0].y);
        // the first & the last curve are quadratic Bezier
        // because I'm using push(), pc[i][1] comes before pc[i][0]
        ctx.quadraticCurveTo(pc[1][1].x, pc[1][1].y, p[1].x, p[1].y);

        if (p.length > 2) {
            // central curves are cubic Bezier
            for (var i = 1; i < p.length - 2; i++) {
                ctx.bezierCurveTo(pc[i][0].x, pc[i][0].y, pc[i + 1][1].x, pc[i + 1][1].y, p[i + 1].x, p[i + 1].y);
            }
            // the first & the last curve are quadratic Bezier
            var n = p.length - 1;
            ctx.quadraticCurveTo(pc[n - 1][0].x, pc[n - 1][0].y, p[n].x, p[n].y);
        }

        //ctx.lineTo(p[p.length-1].x, B.y- 25);
        ctx.stroke();
        ctx.save();
        ctx.fillStyle = grd;
        ctx.fill();
        ctx.restore();
    }

    function arrayMax(array) {
        return Math.max.apply(Math, array);
    };

    function arrayMin(array) {
        return Math.min.apply(Math, array);
    };

    function oMousePos(canvas, evt) {
        var ClientRect = canvas.getBoundingClientRect();
        return { //objeto
            x: Math.round(evt.clientX - ClientRect.left),
            y: Math.round(evt.clientY - ClientRect.top)
        }
    }
</script>





<!-- first graph chart END -->

<script>
    var xyValues = [{
            x: 50,
            y: 7
        },
        {
            x: 60,
            y: 8
        },
        {
            x: 70,
            y: 8
        },
        {
            x: 80,
            y: 9
        },
        {
            x: 90,
            y: 9
        },
        {
            x: 100,
            y: 9
        },
        {
            x: 110,
            y: 10
        },
        {
            x: 120,
            y: 11
        },
        {
            x: 130,
            y: 14
        },
        {
            x: 140,
            y: 14
        },
        {
            x: 150,
            y: 15
        }
    ];

    new Chart("myChart", {
        type: "scatter",
        data: {
            datasets: [{
                pointRadius: 4,
                pointBackgroundColor: "#2d95e3",
                data: xyValues
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    ticks: {
                        min: 40,
                        max: 160
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 6,
                        max: 16
                    }
                }],
            }
        }
    });
</script>