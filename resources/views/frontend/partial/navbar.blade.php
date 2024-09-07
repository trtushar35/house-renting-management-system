<!-- Navbar Start -->
<div class="container-fluid nav-bar bg-transparent">
    <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4">
        <a href="{{ route('frontend.home') }}" class="navbar-brand d-flex align-items-center text-center">
            <div class="icon p-2 me-2">
                <img class="img-fluid" src="{{ url('frontend/') }}/img/icon-deal.png" alt="Icon"
                    style="width: 30px; height: 30px;">
            </div>
            <h1 class="m-0 text-primary">House Rent</h1>
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto">
                <a href="" class="nav-item nav-link active"></a>

                @if (auth('tenantCheck')->check())
                    <a href="{{ route('frontend.saved.list', auth('tenantCheck')->user()->id) }}" class="nav-item nav-link">Saved Property</a>
                @elseif (auth('ownerCheck')->check())
                    <a href="{{ route('frontend.saved.list', auth('ownerCheck')->user()->id) }}" class="nav-item nav-link">Saved Property</a>
                @endif

                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Property List</a>
                    <div class="dropdown-menu rounded-0 m-0">
                        <a href="{{ route('frontend.all.property') }}" class="dropdown-item">All Property List</a>
                        <a href="{{ route('frontend.dhaka.division') }}" class="dropdown-item">Dhaka Division</a>
                        <a href="{{ route('frontend.khulna.division') }}" class="dropdown-item">Khulna Division</a>
                        <a href="{{ route('frontend.mymensingh.division') }}" class="dropdown-item">Mymensingh
                            Division</a>
                        <a href="" class="dropdown-item">Rajshahi Division</a>
                    </div>
                </div>

                @if (!auth('tenantCheck')->check() && !auth('ownerCheck')->check())
                    <a href="{{ route('frontend.user.login') }}" class="nav-item nav-link">Login</a>
                    <a href="{{ route('frontend.registration') }}" id="#registration"
                        class="nav-item nav-link">Registration</a>
                    <a href="" id="" class="nav-item nav-link">Contact</a>
                @endif

                @if (auth('tenantCheck')->check())
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            Profile | {{ auth('tenantCheck')->check() ? 'Tenant' : 'Owner' }}
                        </a>
                        <div class="dropdown-menu rounded-0 m-0">
                            <a href="{{ route('frontend.profile.view', auth('tenantCheck')->user()->id) }}"
                                class="dropdown-item">Profile</a>
                            <a href="{{ route('frontend.booking.list', auth('tenantCheck')->user()->id) }}"
                                class="dropdown-item">Booking List</a>
                            <a href="" class="dropdown-item">Review</a>
                            <a href="{{ route('frontend.logout') }}" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                @endif

                @if (auth('ownerCheck')->check())
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            Profile | {{ auth('tenantCheck')->check() ? 'Tenant' : 'Owner' }}
                        </a>
                        <div class="dropdown-menu rounded-0 m-0">
                            <a href="{{ route('frontend.profile.view', auth('ownerCheck')->user()->id) }}"
                                class="dropdown-item">Profile</a>
                            <a href="{{ route('frontend.booking.list', auth('ownerCheck')->user()->id) }}"
                                class="dropdown-item">Booking List</a>
                            <a href="{{ route('frontend.applicant.list', auth('ownerCheck')->user()->id) }}"
                                class="dropdown-item">Applicant List</a>
                            <a href="{{ route('frontend.house.create') }}" class="dropdown-item">Add Property</a>
                            <a href="{{ route('frontend.house.list') }}" class="dropdown-item">Property List</a>
                            <a href="{{ route('frontend.tenant.list', auth('ownerCheck')->user()->id) }}"
                                class="dropdown-item">Tenant List</a>
                            <a href="" class="dropdown-item">Review</a>
                            <a href="{{ route('frontend.logout') }}" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </nav>
</div>
<!-- Navbar End -->
