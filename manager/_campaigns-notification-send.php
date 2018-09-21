<div class="m-grid__item m-grid__item--fluid m-wrapper">
   <!-- BEGIN: Subheader -->
   <div class="m-subheader ">
      <div class="d-flex align-items-center">
         <div class="mr-auto">
            <h3 class="m-subheader__title m-subheader__title--separator">
               <label id="campaignTitleNotificationSend">Campanhas</label>
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
                  Enviar notificações
                  </span>
                  </a>
               </li>
            </ul>
         </div>
         <a href="main.php?page=campaigns-new" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
         <span>
         <i class="fa fa-paper-plane"></i>
         <span>
         Nova campanha
         </span>
         </span>
         </a>
      </div>
   </div>
   <!-- END: Subheader -->
   <!-- BEGIN: content -->
   <div class="m-content">
      <div class="m-portlet">
         <!--begin::Form-->
         <form class="m-form m-form--label-align-right" id="formCampaignsNotificationSend">
            <div class="m-portlet__body">
               <div class="m-form__section m-form__section--first">
                  <div class="form-group m-form__group row">
                     <label class="col-lg-3 col-form-label">
                     Gêneros:
                     </label>
                     <div class="col-lg-6">
                        <div class="m-form__group form-group">
                           <div class="m-checkbox-list">
                              <label class="m-checkbox m-checkbox--state-primary">
                              <input type="checkbox" id="campaignGenderMale" checked>
                              Masculino
                              <span></span>
                              </label>
                              <label class="m-checkbox m-checkbox--state-danger">
                              <input type="checkbox" id="campaignGenderFemale" checked>
                              Feminino
                              <span></span>
                              </label>
                              <label class="m-checkbox m-checkbox--state-success">
                              <input type="checkbox" id="campaignGenderOthers" checked>
                              Outros
                              <span></span>
                              </label>
                           </div>
                           <span class="m-form__help">
                           Enviar notificação para pessoas do gênero selecionado (Deixe todos os gêneros selecionados para todos usuários receberem)
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="m-form__seperator m-form__seperator--dashed"></div>
                  <br />
                  <div class="form-group m-form__group row">
                     <label class="col-form-label col-lg-3 col-sm-12">
                     Intervalo de idade:
                     </label>
                     <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="row align-items-center">
                           <div class="col-2">
                              <input type="text" class="form-control" id="ageSlider.start"  placeholder="Idade inicial">
                           </div>
                           <div class="col-2">
                              <input type="text" class="form-control" id="ageSlider.stop"  placeholder="Idade final">
                           </div>
                           <div class="col-8">
                              <div id="ageSlider" class="m-nouislider"></div>
                           </div>
                        </div>
                        <span class="m-form__help">
                        Enviar notificação para pessoas com idade entre o intervalo selecionado (Deixe selecionado 0 a 100 para todos usuários receberem)
                        </span>
                     </div>
                  </div>
                  <div class="m-form__seperator m-form__seperator--dashed"></div>
                  <div class="form-group m-form__group row">
                     <label class="col-form-label col-lg-3 col-sm-12">
                     Data do envio:
                     </label>
                     <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="input-group date">
                           <input type="text" class="form-control" id="deliveryDatepicker" readonly placeholder="Dia e horário" disabled/>
                           <div class="input-group-append">
                              <span class="input-group-text">
                              <i class="la la-calendar glyphicon-th"></i>
                              </span>
                           </div>
                        </div>
                        <span class="m-form__help">
                        <span class="m-badge m-badge--danger m-badge--wide m-badge--rounded"><strong>Atenção</strong></span> Ainda não é possível agendar notificações, toda notificação é enviada instantaneamente.
<!-- Escolha o dia e horário para a notificação ser enviada para o usuário (Deixe selecionado a data inicial para envio instantâneo) -->
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
                        <button class="btn btn-primary" id="btnReachCampaignsNotification">
                        Calcular alcance
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
   var deliveryDatepicker = {
       init: function() {
         $("#deliveryDatepicker").datetimepicker({
             todayHighlight: !0,
             autoclose: !0,
             todayBtn: !0,
             format: "yyyy-mm-dd hh:ii:ss"
         })
       }
   };

   var ageSlider = {
     init: function() {

       ! function() {
           var e = document.getElementById("ageSlider");
           noUiSlider.create(e, {
               start: [0, 100],
               connect: !0,
               direction: "ltr",
               tooltips: [wNumb({decimals: 0}), wNumb({decimals: 0})],
               range: {
                   min: 0,
                   max: 100
               }
           });
           var n = document.getElementById("ageSlider.stop"),
               t = [document.getElementById("ageSlider.start"), n];
               e.noUiSlider.on("update", function(e, n) {
               t[n].value = parseFloat(Math.round(e[n] * 100) / 100).toFixed(0);
               // t[n].value = e[n]

           })
       }()

       }
   };

   jQuery(document).ready(function() {

     ageSlider.init();
     deliveryDatepicker.init();

     var isPastOuFuture = "past";

     $("#deliveryDatepicker").datetimepicker("setDate", new Date());

     var searchParams = new URLSearchParams(window.location.search);

     if (searchParams.has('campaign-id')){
       var getCampaignId = searchParams.get('campaign-id');
     }

     if (searchParams.has('campaign-title')){
       var getCampaignTitle = searchParams.get('campaign-title');
       $("#campaignTitleNotificationSend").html("Campanhas - [ " + getCampaignTitle + " ]");
     }

   	if(sessionStorage.getItem("local")){
       var local = JSON.parse(sessionStorage.getItem("local"));
   	}

     $('#deliveryDatepicker').datetimepicker().change(evt => {
     var selectedDate = $('#deliveryDatepicker').datetimepicker('getDate');
     var now = new Date();
     now.setHours(now.getHours());
     if (selectedDate < now) {
       // console.log("É PASSADO");

       $("#deliveryDatepicker").datetimepicker("setDate", new Date());
       toastr.error("Datas ou horários no passado serão alterados para o dia e horário de agora");

       isPastOuFuture = "past";

     } else {

       isPastOuFuture = "future";
       // console.log("É FUTURO");
     }
   });


     var FormValidation = function () {

   		var handleValidation = function() {

   						var form1 = $('#formCampaignsNotificationSend');

   						form1.validate({

   								submitHandler: function (form) {

                     if(sessionStorage.getItem("BASE_URL")){
                   			var BASE_URL = sessionStorage.getItem("BASE_URL");
                   	}

                     var valLocalId = local.localId;

                     var valcampaignDeliveryDate = $("#deliveryDatepicker").val();
                     var valCampaignAgeStart = document.getElementById("ageSlider.start").value;
                     var valCampaignAgeStop = document.getElementById("ageSlider.stop").value;

                     var valCampaignGenderMale = $("#campaignGenderMale").prop('checked') == true ? "M" : "";
                     var valCampaignGenderFemale = $("#campaignGenderFemale").prop('checked') == true ? "F" : "";
                     var valCampaignGenderOthers = $("#campaignGenderOthers").prop('checked') == true ? "O" : "";

                     var valCampaignId = getCampaignId;

                     // console.log("Local ID: " + valLocalId);
                     //
                     // console.log("Campaign ID: " + valCampaignId);
                     //
                     // console.log("Masculino: " + valCampaignGenderMale);
                     // console.log("Feminino: " + valCampaignGenderFemale);
                     // console.log("Outros: " + valCampaignGenderOthers);
                     //
                     // console.log("DeliveryDate: " + valcampaignDeliveryDate);
                     // console.log("AgeStart: " + valCampaignAgeStart);
                     // console.log("AgeStop: " + valCampaignAgeStop);
                     //
                     // console.log("JSON: " + JSON.stringify({localId: valLocalId, campaignNotificationGenderMale: valCampaignGenderMale, campaignNotificationGenderFemale : valCampaignGenderFemale,
                     // campaignNotificationGenderOthers : valCampaignGenderOthers, campaignAgeStart : valCampaignAgeStart, campaignAgeStop : valCampaignAgeStop}));

   									$.ajax({
   											url: BASE_URL+'manager/webapi/campaign/notification/calculate',
   											type: 'POST',
   											contentType: 'application/json',
   											dataType:"json",
   											async: true,
   											data: JSON.stringify({localId: valLocalId, campaignId : valCampaignId, campaignNotificationGenderMale: valCampaignGenderMale, campaignNotificationGenderFemale : valCampaignGenderFemale,
                         campaignNotificationGenderOthers : valCampaignGenderOthers, campaignAgeStart : valCampaignAgeStart, campaignAgeStop : valCampaignAgeStop}),
   											success: function (result) {
   												var response = result;

   												// console.log(response);

   												if(response["status"] == 1){

                             var usuarios = response["usersCount"] == 1 ? "usuário" : "usuários";
                             var receberao = response["usersCount"] == 1 ? "receberá" : "receberão";

   													//mostrar cálculo
                             swal({
                                 title: "De acordo com sua seleção, " + response["usersCount"] + " " + usuarios + " " + receberao + " a notificação.",
                                 text: "Tem certeza de que deseja enviar a notificação para esta campanha?",
                                 type: "info",
                                 showCancelButton: !0,
                                 cancelButtonText: "Cancelar",
                                 confirmButtonText: "Sim"
                             }).then(function(e) {

                               if (e.dismiss != "cancel") {
                                 $.ajax({
                                     url: BASE_URL+'manager/webapi/campaign/notification/send',
                                     type: 'POST',
                                     contentType: 'application/json',
                                     dataType:"json",
                                     async: true,
                                     data: JSON.stringify({localId: valLocalId, campaignId : valCampaignId, campaignNotificationGenderMale: valCampaignGenderMale, campaignNotificationGenderFemale : valCampaignGenderFemale,
                                     campaignNotificationGenderOthers : valCampaignGenderOthers, campaignAgeStart : valCampaignAgeStart, campaignAgeStop : valCampaignAgeStop, campaignDeliveryDate : valcampaignDeliveryDate}),
                                     success: function (result) {
                                       var response = result;

                                       // console.log(response);

                                       if(response["status"] == 1){

                                         if (isPastOuFuture == "past") {
                                           swal({
                                               position: "top-right",
                                               type: "success",
                                               title: response["statusMessage"],
                                               showConfirmButton: !1,
                                               timer: 1500
                                           })
                                         }else if(isPastOuFuture == "future"){
                                           swal({
                                               position: "top-right",
                                               type: "success",
                                               title: "As notificações foram gravadas para serem enviadas no dia e horário escolhidos",
                                               showConfirmButton: !1,
                                               timer: 2500
                                           })
                                         }


                                       }else if(response["status"] == 2){

                                         // toastr.error(response["statusMessage"]);
                                         swal({
                                             position: "top-right",
                                             type: "warning",
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

                             })

   												}else if(response["status"] == 2){
   													// toastr.error(response["statusMessage"]);

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
