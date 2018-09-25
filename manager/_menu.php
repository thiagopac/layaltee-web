<?php
   $page = $_GET["page"];
   $activeItem = "m-menu__item--open m-menu__item--expanded";
   $activeSubItem = "m-menu__item--active";
?>
<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
   <li class="m-menu__item <?=$page == "dashboard" ? $activeItem : ""?>" aria-haspopup="true" >
      <a  href="main.php?page=dashboard" class="m-menu__link ">
      <span class="m-menu__item-here"></span>
      <i class="m-menu__link-icon fa fa-dashboard"></i>
      <span class="m-menu__link-text">
      Dashboard
      </span>
      </a>
   </li>
   <li class="m-menu__item  <?=$page == "manage-local" ? $activeItem : ""?>" aria-haspopup="true"  m-menu-submenu-toggle="hover">
      <a  href="main.php?page=manage-local" class="m-menu__link">
      <span class="m-menu__item-here"></span>
      <i class="m-menu__link-icon fa fa-briefcase"></i>
      <span class="m-menu__link-text">
      Gerenciar local
      </span>
      <i class="m-menu__ver-arrow la la-angle-right"></i>
      </a>
   </li>
<!--    <li class="m-menu__item  --><?//=$page == "visual-settings" ? $activeItem : ""?><!--" aria-haspopup="true"  m-menu-submenu-toggle="hover">-->
<!--        <a  href="main.php?page=visual-settings" class="m-menu__link">-->
<!--            <span class="m-menu__item-here"></span>-->
<!--            <i class="m-menu__link-icon fa fa-eye"></i>-->
<!--            <span class="m-menu__link-text">-->
<!--      Ajustes visuais-->
<!--      </span>-->
<!--            <i class="m-menu__ver-arrow la la-angle-right"></i>-->
<!--        </a>-->
<!--    </li>-->
   <li class="m-menu__item  m-menu__item--submenu <?=$page == "coupons-qrcode" || $page == "coupons-manual" || $page == "coupons-verify" ? $activeItem : ""?>" aria-haspopup="true"  m-menu-submenu-toggle="hover">
      <a  href="javascript:;" class="m-menu__link m-menu__toggle">
      <span class="m-menu__item-here"></span>
      <i class="m-menu__link-icon fa fa-qrcode"></i>
      <span class="m-menu__link-title">
      <span class="m-menu__link-wrap">
      <span class="m-menu__link-text">
      Cupons
      </span>
      </span>
      </span>
      <i class="m-menu__ver-arrow la la-angle-right"></i>
      </a>
      <div class="m-menu__submenu ">
         <span class="m-menu__arrow"></span>
         <ul class="m-menu__subnav">
            <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true" >
               <span class="m-menu__link">
               <span class="m-menu__item-here"></span>
               <span class="m-menu__link-title">
               <span class="m-menu__link-wrap">
               <span class="m-menu__link-text">
               Cupons
               </span>
               </span>
               </span>
               </span>
            </li>
            <li class="m-menu__item <?=$page == "coupons-qrcode" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=coupons-qrcode" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               QR-Code
               </span>
               </a>
            </li>
            <li class="m-menu__item  <?=$page == "coupons-manual" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=coupons-manual" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               Cupom manual
               </span>
               </a>
            </li>
            <li class="m-menu__item <?=$page == "coupons-verify" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=coupons-verify" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               Verificar cupons
               </span>
               </a>
            </li>
         </ul>
      </div>
   </li>
   <li class="m-menu__item  m-menu__item--submenu <?=$page == "prizes-new" || $page == "prizes-list" ? $activeItem : ""?>" aria-haspopup="true"  m-menu-submenu-toggle="hover">
      <a  href="javascript:;" class="m-menu__link m-menu__toggle">
      <span class="m-menu__item-here"></span>
      <i class="m-menu__link-icon fa fa-gift"></i>
      <span class="m-menu__link-title">
      <span class="m-menu__link-wrap">
      <span class="m-menu__link-text">
      Prêmios
      </span>
      </span>
      </span>
      <i class="m-menu__ver-arrow la la-angle-right"></i>
      </a>
      <div class="m-menu__submenu ">
         <span class="m-menu__arrow"></span>
         <ul class="m-menu__subnav">
            <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true" >
               <span class="m-menu__link">
               <span class="m-menu__item-here"></span>
               <span class="m-menu__link-title">
               <span class="m-menu__link-wrap">
               <span class="m-menu__link-text">
               Prêmios
               </span>
               </span>
               </span>
               </span>
            </li>
            <li class="m-menu__item  <?=$page == "prizes-new" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=prizes-new" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               Novo prêmio
               </span>
               </a>
            </li>
            <li class="m-menu__item <?=$page == "prizes-list" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=prizes-list" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               Listar prêmios dados
               </span>
               </a>
            </li>
         </ul>
      </div>
   </li>
   <li class="m-menu__item  m-menu__item--submenu <?=$page == "campaigns-new" || $page == "campaigns-list" || $page == "campaigns-edit" || $page == "campaigns-notification" ? $activeItem : ""?>" aria-haspopup="true"  m-menu-submenu-toggle="hover">
      <a  href="javascript:;" class="m-menu__link m-menu__toggle">
      <span class="m-menu__item-here"></span>
      <i class="m-menu__link-icon fa fa-paper-plane"></i>
      <span class="m-menu__link-title">
      <span class="m-menu__link-wrap">
      <span class="m-menu__link-text">
      Campanhas
      </span>
      </span>
      </span>
      <i class="m-menu__ver-arrow la la-angle-right"></i>
      </a>
      <div class="m-menu__submenu ">
         <span class="m-menu__arrow"></span>
         <ul class="m-menu__subnav">
            <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true" >
               <span class="m-menu__link">
               <span class="m-menu__item-here"></span>
               <span class="m-menu__link-title">
               <span class="m-menu__link-wrap">
               <span class="m-menu__link-text">
               Campanhas
               </span>
               </span>
               </span>
               </span>
            </li>
            <li class="m-menu__item <?=$page == "campaigns-new" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=campaigns-new" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               Criar nova
               </span>
               </a>
            </li>
            <li class="m-menu__item  <?=$page == "campaigns-list" || $page == "campaigns-edit" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=campaigns-list" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               Listar campanhas
               </span>
               </a>
            </li>
            <li class="m-menu__item <?=$page == "campaigns-notification" ? $activeSubItem : ""?>" aria-haspopup="true" >
               <a  href="main.php?page=campaigns-notification" class="m-menu__link ">
               <i class="m-menu__link-bullet m-menu__link-bullet--dot">
               <span></span>
               </i>
               <span class="m-menu__link-text">
               Enviar notificações
               </span>
               </a>
            </li>
         </ul>
      </div>
   </li>
    <li id="menu-unidades" style="visibility: hidden;" class="m-menu__item  m-menu__item--submenu <?=$page == "units-new" || $page == "units-list" || $page == "units-edit"?>" aria-haspopup="true"  m-menu-submenu-toggle="hover">
        <a  href="javascript:;" class="m-menu__link m-menu__toggle">
            <span class="m-menu__item-here"></span>
            <i class="m-menu__link-icon fa fa-map-marker"></i>
            <span class="m-menu__link-title">
      <span class="m-menu__link-wrap">
      <span class="m-menu__link-text">
      Unidades
      </span>
      </span>
      </span>
            <i class="m-menu__ver-arrow la la-angle-right"></i>
        </a>
        <div class="m-menu__submenu ">
            <span class="m-menu__arrow"></span>
            <ul class="m-menu__subnav">
                <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true" >
               <span class="m-menu__link">
               <span class="m-menu__item-here"></span>
               <span class="m-menu__link-title">
               <span class="m-menu__link-wrap">
               <span class="m-menu__link-text">
               Unidades
               </span>
               </span>
               </span>
               </span>
                </li>
                <li class="m-menu__item <?=$page == "units-new" ? $activeSubItem : ""?>" aria-haspopup="true" >
                    <a  href="main.php?page=units-new" class="m-menu__link ">
                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                            <span></span>
                        </i>
                        <span class="m-menu__link-text">
               Criar nova
               </span>
                    </a>
                </li>
                <li class="m-menu__item  <?=$page == "units-list" || $page == "units-edit" ? $activeSubItem : ""?>" aria-haspopup="true" >
                    <a  href="main.php?page=units-list" class="m-menu__link ">
                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                            <span></span>
                        </i>
                        <span class="m-menu__link-text">
               Listar unidades
               </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
<script>

    //REMOVE FROM PROD AFTER UPGRADE
    jQuery(document).ready(function() {

        if(sessionStorage.getItem("local")) {
            //pegar dados da sessão do admin e popular os campos necessários

            var local = JSON.parse(sessionStorage.getItem("local"));
            
            if (local.localId == 1){

                console.log(local.localId);


                $("#menu-unidades").attr("style", "visibility: visible");

            }

        }
    });
</script>