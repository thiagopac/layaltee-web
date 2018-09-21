<div class="m-grid__item m-grid__item--fluid m-wrapper">
   <!-- BEGIN: Subheader -->
   <div class="m-subheader ">
      <div class="d-flex align-items-center">
         <div class="mr-auto">
            <h3 class="m-subheader__title m-subheader__title--separator">
               Campanhas
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
                  Campanhas
                  </span>
                  </a>
               </li>
               <li class="m-nav__separator">
                  -
               </li>
               <li class="m-nav__item">
                  <a href="" class="m-nav__link">
                  <span class="m-nav__link-text">
                  Editar campanha
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
         <form class="m-form m-form--label-align-right" id="formEditCampaign">
            <div class="m-portlet__body">
               <div class="m-form__section m-form__section--first">
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Título:
                     </label>
                     <div class="col-lg-6">
                        <input type="text" class="form-control m-input" placeholder="Título" id="campaignTitleEditCampaign">
                        <span class="m-form__help">
                        Ex: Nova refeição no cardápio
                        </span>
                     </div>
                  </div>
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Mensagem:
                     </label>
                     <div class="col-lg-6">
                        <textarea class="form-control m-input" rows="6" placeholder="Mensagem" id="campaignMessageEditCampaign"></textarea>
                        <span class="m-form__help">
                        Ex: Agora temos um novo item no cardápio, baseado em sua opinião! Venha conferir!
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
                        <button class="btn btn-primary" id="btnSaveEditCampaign">
                        Salvar
                        </button>
                        <button onclick="window.history.go(-1); return false;" class="btn btn-secondary">
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

     if(sessionStorage.getItem("BASE_URL")){
       var BASE_URL = sessionStorage.getItem("BASE_URL");
     }

     let searchParams = new URLSearchParams(window.location.search);

     if (searchParams.has('campaign-id')){
       let valCampaignId = searchParams.get('campaign-id');

       $.ajax({
     			url: BASE_URL+'manager/webapi/campaign/info/'+valCampaignId,
     			type: 'GET',
     			contentType: 'application/json',
     			dataType:"json",
     			async: true,
     			success: function (result) {
     				var response = result;

     				if(response["status"] == 1){

     					campaign = response["info"];

               $('#campaignTitleEditCampaign').val(campaign.campaignTitle);
               $('#campaignMessageEditCampaign').val(campaign.campaignMessage);

     				}else if(response["status"] == 2){
     					toastr.error(response["statusMessage"]);
     				}
     			}, error: function (result) {
     					toastr.error("Erro no servidor. Tente novamente mais tarde.");
     			}
     	});
     }


     var FormValidation = function () {

   		var handleValidation = function() {

   						var form1 = $('#formEditCampaign');

   						form1.validate({
   								errorElement: 'span', //default input error message container
   								errorClass: 'help-block help-block-error', // default input error message class
   								focusInvalid: true, // do not focus the last invalid input
   								ignore: "",  // validate all fields including form hidden input
   								rules: {
   										campaignTitleEditCampaign: {
   												required: true,
                           max: 100
   										},
                       campaignMessageEditCampaign: {
   												required: true,
                           max: 500
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

                     valCampaignLocalId = campaign.campaignLocalId;
                     valCampaignId = campaign.campaignId;
                     valCampaignDeleted = campaign.campaignDeleted;
                     valCampaignTitle = $("#campaignTitleEditCampaign").val();
   									valCampaignMessage = $("#campaignMessageEditCampaign").val()

                     if(sessionStorage.getItem("BASE_URL")){
                   			var BASE_URL = sessionStorage.getItem("BASE_URL");
                   	}

   									$.ajax({
   											url: BASE_URL+'manager/webapi/campaign/info/update',
   											type: 'POST',
   											contentType: 'application/json',
   											dataType:"json",
   											async: true,
   											data: JSON.stringify({campaignLocalId: valCampaignLocalId, campaignId : valCampaignId, campaignTitle : valCampaignTitle, campaignMessage : valCampaignMessage, campaignDeleted : valCampaignDeleted}),
   											success: function (result) {
   												var response = result;

   												if(response["status"] == 1){

   													// toastr.success("Campanha alterada com sucesso");
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
