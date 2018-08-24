<template>

    <main>
        <div>
            <div class="Main">
                <section class="hero is-info welcome is-small">
                    <div class="hero-body">
                        <div class="container">
                            <h1 class="title">
                                최종 결과
                            </h1>

                        </div>
                    </div>
                </section>
                <section class="info-tiles">
                    <div class="tile is-ancestor has-text-centered">
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">1위</p>
                                <p class="title" id="0_nick" ></p>
                                <p class="subtitle" id="0_point"></p>
                                <img id="0_character" src="#" width="250px">
                                <b-progress id="b-progress1"
                                            height="30px"
                                            :value="10"
                                            variant="warning"
                                            striped="striped"
                                            :animated="animate"
                                            class="mb-2 w-100 "></b-progress>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">2위</p>
                                <p class="title" id="1_nick" ></p>
                                <p class="subtitle" id="1_point"></p>
                                <img id="1_character" src="#" width="250px">
                                <b-progress
                                        height="30px"
                                        :value="30"
                                        variant="warning"
                                        striped="striped"
                                        :animated="animate"
                                        class="mb-2 w-100 "></b-progress>
                            </article>
                        </div>
                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">3위</p>
                                <p class="title" id="2_nick" ></p>
                                <p class="subtitle" id="2_point"></p>
                                <img id="2_character" src="#" width="250px">
                                <b-progress
                                        height="30px"
                                        :value="92"
                                        variant="success"
                                        striped="striped"
                                        :animated="animate"
                                        class="mb-2 w-100 "></b-progress>
                            </article>
                        </div>

                        <div class="tile is-parent">
                            <article class="tile is-child box">
                                <p class="title">4위</p>
                                <p class="title" id="3_nick" ></p>
                                <p class="subtitle" id="3_point"></p>
                                <img id="3_character" src="#" width="250px">
                                <b-progress
                                        height="30px"
                                        :value="50"
                                        variant="info"
                                        striped="striped"
                                        :animated="animate"
                                        class="mb-2 w-100 "></b-progress>
                            </article>
                        </div>
                    </div>
                </section>

                <div class="columns">
                    <div class="column is-13">
                        <div class="card events-card">
                            <header class="card-header">
                                <p class="card-header-title" width="40px">
                                    아쉬운 사람들
                                </p>
                                <a href="#" class="card-header-icon" aria-label="more options">
                                                    <span class="icon">
                                                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                                                    </span>
                                </a>
                            </header>
                            <div class="card-table">
                                <div class="content">
                                    <table class="table is-fullwidth is-striped">
                                        <tbody>
                                        <tr>
                                            <td width="5%">
                                                <i class="fa fa-bell-o"></i>
                                            </td>
                                            <td id="4_nick"></td>
                                            <td>
                                                <a class="button is-big is-primary">5등</a>
                                            </td>
                                            <td>
                                                <a class="button is-big is-primary" id="4_point"></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="5%">
                                                <i class="fa fa-bell-o"></i>
                                            </td>
                                            <td id="5_nick"></td>
                                            <td>
                                                <a class="button is-big is-primary">6등</a>
                                            </td>
                                            <td>
                                                <a class="button is-big is-primary" id="5_point"></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="5%">
                                                <i class="fa fa-bell-o"></i>
                                            </td>
                                            <td id="6_nick"></td>
                                            <td>
                                                <a class="button is-big is-primary">7등</a>
                                            </td>
                                            <td>
                                                <a class="button is-big is-primary" id="6_point"></a>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <footer class="card-footer">
                                <a href="#" class="card-footer-item">View All</a>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

</template>

<script>
    export default {
        mounted: function(){
            $confetti.start()

        },
        data() {
            return {animate, show: true}

        },
        methods: {
            start() {
                this
                    .$confetti
                    .start()
            }
        },
        beforeMount(){
            var confetti = this.$confetti;
            this.$confetti.start();

            setTimeout(function(){
                confetti.stop();
            }, 3000);

        },
    }
</script>
<style>
    .inline {
        display: inline-block;
    }
    .divi {
        width: 100%;
    }
    .Main {
        width : 100%;
    }
</style>