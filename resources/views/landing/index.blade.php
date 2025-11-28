<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-wide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}/" data-template="front-pages">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Mobile Shop SaaS - The Ultimate Management Solution</title>

    <meta name="description" content="Manage your mobile shop with ease. Inventory, POS, Repairs, and more in one cloud-based platform." />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />

    <!-- Page CSS -->
    <style>
        .hero-section {
            padding: 8rem 0 4rem;
            background: linear-gradient(to bottom right, #f8f9fa, #e9ecef);
            position: relative;
            overflow: hidden;
        }
        .hero-img {
            max-width: 100%;
            height: auto;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }
        .pricing-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
        }
        .navbar-landing {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Navbar: Start -->
    <nav class="layout-navbar shadow-none py-0">
        <div class="container">
            <div class="navbar navbar-expand-lg landing-navbar px-3 px-lg-4">
                <!-- Menu logo wrapper: Start -->
                <div class="navbar-brand app-brand demo d-flex py-0 me-4">
                    <button class="navbar-toggler border-0 px-0 me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti ti-menu-2 ti-sm align-middle"></i>
                    </button>
                    <a href="{{ url('/') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0" />
                                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0" />
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1">MobileShop SaaS</span>
                    </a>
                </div>
                <!-- Menu logo wrapper: End -->

                <!-- Menu wrapper: Start -->
                <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
                    <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti ti-x ti-sm"></i>
                    </button>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link fw-medium" aria-current="page" href="#landingHero">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="#landingFeatures">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="#landingPricing">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="#landingContact">Contact</a>
                        </li>
                    </ul>
                </div>
                <div class="landing-menu-overlay d-lg-none"></div>
                <!-- Menu wrapper: End -->

                <!-- Toolbar: Start -->
                <ul class="navbar-nav flex-row align-items-center ms-auto">
                    <!-- Style Switcher -->
                    <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <i class="ti ti-sm"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                                    <span class="align-middle"><i class="ti ti-sun me-2"></i>Light</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                    <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                    <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- / Style Switcher-->

                    <li>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <span class="tf-icons ti ti-login scaleX-n1-rtl me-md-1"></span>
                            <span class="d-none d-md-block">Login/Register</span>
                        </a>
                    </li>
                </ul>
                <!-- Toolbar: End -->
            </div>
        </div>
    </nav>
    <!-- Navbar: End -->

    <!-- Hero: Start -->
    <section id="landingHero" class="hero-section landing-hero position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start">
                    <h1 class="display-4 fw-bold mb-4 text-primary">Manage Your Mobile Shop <br> Like a Pro</h1>
                    <p class="lead mb-4 text-muted">
                        The all-in-one SaaS solution for mobile repair shops and retailers. 
                        Track inventory, manage repairs, process sales, and grow your business with our multi-tenant platform.
                    </p>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Get Started Free</a>
                        <a href="#landingFeatures" class="btn btn-outline-secondary btn-lg">Learn More</a>
                    </div>
                    <div class="mt-5">
                        <p class="small text-muted mb-2">Trusted by 500+ shops worldwide</p>
                        <div class="d-flex justify-content-center justify-content-lg-start gap-3 opacity-50">
                            <i class="ti ti-brand-apple fs-3"></i>
                            <i class="ti ti-brand-android fs-3"></i>
                            <i class="ti ti-brand-samsung fs-3"></i>
                            <i class="ti ti-brand-windows fs-3"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <!-- Using a generic illustration from the template assets -->
                    <img src="{{ asset('assets/img/illustrations/girl-with-laptop-light.png') }}" alt="Mobile Shop Management" class="hero-img" data-app-light-img="illustrations/girl-with-laptop-light.png" data-app-dark-img="illustrations/girl-with-laptop-dark.png">
                </div>
            </div>
        </div>
    </section>
    <!-- Hero: End -->

    <!-- Features: Start -->
    <section id="landingFeatures" class="section-py landing-features bg-body">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-label-primary">Features</span>
                <h2 class="h3 mt-2">Everything You Need to Run Your Shop</h2>
                <p class="text-muted">Powerful tools designed specifically for the mobile repair and retail industry.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-label-primary mx-auto">
                                <i class="ti ti-device-mobile"></i>
                            </div>
                            <h5 class="card-title">Repair Tracking</h5>
                            <p class="card-text text-muted">Track repair jobs from intake to completion. Keep customers updated with automated SMS and email notifications.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-label-success mx-auto">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                            <h5 class="card-title">Smart POS</h5>
                            <p class="card-text text-muted">Fast and easy Point of Sale system. Manage sales, returns, and exchanges with barcode scanning support.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-label-warning mx-auto">
                                <i class="ti ti-box"></i>
                            </div>
                            <h5 class="card-title">Inventory Management</h5>
                            <p class="card-text text-muted">Real-time stock tracking. Low stock alerts, supplier management, and automated reordering.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-label-info mx-auto">
                                <i class="ti ti-users"></i>
                            </div>
                            <h5 class="card-title">CRM & Loyalty</h5>
                            <p class="card-text text-muted">Build customer relationships. Track purchase history and reward loyal customers with points.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-label-danger mx-auto">
                                <i class="ti ti-chart-bar"></i>
                            </div>
                            <h5 class="card-title">Advanced Reporting</h5>
                            <p class="card-text text-muted">Gain insights into your business. Sales reports, profit/loss analysis, and employee performance tracking.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-label-secondary mx-auto">
                                <i class="ti ti-cloud"></i>
                            </div>
                            <h5 class="card-title">Multi-Tenant Cloud</h5>
                            <p class="card-text text-muted">Secure cloud hosting. Access your shop data from anywhere, anytime. Safe, reliable, and backed up.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Features: End -->

    <!-- Pricing: Start -->
    <section id="landingPricing" class="section-py landing-pricing">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-label-primary">Pricing Plans</span>
                <h2 class="h3 mt-2">Choose the Right Plan for You</h2>
                <p class="text-muted">Flexible pricing options to scale with your business needs.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <!-- Starter -->
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h4 class="mb-0">Starter</h4>
                                <p class="text-muted">For small repair shops</p>
                                <div class="d-flex justify-content-center align-items-center">
                                    <sup class="h5 pricing-currency mt-3 mb-0 me-1">$</sup>
                                    <h1 class="display-3 mb-0 text-primary">29</h1>
                                    <sub class="h5 pricing-duration mt-auto mb-2">/mo</sub>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> 1 User Account</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Up to 100 Repairs/mo</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Basic Inventory</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Email Support</li>
                                <li class="mb-2 text-muted"><i class="ti ti-x me-2"></i> Multi-store Support</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                <!-- Pro -->
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-primary border-2 shadow">
                        <div class="card-header bg-transparent border-0 text-center pt-4">
                            <span class="badge bg-primary">Most Popular</span>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h4 class="mb-0">Professional</h4>
                                <p class="text-muted">For growing businesses</p>
                                <div class="d-flex justify-content-center align-items-center">
                                    <sup class="h5 pricing-currency mt-3 mb-0 me-1">$</sup>
                                    <h1 class="display-3 mb-0 text-primary">79</h1>
                                    <sub class="h5 pricing-duration mt-auto mb-2">/mo</sub>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> 5 User Accounts</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Unlimited Repairs</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Advanced Inventory</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Priority Support</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> SMS Notifications</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                <!-- Enterprise -->
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h4 class="mb-0">Enterprise</h4>
                                <p class="text-muted">For large chains</p>
                                <div class="d-flex justify-content-center align-items-center">
                                    <sup class="h5 pricing-currency mt-3 mb-0 me-1">$</sup>
                                    <h1 class="display-3 mb-0 text-primary">199</h1>
                                    <sub class="h5 pricing-duration mt-auto mb-2">/mo</sub>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Unlimited Users</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Multi-store Management</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> API Access</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Dedicated Manager</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> White Labeling</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Contact Sales</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Pricing: End -->

    <!-- Footer: Start -->
    <footer class="landing-footer bg-body footer-text">
        <div class="footer-top section-py">
            <div class="container">
                <div class="row gy-5">
                    <div class="col-lg-5">
                        <a href="{{ url('/') }}" class="app-brand-link mb-4">
                            <span class="app-brand-logo demo">
                                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0" />
                                </svg>
                            </span>
                            <span class="app-brand-text demo footer-link fw-bold ms-2 ps-1">MobileShop SaaS</span>
                        </a>
                        <p class="footer-text footer-logo-description mb-4">
                            The #1 choice for mobile repair shops and retailers. <br> Simplify your operations and grow your business.
                        </p>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <h6 class="footer-title mb-3">Product</h6>
                        <ul class="list-unstyled">
                            <li class="mb-3"><a href="#landingFeatures" class="footer-link">Features</a></li>
                            <li class="mb-3"><a href="#landingPricing" class="footer-link">Pricing</a></li>
                            <li class="mb-3"><a href="javascript:void(0);" class="footer-link">API</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <h6 class="footer-title mb-3">Company</h6>
                        <ul class="list-unstyled">
                            <li class="mb-3"><a href="javascript:void(0);" class="footer-link">About Us</a></li>
                            <li class="mb-3"><a href="javascript:void(0);" class="footer-link">Careers</a></li>
                            <li class="mb-3"><a href="javascript:void(0);" class="footer-link">Contact</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <h6 class="footer-title mb-3">Support</h6>
                        <ul class="list-unstyled">
                            <li class="mb-3"><a href="javascript:void(0);" class="footer-link">Help Center</a></li>
                            <li class="mb-3"><a href="javascript:void(0);" class="footer-link">Terms of Service</a></li>
                            <li class="mb-3"><a href="javascript:void(0);" class="footer-link">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom py-3">
            <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
                <div class="mb-2 mb-md-0">
                    <span class="footer-text">© <script>document.write(new Date().getFullYear())</script> MobileShop SaaS. All rights reserved.</span>
                </div>
                <div>
                    <a href="javascript:void(0)" class="footer-link me-3" target="_blank"><i class="ti ti-brand-facebook ti-sm"></i></a>
                    <a href="javascript:void(0)" class="footer-link me-3" target="_blank"><i class="ti ti-brand-twitter ti-sm"></i></a>
                    <a href="javascript:void(0)" class="footer-link me-3" target="_blank"><i class="ti ti-brand-instagram ti-sm"></i></a>
                    <a href="javascript:void(0)" class="footer-link" target="_blank"><i class="ti ti-brand-linkedin ti-sm"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer: End -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    
    <!-- Main JS -->
    <script src="{{ asset('assets/js/front-main.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
