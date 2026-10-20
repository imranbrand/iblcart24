<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <!-- BRAND -->
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="fas fa-shopping-cart me-2"></i>
            IBL CART 24
        </a>


        <!-- MOBILE BUTTON -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>


        <!-- NAVBAR CONTENT -->
        <div
            class="collapse navbar-collapse"
            id="mainNavbar">


            <!-- LEFT MENU -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">


                <!-- HOME -->
                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="{{ url('/') }}">

                        <i class="fas fa-home me-1"></i>
                        Home

                    </a>

                </li>


                <!-- PRODUCTS -->
                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="{{ route('products.index') }}">

                        <i class="fas fa-box me-1"></i>
                        Products

                    </a>

                </li>


                <!-- CART -->
                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="{{ route('cart.index') }}">

                        <i class="fas fa-cart-shopping me-1"></i>
                        Cart

                    </a>

                </li>


                <!-- MY ORDERS -->
                @auth

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="{{ route('orders.index') }}">

                            <i class="fas fa-receipt me-1"></i>
                            My Orders

                        </a>

                    </li>

                @endauth


            </ul>


            <!-- RIGHT MENU -->
            <ul class="navbar-nav">


                <!-- ================================================= -->
                <!-- LOGGED IN USER -->
                <!-- ================================================= -->

                @auth

                    @php
                        $isAdmin = Auth::user()->isAdmin();
                    @endphp


                    <!-- USER DROPDOWN -->
                    <li class="nav-item dropdown">


                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fas fa-user-circle me-1"></i>

                            {{ Auth::user()->name }}

                        </a>


                        <ul class="dropdown-menu dropdown-menu-end">


                            <!-- PROFILE -->
                            @if(Route::has('profile.edit'))

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="{{ route('profile.edit') }}">

                                        <i class="fas fa-user me-2"></i>

                                        Profile

                                    </a>

                                </li>

                            @endif


                            <!-- ADMIN DASHBOARD -->
                            @if($isAdmin && Route::has('admin.dashboard'))

                                <li>

                                    <a
                                        class="dropdown-item fw-bold text-primary"
                                        href="{{ route('admin.dashboard') }}">

                                        <i class="fas fa-chart-line me-2"></i>

                                        Admin Dashboard

                                    </a>

                                </li>

                            @endif


                            <!-- DIVIDER -->
                            <li>

                                <hr class="dropdown-divider">

                            </li>


                            <!-- LOGOUT -->
                            <li>

                                <form
                                    method="POST"
                                    action="{{ route('logout') }}">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="dropdown-item text-danger">

                                        <i class="fas fa-sign-out-alt me-2"></i>

                                        Logout

                                    </button>

                                </form>

                            </li>


                        </ul>

                    </li>


                <!-- ================================================= -->
                <!-- GUEST USER -->
                <!-- ================================================= -->

                @else


                    <!-- LOGIN -->
                    @if(Route::has('login'))

                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="{{ route('login') }}">

                                <i class="fas fa-sign-in-alt me-1"></i>

                                Login

                            </a>

                        </li>

                    @endif


                    <!-- REGISTER -->
                    @if(Route::has('register'))

                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="{{ route('register') }}">

                                <i class="fas fa-user-plus me-1"></i>

                                Register

                            </a>

                        </li>

                    @endif


                @endauth


            </ul>


        </div>

    </div>
</nav>

