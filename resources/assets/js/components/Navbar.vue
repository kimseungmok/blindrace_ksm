<template>
  <div id="header">
    <div>
      <h1 class="fontColor_white"><B class="fontColor_red">&nbsp SMART</B> sasisuseso </h1>
    </div>
    <!-- 메인 바 -->
    <div id="main_bar">
      <b-navbar class="navbar_nav" type="dark" toggleable>
          <b-navbar-toggle target="nav_dropdown_collapse"></b-navbar-toggle>
          <b-collapse is-nav id="nav_dropdown_collapse">
            <b-navbar-nav>
              <b-nav-item href="#"><B>설정</B></b-nav-item>
              <b-nav-item href="#"><B>예약</B></b-nav-item>
              <b-nav-item href="#"><B>통계</B></b-nav-item>
            </b-navbar-nav>
          </b-collapse>
          <b-navbar-nav>
            <b-button><h4>저장</h4></b-button>
          </b-navbar-nav>
        </b-navbar>
    </div>
  </div>
</template>