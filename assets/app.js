import { createApp } from "vue";
import TrackyApp from "./components/TrackyApp.vue";
import "bootstrap/dist/css/bootstrap.css";

const app = createApp(TrackyApp);

app.mount("#tracky-app");
