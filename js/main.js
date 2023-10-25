/*
	Prologue by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

// (function ($) {

// 	var $window = $(window),
// 		$body = $('body'),
// 		$nav = $('#nav');

// 	// Breakpoints.
// 	breakpoints({
// 		wide: ['961px', '1880px'],
// 		normal: ['961px', '1620px'],
// 		narrow: ['961px', '1320px'],
// 		narrower: ['737px', '960px'],
// 		mobile: [null, '736px']
// 	});

// 	// Play initial animations on page load.
// 	$window.on('load', function () {
// 		window.setTimeout(function () {
// 			$body.removeClass('is-preload');
// 		}, 100);
// 	});

// 	// Nav.
// 	var $nav_a = $nav.find('a');

// 	$nav_a
// 		.addClass('scrolly')
// 		.on('click', function (e) {

// 			var $this = $(this);

// 			// External link? Bail.
// 			if ($this.attr('href').charAt(0) != '#')
// 				return;

// 			// Prevent default.
// 			e.preventDefault();

// 			// Deactivate all links.
// 			$nav_a.removeClass('active');

// 			// Activate link *and* lock it (so Scrollex doesn't try to activate other links as we're scrolling to this one's section).
// 			$this
// 				.addClass('active')
// 				.addClass('active-locked');

// 		})
// 		.each(function () {

// 			var $this = $(this),
// 				id = $this.attr('href'),
// 				$section = $(id);

// 			// No section for this link? Bail.
// 			if ($section.length < 1)
// 				return;

// 			// Scrollex.
// 			$section.scrollex({
// 				mode: 'middle',
// 				top: '-10vh',
// 				bottom: '-10vh',
// 				initialize: function () {

// 					// Deactivate section.
// 					$section.addClass('inactive');

// 				},
// 				enter: function () {

// 					// Activate section.
// 					$section.removeClass('inactive');

// 					// No locked links? Deactivate all links and activate this section's one.
// 					if ($nav_a.filter('.active-locked').length == 0) {

// 						$nav_a.removeClass('active');
// 						$this.addClass('active');

// 					}

// 					// Otherwise, if this section's link is the one that's locked, unlock it.
// 					else if ($this.hasClass('active-locked'))
// 						$this.removeClass('active-locked');

// 				}
// 			});

// 		});

// 	// Scrolly.
// 	$('.scrolly').scrolly();

// 	// Header (narrower + mobile).

// 	// Toggle.
// 	$(
// 		'<div id="headerToggle">' +
// 		'<a href="#header" class="toggle"></a>' +
// 		'</div>'
// 	)
// 		.appendTo($body);

// 	// Header.
// 	$('#header')
// 		.panel({
// 			delay: 500,
// 			hideOnClick: true,
// 			hideOnSwipe: true,
// 			resetScroll: true,
// 			resetForms: true,
// 			side: 'left',
// 			target: $body,
// 			visibleClass: 'header-visible'
// 		});

// })(jQuery);

function makeBackendUrl(path) {
  if (getCookieByName("url1") != null && getCookieByName("url2") != null) {
    if (path != "" && path != null) return getCookieByName("url1") + path;
    else {
      return getCookieByName("url2");
    }
  } else {
    alert("連線超時，請重新登入");
    location.href = "index.html";
  }
}

//開啟關閉sidebar
function menu_toggle(e) {
  // e.preventDefault();
  $("#wrapper").toggleClass("toggled");

  $("#icon_right").toggleClass("visible-on-sidebar-mini");
  $("#icon_left").toggleClass("visible-on-sidebar-mini");
}

// Chart.pluginService.register({
// 	beforeDraw: function (chart) {
// 		if (chart.config.options.elements.center) {
// 			// Get ctx from string
// 			var ctx = chart.chart.ctx;

// 			// Get options from the center object in options
// 			var centerConfig = chart.config.options.elements.center;
// 			var fontStyle = centerConfig.fontStyle || 'Arial';
// 			var txt = centerConfig.text;
// 			var color = centerConfig.color || '#000';
// 			var maxFontSize = centerConfig.maxFontSize || 75;
// 			var sidePadding = centerConfig.sidePadding || 20;
// 			var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2)
// 			// Start with a base font of 30px
// 			ctx.font = "30px " + fontStyle;

// 			// Get the width of the string and also the width of the element minus 10 to give it 5px side padding
// 			var stringWidth = ctx.measureText(txt).width;
// 			var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

// 			// Find out how much the font can grow in width.
// 			var widthRatio = elementWidth / stringWidth;
// 			var newFontSize = Math.floor(30 * widthRatio);
// 			var elementHeight = (chart.innerRadius * 2);

// 			// Pick a new font size so it will not be larger than the height of label.
// 			var fontSizeToUse = Math.min(newFontSize, elementHeight, maxFontSize);
// 			var minFontSize = centerConfig.minFontSize;
// 			var lineHeight = centerConfig.lineHeight || 25;
// 			var wrapText = false;

// 			if (minFontSize === undefined) {
// 				minFontSize = 20;
// 			}

// 			if (minFontSize && fontSizeToUse < minFontSize) {
// 				fontSizeToUse = minFontSize;
// 				wrapText = true;
// 			}

// 			// Set font settings to draw it correctly.
// 			ctx.textAlign = 'center';
// 			ctx.textBaseline = 'middle';
// 			var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
// 			var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
// 			ctx.font = fontSizeToUse + "px " + fontStyle;
// 			ctx.fillStyle = color;

// 			if (!wrapText) {
// 				ctx.fillText(txt, centerX, centerY);
// 				return;
// 			}

// 			var words = txt.split(' ');
// 			var line = '';
// 			var lines = [];

// 			// Break words up into multiple lines if necessary
// 			for (var n = 0; n < words.length; n++) {
// 				var testLine = line + words[n] + ' ';
// 				var metrics = ctx.measureText(testLine);
// 				var testWidth = metrics.width;
// 				if (testWidth > elementWidth && n > 0) {
// 					lines.push(line);
// 					line = words[n] + ' ';
// 				} else {
// 					line = testLine;
// 				}
// 			}

// 			// Move the center up depending on line height and number of lines
// 			centerY -= (lines.length / 2) * lineHeight;

// 			for (var n = 0; n < lines.length; n++) {
// 				ctx.fillText(lines[n], centerX, centerY);
// 				centerY += lineHeight;
// 			}
// 			//Draw text in center
// 			ctx.fillText(line, centerX, centerY);
// 		}
// 	}
// });

function parseCookie() {
  var cookieObj = {};
  var cookieAry = document.cookie.split(";");
  var cookie;

  for (var i = 0, l = cookieAry.length; i < l; ++i) {
    cookie = jQuery.trim(cookieAry[i]);
    cookie = cookie.split("=");
    cookieObj[cookie[0]] = cookie[1];
  }

  return cookieObj;
}

function setCookie(name, value, days) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + escape(value || "") + expires + "; path=/";
}

function getCookieByName(name) {
  var value = parseCookie()[name];
  if (value) {
    value = decodeURIComponent(unescape(value));
  }

  return value;
}

function delCookie(name) {
  //   document.cookie = name + "=; Max-Age=-99999999;";
  document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

//登出
function logout() {
  delCookie("account");
  delCookie("name");
  delCookie("status");
  delCookie("auth");
  window.location.replace("./index.html");
}

//檢查連線是否過期
function checkConnect() {
  if (
    getCookieByName("name") == "" ||
    getCookieByName("name") == undefined ||
    getCookieByName("name") == null
  ) {
    logout();
  }
}

function checkForm() {
  return false;
}

//取url字串
var strUrl = location.search;
var getPara, ParaVal;
var aryPara = [];
var thing1 = null;
var thing2 = null;
var thing3 = null;
if (strUrl.indexOf("?") != -1) {
  var getSearch = strUrl.split("?");
  getPara = getSearch[1].split("&");
  for (i = 0; i < getPara.length; i++) {
    ParaVal = getPara[i].split("=");
    aryPara.push(ParaVal[0]);
    aryPara[ParaVal[0]] = ParaVal[1];
  }
}

// binaryTObase64
function base64Encode(str) {
  var CHARS =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var out = "",
    i = 0,
    len = str.length,
    c1,
    c2,
    c3;
  while (i < len) {
    c1 = str.charCodeAt(i++) & 0xff;
    if (i == len) {
      out += CHARS.charAt(c1 >> 2);
      out += CHARS.charAt((c1 & 0x3) << 4);
      out += "==";
      break;
    }
    c2 = str.charCodeAt(i++);
    if (i == len) {
      out += CHARS.charAt(c1 >> 2);
      out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xf0) >> 4));
      out += CHARS.charAt((c2 & 0xf) << 2);
      out += "=";
      break;
    }
    c3 = str.charCodeAt(i++);
    out += CHARS.charAt(c1 >> 2);
    out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xf0) >> 4));
    out += CHARS.charAt(((c2 & 0xf) << 2) | ((c3 & 0xc0) >> 6));
    out += CHARS.charAt(c3 & 0x3f);
  }
  return out;
}

//取得指定圖片
function get_image(image_key) {
  var image_data = "";
  $.ajax({
    headers: {
      Authorization: getCookieByName("Authorization"),
    },
    url: makeBackendUrl("/image/key/") + image_key,
    type: "GET",
    contentType: "application/json",
    async: false,
    mimeType: "text/plain; charset=x-user-defined",
    success: function (data) {
      image_data = data;
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(
        XMLHttpRequest.readyState +
          XMLHttpRequest.status +
          XMLHttpRequest.responseText
      );
    },
  });
  // $("id").attr('src', 'data:image/png;base64,' + base64Encode(image_data));
  return image_data;
}

//檔案上傳
function UploadData(fileObj, url) {
  if (typeof fileObj == "undefined" || fileObj.size <= 0) {
    alert("請選擇檔案");
    return;
  }

  var formFile = new FormData();
  formFile.append("File", fileObj); //加入文件對象

  $.ajax({
    headers: {
      Authorization: getCookieByName("Authorization"),
    },
    url: url,
    data: formFile,
    type: "POST",
    dataType: "json",
    cache: false, //上傳文件無需緩存
    processData: false, //用於對data參數進行序列化處理 這裏必須false
    contentType: false, //必須
    async: false,
    success: function (result) {
      refresh();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest.responseText);
      alert("檔案上傳失敗");
    },
  });
}

//定義中文訊息
var cnmsg = {
  required: "*必填欄位",
  email: "*請輸入正確格式的電子郵件",
  equalTo: "*密碼輸入不一致",
  maxlength: "*長度不能大於{0}",
  minlength: "*長度不能小於{0}",
};
jQuery.extend(jQuery.validator.messages, cnmsg);

//日期
function GetDateStr(AddDayCount) {
  var dd = new Date();
  dd.setDate(dd.getDate() + AddDayCount); //获取AddDayCount天后的日期
  var y = dd.getFullYear();
  var m =
    dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1; //取當月份的日期
  var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate();
  return y + "-" + m + "-" + d;
}

//日期時間
function GetDateTimeStr(AddHourCount) {
  var dd = new Date();
  dd.setHours(dd.getHours() + AddHourCount); //取AddHourCount時後的時間
  var y = dd.getFullYear();
  var m =
    dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1; //取當月份的日期
  var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate();
  var hh = dd.getHours() < 10 ? "0" + dd.getHours() : dd.getHours();
  var mm = dd.getMinutes() < 10 ? "0" + dd.getMinutes() : dd.getMinutes();
  var ss = dd.getSeconds() < 10 ? "0" + dd.getSeconds() : dd.getSeconds();
  return y + "-" + m + "-" + d + " " + hh + ":" + mm + ":" + ss;
}

//年月
function GetYearStr(AddDayCount) {
  var dd = new Date();
  dd.setDate(dd.getDate() + AddDayCount); //获取AddDayCount天后的日期
  var y = dd.getFullYear();
  var m =
    dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1; //取當月份的日期
  //   var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate();
  return y + "-" + m;
}

//取得本月第一天
function GetDayOneOfPreviousMonthStr() {
  var dd = new Date();
  var y = dd.getFullYear();
  var m =
    dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1; //取當月份的日期
  return y + "-" + m + "-01";
}

//西元轉民國年月日
function CoverRepublicOfChinaStr(date) {
  var dd = new Date(date);
  var y = dd.getFullYear() - 1911;
  var m =
    dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1; //取當月份的日期
  var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate();
  return y.toString() + m.toString() + d.toString();
}

//時間
function GetTimeStr() {
  var dd = new Date();
  var hh = dd.getHours() < 10 ? "0" + dd.getHours() : dd.getHours();
  var mm = dd.getMinutes() < 10 ? "0" + dd.getMinutes() : dd.getMinutes();
  var ss = dd.getSeconds() < 10 ? "0" + dd.getSeconds() : dd.getSeconds();
  return hh + "" + mm + "" + ss;
}

// MENU下拉式的JS
$(function () {
  $("#menu_slide").click(function () {
    $(".menu-slide-open").slideToggle("fast");
    $(".xs1").toggle();
    $(".xs2").toggle();
  });
});

$(function () {
  $("#menu_slide2").click(function () {
    $(".menu-slide-open2").slideToggle("fast");
    $(".xs1").toggle();
    $(".xs2").toggle();
  });
});

$(function () {
  $("#menu_slide3").click(function () {
    $(".menu-slide-open3").slideToggle("fast");
    $(".xs1").toggle();
    $(".xs2").toggle();
  });
});

emptyStrToUndefined = (v) => {
  return v === "" ? undefined : v;
};
