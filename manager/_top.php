<div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general">
   <div class="m-stack__item m-topbar__nav-wrapper">
      <ul class="m-topbar__nav m-nav m-nav--inline">
         <li class="
            m-nav__item m-dropdown m-dropdown--large m-dropdown--arrow m-dropdown--align-center m-dropdown--mobile-full-width m-dropdown--skin-light	m-list-search m-list-search--skin-light"
            m-dropdown-toggle="click" m-dropdown-persistent="1" id="m_quicksearch" m-quicksearch-mode="dropdown">
            <div class="m-dropdown__wrapper">
               <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
               <div class="m-dropdown__inner ">
                  <div class="m-dropdown__body">
                     <div class="m-dropdown__scrollable m-scrollable" data-scrollable="true" data-max-height="300" data-mobile-max-height="200">
                        <div class="m-dropdown__content"></div>
                     </div>
                  </div>
               </div>
            </div>
         </li>
         <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light" m-dropdown-toggle="click">
            <a href="#" class="m-nav__link m-dropdown__toggle">
            <span class="m-topbar__userpic">
            <img src="assets/app/media/img/users/avatar-placeholder.jpg" alt=""/>
            </span>
            </a>
            <div class="m-dropdown__wrapper">
               <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
               <div class="m-dropdown__inner">
                  <div class="m-dropdown__header m--align-center" style="background: url(assets/app/media/img/misc/quick_actions_bg.jpg); background-size: cover;">
                     <div class="m-card-user m-card-user--skin-dark">
                        <div class="m-card-user__pic">
                           <img src="assets/app/media/img/users/avatar-placeholder.jpg" alt=""/>
                        </div>
                        <div class="m-card-user__details">
                           <span class="m-card-user__name m--font-weight-500">
                           <span id="adminFirstName">FirstName LastName</span>
                           </span>
                           <a href="" class="m-card-user__email m--font-weight-300 m-link" style="color:white; word-break: break-all">
                           <span id="adminEmail">Email</span>
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="m-dropdown__body">
                     <div class="m-dropdown__content">
                        <ul class="m-nav m-nav--skin-light">
                           <li class="m-nav__section m--hide">
                              <span class="m-nav__section-text">
                              Section
                              </span>
                           </li>
                           <li class="m-nav__item">
                              <a href="main.php?page=my-account" class="m-nav__link">
                              <i class="m-nav__link-icon flaticon-profile-1"></i>
                              <span class="m-nav__link-title">
                              <span class="m-nav__link-wrap">
                              <span class="m-nav__link-text">
                              Minha conta
                              </span>
                              </span>
                              </span>
                              </a>
                           </li>
                           <li class="m-nav__separator m-nav__separator--fit"></li>
                           <li class="m-nav__item">
                              <a href="javascript:;" class="btn m-btn--pill btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder" id="btnLogout">
                              Logout
                              </a>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </li>
      </ul>
   </div>
</div>
<script>
   jQuery(document).ready(function() {

   	if(sessionStorage.getItem("admin")){
       //pegar dados da sessão do admin e popular os campos necessários

       var admin = JSON.parse(sessionStorage.getItem("admin"));

       $('#adminFirstName').html(admin.adminFirstName + " " + admin.adminLastName);
       $('#adminEmail').html(admin.adminEmail);

   	}else{
       //se não tiver os dados do usuário, enviar para página de login
       location.href = "./login.php";
     }

     $("#btnLogout").click(function () {
       //apagar dados da sessão do admin e enviar para página de login
       sessionStorage.removeItem("admin");
       location.href = "./login.php";
     });

   });
</script>
