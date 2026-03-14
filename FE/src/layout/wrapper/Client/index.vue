<template>
    <div class="wrapper">
        <TopClient></TopClient>
            <router-view> </router-view>
        <BotClient></BotClient>
    </div>
</template>
<script>
import "../../../assets/js/jquery.min.js";
import "../../../assets/plugins/simplebar/js/simplebar.min.js";
import "../../../assets/plugins/metismenu/js/metisMenu.min.js";
import "../../../assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js";
import "../../../assets/js/index.js";
import "../../../assets/js/app.js";
import "../../../assets/js/pace.min.js";
import TopClient from "../../components/Client/TopClient.vue";
import BotClient from "../../components/Client/BotClient.vue";

export default {
    name: "app",
    components: {
        BotClient, TopClient
    }

}
</script>
<style>
@import "../../../assets/plugins/simplebar/css/simplebar.css";
@import "../../../assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css";
@import "../../../assets/plugins/metismenu/css/metisMenu.min.css";
@import "../../../assets/css/pace.min.css";
@import "../../../assets/css/bootstrap.min.css";
@import "../../../assets/css/bootstrap-extended.css";
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap");
@import "../../../assets/css/app.css";
@import "../../../assets/css/icons.css";
@import "../../../assets/css/dark-theme.css";
@import "../../../assets/css/semi-dark.css";
@import "../../../assets/css/header-colors.css";
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
</style>