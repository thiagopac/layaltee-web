<!DOCTYPE html>
<html lang="en" >
   <!-- begin::Head -->
   <head>
      <meta charset="utf-8" />
      <title>
         Loyaltee | Login
      </title>
      <meta name="description" content="Seu aplicativo, seu programa de fidelidade">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <!--begin::Web font -->
      <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
      <script>
         WebFont.load({
           google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
           active: function() {
               sessionStorage.fonts = true;
           }
         });
      </script>
      <!--end::Web font -->
      <!--begin::Base Styles -->
      <link href="assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css" />
      <link href="assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css" />
      <!--end::Base Styles -->
      <link rel="shortcut icon" href="assets/demo/default/media/img/logo/favicon.ico" />
   </head>
   <!-- end::Head -->
   <!-- end::Body -->
   <body  class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
      <!-- begin:: Page -->
      <div class="m-grid m-grid--hor m-grid--root m-page">
         <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-3" id="m_login" style="background-image: url(assets/app/media/img//bg/bg-2.jpg);">
            <div class="m-grid__item m-grid__item--fluid	m-login__wrapper">
               <div class="m-login__container">
                  <div class="m-login__logo">
                     <a href="#">
                     <img src="assets/app/media/img//logos/logo.png" width="300">
                     </a>
                  </div>
                  <div class="m-login__signin">
                     <div class="m-login__head">
                        <h3 class="m-login__title">
                           Redefina sua senha
                        </h3>
                     </div>
                     <form class="m-login__form m-form" id="formResetPassword">
                        <div class="form-group m-form__group">
                          <input class="form-control m-input" type="password" placeholder="Nova senha" id="userPassword" autocomplete="off">
                       </div>
                       <div class="form-group m-form__group">
                          <input class="form-control m-input m-login__form-input--last" type="password" placeholder="Confirme a nova senha" id="userPasswordConfirm">
                        </div>
                        <div class="m-login__form-action">
                           <button class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn">
                           Redefinir
                           </button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end:: Page -->
      <!--begin::Base Scripts -->
      <script src="assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
      <script src="assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
      <!--end::Base Scripts -->
      <!--begin::Page Resources -->
      <script src="assets/demo/default/custom/components/base/toastr.js" type="text/javascript"></script>
      <!--end::Page Resources -->
   </body>
   <!-- end::Body -->
</html>
<script>
   jQuery(document).ready(function() {

   	sessionStorage.setItem("BASE_URL", "http://localhost/");
   	// sessionStorage.setItem("BASE_URL", "http://enote.org.br/");

    let searchParams = new URLSearchParams(window.location.search);
    var valUserRequest;

    if (searchParams.has('r')){
      valUserRequest = searchParams.get('r');
    }

   	var FormValidation = function () {

   		var handleValidation = function() {

   						var form1 = $('#formResetPassword');

   						form1.validate({
   								errorElement: 'span', //default input error message container
   								errorClass: 'help-block help-block-error', // default input error message class
   								focusInvalid: true, // do not focus the last invalid input
   								ignore: "",  // validate all fields including form hidden input
   								rules: {
   										userPassword: {
   												required: true
   										},
   										userPasswordConfirm: {
   												required: true
   										}
   								},

   								invalidHandler: function (event, validator) { //display error alert on form submit
   										toastr.error("<?= $t->{'Preencha os campos corretamente.'}; ?>");
   								},

   								highlight: function (element) { // hightlight error inputs
   										$(element)
   												.closest('.form-group').addClass('has-error'); // set error class to the control group
   								},

   								unhighlight: function (element) { // revert the change done by hightlight
   										$(element)
   												.closest('.form-group').removeClass('has-error'); // set error class to the control group
   								},

   								success: function (label) {
   										label
   												.closest('.form-group').removeClass('has-error'); // set success class to the control group
   								},

   								submitHandler: function (form) {

   									valUserPassword = $("#userPassword").val()

   									if(sessionStorage.getItem("BASE_URL")){
   											var BASE_URL = sessionStorage.getItem("BASE_URL");
   									}

                    if ($("#userPassword").val() == $("#userPasswordConfirm").val()) {

                      $.ajax({
                          url: BASE_URL+'manager/webapi/user/forgotpassword',
                          type: 'POST',
                          contentType: 'application/json',
                          dataType:"json",
                          async: true,
                          data: JSON.stringify({userRequest: valUserRequest, userPassword: valUserPassword}),
                          success: function (result) {
                            var response = result;

                            // console.log(response);

                            if(response["status"] == 1){

                              toastr.success(response["statusMessage"]);

                            }else if(response["status"] == 2){
                              toastr.error(response["statusMessage"]);
                            }
                          }, error: function (result) {
                              toastr.error("Erro no servidor. Tente novamente mais tarde.");
                          }
                      });

                    }else{
                      toastr.error("Os campos de senha devem ser iguais.");
                    }

   								}
   						});
   					}
   				handleValidation();
   		 }();

   });
</script>
