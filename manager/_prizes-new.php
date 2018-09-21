<script src="qrcode/construct-encoded.js"></script>
<div class="m-grid__item m-grid__item--fluid m-wrapper">
   <!-- BEGIN: Subheader -->
   <div class="m-subheader ">
      <div class="d-flex align-items-center">
         <div class="mr-auto">
            <h3 class="m-subheader__title m-subheader__title--separator">
               Prêmios
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
                  Prêmios
                  </span>
                  </a>
               </li>
               <li class="m-nav__separator">
                  -
               </li>
               <li class="m-nav__item">
                  <a href="" class="m-nav__link">
                  <span class="m-nav__link-text">
                  Novo
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
         <div class="m-portlet m-portlet--tab">
            <!--begin::Form-->
            <form class="m-form m-form--fit m-form--label-align-right" id="formPrizesNew">
               <div class="m-portlet__body">
                  <div class="row align-items-center">
                     <div class="col-xl-4 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                           <div class="col-md-12">
                              <div class="form-group m-form__group">
                                 <label for="exampleInputEmail1">
                                 Insira o código do usuário para conceder o prêmio
                                 </label>
                                 <div class="input-group input-group-lg m-input-group">
                                    <div class="input-group-prepend">
                                       <span class="input-group-text" id="basic-addon1">
                                       <i class="la la-font"></i>
                                       </span>
                                    </div>
                                    <input type="text" class="form-control m-input" style="text-transform: uppercase;" placeholder="Código" id="userCodeCouponsManual">
                                    <div class="input-group-append">
   													          <button class="btn btn-primary" type="button">
                                         <i class="flaticon-search"></i>
   													           </button>
   												           </div>
                                 </div>
                                 <span class="m-form__help">
                                 O código pode ser encontrado no aplicativo, em MENU > MINHA CONTA > USUÁRIO. (Ex: Cód: AAAXXX) O código posui 6 letras aleatórias.
                                 </span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-xl-8 order-1 order-xl-2 m--align-left">
                        <h1 id="labelFirstNameLastName"></h1>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                     </div>
                  </div>
               </div>
               <div class="form-group m-form__group">
                  <span class="m-badge m-badge--danger m-badge--wide m-badge--rounded"><strong>Atenção</strong></span> Ao conceder um prêmio a um usuário, o administrador é registrado na premiação, para fins de auditoria.
               </div>
               <div class="m-portlet__foot m-portlet__foot--fit">
                  <div class="m-form__actions">
                     <button class="btn btn-primary">
                     Conceder prêmio
                     </button>
                     <button onclick="window.history.go(-1); return false;" class="btn btn-secondary">
                     Cancelar
                     </button>
                  </div>
               </div>
            </form>
            <!--end::Form-->
         </div>
      </div>
   </div>
   <!-- END: content -->
</div>
<script>
   jQuery(document).ready(function() {

     var constructEncoded = constructPass();

     if(sessionStorage.getItem("BASE_URL")){
         var BASE_URL = sessionStorage.getItem("BASE_URL");
     }

   	if(sessionStorage.getItem("local")){
         var local = JSON.parse(sessionStorage.getItem("local"));
   	}

     if(sessionStorage.getItem("admin")){
         var admin = JSON.parse(sessionStorage.getItem("admin"));
     }

     var user = null;

     $("#userCodeCouponsManual").change(function(){

            var valUserCode = $(this).val().trim();
            valUserCode = valUserCode.replace(/\s+/g, '');

            if(valUserCode.length % 6 == 0 && valUserCode.length > 0) {

              $.ajax({
                  url: BASE_URL+'manager/webapi/user/profile/'+valUserCode+'/'+local.localId,
                  type: 'GET',
                  contentType: 'application/json',
                  dataType:"json",
                  async: true,
                  data: JSON.stringify({localId: valLocalId}),
                  success: function (result) {
                    var response = result;

                    // console.log(response);

                    if(response["status"] == 1){

                      user = response["user"];

                      $("#labelFirstNameLastName").html("Usuário: "+user.userFirstName+" "+user.userLastName+" ("+user.userLogin+") - <strong>Cupons válidos: </strong>"+user.validCheckins);

                    }else if(response["status"] == 2){

                      $("#labelFirstNameLastName").html(response["statusMessage"]);
                    }
                  }, error: function (result) {
                      toastr.error("Erro no servidor. Tente novamente mais tarde.");
                  }
              });

            }else{
              $("#labelFirstNameLastName").html("");
            }

       });


     var FormValidation = function () {

   		var handleValidation = function() {

   						var form1 = $('#formPrizesNew');

   						form1.validate({
   								errorElement: 'span', //default input error message container
   								errorClass: 'help-block help-block-error', // default input error message class
   								focusInvalid: true, // do not focus the last invalid input
   								rules: {
   										userCodeCouponsManual: {
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

   								submitHandler: function (form1) {

                     if(sessionStorage.getItem("BASE_URL")){
                   			var BASE_URL = sessionStorage.getItem("BASE_URL");
                   	}

   									$.ajax({
   											url: BASE_URL+'manager/webapi/prize/add',
   											type: 'POST',
   											contentType: 'application/json',
   											dataType:"json",
   											async: true,
   											data: JSON.stringify({localId: local.localId, userId : user.userId, adminId : admin.adminId}),
   											success: function (result) {
   												var response = result;

   												if(response["status"] == 1){

   													// toastr.success(response["statusMessage"]);
                            swal({
                                position: "top-right",
                                type: "success",
                                title: response["statusMessage"],
                                showConfirmButton: !1,
                                timer: 1500
                            })

   												}else if(response["status"] == 2){
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
