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
            padding: 10rem 0 6rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            color: white;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="50" r="2" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero-img {
            max-width: 100%;
            height: auto;
            animation: float 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1));
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .feature-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .pricing-card {
            transition: all 0.3s ease;
            border-radius: 16px;
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .navbar-landing {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .stats-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 4rem 0;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .testimonial-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 1rem;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .section-py {
            padding: 5rem 0;
        }
        .feature-card {
            transition: all 0.3s ease;
            border-radius: 16px;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
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
                <div class="col-lg-6 text-center text-lg-start hero-content">
                    <div class="mb-4">
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 mb-3">🚀 #1 Mobile Shop Management Platform</span>
                    </div>
                    <h1 class="display-3 fw-bold mb-4">Transform Your Mobile Repair Business</h1>
                    <p class="lead mb-5 fs-5">
                        Streamline operations, boost revenue, and delight customers with our all-in-one SaaS platform.
                        From repair tracking to smart POS, we've got everything covered.
                    </p>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3 mb-4">
                        <a href="{{ route('login') }}" class="btn btn-gradient btn-lg px-4 py-3">Start Free Trial</a>
                        <a href="#landingFeatures" class="btn btn-outline-light btn-lg px-4 py-3">Watch Demo</a>
                    </div>
                    <div class="row text-center text-lg-start">
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="stat-number">500+</div>
                            <small class="text-white-50">Happy Shops</small>
                        </div>
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="stat-number">50K+</div>
                            <small class="text-white-50">Repairs Managed</small>
                        </div>
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="stat-number">99.9%</div>
                            <small class="text-white-50">Uptime</small>
                        </div>
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="stat-number">24/7</div>
                            <small class="text-white-50">Support</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="{{ asset('assets/img/illustrations/girl-with-laptop-light.png') }}" alt="Mobile Shop Management Dashboard" class="hero-img" data-app-light-img="illustrations/girl-with-laptop-light.png" data-app-dark-img="illustrations/girl-with-laptop-dark.png">
                </div>
            </div>
        </div>
    </section>
    <!-- Hero: End -->

    <!-- Stats: Start -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-number">500+</div>
                    <p class="mb-0">Active Shops</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-number">50K+</div>
                    <p class="mb-0">Repairs Completed</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-number">98%</div>
                    <p class="mb-0">Customer Satisfaction</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-number">$2M+</div>
                    <p class="mb-0">Revenue Generated</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Stats: End -->

    <!-- Features: Start -->
    <section id="landingFeatures" class="section-py landing-features bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-primary">✨ Powerful Features</span>
                <h2 class="display-5 fw-bold mt-3 mb-3">Everything You Need to Succeed</h2>
                <p class="lead text-muted">Transform your mobile repair business with our comprehensive suite of tools designed for modern shop owners.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary bg-gradient text-white mx-auto">
                                <i class="ti ti-device-mobile"></i>
                            </div>
                            <h5 class="card-title fw-bold">Smart Repair Tracking</h5>
                            <p class="card-text text-muted">Complete repair lifecycle management with automated customer notifications, status updates, and detailed work logs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-success bg-gradient text-white mx-auto">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                            <h5 class="card-title fw-bold">Lightning-Fast POS</h5>
                            <p class="card-text text-muted">Streamlined checkout process with barcode scanning, inventory sync, and seamless payment processing.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-warning bg-gradient text-white mx-auto">
                                <i class="ti ti-box"></i>
                            </div>
                            <h5 class="card-title fw-bold">Intelligent Inventory</h5>
                            <p class="card-text text-muted">Real-time stock management with low-stock alerts, supplier integration, and automated reorder suggestions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-info bg-gradient text-white mx-auto">
                                <i class="ti ti-users"></i>
                            </div>
                            <h5 class="card-title fw-bold">Customer Relationship Management</h5>
                            <p class="card-text text-muted">Build lasting customer relationships with purchase history tracking, loyalty programs, and personalized marketing.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-danger bg-gradient text-white mx-auto">
                                <i class="ti ti-chart-bar"></i>
                            </div>
                            <h5 class="card-title fw-bold">Business Intelligence</h5>
                            <p class="card-text text-muted">Comprehensive analytics dashboard with profit/loss reports, performance metrics, and actionable insights.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-secondary bg-gradient text-white mx-auto">
                                <i class="ti ti-cloud"></i>
                            </div>
                            <h5 class="card-title fw-bold">Cloud-Powered Platform</h5>
                            <p class="card-text text-muted">Secure, scalable cloud infrastructure with 99.9% uptime, automatic backups, and multi-device synchronization.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Features: End -->

    <!-- Testimonials: Start -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Trusted by Shop Owners Worldwide</h2>
                <p class="lead text-muted">See what our customers say about transforming their businesses</p>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-primary">RS</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Rahul Sharma</h6>
                                <small class="text-muted">MobileFix Delhi</small>
                            </div>
                        </div>
                        <p class="mb-0">"MobileShop SaaS has revolutionized our repair shop. We've increased efficiency by 40% and customer satisfaction is at an all-time high. The automated notifications keep our customers informed throughout the repair process."</p>
                        <div class="mt-3">
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-success">AK</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Anjali Kumar</h6>
                                <small class="text-muted">TechHub Mumbai</small>
                            </div>
                        </div>
                        <p class="mb-0">"The inventory management system is a game-changer. We never run out of stock anymore, and the POS integration makes checkout lightning fast. Our revenue has grown 35% since implementing MobileShop SaaS."</p>
                        <div class="mt-3">
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                <span class="avatar-initial rounded-circle bg-info">VP</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Vikram Patel</h6>
                                <small class="text-muted">GadgetPro Bangalore</small>
                            </div>
                        </div>
                        <p class="mb-0">"Outstanding customer support and intuitive interface. The reporting features help us make data-driven decisions. MobileShop SaaS has helped us scale from 1 to 3 locations seamlessly."</p>
                        <div class="mt-3">
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                            <i class="ti ti-star-filled text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials: End -->

    <!-- Pricing: Start -->
    <section id="landingPricing" class="section-py landing-pricing bg-body">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-primary">💰 Simple, Transparent Pricing</span>
                <h2 class="display-5 fw-bold mt-3 mb-3">Choose Your Growth Plan</h2>
                <p class="lead text-muted">Start free, upgrade as you grow. No hidden fees, no long-term contracts.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <!-- Starter -->
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h4 class="mb-2 fw-bold">Starter</h4>
                                <p class="text-muted mb-3">Perfect for small shops</p>
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <sup class="h6 pricing-currency mt-2 me-1 text-muted">$</sup>
                                    <h1 class="display-4 mb-0 text-primary fw-bold">29</h1>
                                    <sub class="h6 pricing-duration mt-auto text-muted">/month</sub>
                                </div>
                                <small class="text-muted">14-day free trial</small>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> <strong>1 User Account</strong></li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Up to 100 Repairs/month</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Basic Inventory Management</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Email Support</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Mobile App Access</li>
                                <li class="mb-3 text-muted"><i class="ti ti-x text-muted me-2"></i> Multi-store Support</li>
                                <li class="mb-3 text-muted"><i class="ti ti-x text-muted me-2"></i> Advanced Reporting</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 fw-bold">Start Free Trial</a>
                        </div>
                    </div>
                </div>
                <!-- Pro -->
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-primary border-3 shadow-lg position-relative">
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-primary py-2 px-3">Most Popular</span>
                        </div>
                        <div class="card-body p-4 pt-5">
                            <div class="text-center mb-4">
                                <h4 class="mb-2 fw-bold">Professional</h4>
                                <p class="text-muted mb-3">For growing businesses</p>
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <sup class="h6 pricing-currency mt-2 me-1 text-muted">$</sup>
                                    <h1 class="display-4 mb-0 text-primary fw-bold">79</h1>
                                    <sub class="h6 pricing-duration mt-auto text-muted">/month</sub>
                                </div>
                                <small class="text-muted">Save $20/month vs monthly</small>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> <strong>5 User Accounts</strong></li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> <strong>Unlimited Repairs</strong></li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Advanced Inventory</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Priority Support</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> SMS Notifications</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Business Analytics</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> API Access</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-gradient w-100 fw-bold">Start Free Trial</a>
                        </div>
                    </div>
                </div>
                <!-- Enterprise -->
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h4 class="mb-2 fw-bold">Enterprise</h4>
                                <p class="text-muted mb-3">For large chains & franchises</p>
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <sup class="h6 pricing-currency mt-2 me-1 text-muted">$</sup>
                                    <h1 class="display-4 mb-0 text-primary fw-bold">199</h1>
                                    <sub class="h6 pricing-duration mt-auto text-muted">/month</sub>
                                </div>
                                <small class="text-muted">Custom enterprise features</small>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> <strong>Unlimited Users</strong></li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> <strong>Multi-store Management</strong></li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Advanced API Access</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Dedicated Account Manager</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> White Labeling</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> Custom Integrations</li>
                                <li class="mb-3"><i class="ti ti-check text-success me-2"></i> 24/7 Phone Support</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 fw-bold">Contact Sales</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <p class="text-muted mb-2">All plans include:</p>
                <div class="d-flex justify-content-center gap-4 flex-wrap">
                    <span class="badge bg-light text-dark px-3 py-2">✓ 14-day free trial</span>
                    <span class="badge bg-light text-dark px-3 py-2">✓ No setup fees</span>
                    <span class="badge bg-light text-dark px-3 py-2">✓ Cancel anytime</span>
                    <span class="badge bg-light text-dark px-3 py-2">✓ Secure data</span>
                </div>
            </div>
        </div>
    </section>
    <!-- Pricing: End -->

    <!-- Final CTA: Start -->
    <section class="py-5" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white;">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Transform Your Mobile Shop?</h2>
            <p class="lead mb-4">Join 500+ successful shop owners who have already upgraded their business with MobileShop SaaS</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                    <i class="ti ti-rocket me-2"></i> Start Your Free Trial
                </a>
                <a href="#landingContact" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold">
                    <i class="ti ti-phone me-2"></i> Talk to Sales
                </a>
            </div>
            <div class="mt-4">
                <small class="text-white-50">✓ No credit card required • ✓ 14-day free trial • ✓ Setup in under 5 minutes</small>
            </div>
        </div>
    </section>
    <!-- Final CTA: End -->

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
