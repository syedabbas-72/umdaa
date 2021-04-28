<style>
* {
    margin: 0;
    padding: 0
}

html {
    height: 100%
}


#msform {
    text-align: center;
    position: relative;
    margin-top: 20px
}

#msform fieldset .form-card {
    background: white;
    border: 0 none;
    border-radius: 0px;
    box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
    padding: 20px 40px 30px 40px;
    box-sizing: border-box;
    width: 94%;
    margin: 0 3% 20px 3%;
    position: relative
}

#msform fieldset {
    background: white;
    border: 0 none;
    border-radius: 0.5rem;
    box-sizing: border-box;
    width: 100%;
    margin: 0;
    padding-bottom: 20px;
    position: relative
}

#msform fieldset:not(:first-of-type) {
    display: none
}

#msform fieldset .form-card {
    text-align: left;
    color: #9E9E9E
}

/* #msform input,
#msform textarea {
    padding: 0px 8px 4px 8px;
    border: none;
    border-bottom: 1px solid #ccc;
    border-radius: 0px;
    margin-bottom: 25px;
    margin-top: 2px;
    width: 100%;
    box-sizing: border-box;
    font-family: montserrat;
    color: #2C3E50;
    font-size: 16px;
    letter-spacing: 1px
} */
/* 
#msform input:focus,
#msform textarea:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: none;
    font-weight: bold;
    border-bottom: 2px solid skyblue;
    outline-width: 0
} */

#msform .action-button {
    width: 100px;
    background: #10367a;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 0px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px
}

#msform .action-button:hover,
#msform .action-button:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #10367a
}

#msform .action-button-previous {
    width: 100px;
    background: #616161;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 0px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px
}

#msform .action-button-previous:hover,
#msform .action-button-previous:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #616161
}

select.list-dt {
    border: none;
    outline: 0;
    border-bottom: 1px solid #ccc;
    padding: 2px 5px 3px 5px;
    margin: 2px
}

select.list-dt:focus {
    border-bottom: 2px solid #10367a
}

.card {
    z-index: 0;
    border: none;
    border-radius: 0.5rem;
    position: relative
}

.fs-title {
    font-size: 25px;
    color: #2C3E50;
    margin-bottom: 10px;
    font-weight: bold;
    text-align: left
}

#progressbar {
    margin-bottom: 30px;
    overflow: hidden;
    color: lightgrey
}

#progressbar .active {
    color: #000000
}

#progressbar li {
    list-style-type: none;
    font-size: 12px;
    width: 25%;
    float: left;
    position: relative
}

#progressbar #account:before {
    font-family: FontAwesome;
    content: "\f023"
}

#progressbar #personal:before {
    font-family: FontAwesome;
    content: "\f007"
}

#progressbar #payment:before {
    font-family: FontAwesome;
    content: "\f09d"
}

#progressbar #confirm:before {
    font-family: FontAwesome;
    content: "\f00c"
}

#progressbar li:before {
    width: 50px;
    height: 50px;
    line-height: 45px;
    display: block;
    font-size: 18px;
    color: #ffffff;
    background: lightgray;
    border-radius: 50%;
    margin: 0 auto 10px auto;
    padding: 2px
}

#progressbar li:after {
    content: '';
    width: 100%;
    height: 2px;
    background: lightgray;
    position: absolute;
    left: 0;
    top: 25px;
    z-index: -1
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: #10367a
}

.radio-group {
    position: relative;
    margin-bottom: 25px
}

.radio {
    display: inline-block;
    width: 204;
    height: 104;
    border-radius: 0;
    background: lightblue;
    box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.2);
    box-sizing: border-box;
    cursor: pointer;
    margin: 8px 2px
}

.radio:hover {
    box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.3)
}

.radio.selected {
    box-shadow: 1px 1px 2px 2px rgba(0, 0, 0, 0.1)
}

.fit-image {
    width: 100%;
    object-fit: cover
}
</style>
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- MultiStep Form -->
                <div class="container-fluid" id="grad1">
                    <h4 class="page-title">create orders</h4>
                    <div class="row justify-content-center mt-0">
                        <div class="col-md-10">
                            <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                                <!-- <h2><strong>Creating Patient Orders</strong></h2>
                                <p>Fill all form field to go to next step</p> -->
                                <div class="row">
                                    <div class="col-md-12 mx-0">
                                        <form id="msform">
                                            <!-- progressbar -->
                                            <ul id="progressbar">
                                                <li class="active" id="account"><strong>New order</strong></li>
                                                <li id="personal"><strong>Adding Investigaiton</strong></li>
                                                <li id="payment"><strong>Payment</strong></li>
                                                <li id="confirm"><strong>Finish</strong></li>

                                            </ul> <!-- fieldsets -->
                                            <fieldset>
                                                <div class="form-card">
                                                    <h2 class="fs-title">New Orders</h2>
                                                    <div class="justify-content-center">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="pname" class="col-form-label">Mobile No<span
                                                                        class="imp">*</span></label>
                                                                <input type="number" class=" form-control" name="number"
                                                                    placeholder="mobile" />
                                                                <label for="pname" class="col-form-label">Customer Name
                                                                    <span class="imp">*</span></label>
                                                                <input type="text" class="form-control" name="uname"
                                                                    placeholder="Name" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="number" class="col-form-label">Age<span
                                                                        class="imp">*</span></label>
                                                                <input type="text" class=" form-control" name="pwd"
                                                                    placeholder="Age" />
                                                                <label for="text" class=" col-form-label">Gender<span
                                                                        class="imp">*</span></label>
                                                                <input type="text" class="form-control" name="cpwd"
                                                                    placeholder="Gender" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="button" name="next" class="next action-button"
                                                    value="Next Step" />
                                            </fieldset>
                                            <fieldset>
                                                <div class="form-card">
                                                    <h2 class="fs-title">Adding Investigation</h2>

                                                    <div class="row form-group">
                                                        <label for="pname" class="col-form-label">Search the
                                                            investigation<span class="imp">*</span></label>
                                                        <input class="form-control" type="text" autocomplete="off"
                                                            placeholder="Search by investigation name"
                                                            id="search_investigations" onclick="search(this.value)" />
                                                    </div>
                                                    <br><br><br>
                                                    <table class="table table-hover table-bordered" id="myTable">
                                                        <thead>
                                                            <tr class="fs-title">
                                                                <th scope="col" class="text-center">S No</th>
                                                                <th scope="col" class="text-center">INVESTIGATION /
                                                                    PACKAGE</th>
                                                                <th scope="col" class="text-center">MRP</th>
                                                                <th scope="col" class="text-center">ACTION</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row" class="text-center">1</th>
                                                                <td class="text-center">125-Di Hydroxy Cholecalciferol
                                                                    (Vitamin D3)</td>
                                                                <td class="text-center">500/-</td>
                                                                <td class="text-center"><i class="fa fa-trash"
                                                                        aria-hidden="true"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row" class="text-center">2</th>
                                                                <td class="text-center">Complete Urine Analysis (CUE)
                                                                </td>
                                                                <td class="text-center">1500/-</td>
                                                                <td class="text-center"><i class="fa fa-trash"
                                                                        aria-hidden="true"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row" class="text-center">3</th>
                                                                <td class="text-center">total health checkup</td>
                                                                <td class="text-center">3580/-</td>
                                                                <td class="text-center"><i class="fa fa-trash"
                                                                        aria-hidden="true"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div>
                                                        <p class="text-bold text-right font-weight-bold">TOTAL AMOUNT 1000 &#x20B9</p>
                                                    </div>
                                                </div>
                                                <input type="button" name="previous"
                                                    class="previous action-button-previous" value="Previous" />
                                                <input type="button" name="next" class="next action-button"
                                                    value="Next Step" />
                                            </fieldset>
                                            <fieldset>
                                            <p class="text-bold text-right font-weight-bold">TOTAL AMOUNT 1000 &#x20B9</p>
                                            
                                                <input type="button" name="previous"
                                                    class="previous action-button-previous" value="Previous" />
                                                <input type="button" name="make_payment" class="next action-button"
                                                    value="Confirm" />
                                            </fieldset>
                                            <fieldset>
                                                <div class="form-card">
                                                    <h2 class="title text-center">Success !</h2> <br><br><br>
                                                    <div class="row justify-content-center">
                                                        <div class="col-3">
                                                            <i class="fas fa-check-circle fa-10x"
                                                                style="color: #10367a;"></i>
                                                            <!-- <i class=""></i> -->
                                                        </div>
                                                    </div> <br><br>
                                                    <div class="row justify-content-center">
                                                        <div class="col-7 text-center">
                                                            <h5>You Have Successfully Signed Up</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {

    var current_fs, next_fs, previous_fs; //fieldsets
    var opacity;

    $(".next").click(function() {

        current_fs = $(this).parent();
        next_fs = $(this).parent().next();

        //Add Class Active
        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

        //show the next fieldset
        next_fs.show();
        //hide the current fieldset with style
        current_fs.animate({
            opacity: 0
        }, {
            step: function(now) {
                // for making fielset appear animation
                opacity = 1 - now;

                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                next_fs.css({
                    'opacity': opacity
                });
            },
            duration: 600
        });
    });

    $(".previous").click(function() {

        current_fs = $(this).parent();
        previous_fs = $(this).parent().prev();

        //Remove class active
        $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

        //show the previous fieldset
        previous_fs.show();

        //hide the current fieldset with style
        current_fs.animate({
            opacity: 0
        }, {
            step: function(now) {
                // for making fielset appear animation
                opacity = 1 - now;

                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                previous_fs.css({
                    'opacity': opacity
                });
            },
            duration: 600
        });
    });

    $('.radio-group .radio').click(function() {
        $(this).parent().find('.radio').removeClass('selected');
        $(this).addClass('selected');
    });

    $(".submit").click(function() {
        return false;
    })

});
</script>

<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        searching: false,
        "paging": false,
        "showNEntries": false,
        "bInfo": false,
        "ordering": false
    });
});
</script>
<!-- investigation search -->
<script>
function search(search) {

    $('#search_investigations').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '<?=base_url('LabNew/Investigation_search')?>',
                type: 'POST',
                data: {
                    search: search
                },
                success: function(result) {
                    response($.parseJSON(result))
                }
            })
        },

        select: function(event, ui) {
            var inv_id = ui.item.investigation_id
            var inv_name = ui.item.investigation
            var price = $('#price').val();
            $("#inv_name").val(inv_name);
            $("#somediv").val(inv_id);
            event.preventDefault();
            $("#search_investigations").val(inv_name);
            // model(inv_id, inv_name);
            $("#price_input").show();

            console.log("this is investigation  " + inv_name + "  this is investigation id  " + inv_id);
            alert("invsetigation is selected")
        },

        create: function() {

            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {
                console.log(item)

                return $('<li>')
                    .append(
                        '<a value="' + item.investigation_id +
                        '" id="searching"><div class="inline-block srchRes w-100"><div class="row"><div class="col-md-12"><p class="m-0 p-0 font-weight-light trade_name">' +
                        item.investigation + '</div></a>')
                    .appendTo(ul);

            };

        }


    });
}
</script>