<div class="m-grid__item m-grid__item--fluid m-wrapper">
   <!-- BEGIN: Subheader -->
   <div class="m-subheader ">
      <div class="d-flex align-items-center">
         <div class="mr-auto">
            <h3 class="m-subheader__title m-subheader__title--separator">
               Gerenciar local
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
                  Gerenciar local
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
         <form class="m-form m-form--label-align-right" id="formManageLocal">
            <div class="m-portlet__body">
               <div class="m-form__section m-form__section--first">
                  <input type="hidden" class="form-control m-input" placeholder="Nome do local" id="localIdManageLocal">
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Nome do local:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Nome do local" id="localNameManageLocal">
                        <span class="m-form__help">
                        Ex: Restaurante do Thiago
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Rua/Avenida:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Rua/Avenida" id="localStreetManageLocal">
                        <span class="m-form__help">
                        Ex: Rua da Bahia / Av. Afonso Pena
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Número (Endereço):
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Número" id="localNumberManageLocal">
                        <span class="m-form__help">
                        Ex: 1200
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Bairro:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Bairro" id="localNeiborhoodManageLocal">
                        <span class="m-form__help">
                        Ex: Santa Tereza
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Cidade:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Cidade" id="localCityManageLocal">
                        <span class="m-form__help">
                        Ex: Belo Horizonte
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Estado:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Estado" id="localStateManageLocal">
                        <span class="m-form__help">
                        Ex: MG
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     CEP:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="CEP" id="localZipcodeManageLocal">
                        <span class="m-form__help">
                        Ex: 31300-200
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Latitude:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Latitude" id="localLatitudeManageLocal">
                        <span class="m-form__help">
                        Ex: -19.000000
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Longitude:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Longitude" id="localLongitudeManageLocal">
                        <span class="m-form__help">
                        Ex: -43.000000
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Horário de funcionamento:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Horário de funcionamento" id="localOperatingHoursManageLocal">
                        <span class="m-form__help">
                        Ex: Terça-feira à Quinta-feira das 18h às 23h, Sexta-feira, Sábado e Domingo das 18h às 00h
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Contatos:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Contatos" id="localContactsManageLocal">
                        <span class="m-form__help">
                        Ex: (31)3333-3333 | (31)98888-8888
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Oferta de cupons: (Tamanho da cartela)
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Oferta de cupons" id="localCouponsOfferingManageLocal">
                        <span class="m-form__help">
                        Ex: 10
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Prêmio ao completar total de oferta de cupons:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Oferta de cupons" id="localCouponsPrizeManageLocal">
                        <span class="m-form__help">
                        Ex: Uma refeição / Um corte de cabelo
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
                        <button class="btn btn-primary" id="btnSaveManageLocal">
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

   	if(sessionStorage.getItem("local")){
       //pegar dados da sessão do admin e popular os campos necessários

       var local = JSON.parse(sessionStorage.getItem("local"));

       $('#localIdManageLocal').val(local.localId);
       $('#localNameManageLocal').val(local.localName);
       $('#localStreetManageLocal').val(local.localStreet);
       $('#localNumberManageLocal').val(local.localNumber);
       $('#localNeiborhoodManageLocal').val(local.localNeiborhood);
       $('#localCityManageLocal').val(local.localCity)
       $('#localStateManageLocal').val(local.localState)
       $('#localZipcodeManageLocal').val(local.localZipcode)
       $('#localLatitudeManageLocal').val(local.localLatitude);
       $('#localLongitudeManageLocal').val(local.localLongitude);
       $('#localOperatingHoursManageLocal').val(local.localOperatingHours);
       $('#localContactsManageLocal').val(local.localContacts);
       $('#localCouponsOfferingManageLocal').val(local.localCouponsOffering);
       $('#localCouponsPrizeManageLocal').val(local.localCouponsPrize);

   	}

     $("#btnSave").click(function () {
       //apagar dados da sessão do admin e enviar para página de login

     });

     var FormValidation = function () {

   		var handleValidation = function() {

   						var form1 = $('#formManageLocal');

   						form1.validate({
   								errorElement: 'span', //default input error message container
   								errorClass: 'help-block help-block-error', // default input error message class
   								focusInvalid: true, // do not focus the last invalid input
   								ignore: "",  // validate all fields including form hidden input
   								rules: {
   										localNameManageLocal: {
   												required: true
   										},
   										localStreetManageLocal: {
   												required: false
   										},
   										localNumberManageLocal: {
   												required: true
   										},
                       localNeiborhoodManageLocal: {
   												required: true
   										},
                       localCityManageLocal: {
   												required: true
   										},
                       localStateManageLocal: {
   												required: true
   										},
                       localZipcodeManageLocal: {
   												required: true
   										},
                       localLatitudeManageLocal: {
   												required: true
   										},
                       localLongitudeManageLocal: {
   												required: true
   										},
                       localOperatingHoursManageLocal: {
   												required: true
   										},
                       localContactsManageLocal: {
   												required: true
   										},
                       localCouponsOfferingManageLocal: {
   												required: true
   										},
                       localCouponsPrizeManageLocal: {
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

                     valLocalId = $("#localIdManageLocal").val();
   									 valLocalName = $("#localNameManageLocal").val();
   									 valLocalStreet = $("#localStreetManageLocal").val();
                     valLocalNumber = $("#localNumberManageLocal").val();
                     valLocalNeiborhood = $("#localNeiborhoodManageLocal").val();
                     valLocalCity = $("#localCityManageLocal").val();
                     valLocalState = $("#localStateManageLocal").val();
   									 valLocalZipcode = $("#localZipcodeManageLocal").val();
                     valLocalLatitude = $("#localLatitudeManageLocal").val();
                     valLocalLongitude = $("#localLongitudeManageLocal").val();
                     valLocalOperatingHours = $("#localOperatingHoursManageLocal").val();
                     valLocalContacts = $("#localContactsManageLocal").val();
   									 valLocalCouponsOffering = $("#localCouponsOfferingManageLocal").val();
                     valLocalCouponsPrize = $("#localCouponsPrizeManageLocal").val();
                     valLocalDeleted = "0";

                     if(sessionStorage.getItem("BASE_URL")){
                   			var BASE_URL = sessionStorage.getItem("BASE_URL");
                   	}

   									$.ajax({
   											url: BASE_URL+'manager/webapi/local/info/update',
   											type: 'POST',
   											contentType: 'application/json',
   											dataType:"json",
   											async: true,
   											data: JSON.stringify({localId: valLocalId, localName: valLocalName, localStreet : valLocalStreet, localNumber : valLocalNumber, localNeiborhood : valLocalNeiborhood,
                           localCity : valLocalCity, localState : valLocalState, localZipcode : valLocalZipcode, localLatitude : valLocalLatitude, localLongitude : valLocalLongitude,
                           localOperatingHours : valLocalOperatingHours, localContacts : valLocalContacts, localCouponsOffering : valLocalCouponsOffering, localCouponsPrize : valLocalCouponsPrize,
                            localDeleted : valLocalDeleted}),
   											success: function (result) {
   												var response = result;

   												// console.log(response);

   												if(response["status"] == 1){

   													local = response["info"];
                            sessionStorage.removeItem("local");
   													sessionStorage.setItem("local",JSON.stringify(local));

   													// toastr.success("Sucesso! Os dados do local foram salvos.");
                            // swal("Prontinho!", response["statusMessage"], "success");
                            swal({
                                position: "top-right",
                                type: "success",
                                title: response["statusMessage"],
                                showConfirmButton: !1,
                                timer: 1500
                            })

   												}else if(response["status"] == 2){
   													// toastr.error(response["statusMessage"]);
                            // swal("Ops!", response["statusMessage"], "error");
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
