<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Qtasnim Coding Test</title>
    <link rel="stylesheet" href="{{ url('vendor/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendor/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ url('vendor/css/sweetalert2.min.css') }}">
</head>
<body>
    <div class="d-flex flex-row" style="min-height: 100vh">
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none text-dark" style="padding-left: 16px">
                <span class="fs-4">Qtasnim</span>
            </a>
            <hr>
            <nav class="nav nav-pills flex-column mb-auto">
                <a class="nav-link {{ request()->route()->getName() == 'summary' ? 'active' : 'text-dark' }}" href="{{ url('sales-summary') }}">Rangkuman</a>
                <a class="nav-link {{ request()->route()->getName() == 'transaction' ? 'active' : 'text-dark' }}" href="{{ url('transaction') }}">Transaksi</a>
                <a class="nav-link {{ request()->route()->getName() == 'product' ? 'active' : 'text-dark' }}" href="{{ url('product') }}">Produk</a>
                <a class="nav-link {{ request()->route()->getName() == 'category' ? 'active' : 'text-dark' }}" href="{{ url('product-category') }}">Kategori</a>
            </nav>
        </div>
        <div class="container p-5">
            <h4 class="mb-4">@yield('title')</h4>
            @yield('content')
        </div>
    </div>

    <script src="{{ url('vendor/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('vendor/js/jquery.min.js') }}"></script>
    <script src="{{ url('vendor/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('vendor/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ url('vendor/js/sweetalert2.all.min.js') }}"></script>
    <script>
        const baseUrl = "{{ url('/') }}";

        function getAuthCode() {
            return new Promise(async (resolve, reject) => {
                let authCode = getLocalStorageWithExpiry('qtasnim-coding-test-auth-code');
                if (true) {
                    const res = await $.ajax({
                        async: true,
                        url: baseUrl+'/api/get-auth-code',
                        type: 'get'
                    });
                    authCode = res.code;
                    setLocalStorageWithExpiry('qtasnim-coding-test-auth-code', authCode, 4*60*60*1000);
                }
                resolve(authCode);
            });
        }

        /**
         * Local Storage Helper
         */

        function setLocalStorageWithExpiry(key, value, ttl) {
            const now = new Date()

            // `item` is an object which contains the original value
            // as well as the time when it's supposed to expire
            const item = {
                value: value,
                expiry: now.getTime() + ttl,
            }
            localStorage.setItem(key, JSON.stringify(item))
        }

        function getLocalStorageWithExpiry(key) {
            const itemStr = localStorage.getItem(key)

            // if the item doesn't exist, return null
            if (!itemStr) {
                return null
            }

            const item = JSON.parse(itemStr)
            const now = new Date()

            // compare the expiry time of the item with the current time
            if (now.getTime() > item.expiry) {
                // If the item is expired, delete the item from storage
                // and return null
                localStorage.removeItem(key)
                return null
            }
            return item.value
        }

        function getFormObj(formId) {
            const formObj = {};
            const inputs = $('#'+formId).serializeArray();
            $.each(inputs, function (i, input) {
                formObj[input.name] = input.value;
            });
            return formObj;
        }

        function formValidationFailHandler(jqxhr, scopeElement = null) {
            let response = jqxhr.responseJSON

            /**
             * Display error message at bottom of input element
             */
            for(let i in response.errors){
                let el = undefined;
                if(scopeElement == null){
                    if (i.split('.')[1]) {
                        // field is array
                        const [fieldKey, arrIdx] = i.split('.');
                        el = $(`[name='${fieldKey}[]']`).eq(arrIdx);
                    } else {
                        // field is not array
                        el = $(`[name='${i}']`);
                    }
                } else {
                    el = $(`${scopeElement} [name='${i}']`)
                }
                // if element not found
                if( el.length == 0 ){
                    Swal.fire({
                        icon: 'error',
                        title: response.errors[i][0],
                    });
                } else {
                    el.parent('div').append(`
                        <span class="form-alert text-danger">${this.capitalizeFirstLetter(response.errors[i][0])}</span>
                    `);
                }

            }
        }
    </script>
    @yield('scripts')
</body>
</html>
