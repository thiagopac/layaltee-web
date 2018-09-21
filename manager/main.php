<!DOCTYPE html>
<html lang="en" >
   <!-- begin::Head -->
   <head>
      <meta charset="utf-8" />
      <title>
         Loyaltee | Admin
      </title>
      <meta name="description" content="Seu aplicativo, seu programa de fidelidade">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <!--begin::Web font -->
      <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
      <script>
         WebFont.load({
           google: {"families":["Montserrat:300,400,500,600,700","Roboto:300,400,500,600,700"]},
           active: function() {
               sessionStorage.fonts = true;
           }
         });
      </script>
      <!--begin::Base Scripts -->
      <script src="assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
      <script src="assets/demo/demo3/base/scripts.bundle.js" type="text/javascript"></script>
      <!--end::Base Scripts -->
      <!--end::Web font -->
      <!--begin::Base Styles -->
      <link href="assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css" />
      <link href="assets/demo/demo3/base/style.bundle.css" rel="stylesheet" type="text/css" />
      <!--end::Base Styles -->
      <link rel="shortcut icon" href="assets/demo/demo3/media/img/logo/favicon.ico" />
   </head>
   <!-- end::Head -->
   <!-- end::Body -->
   <body  class="m-page--fluid page-boxed m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
      <!-- begin:: Page -->
      <div class="m-grid m-grid--hor m-grid--root m-page">
         <!-- BEGIN: Header -->
         <header id="m_header" class="m-grid__item m-header "  minimize-offset="200" minimize-mobile-offset="200" >
            <div class="m-container m-container--fluid m-container--full-height">
               <div class="m-stack m-stack--ver m-stack--desktop">
                  <!-- BEGIN: Brand -->
                  <div class="m-stack__item m-brand  m-brand--skin-dark ">
                     <div class="m-stack m-stack--ver m-stack--general">
                        <div class="m-stack__item m-stack__item--middle m-stack__item--center m-brand__logo">
                           <a href="main.php?page=dashboard" class="m-brand__logo-wrapper">
                           <img alt="" src="assets/app/media/img/logos/logo-l.png"/>
                           </a>
                        </div>
                        <div class="m-stack__item m-stack__item--middle m-brand__tools">
                           <!-- BEGIN: Responsive Aside Left Menu Toggler -->
                           <a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                           <span></span>
                           </a>
                           <!-- END -->

                           <!-- BEGIN: Topbar Toggler -->
                           <a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
                           <i class="flaticon-more"></i>
                           </a>
                           <!-- BEGIN: Topbar Toggler -->
                        </div>
                     </div>
                  </div>
                  <!-- END: Brand -->
                  <div class="m-stack__item m-stack__item--fluid m-header-head" style="background:#5758BB" id="m_header_nav">
                     <!-- BEGIN: Horizontal Menu -->
                     <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark " id="m_aside_header_menu_mobile_close_btn">
                     <i class="la la-close"></i>
                     </button>
                     <!-- END: Horizontal Menu -->
                     <!-- BEGIN: Topbar -->
                     <?php include("_top.php"); ?>
                     <!-- END: Topbar -->
                  </div>
               </div>
            </div>
         </header>
         <!-- END: Header -->
         <!-- begin::Body -->
         <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
            <!-- BEGIN: Left Aside -->
            <button class="m-aside-left-close m-aside-left-close--skin-dark" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
            </button>
            <div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
               <!-- BEGIN: Aside Menu -->
               <div id="m_ver_menu"class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark m-aside-menu--dropdown "data-menu-vertical="true" m-menu-dropdown="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500">
                  <!-- BEGIN: menu  -->
                  <?php include("_menu.php"); ?>
                  <!-- END: menu  -->
               </div>
               <!-- END: Aside Menu -->
            </div>
            <!-- END: Left Aside -->
            <!-- BEGIN: content -->
            <?php
               $fileExists = file_exists("_".$_GET["page"].".php");

               if ($fileExists == true) {
               	//carrega página que veio no get
               	include("_".$_GET["page"].".php");
               }else{
               	//exige erro 404
               	print("<meta http-equiv='refresh' content='0;url=404.php'>");
               }

               ?>
            <!-- END: content -->
         </div>
         <!-- end:: Body -->
         <!-- begin::Footer -->
         <?php include("_footer.php"); ?>
         <!-- end::Footer -->
      </div>
      <!-- end:: Page -->
      <!-- begin::Scroll Top -->
      <div id="m_scroll_top" class="m-scroll-top">
         <i class="la la-arrow-up"></i>
      </div>

   </body>
   <!-- end::Body -->
</html>
<script>
   jQuery(document).ready(function() {

   	if(sessionStorage.getItem("admin")){
   			var admin = JSON.parse(sessionStorage.getItem("admin"));
   			valLocalId = admin.adminLocalId;
   	}

   	if(sessionStorage.getItem("BASE_URL")){
   			var BASE_URL = sessionStorage.getItem("BASE_URL");
   	}

   	$.ajax({
   			url: BASE_URL+'manager/webapi/local/info/'+valLocalId,
   			type: 'GET',
   			contentType: 'application/json',
   			dataType:"json",
   			async: true,
   			success: function (result) {
   				var response = result;

   				// console.log(response);

   				if(response["status"] == 1){

   					local = response["info"];
   					sessionStorage.setItem("local",JSON.stringify(local));


   				}else if(response["status"] == 2){
   					toastr.error(response["statusMessage"]);
   				}
   			}, error: function (result) {
   					toastr.error("Erro no servidor. Tente novamente mais tarde.");
   			}
   	});

   });
</script>
