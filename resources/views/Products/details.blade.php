
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <input type="hidden" name="csrf" value="{{ csrf_token() }}" id="csrf">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .alert.parsley {
            margin-top: 5px;
            margin-bottom: 0px;
            padding: 10px 15px 10px 15px;
        }
        .check .alert {
            margin-top: 20px;
        }
        .credit-card-box .panel-title {
            display: inline;
            font-weight: bold;
        }
        .credit-card-box .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 100%;
        }
        .credit-card-box .display-tr {
            display: table-row;
        }
        #success_msg_new{
            text-align: center;font-size: 18px;color: green
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

</head>
<body id="app-layout">

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default credit-card-box">
            <div class="panel-heading display-table" >
                <div class="row display-tr" >
                    <strong>Product Details</strong>
                </div>
            </div>
            <div class="panel-body">
                    <div class="col-lg-12 mx-auto">
                        <!-- List group-->
                        <div class="row align-items-start align-items-start">
                            <!-- list group item-->

                                <div class="col-12">
                                    <div class="media-body order-2 order-lg-1">
                                        <h5 class="mt-0 font-weight-bold mb-2">{{$details->name}}</h5>
                                        <p class="font-italic text-muted mb-0 small">{{$details->description}}</p>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <h6 class="font-weight-bold my-2">₹{{$details->price}}</h6>
                                        </div>

                                    </div>
                                </div>
                        </div><!-- End -->
                    </div>
                <input type="hidden" name="prod_id" value="{{ $details->id }}" id="prod_id">


            </div>
        </div>
        <p id="success_msg_new"></p>
        <input id="card-holder-name" type="text">

        <!-- Stripe Elements Placeholder -->
        <div id="card-element"></div>

        <button id="card-button">
            Process Payment
        </button>

    </div>
</div>

<script>
    window.ParsleyConfig = {
        errorsWrapper: '<div></div>',
        errorTemplate: '<div class="alert alert-danger parsley" role="alert"></div>',
        errorClass: 'has-error',
        successClass: 'has-success'
    };
</script>

<script src="http://parsleyjs.org/dist/parsley.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
    Stripe.setPublishableKey('{{ env("STRIPE_KEY") }}');
    jQuery(function($) {
        $('#payment-form').submit(function(event) {
            event.preventDefault();
            alert();
            var $form = $(this);
            $form.parsley().subscribe('parsley:form:validate', function(formInstance) {
                formInstance.submitEvent.preventDefault();
                alert();
                return false;
            });
            $form.find('#submitBtn').prop('disabled', true);
            alert();
            Stripe.card.createToken($form, stripeResponseHandler);
            return false;
        });
    });
    function stripeResponseHandler(status, response) {
        var $form = $('#payment-form');
        if (response.error) {
            $form.find('.payment-errors').text(response.error.message);
            $form.find('.payment-errors').addClass('alert alert-danger');
            $form.find('#submitBtn').prop('disabled', false);
            $('#submitBtn').button('reset');
        } else {
            var token = response.id;
            $form.append($('<input type="hidden" name="stripeToken" />').val(token));
            $form.get(0).submit();
        }
    };
</script>



<script src="https://js.stripe.com/v3/"></script>

<script>
    const stripe = Stripe('{{ env("STRIPE_KEY") }}');

    const elements = stripe.elements();
    const cardElement = elements.create('card');

    cardElement.mount('#card-element');


    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');

    cardButton.addEventListener('click', async (e) => {
        const { paymentMethod, error } = await stripe.createPaymentMethod(
            'card', cardElement, {
                billing_details: { name: cardHolderName.value }
            }
        );

        if (error) {
            // Display "error.message" to the user...
        } else {

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "/purchase");

            xhr.setRequestHeader("Accept", "application/json");
            xhr.setRequestHeader("Content-Type", "application/json");

            let data = `{
              "_token": $("#csrf").val(),
              "data": paymentMethod,
            }`;

            //xhr.onload = () => console.log(xhr.responseText);
            //xhr.send(data);

            $.ajax({
                type:'POST',
                url:'/purchase',
                data: {_token: $('#csrf').val(), my_data: JSON.stringify(paymentMethod), product_id: $('#prod_id').val()},
                success: function (msg) {
                   $('#success_msg_new').html('Completed');
                }
            });
        }
    });
</script>
</body>
</html>
