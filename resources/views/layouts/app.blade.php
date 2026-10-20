<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Admin Dashboard') - IBL CART 24
    </title>


    <!-- ============================= -->
    <!-- BOOTSTRAP 5 -->
    <!-- ============================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ============================= -->
    <!-- FONT AWESOME -->
    <!-- ============================= -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >


    <!-- ============================= -->
    <!-- ADMIN GLOBAL CSS -->
    <!-- ============================= -->

    <style>

        * {
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            margin: 0;
            padding: 0;

            background: #f5f7fb;

            color: #111827;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

        }


        /* ============================= */
        /* MAIN CONTENT */
        /* ============================= */

        .admin-content {

            margin-left: 260px;

            min-height: 100vh;

            transition:
                margin-left .3s ease;

        }


        /* ============================= */
        /* TOPBAR */
        /* ============================= */

        .admin-topbar {

            height: 70px;

            background: #ffffff;

            border-bottom:
                1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 25px;

            position: sticky;

            top: 0;

            z-index: 1000;

        }


        /* ============================= */
        /* MOBILE MENU */
        /* ============================= */

        .mobile-menu-btn {

            display: none;

            border: none;

            background: #111827;

            color: #ffffff;

            width: 42px;

            height: 42px;

            border-radius: 10px;

            align-items: center;

            justify-content: center;

            cursor: pointer;

            transition: all .2s ease;

        }


        .mobile-menu-btn:hover {

            background: #374151;

        }


        /* ============================= */
        /* PAGE CONTENT */
        /* ============================= */

        .admin-page-content {

            padding: 25px;

            min-height:
                calc(100vh - 140px);

        }


        /* ============================= */
        /* TOPBAR TITLE */
        /* ============================= */

        .topbar-title {

            font-size: 18px;

            font-weight: 700;

            color: #111827;

            line-height: 1.2;

        }


        .topbar-subtitle {

            font-size: 12px;

            color: #6b7280;

            margin-top: 3px;

        }


        /* ============================= */
        /* USER AREA */
        /* ============================= */

        .topbar-user {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        /* ============================= */
        /* AVATAR */
        /* ============================= */

        .topbar-avatar {

            width: 40px;

            height: 40px;

            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    #6366f1,
                    #8b5cf6
                );

            color: #ffffff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 700;

            font-size: 16px;

            box-shadow:
                0 4px 12px
                rgba(99,102,241,.25);

        }


        /* ============================= */
        /* LOGOUT BUTTON */
        /* ============================= */

        .logout-btn {

            border:
                1px solid #dc2626;

            background: #ffffff;

            color: #dc2626;

            padding:
                8px 13px;

            border-radius: 8px;

            font-size: 13px;

            font-weight: 600;

            transition:
                all .2s ease;

            cursor: pointer;

        }


        .logout-btn:hover {

            background: #dc2626;

            color: #ffffff;

        }


        /* ============================= */
        /* ALERT */
        /* ============================= */

        .admin-page-content
        .alert {

            border-radius: 12px;

            border-width: 1px;

        }


        /* ============================= */
        /* FOOTER */
        /* ============================= */

        .admin-footer {

            margin-left: 260px;

            background: #ffffff;

            border-top:
                1px solid #e5e7eb;

            padding: 20px;

            text-align: center;

            transition:
                margin-left .3s ease;

        }


        .admin-footer .fw-bold {

            color: #111827;

            font-size: 15px;

        }


        .admin-footer .text-muted {

            color: #6b7280 !important;

        }


        /* ============================= */
        /* LINKS */
        /* ============================= */

        a {

            text-decoration: none;

        }


        /* ============================= */
        /* BUTTON */
        /* ============================= */

        button {

            font-family:
                inherit;

        }


        /* ============================= */
        /* TABLE RESPONSIVE */
        /* ============================= */

        .table-responsive {

            border-radius: 12px;

        }


        /* ============================= */
        /* MOBILE */
        /* ============================= */

        @media (max-width: 991.98px) {

            .admin-content {

                margin-left: 0;

            }


            .admin-footer {

                margin-left: 0;

            }


            .mobile-menu-btn {

                display: inline-flex;

            }


            .admin-topbar {

                padding:
                    0 15px;

            }


            .admin-page-content {

                padding: 15px;

            }


            .logout-text {

                display: none;

            }


            .logout-btn {

                width: 40px;

                height: 40px;

                padding: 0;

                display: flex;

                align-items: center;

                justify-content: center;

            }

        }


        /* ============================= */
        /* SMALL MOBILE */
        /* ============================= */

        @media (max-width: 575.98px) {

            .admin-topbar {

                height: 64px;

            }


            .topbar-title {

                font-size: 15px;

            }


            .topbar-subtitle {

                display: none;

            }


            .topbar-avatar {

                width: 36px;

                height: 36px;

                font-size: 14px;

            }


            .mobile-menu-btn {

                width: 38px;

                height: 38px;

            }

        }

    </style>


    @stack('styles')

</head>


<body>


    <!-- ================================================= -->
    <!-- ADMIN SIDEBAR -->
    <!-- ================================================= -->



    <!-- ================================================= -->
    <!-- MAIN ADMIN CONTENT -->
    <!-- ================================================= -->

    <div class="admin-content">


        <!-- ================================================= -->
        <!-- TOP NAVBAR -->
        <!-- ================================================= -->

        <header class="admin-topbar">


            <!-- LEFT SIDE -->

            <div class="d-flex align-items-center gap-3">


                <!-- MOBILE MENU -->

                <button
                    type="button"
                    class="mobile-menu-btn"
                    onclick="toggleAdminSidebar()"
                    aria-label="Open menu"
                >

                    <i class="fa-solid fa-bars"></i>

                </button>


                <!-- PAGE TITLE -->

                <div>

                    <div class="topbar-title">

                        @yield(
                            'page-title',
                            'Admin Panel'
                        )

                    </div>


                    <div class="topbar-subtitle">

                        IBL CART 24 Management System

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- RIGHT USER AREA -->
            <!-- ================================================= -->

            <div class="topbar-user">


                <!-- USER NAME -->

                <div class="d-none d-md-block text-end">

                    <div class="fw-bold small">

                        {{ auth()->user()->name ?? 'Admin' }}

                    </div>


                    <div
                        class="text-muted"
                        style="font-size: 11px;"
                    >

                        Administrator

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- AVATAR -->
                <!-- ================================================= -->

                <div class="topbar-avatar">

                    {{
                        strtoupper(
                            substr(
                                auth()->user()->name ?? 'A',
                                0,
                                1
                            )
                        )
                    }}

                </div>


                <!-- ================================================= -->
                <!-- LOGOUT -->
                <!-- ================================================= -->

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="m-0"
                >

                    @csrf

                    <button
                        type="submit"
                        class="logout-btn"
                        title="Logout"
                    >

                        <i
                            class="fa-solid fa-right-from-bracket me-md-1"
                        ></i>


                        <span class="logout-text">

                            Logout

                        </span>

                    </button>

                </form>


            </div>


        </header>


        <!-- ================================================= -->
        <!-- PAGE CONTENT -->
        <!-- ================================================= -->

        <main class="admin-page-content">


            <!-- ================================================= -->
            <!-- SUCCESS MESSAGE -->
            <!-- ================================================= -->

            @if(session('success'))

                <div
                    class="alert alert-success alert-dismissible fade show shadow-sm"
                    role="alert"
                >

                    <i
                        class="fa-solid fa-circle-check me-2"
                    ></i>


                    {{ session('success') }}


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- ERROR MESSAGE -->
            <!-- ================================================= -->

            @if(session('error'))

                <div
                    class="alert alert-danger alert-dismissible fade show shadow-sm"
                    role="alert"
                >

                    <i
                        class="fa-solid fa-circle-exclamation me-2"
                    ></i>


                    {{ session('error') }}


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- VALIDATION ERRORS -->
            <!-- ================================================= -->

            @if($errors->any())

                <div
                    class="alert alert-danger alert-dismissible fade show shadow-sm"
                    role="alert"
                >

                    <strong>

                        <i
                            class="fa-solid fa-triangle-exclamation me-2"
                        ></i>

                        Please fix the following errors:

                    </strong>


                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)

                            <li>

                                {{ $error }}

                            </li>

                        @endforeach

                    </ul>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- ACTUAL PAGE -->
            <!-- ================================================= -->

            @yield('content')


        </main>


    </div>


    <!-- ================================================= -->
    <!-- FOOTER -->
    <!-- ================================================= -->

    <footer class="admin-footer">


        <div class="fw-bold">

            IBL CART 24

        </div>


        <div class="text-muted small mt-1">

            &copy; {{ date('Y') }}

            IBL CART 24.

            All Rights Reserved.

        </div>


    </footer>


    <!-- ================================================= -->
    <!-- BOOTSTRAP JS -->
    <!-- ================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    ></script>


    <!-- ================================================= -->
    <!-- SIDEBAR SCRIPT -->
    <!-- ================================================= -->

    <script>

        function toggleAdminSidebar() {

            const sidebar =
                document.querySelector('.admin-sidebar');

            if (!sidebar) {
                return;
            }

            sidebar.classList.toggle('show');

        }


        /*
         * Close sidebar when clicking outside
         * on mobile.
         */

        document.addEventListener(
            'click',
            function(event) {

                const sidebar =
                    document.querySelector('.admin-sidebar');

                const button =
                    document.querySelector('.mobile-menu-btn');

                if (!sidebar || !button) {
                    return;
                }


                if (
                    window.innerWidth <= 991 &&
                    sidebar.classList.contains('show') &&
                    !sidebar.contains(event.target) &&
                    !button.contains(event.target)
                ) {

                    sidebar.classList.remove('show');

                }

            }
        );


        /*
         * Close sidebar automatically
         * when resizing to desktop.
         */

        window.addEventListener(
            'resize',
            function() {

                const sidebar =
                    document.querySelector('.admin-sidebar');

                if (!sidebar) {
                    return;
                }


                if (window.innerWidth > 991) {

                    sidebar.classList.remove('show');

                }

            }
        );

    </script>


    @stack('scripts')


</body>

</html>
