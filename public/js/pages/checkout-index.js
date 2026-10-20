document.addEventListener('DOMContentLoaded', function () {

    const paymentRadios =
        document.querySelectorAll('.payment-radio');

    const paymentOptions =
        document.querySelectorAll('.payment-option');

    const bkashBox =
        document.getElementById('bkashPaymentBox');

    const nagadBox =
        document.getElementById('nagadPaymentBox');

    const onlineBox =
        document.getElementById('onlinePaymentBox');

    const bkashInput =
        document.getElementById('bkash_transaction_id');

    const nagadInput =
        document.getElementById('nagad_transaction_id');

    const hiddenTransactionInput =
        document.getElementById('payment_transaction_id');

    const paymentWarning =
        document.getElementById('paymentWarning');

    const form =
        document.getElementById('checkoutForm');

    const button =
        document.getElementById('placeOrderBtn');


    /*
    |--------------------------------------------------------------------------
    | Get Payment Key
    |--------------------------------------------------------------------------
    */

    function getPaymentKey(value) {

        if (value === 'Cash on Delivery') {
            return 'cod';
        }

        if (value === 'bKash') {
            return 'bkash';
        }

        if (value === 'Nagad') {
            return 'nagad';
        }

        if (value === 'Online Payment') {
            return 'online';
        }

        return '';
    }


    /*
    |--------------------------------------------------------------------------
    | Update Payment UI
    |--------------------------------------------------------------------------
    */

    function updatePaymentUI() {

        const selected =
            document.querySelector(
                'input[name="payment_method"]:checked'
            );


        paymentOptions.forEach(function (option) {

            option.classList.remove('active');

        });


        bkashBox.classList.remove('show');
        nagadBox.classList.remove('show');
        onlineBox.classList.remove('show');


        if (!selected) {
            return;
        }


        const selectedOption =
            document.querySelector(
                '[data-payment-option="' +
                getPaymentKey(selected.value) +
                '"]'
            );


        if (selectedOption) {

            selectedOption.classList.add('active');

        }


        if (selected.value === 'bKash') {

            bkashBox.classList.add('show');

        }

        else if (selected.value === 'Nagad') {

            nagadBox.classList.add('show');

        }

        else if (selected.value === 'Online Payment') {

            onlineBox.classList.add('show');

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Sync Transaction ID
    |--------------------------------------------------------------------------
    */

    function syncTransactionId() {

        const selected =
            document.querySelector(
                'input[name="payment_method"]:checked'
            );


        if (!selected) {

            hiddenTransactionInput.value = '';

            return;

        }


        if (selected.value === 'bKash') {

            hiddenTransactionInput.value =
                bkashInput
                    ? bkashInput.value.trim()
                    : '';

        }

        else if (selected.value === 'Nagad') {

            hiddenTransactionInput.value =
                nagadInput
                    ? nagadInput.value.trim()
                    : '';

        }

        else {

            hiddenTransactionInput.value = '';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Payment Radio Change
    |--------------------------------------------------------------------------
    */

    paymentRadios.forEach(function (radio) {

        radio.addEventListener(
            'change',
            function () {

                paymentWarning.classList.add('d-none');

                syncTransactionId();

                updatePaymentUI();

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Payment Option Click
    |--------------------------------------------------------------------------
    */

    paymentOptions.forEach(function (option) {

        option.addEventListener(
            'click',
            function (event) {

                if (
                    event.target.tagName.toLowerCase() ===
                    'input'
                ) {

                    return;

                }


                const radio =
                    option.querySelector(
                        '.payment-radio'
                    );


                if (radio) {

                    radio.checked = true;

                    radio.dispatchEvent(
                        new Event('change')
                    );

                }

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | bKash Transaction ID
    |--------------------------------------------------------------------------
    */

    if (bkashInput) {

        bkashInput.addEventListener(
            'input',
            function () {

                const selected =
                    document.querySelector(
                        'input[name="payment_method"]:checked'
                    );


                if (
                    selected &&
                    selected.value === 'bKash'
                ) {

                    hiddenTransactionInput.value =
                        this.value.trim();

                    paymentWarning.classList.add(
                        'd-none'
                    );

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Nagad Transaction ID
    |--------------------------------------------------------------------------
    */

    if (nagadInput) {

        nagadInput.addEventListener(
            'input',
            function () {

                const selected =
                    document.querySelector(
                        'input[name="payment_method"]:checked'
                    );


                if (
                    selected &&
                    selected.value === 'Nagad'
                ) {

                    hiddenTransactionInput.value =
                        this.value.trim();

                    paymentWarning.classList.add(
                        'd-none'
                    );

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Form Submit
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                syncTransactionId();


                const selected =
                    document.querySelector(
                        'input[name="payment_method"]:checked'
                    );


                const customerName =
                    document.getElementById(
                        'customer_name'
                    );


                const phone =
                    document.getElementById(
                        'phone'
                    );


                const address =
                    document.getElementById(
                        'address'
                    );


                /*
                |--------------------------------------------------------------------------
                | Customer Name
                |--------------------------------------------------------------------------
                */

                if (
                    !customerName ||
                    customerName.value.trim() === ''
                ) {

                    event.preventDefault();

                    if (customerName) {
                        customerName.focus();
                    }

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Phone
                |--------------------------------------------------------------------------
                */

                if (
                    !phone ||
                    phone.value.trim() === ''
                ) {

                    event.preventDefault();

                    if (phone) {
                        phone.focus();
                    }

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Address
                |--------------------------------------------------------------------------
                */

                if (
                    !address ||
                    address.value.trim() === ''
                ) {

                    event.preventDefault();

                    if (address) {
                        address.focus();
                    }

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Manual Payment Validation
                |--------------------------------------------------------------------------
                */

                if (
                    selected &&
                    (
                        selected.value === 'bKash' ||
                        selected.value === 'Nagad'
                    )
                ) {

                    const transactionId =
                        hiddenTransactionInput.value.trim();


                    if (transactionId === '') {

                        event.preventDefault();

                        paymentWarning.classList.remove(
                            'd-none'
                        );


                        if (
                            selected.value === 'bKash' &&
                            bkashInput
                        ) {

                            bkashInput.focus();

                        }


                        if (
                            selected.value === 'Nagad' &&
                            nagadInput
                        ) {

                            nagadInput.focus();

                        }

                        return;

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Submit Button Loading State
                |--------------------------------------------------------------------------
                */

                paymentWarning.classList.add(
                    'd-none'
                );


                if (button) {

                    button.disabled = true;

                    button.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin me-2"></i>' +
                        'Processing Order...';

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Initial Payment UI
    |--------------------------------------------------------------------------
    */

    updatePaymentUI();

});
