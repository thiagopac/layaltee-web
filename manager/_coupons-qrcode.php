<?php

  date_default_timezone_set("America/Sao_Paulo");
?>
<link href="qrcode/styles.css" rel="stylesheet">
<script src="qrcode/kjua-0.1.1.min.js"></script>
<script src="qrcode/construct-encoded.js"></script>
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
                  QR-Code
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
      <div class="m-portlet m-portlet--creative m-portlet--first m-portlet--bordered-semi">
         <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
               <div class="m-portlet__head-title">
                  <span class="m-portlet__head-icon m--hide">
                  <i class="flaticon-statistics"></i>
                  </span>
                  <h3 class="m-portlet__head-text">
                     Certifique-se que este QR-Code seja usado na data correta, ele só é válido no dia <strong><?=date('d/m/Y');?></strong>. O QR-Code só pode ser usado pelo mesmo usuário uma vez no dia.
                  </h3>
                  <h2 class="m-portlet__head-label m-portlet__head-label--danger">
                     <span>
                     Válido para o dia: <strong><?=date('d/m/Y');?></strong>
                     </span>
                  </h2>
               </div>
            </div>
         </div>
         <div class="m-portlet__body">
            <div id="container"></div>
            <div class="control right">
               <div id="image"></div>
            </div>
         </div>
      </div>
   </div>
   <!-- END: content -->
</div>
<script>
   jQuery(document).ready(function() {

   	if(sessionStorage.getItem("local")){
       var local = JSON.parse(sessionStorage.getItem("local"));
       $('#localIdManageLocal').val(local.localId);
   	}

   });

   (function () {

       var constructEncoded = constructPass();
       // console.log(constructEncoded);

       var win = window;
       var doc = win.document;

       function elById(id) {
           return doc.getElementById(id);
       }

       function onEvent(el, type, fn) {
           el.addEventListener(type, fn);
       }

       function onReady(fn) {
           onEvent(doc, 'DOMContentLoaded', fn);
       }

       function forEach(list, fn) {
           Array.prototype.forEach.call(list, fn);
       }



       var width = $(document).width();
       if (width > 1500) {
         width = width/4;
       }else{
         width = width/2.5;
       }

       function updateQrCode() {
           var options = {
               render: image,
               crisp: false,
               ecLevel: 'H',
               minVersion: 1,

               fill: '#333',
               text: constructEncoded,
               size: width,
               rounded: 100,
               quiet: 2,

               mode: 'label',

               mSize: 30,
               mPosX: 50,
               mPosY: 50,

               label: '',
               fontname: 'sans',
               fontcolor: '#ff0000',


           };

           var container = elById('container');
           var qrcode = kjua(options);
           forEach(container.childNodes, function (child) {
               container.removeChild(child);
           });
           if (qrcode) {
               container.appendChild(qrcode);
           }
       }

       function update() {
           updateQrCode();
       }

       onReady(function () {
           onEvent(win, 'load', update);
           // update();
       });




   }());


</script>
