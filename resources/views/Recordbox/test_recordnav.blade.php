<style>
    .recordbox_navbar {
        margin: 0;
        padding: 0;
        height: 50px;
        min-width: 700px;
        width: 100%;
        transition: top 0.2s ease-in-out;
        position: relative;
        display: block;
        z-index: 1;
    }
    .nav-up {
        margin: 0;
        padding: 0;
        top: 0;
        left: 12%;
        height: 50px;
        width: 88%;
        position: fixed;
        z-index: 2;
    }
    .recordbox.navbar.navbar-default {
        background: #fff;
        border: 1px solid #e5e6e8;
        margin: 0;
    }
    .container-fluid {
        height: 50px;
        width: 100%;
    }

</style>

<nav class="recordbox navbar navbar-default">
    <div class="container-fluid" >
        <div class="navbar-header">
            <a class="navbar-brand" id="nav_group_name">클래스 이름</a>
        </div>
        <ul class="nav navbar-nav nav-toggle" id="race_nav">
            <li><a id="history" href="#" onclick="recordControl(this.id)">최근 기록</a></li>
            <li><a id="students" href="#" onclick="recordControl(this.id)">학생 관리</a></li>
            <li><a id="feedback" href="#" onclick="recordControl(this.id)">피드백</a></li>
        </ul>
    </div>
</nav>

