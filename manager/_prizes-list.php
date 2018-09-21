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
                  Listar prêmios dados
                  </span>
                  </a>
               </li>
            </ul>
         </div>
         <a href="main.php?page=prizes-new" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
         <span>
         <i class="fa fa-gift"></i>
         <span>
         Novo prêmio
         </span>
         </span>
         </a>
      </div>
   </div>
   <!-- END: Subheader -->
   <!-- BEGIN: content -->
   <div class="m-content">
      <div class="m-portlet m-portlet--mobile" id="datatablePrizesList">
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
<script>
   jQuery(document).ready(function() {

     if(sessionStorage.getItem("admin")){
         var admin = JSON.parse(sessionStorage.getItem("admin"));
         valLocalId = admin.adminLocalId;
     }

     if(sessionStorage.getItem("BASE_URL")){
   			var BASE_URL = sessionStorage.getItem("BASE_URL");
   	}

    $('#datatablePrizesList').on('click', 'button.btnRemove', function () {

      var valPrizeId = $(this).attr('id');

      swal({
          title: "Atenção",
          text: "Tem certeza de que deseja apagar este prêmio?",
          type: "warning",
          showCancelButton: !0,
          cancelButtonText: "Cancelar",
          confirmButtonText: "Sim"
      }).then(function(e) {

        if (e.dismiss != "cancel") {

          $.ajax({
              url: BASE_URL+'manager/webapi/prize/delete',
              type: 'POST',
              contentType: 'application/json',
              dataType:"json",
              async: true,
              data: JSON.stringify({prizeId: valPrizeId}),
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

     var DatatablePrizesList = {
         init: function() {

             var t;
             t = $(".m_datatable").mDatatable({
                 data: {
                     type: "remote",
                     method: 'GET',
                     source: {
                         read: {
                             url: BASE_URL+"manager/webapi/prize/list/"+valLocalId,
                             method: 'GET',
                             map: function(t) {
                                 var e = t;
                                 return void 0 !== t.prizes && (e = t.prizes ), e
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
                     field: "prizeId",
                     title: "ID",
                     filterable: 1,
                     textAlign: "center",
                     width: 40
                 }, {
                     field: "prizeDate",
                     title: "Data",
                     filterable: 1,
                     width: 180
                 }, {
                     field: "userFullName",
                     title: "Beneficiado",
                     filterable: 1,
                     width: 200
                 }, {
                     field: "adminFullName",
                     title: "Responsábel",
                     filterable: 1,
                     width: 200
                 }, {
                     field: "Actions",
                     width: 80,
                     title: "Ações",
                     sortable: !1,
                     overflow: "visible",
                     textAlign: "center",
                     template: function(t, e, a) {
                         return '<button id="'+t.prizeId+'" class="btnRemove btn m-btn--pill btn-danger btn-sm m-btn m-btn--custom"><i class="fa fa-remove"></i> Apagar</button>'
                     }
                 }]
             })
         }
     };
     jQuery(document).ready(function() {
         DatatablePrizesList.init()
     });


   });
</script>
