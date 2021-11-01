<header class="header-top">
    <nav class="navbar navbar-light">
        <div class="navbar-left">
            <a href="" class="sidebar-toggle">
                <img class="svg" src="<?php echo e(assets('img/svg/bars.svg')); ?>" alt="img">
            </a>
            <a class="navbar-brand" href="#"><img class="svg dark" src="<?php echo e(assets('img/Logo_Dark.svg')); ?>" alt="">
                <img class="light" src="<?php echo e(assets('img/Logo_white.png')); ?> " alt="">
            </a>
            <form action="/" class="search-form">
                <span data-feather="search"></span>
                <input class="form-control mr-sm-2 box-shadow-none" type="search" placeholder="Search..."
                       aria-label="Search">
            </form>
        </div>
        <!-- ends: navbar-left -->
        <div class="navbar-right">
            <ul class="navbar-right__menu">
                <li class="nav-search d-none">
                    <a href="#" class="search-toggle">
                        <i class="la la-search"></i>
                        <i class="la la-times"></i>
                    </a>
                    <form action="/" class="search-form-topMenu">
                        <span class="search-icon" data-feather="search"></span>
                        <input class="form-control box-shadow-none w-100" type="search" placeholder="Search..."
                               aria-label="Search">
                    </form>
                </li>
                <li class="nav-notification">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle">
                            <span data-feather="shopping-cart" class="nav-icon"></span>
                        </a>
                        <div class="dropdown-wrapper">
                            <h2 class="dropdown-wrapper__title">Shopping Cart Items <span
                                    class="badge-circle badge-success ml-1">4</span></h2>
                            <ul>
                                <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                    <div class="nav-notification__type nav-notification__type--primary">
                                        <span data-feather="inbox"></span>
                                    </div>
                                    <div class="nav-notification__details">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">James</a>
                                            <span>sent you a message</span>
                                        </p>
                                        <p>
                                            <span class="time-posted">5 hours ago</span>
                                        </p>
                                    </div>
                                </li>

                            </ul>
                            <a href="" class="dropdown-wrapper__more">Check Out</a>
                        </div>
                    </div>
                </li>

                <!-- end cart pannel -->
                <li class="nav-message">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle">
                            <span data-feather="mail"></span></a>
                        <div class="dropdown-wrapper">
                            <h2 class="dropdown-wrapper__title">Messages <span
                                    class="badge-circle badge-success ml-1">2</span></h2>
                            <ul>
                                <li class="author-online has-new-message">
                                    <div class="user-avater">
                                        <img src="<?php echo e(assets('img/team-1.png')); ?>" alt="">
                                    </div>
                                    <div class="user-message">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">Web Design</a>
                                            <span class="time-posted">3 hrs ago</span>
                                        </p>
                                        <p>
                                            <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum dolor amet cosec Lorem ipsum</span>
                                            <span class="msg-count badge-circle badge-success badge-sm">1</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="author-offline has-new-message">
                                    <div class="user-avater">
                                        <img src="<?php echo e(assets('img/team-1.png')); ?> " alt="">
                                    </div>
                                    <div class="user-message">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">Web Design</a>
                                            <span class="time-posted">3 hrs ago</span>
                                        </p>
                                        <p>
                                            <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum dolor amet cosec Lorem ipsum</span>
                                            <span class="msg-count badge-circle badge-success badge-sm">1</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="author-online has-new-message">
                                    <div class="user-avater">
                                        <img src="<?php echo e(assets('img/team-1.png')); ?>" alt="">
                                    </div>
                                    <div class="user-message">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">Web Design</a>
                                            <span class="time-posted">3 hrs ago</span>
                                        </p>
                                        <p>
                                            <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum dolor amet cosec Lorem ipsum</span>
                                            <span class="msg-count badge-circle badge-success badge-sm">1</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="author-offline">
                                    <div class="user-avater">
                                        <img src="<?php echo e(assets('img/team-1.png')); ?>" alt="">
                                    </div>
                                    <div class="user-message">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">Web Design</a>
                                            <span class="time-posted">3 hrs ago</span>
                                        </p>
                                        <p>
                                            <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum dolor amet cosec Lorem ipsum</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="author-offline">
                                    <div class="user-avater">
                                        <img src="<?php echo e(assets('img/team-1.png')); ?>" alt="">
                                    </div>
                                    <div class="user-message">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">Web Design</a>
                                            <span class="time-posted">3 hrs ago</span>
                                        </p>
                                        <p>
                                            <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum dolor amet cosec Lorem ipsum</span>
                                        </p>
                                    </div>
                                </li>
                            </ul>
                            <a href="" class="dropdown-wrapper__more">See All Message</a>
                        </div>
                    </div>
                </li>
                <!-- ends: nav-message -->

                <li class="nav-notification">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle">
                            <span data-feather="bell"></span></a>
                        <div class="dropdown-wrapper">
                            <h2 class="dropdown-wrapper__title">Notifications <span
                                    class="badge-circle badge-warning ml-1">4</span></h2>
                            <ul>
                                <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                    <div class="nav-notification__type nav-notification__type--primary">
                                        <span data-feather="inbox"></span>
                                    </div>
                                    <div class="nav-notification__details">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">James</a>
                                            <span>sent you a message</span>
                                        </p>
                                        <p>
                                            <span class="time-posted">5 hours ago</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                    <div class="nav-notification__type nav-notification__type--secondary">
                                        <span data-feather="upload"></span>
                                    </div>
                                    <div class="nav-notification__details">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">James</a>
                                            <span>sent you a message</span>
                                        </p>
                                        <p>
                                            <span class="time-posted">5 hours ago</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                    <div class="nav-notification__type nav-notification__type--success">
                                        <span data-feather="log-in"></span>
                                    </div>
                                    <div class="nav-notification__details">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">James</a>
                                            <span>sent you a message</span>
                                        </p>
                                        <p>
                                            <span class="time-posted">5 hours ago</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="nav-notification__single nav-notification__single d-flex flex-wrap">
                                    <div class="nav-notification__type nav-notification__type--info">
                                        <span data-feather="at-sign"></span>
                                    </div>
                                    <div class="nav-notification__details">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">James</a>
                                            <span>sent you a message</span>
                                        </p>
                                        <p>
                                            <span class="time-posted">5 hours ago</span>
                                        </p>
                                    </div>
                                </li>
                                <li class="nav-notification__single nav-notification__single d-flex flex-wrap">
                                    <div class="nav-notification__type nav-notification__type--danger">
                                        <span data-feather="heart"></span>
                                    </div>
                                    <div class="nav-notification__details">
                                        <p>
                                            <a href="" class="subject stretched-link text-truncate"
                                               style="max-width: 180px;">James</a>
                                            <span>sent you a message</span>
                                        </p>
                                        <p>
                                            <span class="time-posted">5 hours ago</span>
                                        </p>
                                    </div>
                                </li>
                            </ul>
                            <a href="" class="dropdown-wrapper__more">See all incoming activity</a>
                        </div>
                    </div>
                </li>
                <!-- ends: .nav-notification -->
                <li class="nav-settings">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle">
                            <span data-feather="settings"></span></a>
                        <div class="dropdown-wrapper dropdown-wrapper--large">
                            <ul class="list-settings">
                                <li class="d-flex">
                                    <div class="mr-3"><img src="<?php echo e(assets('img/mail.png')); ?>" alt=""></div>
                                    <div class="flex-grow-1">
                                        <h6>
                                            <a href="" class="stretched-link">All Features</a>
                                        </h6>
                                        <p>Introducing Increment subscriptions </p>
                                    </div>
                                </li>
                                <li class="d-flex">
                                    <div class="mr-3"><img src="<?php echo e(assets('img/color-palette.png')); ?>" alt=""></div>
                                    <div class="flex-grow-1">
                                        <h6>
                                            <a href="" class="stretched-link">Themes</a>
                                        </h6>
                                        <p>Third party themes that are compatible</p>
                                    </div>
                                </li>
                                <li class="d-flex">
                                    <div class="mr-3"><img src="<?php echo e(assets('img/home.png')); ?>" alt=""></div>
                                    <div class="flex-grow-1">
                                        <h6>
                                            <a href="" class="stretched-link">Payments</a>
                                        </h6>
                                        <p>We handle billions of dollars</p>
                                    </div>
                                </li>
                                <li class="d-flex">
                                    <div class="mr-3"><img src="<?php echo e(assets('img/video-camera.png')); ?>" alt=""></div>
                                    <div class="flex-grow-1">
                                        <h6>
                                            <a href="" class="stretched-link">Design Mockups</a>
                                        </h6>
                                        <p>Share planning visuals with clients</p>
                                    </div>
                                </li>
                                <li class="d-flex">
                                    <div class="mr-3"><img src="<?php echo e(assets('img/document.png')); ?>" alt=""></div>
                                    <div class="flex-grow-1">
                                        <h6>
                                            <a href="" class="stretched-link">Content Planner</a>
                                        </h6>
                                        <p>Centralize content gethering and editing</p>
                                    </div>
                                </li>
                                <li class="d-flex">
                                    <div class="mr-3"><img src="<?php echo e(assets('img/microphone.png')); ?>" alt=""></div>
                                    <div class="flex-grow-1">
                                        <h6>
                                            <a href="" class="stretched-link">Diagram Maker</a>
                                        </h6>
                                        <p>Plan user flows & test scenarios</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <!-- ends: .nav-settings -->
                <li class="nav-support">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle">
                            <span data-feather="help-circle"></span></a>
                        <div class="dropdown-wrapper">
                            <div class="list-group">
                                <span>Documentation</span>
                                <ul>
                                    <li>
                                        <a href="">How to customize admin</a>
                                    </li>
                                    <li>
                                        <a href="">How to use</a>
                                    </li>
                                    <li>
                                        <a href="">The relation of vertical spacing</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="list-group">
                                <span>Payments</span>
                                <ul>
                                    <li>
                                        <a href="">How to customize admin</a>
                                    </li>
                                    <li>
                                        <a href="">How to use</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="list-group">
                                <span>Content Planner</span>
                                <ul>
                                    <li>
                                        <a href="">How to customize admin</a>
                                    </li>
                                    <li>
                                        <a href="">How to use</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- ends: .nav-support -->
                <li class="nav-flag-select">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle"><img src="<?php echo e(assets('img/flag.png')); ?>" alt=""
                                                                            class="rounded-circle"></a>
                        <div class="dropdown-wrapper dropdown-wrapper--small">
                            <a href=""><img src="<?php echo e(assets('img/ger.png')); ?>" alt=""> German</a>
                            <a href=""><img src="<?php echo e(assets('img/spa.png')); ?>" alt=""> Spanish</a>
                            <a href=""><img src="<?php echo e(assets('img/tur.png')); ?>" alt=""> Turkish</a>
                            <a href=""><img src="<?php echo e(assets('img/eng.png')); ?>" alt=""> English</a>
                        </div>
                    </div>
                </li>
                <!-- ends: .nav-flag-select -->
                <li class="nav-author">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle"><img src="<?php echo e(assets('img/author-nav.jpg')); ?>"
                                                                            alt="" class="rounded-circle"></a>
                        <div class="dropdown-wrapper">
                            <div class="nav-author__info">
                                <div class="author-img">
                                    <img src="<?php echo e(assets('img/author-nav.jpg')); ?>" alt="" class="rounded-circle">
                                </div>
                                <div>
                                    
                                    <span>UI Designer</span>
                                </div>
                            </div>
                            <div class="nav-author__options">
                                <ul>
                                    <li>
                                        <a href="">
                                            <span data-feather="user"></span> Profile</a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <span data-feather="settings"></span> Settings</a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <span data-feather="key"></span> Billing</a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <span data-feather="users"></span> Activity</a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <span data-feather="bell"></span> Help</a>
                                    </li>
                                </ul>
                                <a href="<?php echo e(url('logout')); ?>" class="nav-author__signout" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    <span data-feather="log-out"></span> Sign Out</a>
                                <form id="logout-form" action="<?php echo e(url('logout')); ?>" method="POST" class="d-none">
                                    <?php echo csrf_field(); ?>
                                </form>
                            </div>
                        </div>
                        <!-- ends: .dropdown-wrapper -->
                    </div>
                </li>
                <!-- ends: .nav-author -->
            </ul>
            <!-- ends: .navbar-right__menu -->
            <div class="navbar-right__mobileAction d-md-none">
                <a href="#" class="btn-search">
                    <span data-feather="search"></span>
                    <span data-feather="x"></span></a>
                <a href="#" class="btn-author-action">
                    <span data-feather="more-vertical"></span></a>
            </div>
        </div>
        <!-- ends: .navbar-right -->
    </nav>
</header>
<?php /**PATH C:\xampp\htdocs\supermarket\app\views/partials/admin/header.blade.php ENDPATH**/ ?>