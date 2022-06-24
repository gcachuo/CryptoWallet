import {Defaults} from "./defaults";

$(() => {
    Defaults.init();
    Defaults.initNotifications();
});

import "expose-loader?exposes[]=$&exposes[]=jQuery!jquery";
import "expose-loader?exposes[]=JSZip!jszip";
import "expose-loader?exposes[]=App!./modules/app";
