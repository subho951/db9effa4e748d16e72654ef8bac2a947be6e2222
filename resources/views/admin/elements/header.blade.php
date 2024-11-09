<div class="navbar-nav-wrap">
  <!-- Logo -->
  <a class="navbar-brand" href="index.html" aria-label="Front">
    <img class="navbar-brand-logo" src="<?=env('ADMIN_ASSETS_URL')?>assets/svg/logos/logo.svg" alt="Logo" data-hs-theme-appearance="default">
    <img class="navbar-brand-logo" src="<?=env('ADMIN_ASSETS_URL')?>assets/svg/logos-light/logo.svg" alt="Logo" data-hs-theme-appearance="dark">
    <img class="navbar-brand-logo-mini" src="<?=env('ADMIN_ASSETS_URL')?>assets/svg/logos/logo-short.svg" alt="Logo" data-hs-theme-appearance="default">
    <img class="navbar-brand-logo-mini" src="<?=env('ADMIN_ASSETS_URL')?>assets/svg/logos-light/logo-short.svg" alt="Logo" data-hs-theme-appearance="dark">
  </a>
  <!-- End Logo -->
  <div class="navbar-nav-wrap-content-start">
    <!-- Navbar Vertical Toggle -->
    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
      <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
      <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
    </button>
    <!-- End Navbar Vertical Toggle -->
    <!-- Search Form -->
    <div class="dropdown ms-2">
      <!-- Input Group -->
      <!-- <div class="d-none d-lg-block">
        <div class="input-group input-group-merge input-group-borderless input-group-hover-light navbar-input-group">
          <div class="input-group-prepend input-group-text">
            <i class="bi-search"></i>
          </div>
          <input type="search" class="js-form-search form-control" placeholder="Search in front" aria-label="Search in front" data-hs-form-search-options='{
                   "clearIcon": "#clearSearchResultsIcon",
                   "dropMenuElement": "#searchDropdownMenu",
                   "dropMenuOffset": 20,
                   "toggleIconOnFocus": true,
                   "activeClass": "focus"
                 }'>
          <a class="input-group-append input-group-text" href="javascript:;">
            <i id="clearSearchResultsIcon" class="bi-x-lg" style="display: none;"></i>
          </a>
        </div>
      </div>
      <button class="js-form-search js-form-search-mobile-toggle btn btn-ghost-secondary btn-icon rounded-circle d-lg-none" type="button" data-hs-form-search-options='{
                   "clearIcon": "#clearSearchResultsIcon",
                   "dropMenuElement": "#searchDropdownMenu",
                   "dropMenuOffset": 20,
                   "toggleIconOnFocus": true,
                   "activeClass": "focus"
                 }'>
        <i class="bi-search"></i>
      </button> -->
      <!-- End Input Group -->
      <!-- Card Search Content -->
      <div id="searchDropdownMenu" class="hs-form-search-menu-content dropdown-menu dropdown-menu-form-search navbar-dropdown-menu-borderless">
        <div class="card">
          <!-- Body -->
          <div class="card-body-height">
            <div class="d-lg-none">
              <div class="input-group input-group-merge navbar-input-group mb-5">
                <div class="input-group-prepend input-group-text">
                  <i class="bi-search"></i>
                </div>
                <input type="search" class="form-control" placeholder="Search in front" aria-label="Search in front">
                <a class="input-group-append input-group-text" href="javascript:;">
                  <i class="bi-x-lg"></i>
                </a>
              </div>
            </div>
            <span class="dropdown-header">Recent searches</span>
            <div class="dropdown-item bg-transparent text-wrap">
              <a class="btn btn-soft-dark btn-xs rounded-pill" href="index.html">
                Gulp <i class="bi-search ms-1"></i>
              </a>
              <a class="btn btn-soft-dark btn-xs rounded-pill" href="index.html">
                Notification panel <i class="bi-search ms-1"></i>
              </a>
            </div>
            <div class="dropdown-divider"></div>
            <span class="dropdown-header">Tutorials</span>
            <a class="dropdown-item" href="index.html">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <span class="icon icon-soft-dark icon-xs icon-circle">
                    <i class="bi-sliders"></i>
                  </span>
                </div>
                <div class="flex-grow-1 text-truncate ms-2">
                  <span>How to set up Gulp?</span>
                </div>
              </div>
            </a>
            <a class="dropdown-item" href="index.html">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <span class="icon icon-soft-dark icon-xs icon-circle">
                    <i class="bi-paint-bucket"></i>
                  </span>
                </div>
                <div class="flex-grow-1 text-truncate ms-2">
                  <span>How to change theme color?</span>
                </div>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <span class="dropdown-header">Members</span>
            <a class="dropdown-item" href="index.html">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <img class="avatar avatar-xs avatar-circle" src="<?=env('ADMIN_ASSETS_URL')?>assets/img/160x160/img10.jpg" alt="Image Description">
                </div>
                <div class="flex-grow-1 text-truncate ms-2">
                  <span>Amanda Harvey <i class="tio-verified text-primary" data-toggle="tooltip" data-placement="top" title="Top endorsed"></i></span>
                </div>
              </div>
            </a>
            <a class="dropdown-item" href="index.html">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <img class="avatar avatar-xs avatar-circle" src="<?=env('ADMIN_ASSETS_URL')?>assets/img/160x160/img3.jpg" alt="Image Description">
                </div>
                <div class="flex-grow-1 text-truncate ms-2">
                  <span>David Harrison</span>
                </div>
              </div>
            </a>
            <a class="dropdown-item" href="index.html">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="avatar avatar-xs avatar-soft-info avatar-circle">
                    <span class="avatar-initials">A</span>
                  </div>
                </div>
                <div class="flex-grow-1 text-truncate ms-2">
                  <span>Anne Richard</span>
                </div>
              </div>
            </a>
          </div>
          <!-- End Body -->
          <!-- Footer -->
          <a class="card-footer text-center" href="index.html">
            See all results <i class="bi-chevron-right small"></i>
          </a>
          <!-- End Footer -->
        </div>
      </div>
      <!-- End Card Search Content -->
    </div>
    <!-- End Search Form -->
  </div>
  <div class="navbar-nav-wrap-content-end">
    <!-- Navbar -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <!-- Account -->
        <div class="dropdown">
          <a class="navbar-dropdown-account-wrapper" href="javascript:;" id="accountNavbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" data-bs-dropdown-animation>
            <div class="avatar avatar-sm avatar-circle">
              <img class="avatar-img" src="<?=(($admin->image != '')?env('UPLOADS_URL').$admin->image:env('NO_USER_IMAGE'))?>" alt="<?=$admin->name?>">
              <span class="avatar-status avatar-sm-status avatar-status-success"></span>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-account" aria-labelledby="accountNavbarDropdown" style="width: 16rem;">
            <div class="dropdown-item-text">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm avatar-circle">
                  <img class="avatar-img" src="<?=(($admin->image != '')?env('UPLOADS_URL').$admin->image:env('NO_USER_IMAGE'))?>" alt="<?=$admin->name?>">
                </div>
                <div class="flex-grow-1 ms-3">
                  <h5 class="mb-0"><?=$admin->name?></h5>
                  <p class="card-text text-body"><?=$admin->email?></p>
                </div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <!-- Dropdown -->
            <!-- <div class="dropdown">
              <a class="navbar-dropdown-submenu-item dropdown-item dropdown-toggle" href="javascript:;" id="navSubmenuPagesAccountDropdown1" data-bs-toggle="dropdown" aria-expanded="false">Set status</a>
              <div class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-sub-menu" aria-labelledby="navSubmenuPagesAccountDropdown1">
                <a class="dropdown-item" href="#">
                  <span class="legend-indicator bg-success me-1"></span> Available
                </a>
                <a class="dropdown-item" href="#">
                  <span class="legend-indicator bg-danger me-1"></span> Busy
                </a>
                <a class="dropdown-item" href="#">
                  <span class="legend-indicator bg-warning me-1"></span> Away
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#"> Reset status
                </a>
              </div>
            </div> -->
            <!-- End Dropdown -->
            <a class="dropdown-item" href="<?=url('admin/settings')?>">Profile &amp; Settings</a>
            <!-- <a class="dropdown-item" href="#">Settings</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="avatar avatar-sm avatar-dark avatar-circle">
                    <span class="avatar-initials">HS</span>
                  </div>
                </div>
                <div class="flex-grow-1 ms-2">
                  <h5 class="mb-0">Htmlstream <span class="badge bg-primary rounded-pill text-uppercase ms-1">PRO</span></h5>
                  <span class="card-text">hs.example.com</span>
                </div>
              </div>
            </a> -->
            <div class="dropdown-divider"></div>
            <!-- Dropdown -->
            <!-- <div class="dropdown">
              <a class="navbar-dropdown-submenu-item dropdown-item dropdown-toggle" href="javascript:;" id="navSubmenuPagesAccountDropdown2" data-bs-toggle="dropdown" aria-expanded="false">Customization</a>
              <div class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-sub-menu" aria-labelledby="navSubmenuPagesAccountDropdown2">
                <a class="dropdown-item" href="#">
                  Invite people
                </a>
                <a class="dropdown-item" href="#">
                  Analytics
                  <i class="bi-box-arrow-in-up-right"></i>
                </a>
                <a class="dropdown-item" href="#">
                  Customize Front
                  <i class="bi-box-arrow-in-up-right"></i>
                </a>
              </div>
            </div> -->
            <!-- End Dropdown -->
            <!-- <a class="dropdown-item" href="#">Manage team</a>
            <div class="dropdown-divider"></div> -->
            <a class="dropdown-item" href="<?=url('admin/logout')?>">Sign Out</a>
          </div>
        </div>
        <!-- End Account -->
      </li>
    </ul>
    <!-- End Navbar -->
  </div>
</div>