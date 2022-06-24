import {Defaults} from "./defaults";

$(() => {
    Defaults.init();

    if (!Notification) {
        alert('Desktop notifications not available in your browser. Try Chromium.');
        return;
    }

    if (Notification.permission !== "granted") {
        Notification.requestPermission().then(() => Defaults.browserNotification({
            title: 'Notificaciones Activadas',
            body: 'Has activado las notificaciones correctamente'
        }));
    }
});

import "expose-loader?exposes[]=$&exposes[]=jQuery!jquery";
import "expose-loader?exposes[]=JSZip!jszip";
import "expose-loader?exposes[]=App!./modules/app";
