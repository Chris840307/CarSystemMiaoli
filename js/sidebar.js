// $(function () {
//     $("#sidebar_menu").load("menu.html");
// });

window.addEventListener("load", () => {
  $("#menuUserName").text("Hi, " + getCookieByName("name"));

  if (getCookieByName("auth") === "1") {
    //使用者
    $("#sidebar_menu").append(
      '<div class="sidebar"> <div class="sidebar-inner"> <!-- ### $Sidebar Header ### --> <div class="sidebar-logo"> <div class="peers ai-c fxw-nw"> <div class="peer peer-greed"> <a class="sidebar-link td-n" href="main.html"> <div class="peers ai-c fxw-nw"> <div class="peer"> <div class="logo text-center"> <img src="img/logo.png" width="70px" alt="" /> </div> </div> <div class="peer peer-greed"> <h6 class="lh-1 mB-0 logo-text ml-2">車輛查詢系統</h6> </div> </div> </a> </div> <div class="peer"> <div class="mobile-toggle sidebar-toggle td-n text-right" onclick="sidebarToggle();" > <i class="ti-arrow-circle-left"></i> </div> </div> </div> </div> <!-- ### $Sidebar Menu ### --> <ul class="sidebar-menu scrollable pos-r ps ps--active-y"> <li class="nav-item mT-30 actived"> <a class="sidebar-link" href="main.html"> <span class="icon-holder"> <i class="c-blue-500 ti-pulse"></i> </span> <span class="title">主畫面</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="top.html"> <span class="icon-holder"> <i class="c-pink-500 ti-car"></i> </span> <span class="title">車輛查詢</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="bechecked.html"> <span class="icon-holder"> <i class="c-orange-500 ti-layout-list-thumb"></i> </span> <span class="title">已開單</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="uncheck.html"> <span class="icon-holder"> <i class="c-deep-purple-500 ti-layout-list-thumb-alt"></i> </span> <span class="title">不舉發單</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="timeSync.html"> <span class="icon-holder"> <i class="c-purple-500 ti-alarm-clock"></i> </span> <span class="title">時間同步功能</span> </a> </li> <li class="nav-item dropdown"> <a class="dropdown-toggle" href="javascript:void(0);"> <span class="icon-holder"> <i class="c-teal-500 ti-bar-chart"></i> </span> <span class="title">統計報表生成</span> <span class="arrow"> <i class="ti-angle-right"></i> </span> </a> <ul class="dropdown-menu"> <li> <a class="sidebar-link" href="export1.html">日期區間違規查詢</a> </li> <li><a class="sidebar-link" href="export2.html">違規熱時分析</a></li> <li> <a class="sidebar-link" href="export3.html">區間時段違規統計表</a> </li> <li><a class="sidebar-link" href="export4.html">月違規統計表</a></li> <li> <a class="sidebar-link" href="export5.html">違規車輛明細表</a> </li> <li> <a class="sidebar-link" href="export6.html">車種違規統計表</a> </li> </ul> </li> <li class="nav-item"> <a class="sidebar-link" href="parameter.html"> <span class="icon-holder"> <i class="c-blue-500 ti-write"></i> </span> <span class="title">參數設定</span> </a> </li> <li class="nav-item dropdown"> <a class="dropdown-toggle" href="javascript:void(0);"> <span class="icon-holder"> <i class="c-brown-500 ti-settings"></i> </span> <span class="title">系統管理設定</span> <span class="arrow"> <i class="ti-angle-right"></i> </span> </a> <ul class="dropdown-menu"> <li><a class="sidebar-link" href="sysAcc.html">帳號狀態</a></li> <li><a class="sidebar-link" href="sysDaily.html">操作日誌查詢</a></li> </ul> </li> <div class="ps__rail-x" style="left: 0px; bottom: 0px"> <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px" ></div> </div> <div class="ps__rail-y" style="top: 0px; height: 240px; right: 0px"> <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 126px" ></div> </div> </ul> </div> </div>'
    );
  } else {
    //管理者
    $("#sidebar_menu").append(
      '<div class="sidebar"> <div class="sidebar-inner"> <!-- ### $Sidebar Header ### --> <div class="sidebar-logo"> <div class="peers ai-c fxw-nw"> <div class="peer peer-greed"> <a class="sidebar-link td-n" href="main.html"> <div class="peers ai-c fxw-nw"> <div class="peer"> <div class="logo text-center"> <img src="img/logo.png" width="70px" alt="" /> </div> </div> <div class="peer peer-greed"> <h6 class="lh-1 mB-0 logo-text ml-2">車輛查詢系統</h6> </div> </div> </a> </div> <div class="peer"> <div class="mobile-toggle sidebar-toggle td-n text-right" onclick="sidebarToggle();" > <i class="ti-arrow-circle-left"></i> </div> </div> </div> </div> <!-- ### $Sidebar Menu ### --> <ul class="sidebar-menu scrollable pos-r ps ps--active-y"> <li class="nav-item mT-30 actived"> <a class="sidebar-link" href="main.html"> <span class="icon-holder"> <i class="c-blue-500 ti-pulse"></i> </span> <span class="title">主畫面</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="top.html"> <span class="icon-holder"> <i class="c-pink-500 ti-car"></i> </span> <span class="title">車輛查詢</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="bechecked.html"> <span class="icon-holder"> <i class="c-orange-500 ti-layout-list-thumb"></i> </span> <span class="title">已開單</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="uncheck.html"> <span class="icon-holder"> <i class="c-deep-purple-500 ti-layout-list-thumb-alt"></i> </span> <span class="title">不舉發單</span> </a> </li> <li class="nav-item"> <a class="sidebar-link" href="timeSync.html"> <span class="icon-holder"> <i class="c-purple-500 ti-alarm-clock"></i> </span> <span class="title">時間同步功能</span> </a> </li> <li class="nav-item dropdown"> <a class="dropdown-toggle" href="javascript:void(0);"> <span class="icon-holder"> <i class="c-teal-500 ti-bar-chart"></i> </span> <span class="title">統計報表生成</span> <span class="arrow"> <i class="ti-angle-right"></i> </span> </a> <ul class="dropdown-menu"> <li> <a class="sidebar-link" href="export1.html">日期區間違規查詢</a> </li> <li><a class="sidebar-link" href="export2.html">違規熱時分析</a></li> <li> <a class="sidebar-link" href="export3.html">區間時段違規統計表</a> </li> <li><a class="sidebar-link" href="export4.html">月違規統計表</a></li> <li> <a class="sidebar-link" href="export5.html">違規車輛明細表</a> </li> <li> <a class="sidebar-link" href="export6.html">車種違規統計表</a> </li> </ul> </li> <li class="nav-item"> <a class="sidebar-link" href="parameter.html"> <span class="icon-holder"> <i class="c-blue-500 ti-write"></i> </span> <span class="title">參數設定</span> </a> </li> <li class="nav-item dropdown"> <a class="dropdown-toggle" href="javascript:void(0);"> <span class="icon-holder"> <i class="c-brown-500 ti-settings"></i> </span> <span class="title">系統管理設定</span> <span class="arrow"> <i class="ti-angle-right"></i> </span> </a> <ul class="dropdown-menu"> <li><a class="sidebar-link" href="sysAcc.html">帳號狀態</a></li> <li><a class="sidebar-link" href="sysDaily.html">操作日誌查詢</a></li> <li><a class="sidebar-link" href="whiteList.html">白名單管理</a></li> <li><a class="sidebar-link" href="carTypeList.html">車種設定</a></li> </ul> </li> <div class="ps__rail-x" style="left: 0px; bottom: 0px"> <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px" ></div> </div> <div class="ps__rail-y" style="top: 0px; height: 240px; right: 0px"> <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 126px" ></div> </div> </ul> </div> </div>'
    );
  }

  const EVENT = document.createEvent("UIEvents");
  window.EVENT = EVENT;
  EVENT.initUIEvent("resize", true, false, window, 0);

  /**
   * Trigger window resize event after page load
   * for recalculation of masonry layout.
   */
  window.dispatchEvent(EVENT);

  // Trigger resize on any element click
  document.addEventListener("click", () => {
    window.dispatchEvent(window.EVENT);
  });

  // Sidebar links
  $(".sidebar .sidebar-menu li a").on("click", function () {
    const $this = $(this);

    if ($this.parent().hasClass("open")) {
      $this
        .parent()
        .children(".dropdown-menu")
        .slideUp(200, () => {
          $this.parent().removeClass("open");
        });
    } else {
      $this
        .parent()
        .parent()
        .children("li.open")
        .children(".dropdown-menu")
        .slideUp(200);

      $this
        .parent()
        .parent()
        .children("li.open")
        .children("a")
        .removeClass("open");

      $this.parent().parent().children("li.open").removeClass("open");

      $this
        .parent()
        .children(".dropdown-menu")
        .slideDown(200, () => {
          $this.parent().addClass("open");
        });
    }
  });

  // Sidebar Activity Class
  const sidebarLinks = $(".sidebar").find(".sidebar-link");

  sidebarLinks
    .each((index, el) => {
      $(el).removeClass("active");
    })
    .filter(function () {
      const href = $(this).attr("href");
      const pattern = href[0] === "/" ? href.substr(1) : href;
      return pattern === window.location.pathname.substr(1);
    })
    .addClass("active");

  // ٍSidebar Toggle
  $("#sidebar-toggle").on("click", (e) => {
    $(".app").toggleClass("is-collapsed");
    e.preventDefault();
  });

  /**
   * Wait untill sidebar fully toggled (animated in/out)
   * then trigger window resize event in order to recalculate
   * masonry layout widths and gutters.
   */
  // $('#sidebar-toggle').click(e => {
  //     e.preventDefault();
  //     setTimeout(() => {
  //         window.dispatchEvent(window.EVENT);
  //     }, 300);
  // });

  //點擊外部關閉
  $("main").on("click", (e) => {
    if ($("body[class='app is-collapsed']").length != 0) {
      $(".app").toggleClass("is-collapsed");
      e.preventDefault();
    }
  });
});

function sidebarToggle(e) {
  $(".app").toggleClass("is-collapsed");
}
