<div class="m-grid__item m-grid__item--fluid m-wrapper">
   <!-- BEGIN: Subheader -->
   <div class="m-subheader ">
      <div class="d-flex align-items-center">
         <div class="mr-auto">
            <h3 class="m-subheader__title m-subheader__title--separator">
               Minha conta
            </h3>
            <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
               <li class="m-nav__item m-nav__item--home">
                  <a href="#" class="m-nav__link m-nav__link--icon">
                  <i class="m-nav__link-icon la la-home"></i>
                  </a>
               </li>
               <li class="m-nav__separator">
                  -
               </li>
               <li class="m-nav__item">
                  <a href="" class="m-nav__link">
                  <span class="m-nav__link-text">
                  Minha conta
                  </span>
                  </a>
               </li>
               <li class="m-nav__separator">
                  -
               </li>
               <li class="m-nav__item">
                  <a href="" class="m-nav__link">
                  <span class="m-nav__link-text">
                  Editar dados
                  </span>
                  </a>
               </li>
            </ul>
         </div>
      </div>
   </div>
   <!-- END: Subheader -->
   <!-- BEGIN: content -->
   <div class="m-content">
      <div class="m-portlet">
         <!--begin::Form-->
         <form class="m-form m-form--label-align-right" id="formMyAccount">
            <div class="m-portlet__body">
               <div class="m-form__section m-form__section--first">
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Login:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Login" id="adminLoginMyAccount" disabled>
                        <span class="m-form__help">
                        Não é possível alterar o login
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Nova senha
                     </label>
                     <div class="col-lg-6">
                        <input type="password" class="form-control m-input" placeholder="Senha" id="adminPasswordMyAccount">
                        <span class="m-form__help">
                        Insira 6 ou mais caracteres
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Confirme a nova senha
                     </label>
                     <div class="col-lg-6">
                        <input type="password" class="form-control m-input" placeholder="Senha" id="adminPasswordConfirmMyAccount">
                        <span class="m-form__help">
                        Certifique-se a confirmação ser igual ao campo Nova senha
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Primeiro nome
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Primeiro nome" id="adminFirstNameMyAccount">
                        <span class="m-form__help">
                        Ex: Thiago
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Último sobrenome
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Último sobrenome" id="adminLastNameMyAccount">
                        <span class="m-form__help">
                        Ex: Castro
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     E-mail
                     </label>
                     <div class="col-lg-6">
                        <input type="email" class="form-control m-input" placeholder="E-mail" id="adminEmailMyAccount">
                        <span class="m-form__help">
                        Ex: contato@thiago.com.br
                        </span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="m-portlet__foot m-portlet__foot--fit">
               <div class="m-form__actions m-form__actions">
                  <div class="row">
                     <div class="col-lg-3"></div>
                     <div class="col-lg-6">
                        <button class="btn btn-primary" id="btnSaveMyAccount">
                        Salvar
                        </button>
                        <button class="btn btn-secondary" onclick="window.history.go(-1); return false;">
                        Cancelar
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </form>
         <!--end::Form-->
      </div>
   </div>
   <!-- END: content -->
</div>
<script>
   jQuery(document).ready(function() {

   	if(sessionStorage.getItem("admin")){
       //pegar dados da sessão do admin e popular os campos necessários

       var admin = JSON.parse(sessionStorage.getItem("admin"));

       $('#adminLoginMyAccount').val(admin.adminLogin);
       $('#adminFirstNameMyAccount').val(admin.adminFirstName);
       $('#adminLastNameMyAccount').val(admin.adminLastName);
       $('#adminEmailMyAccount').val(admin.adminEmail);

   	}

     var FormValidation = function () {

   		var handleValidation = function() {

   						var form1 = $('#formMyAccount');

   						form1.validate({
   								errorElement: 'span', //default input error message container
   								errorClass: 'help-block help-block-error', // default input error message class
   								focusInvalid: true, // do not focus the last invalid input
   								ignore: "",  // validate all fields including form hidden input
   								rules: {
   										adminPasswordMyAccount: {
   												required: false,
                           minlength: 6,
   										},
   										adminPasswordConfirmMyAccount: {
   												required: false,
                           minlength: 6,
   										},
   										adminFirstNameMyAccount: {
   												required: true
   										},
                       adminLastNameMyAccount: {
   												required: true
   										},
                       adminEmailMyAccount: {
   												required: true
   										}
   								},

   								invalidHandler: function (event, validator) { //display error alert on form submit
   										toastr.error("Certifique-se de obedecer a regra de preenchimento de cada campo");
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

   									valAdminLogin = $("#adminLoginMyAccount").val();
   									valAdminPassword = $("#adminPasswordMyAccount").val();
                     valAdminFirstName = $("#adminFirstNameMyAccount").val();
                     valAdminLastName = $("#adminLastNameMyAccount").val();
                     valAdminEmail = $("#adminEmailMyAccount").val();

                     if(sessionStorage.getItem("BASE_URL")){
                   			var BASE_URL = sessionStorage.getItem("BASE_URL");
                   	}

   									$.ajax({
                         url: BASE_URL+'manager/webapi/admin/profile/update',
   											type: 'POST',
   											contentType: 'application/json',
   											dataType:"json",
   											async: true,
   											data: JSON.stringify({adminLogin: valAdminLogin, adminPassword: valAdminPassword, adminFirstName : valAdminFirstName, adminLastName : valAdminLastName, adminEmail : valAdminEmail}),
   											success: function (result) {
   												var response = result;

   												// console.log(response);

   												if(response["status"] == 1){

   													admin = response["admin"];
                            sessionStorage.removeItem("admin");
   													sessionStorage.setItem("admin",JSON.stringify(admin));
                            // swal("Prontinho!", response["statusMessage"], "success");
   													// toastr.success("Sucesso! Seus dados foram salvos.");
                            swal({
                                position: "top-right",
                                type: "success",
                                title: response["statusMessage"],
                                showConfirmButton: !1,
                                timer: 1500
                            })

   												}else if(response["status"] == 2){
                            // swal("Ops!", response["statusMessage"], "error");
   													// toastr.error(response["statusMessage"]);
                            swal({
                                position: "top-right",
                                type: "error",
                                title: response["statusMessage"],
                                showConfirmButton: !1,
                                timer: 1500
                            })
   												}
   											}, error: function (result) {
   													toastr.error("Erro no servidor. Tente novamente mais tarde.");
   											}
   									});

   								}
   						});
   					}
   				handleValidation();
   		 }();

   });
</script>
