<div class="m-grid__item m-grid__item--fluid m-wrapper">
  <!-- BEGIN: Subheader -->
  <div class="m-subheader ">
    <div class="d-flex align-items-center">
      <div class="mr-auto">
        <h3 class="m-subheader__title m-subheader__title--separator">
          Page name
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
                Page name
              </span>
            </a>
          </li>
          <li class="m-nav__separator">
            -
          </li>
          <li class="m-nav__item">
            <a href="" class="m-nav__link">
              <span class="m-nav__link-text">
                Page functionality
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


  </div>
  <!-- END: content -->

</div>
<script>
jQuery(document).ready(function() {

	if(sessionStorage.getItem("local")){
    //pegar dados da sessão do admin e popular os campos necessários

    var local = JSON.parse(sessionStorage.getItem("local"));

    $('#localIdManageLocal').val(local.localId);

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

                  if(sessionStorage.getItem("BASE_URL")){
                			var BASE_URL = sessionStorage.getItem("BASE_URL");
                	}

									$.ajax({
											url: BASE_URL+'manager/webapi/local/info/update',
											type: 'POST',
											contentType: 'application/json',
											dataType:"json",
											async: true,
											data: JSON.stringify({localId: valLocalId}),
											success: function (result) {
												var response = result;

												// console.log(response);

												if(response["status"] == 1){

													local = response["info"];
                          sessionStorage.removeItem("local");
													sessionStorage.setItem("local",JSON.stringify(local));

													toastr.success("Sucesso! Os dados do local foram salvos.");

												}else if(response["status"] == 2){
													toastr.error(response["statusMessage"]);
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
