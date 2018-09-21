<div class="m-grid__item m-grid__item--fluid m-wrapper">
   <!-- BEGIN: Subheader -->
   <div class="m-subheader ">
      <div class="d-flex align-items-center">
         <div class="mr-auto">
            <h3 class="m-subheader__title m-subheader__title--separator">
               Cupons
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
                  Cupons
                  </span>
                  </a>
               </li>
               <li class="m-nav__separator">
                  -
               </li>
               <li class="m-nav__item">
                  <a href="" class="m-nav__link">
                  <span class="m-nav__link-text">
                  Verificar
                  </span>
                  </a>
               </li>
            </ul>
         </div>
         <a href="main.php?page=coupons-manual" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
         <span>
         <i class="fa fa-qrcode"></i>
         <span>
         Cupom manual
         </span>
         </span>
         </a>
      </div>
   </div>
   <!-- END: Subheader -->
   <!-- BEGIN: content -->
   <div class="m-content">
      <div class="m-portlet m-portlet--tab">
         <!--begin::Form-->
         <form class="m-form m-form--fit m-form--label-align-right" id="formCouponsManual">
            <div class="m-portlet__body">
               <div class="row align-items-center">
                  <div class="col-xl-4 order-2 order-xl-1">
                     <div class="form-group m-form__group row align-items-center">
                        <div class="col-md-12">
                           <div class="form-group m-form__group">
                              <label for="exampleInputEmail1">
                              Insira o código do usuário para verificar seus cupons
                              </label>
                              <div class="input-group input-group-lg m-input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                    <i class="la la-font"></i>
                                    </span>
                                 </div>
                                 <input type="text" class="form-control m-input" style="text-transform: uppercase;" placeholder="Código" id="userCodeCouponsVerify">
                                 <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btnFindCouponsVerify">
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
         </form>
         <!--end::Form-->
      </div>
      <div class="m-portlet m-portlet--mobile" id="dataTableCouponsVerify">
         <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
               <div class="row align-items-center">
                  <div class="col-xl-6 order-2 order-xl-1">
                     <div class="form-group m-form__group row align-items-center">
                        <div class="col-md-6">
                           <div class="m-input-icon m-input-icon--left">
                              <input type="text" class="form-control m-input" placeholder="Buscar..." id="generalSearch">
                              <span class="m-input-icon__icon m-input-icon__icon--left">
                              <span>
                              <i class="la la-search"></i>
                              </span>
                              </span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                  <h5 id="labelValidCheckinsCouponsVerify"></h5>
                </div>
                  <div class="col-xl-2 order-1 order-xl-2 m--align-right">
                    <a href="#" class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" id="btnRefreshCouponsVerify">
                     <span>
                     <i class="flaticon-refresh"></i>
                     <span>
                     Recarregar
                     </span>
                     </span>
                     </a>
                     <div class="m-separator m-separator--dashed d-xl-none"></div>
                  </div>
               </div>
            </div>
            <!--end: Search Form -->
            <!--begin: Datatable -->
            <div class="m_datatable" id="ajax_data"></div>
            <!--end: Datatable -->
         </div>
      </div>
   </div>
   <!-- END: content -->
</div>
<!-- <script src="assets/demo/default/custom/components/datatables/base/data-ajax.js" type="text/javascript"></script> -->
<script>
   jQuery(document).ready(function() {

     var user = null;

     if(sessionStorage.getItem("admin")){
         var admin = JSON.parse(sessionStorage.getItem("admin"));
         valLocalId = admin.adminLocalId;
     }

     if(sessionStorage.getItem("local")){
        var local = JSON.parse(sessionStorage.getItem("local"));
        valLocalCouponsOffering = local.localCouponsOffering;
      }

     if(sessionStorage.getItem("BASE_URL")){
         var BASE_URL = sessionStorage.getItem("BASE_URL");
     }

     $("#btnFindCouponsVerify").click(function() {

     });

     $("#btnRefreshCouponsVerify").click(function() {
       location.reload();
     });

     $("#dataTableCouponsVerify").hide();


     $("#userCodeCouponsVerify").change(function(){

            valUserCode = $(this).val().trim();
            valUserCode = valUserCode.replace(/\s+/g, '');

            if(valUserCode.length % 6 == 0 && valUserCode.length > 0) {

              getCouponsData(valUserCode);

            }else{
              $("#labelFirstNameLastName").html("");
              $("#dataTableCouponsVerify").hide();

            }

       });

       function getCouponsData(valUserCode){
         $("#dataTableCouponsVerify").show();

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

                 $("#labelFirstNameLastName").html("Usuário: "+user.userFirstName+" "+user.userLastName+" ("+user.userLogin+")");
                 getDataTableForUser(user);

                 $("#userCodeCouponsVerify").attr('disabled','disabled');

               }else if(response["status"] == 2){

                 $("#labelFirstNameLastName").html(response["statusMessage"]);
               }
             }, error: function (result) {
                 toastr.error("Erro no servidor. Tente novamente mais tarde.");
             }
         });
       }

       var validCheckins = 0;

       $('#dataTableCouponsVerify').on('click', 'button.btnCoupon', function () {
            var checkinId = $(this).attr('id');

            if ($(this).attr("data-consumed") == 0) {

              validCheckins = validCheckins - 1;

              $(this).html("Marcar não-consumido");
              $(this).attr("data-consumed", 1);
              $("#span"+checkinId).html("Sim");
              $("#span"+checkinId).addClass("m-badge--danger");
              $("#span"+checkinId).removeClass("m-badge--success");
              $("#labelValidCheckinsCouponsVerify").html("Cupons válidos: " + validCheckins + "/" + valLocalCouponsOffering);
            }else{

              validCheckins = validCheckins + 1;

              $(this).html("Marcar consumido");
              $(this).attr("data-consumed", 0);
              $("#span"+checkinId).html("Não");
              $("#span"+checkinId).addClass("m-badge--success");
              $("#span"+checkinId).removeClass("m-badge--danger");
              $("#labelValidCheckinsCouponsVerify").html("Cupons válidos: " + validCheckins + "/" + valLocalCouponsOffering);
            }

            $.ajax({
                url: BASE_URL+'manager/webapi/checkin/consume',
                type: 'POST',
                contentType: 'application/json',
                dataType:"json",
                async: true,
                data: JSON.stringify({checkinId: checkinId}),
                success: function (result) {
                  var response = result;

                  // console.log(response);

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

       });

     function getDataTableForUser(user){

       var DatatableCouponsVerify = {

           init: function() {
               var t;

               var options = {
                   data: {
                       type: "remote",
                       method: 'GET',
                       source: {
                           read: {
                               url: BASE_URL+"manager/webapi/checkin/list/"+user.userId+"/"+local.localId,
                               method: 'GET',
                               map: function(t) {
                                   var e = t;
                                   return void 0 !== t.checkins && (e = t.checkins ), e
                               }
                           }
                       },
                       pageSize: 10,
                       serverPaging: 0,
                       serverFiltering: 0,
                       serverSorting: 0
                   },
                   layout: {
                       scroll: !1,
                       footer: !1
                   },
                   sortable: 1,
                   pagination: 1,
                   toolbar: {
                       items: {
                           pagination: {
                               pageSizeSelect: [10, 20, 30, 50, 100]
                           }
                       }
                   },
                   search: {
                       input: $("#generalSearch")
                   },
                   columns: [{
                       field: "date",
                       title: "Data",
                       filterable: 1,
                       width: 180,
                       template: function(t){

                         var date = new Date(t.date);
                         return date.toLocaleString("en-GB");
                       }
                   }, {
                       field: "checkinType",
                       title: "Tipo",
                       width: 180
                   }, {
                       field: "checkinIntermediary",
                       title: "Responsável",
                       width: 300
                   }, {
                       field: "consumed",
                       title: "Consumido",
                       width: 180,
                       textAlign: "center",
                       template: function(t) {
                           var e = {
                               0: {
                                   title: "Não",
                                   class: "m-badge--success"
                               },
                               1: {
                                   title: "Sim",
                                   class: " m-badge--danger"
                               }
                           };
                           return '<span data-consumed="'+t.consumed+'" id="span'+t.checkinId+'" class="font-weight-bold m-badge ' + e[t.consumed].class + ' m-badge--wide">' + e[t.consumed].title + '</span>'
                       }
                   }, {
                                      field: "Actions",
                                      width: 160,
                                      title: "Ações",
                                      sortable: !1,
                                      overflow: "visible",
                                      textAlign: "center",
                                      template: function(t, e, a) {
                                        var e = {
                                            0: {
                                                title: "Marcar consumido"
                                            },
                                            1: {
                                                title: "Marcar não-consumido"
                                            }
                                        };
                                          validCheckins = a.lastResponse.validCheckins;
                                          $("#labelValidCheckinsCouponsVerify").html("Cupons válidos: " + validCheckins + "/" + valLocalCouponsOffering);
                                          return '<button id="'+t.checkinId+'" data-consumed="'+t.consumed+'" class="btnCoupon btn m-btn--pill btn-primary btn-sm m-btn m-btn--custom">' + e[t.consumed].title + '</button>'
                                      }
                                  }]
               };


               t = $(".m_datatable").mDatatable(options);
           }
       };

       jQuery(document).ready(function() {
           DatatableCouponsVerify.init()
       });
     }


   });
</script>
