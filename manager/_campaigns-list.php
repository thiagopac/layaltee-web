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
                  Listar campanhas
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
      <div class="m-portlet m-portlet--mobile" id="datatableCampaignsList">
         <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
               <div class="row align-items-center">
                  <div class="col-xl-8 order-2 order-xl-1">
                     <div class="form-group m-form__group row align-items-center">
                        <div class="col-md-4">
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

     if(sessionStorage.getItem("admin")){
         var admin = JSON.parse(sessionStorage.getItem("admin"));
         valLocalId = admin.adminLocalId;
     }

     if(sessionStorage.getItem("BASE_URL")){
   			var BASE_URL = sessionStorage.getItem("BASE_URL");
   	}
    $('#datatableCampaignsList').on('click', 'button.btnEdit', function () {
         var campaignId = $(this).attr('id');
         window.location.href = "main.php?page=campaigns-edit&campaign-id="+campaignId;

    });

    $('#datatableCampaignsList').on('click', 'button.btnRemove', function () {

      var valCampaignId = $(this).attr('id');

      swal({
          title: "Atenção",
          text: "Tem certeza de que deseja apagar esta campanha?",
          type: "warning",
          showCancelButton: !0,
          cancelButtonText: "Cancelar",
          confirmButtonText: "Sim"
      }).then(function(e) {

        if (e.dismiss != "cancel") {

          $.ajax({
              url: BASE_URL+'manager/webapi/campaign/delete',
              type: 'POST',
              contentType: 'application/json',
              dataType:"json",
              async: true,
              data: JSON.stringify({campaignId: valCampaignId}),
              success: function (result) {
                var response = result;

                // console.log(response);

                if(response["status"] == 1){

                  swal({
                      position: "top-right",
                      type: "success",
                      title: response["statusMessage"],
                      showConfirmButton: !1,
                      timer: 1500
                  })

                  $('.m_datatable').mDatatable().reload();

                }else if(response["status"] == 2){

                  toastr.error(response["statusMessage"]);

                }
              }, error: function (result) {
                  toastr.error("Erro no servidor. Tente novamente mais tarde.");
              }
          });
        }

      })
    });

     var DatatableCampaignsList = {
         init: function() {

             var t;
             t = $(".m_datatable").mDatatable({
                 data: {
                     type: "remote",
                     method: 'GET',
                     source: {
                         read: {
                             url: BASE_URL+"manager/webapi/campaign/list/"+valLocalId,
                             method: 'GET',
                             map: function(t) {
                                 var e = t;
                                 return void 0 !== t.campaigns && (e = t.campaigns ), e
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
                     field: "campaignId",
                     title: "ID",
                     filterable: 1,
                     textAlign: "center",
                     width: 40
                 }, {
                     field: "campaignTitle",
                     title: "Título",
                     width: 180
                 }, {
                     field: "campaignMessage",
                     title: "Mensagem",
                     width: 1000
                 }, {
                     field: "Actions",
                     width: 80,
                     title: "Ações",
                     sortable: !1,
                     overflow: "visible",
                     textAlign: "center",
                     template: function(t, e, a) {
                         return '<button id="'+t.campaignId+'" class="btnEdit btn m-btn--pill btn-success btn-sm m-btn m-btn--custom"><i class="fa fa-pencil-square-o"></i> Editar</button><br /><br /><button id="'+t.campaignId+'" class="btnRemove btn m-btn--pill btn-danger btn-sm m-btn m-btn--custom"><i class="fa fa-remove"></i> Apagar</button>'
                     }
                 }]
             })
         }
     };
     jQuery(document).ready(function() {
         DatatableCampaignsList.init()
     });


   });
</script>
