/**
 * Created by Memo on 20/feb/2017.
 */
var getVars;
$(function () {
    $("#loader").attr("disabled", false);
    $(".loader").hide();
    $("[ui-nav] a").click(function (e) {
        var $this = $(e.target), $active, $li;
        $this.is('a') || ($this = $this.closest('a'));

        $li = $this.parent();
        $active = $li.siblings(".active");
        $li.toggleClass('active');
        $active.removeClass('active');
    });
    $(".dropdown > a").click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".dropdown").not($(this).parent()).removeClass('open');
        $(this).parent().toggleClass('open');
    });
    $(".dropdown > .dropdown-menu").click(function (e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $(window).click(function () {
        $(".dropdown").removeClass('open');
    });
    /*$(".select2").select2({
     placeholder: ""
     });*/
    Dropzone.autoDiscover = false;
    $("#selectEstado").change(function () {
        $("#selectCiudad").attr("disabled", true);
        ajax('buildListaCiudades');
    });
    $(".floatingButton").click(function () {
        $(this).toggleClass("open");
        $(".quickMenu").toggleClass('hidden').find(".btn-icon").toggleClass("open");
        $(".quickMenu").find(".label").toggleClass("open");
    });
    $(".quickMenu").find(".btn-icon").click(function () {
        $(".floatingButton").toggleClass("open");
        $(".quickMenu").toggleClass('hidden').find(".btn-icon").toggleClass("open");
        $(".quickMenu").find(".label").toggleClass("open");
    });
    cargarSwitchery();
    if (getVars !== undefined)
        if (getVars.tryit !== undefined) {
            aside("login", "registro");
        }
});


// request permission on page load
document.addEventListener('DOMContentLoaded', function () {
    if (!Notification) {
        alert('Desktop notifications not available in your browser. Try Chromium.');
        return;
    }
    if (Notification.permission !== "granted")
        Notification.requestPermission();
});

function showMessage(message, color) {
    $("#messages").show();
    $("#messages").find("li").html(message);
    $("#messages").addClass("fadeInDown").addClass(color);
    var interval = setInterval(function () {
        $("#messages").removeClass("fadeInDown");
        $("#messages").addClass("fadeOutUp");
        clearInterval(interval);
        interval = setInterval(function () {
            $("#messages").removeClass("fadeOutUp");
            $("#messages").hide();
            clearInterval(interval);
        }, 1000);
    }, 2000);
}

function notifyMe(title, message) {
    if (Notification.permission !== "granted")
        Notification.requestPermission();
    else {
        var notification = new Notification(title, {
            icon: 'recursos/img/logo.png',
            body: message
        });

        notification.onclick = function () {
            window.open('http://gcachuo.xyz/cryptowallet/');
        };

    }

}

function ajax(fn, post, modulo) {
    var formValido = false;
    if ($("#txtAside").val() == 0) formValido = validarFormulario($('#frmSistema'));
    else formValido = validarFormulario($('#frmAside'));
    if (formValido) {
        $("a.btn").addClass("disabled");
        $.post("index.php",
            {
                fn: fn,
                form: $("#frmSistema").serialize(),
                aside: $("#frmAside").serialize(),
                post: post,
                modulo: modulo
            },
            function (result) {
                $("a.btn").removeClass("disabled");
                if (typeof result === 'string') {
                    alert(result);
                    console.error(result);
                }
                else
                    window[fn](result);
            },
            'json'
        ).fail(function (result) {
                $("a.btn").removeClass("disabled");
                alert(result.responseText);
                console.error(result.responseText);
            }
        );
    }
}

function navegar(modulo, accion, post) {
    $(".loader").show();
    console.time('navegar');
    if (accion != null) accion = modulo + "/" + accion;
    $.post(
        "index.php",
        {
            vista: modulo,
            accion: accion,
            post: post
        },
        function (result) {
            console.timeEnd('navegar');
            location.reload();
        }
    );
}

function aside(modulo, accion, post) {
    $("#txtAside").val(1);
    $("#rightBar").modal();
    $("#rightBarContent").html("<div class='loading'></div>");
    $.post(
        "index.php?aside=1",
        {
            asideModulo: modulo,
            asideAccion: accion,
            form: $("#frmSistema").serialize(),
            post: post
        },
        function (result) {
            $("#rightBarContent").html(result);
            var fn = "aside" + modulo + accion;
            if (typeof window[fn] === "function") {
                window[fn]();
            }
        }
    );
}

function cerrarAside() {
    $("#txtAside").val(0);
    $("#rightBar").modal('hide');
    $("#rightBarContent").html("");
}

function cargarDatatable(idioma, columnDefs, buttons) {
    try {
        buttons = buttons != undefined ? buttons : [];
        return $("table").DataTable({
            "iDisplayLength": -1,
            "bLengthChange": false,
            "bSort": false,
            "responsive": {
                details: {
                    type: 'column',
                    target: 'tr',
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.hidden ?
                                '<div class="row child"><div class="col-xs-12"><div class="form-group">' + col.data + '</div></div></div>' :
                                '';
                        }).join('');

                        return data ?
                            $('<table/>').append(data) :
                            false;
                    }
                }
            },
            "columnDefs": columnDefs,
            "dom": "<'row'<'col-xs-2'B><'col-xs-10'f>>" /*'fBrtip'*/,
            "dom": "<'row'<'col-xs-2'B><'col-xs-10'f>>" /*'fBrtip'*/,
            "buttons": buttons,
            "oClasses": {
                "sFilterInput": "form-control form-control-sm p-x b-a",
                "sPageButton": "btn btn-sm btn-default",
                "sLengthSelect": "btn white btn-sm dropdown-toggle"
            },
            "language": {
                "paginate": {
                    "next": idioma.next,
                    "previous": idioma.previous
                },
                "search": "",
                "sSearchPlaceholder": idioma.sSearchPlaceholder,
                "sLengthMenu": idioma.sLengthMenu,
                "sInfoEmpty": idioma.sInfoEmpty,
                "sInfo": idioma.sInfo,
                "emptyTable": idioma.sEmptyTable
            }
        });
    }
    catch (e) {
        console.error(e);
    }
}

function cargarDropzone(idioma) {
    try {
        $(".dropzone").dropzone(
            {
                dictDefaultMessage: idioma.dictDefaultMessage,
                dictRemoveFile: idioma.dictRemoveFile,
                url: "index.php?file=true",
                /*addRemoveLinks: true,*/
                acceptedFiles: 'image/*',
                uploadMultiple: false,
                maxFiles: 1,
                autoProcessQueue: false,
                init: function () {
                    var myDropzone = this;
                    var editedFile;
                    this.on("addedfile", function (file) {
                        try {
                            var reader = new FileReader();

                            reader.addEventListener("load", function (event) {

                                var origImg = new Image();
                                origImg.src = event.target.result;

                                origImg.addEventListener("load", function (event) {
                                    try {
                                        comp = jic.compress(origImg, 30, "jpg");
                                        editedFile = dataURItoBlob(comp.src);
                                        editedFile.lastModifiedDate = new Date();
                                        editedFile.name = file.name;
                                        editedFile.status = Dropzone.ADDED;
                                        editedFile.accepted = true;

                                        /*var origFileIndex = myDropzone.files.indexOf(file);
                                         myDropzone.files[origFileIndex] = editedFile;*/

                                        myDropzone.files.push(editedFile);
                                        myDropzone.emit('addedFile', editedFile);
                                        myDropzone.createThumbnailFromUrl(editedFile, "recursos/imagenes/transacciones/20170508111232_potw1636a.jpg");
                                        myDropzone.emit('complete', editedFile);

                                        console.log(myDropzone.files);
                                        console.log(file);
                                        console.log(editedFile);

                                        myDropzone.enqueueFile(editedFile);

                                        file.status = Dropzone.SUCCESS;
                                        file.upload.progress = 100;
                                        file.upload.bytesSent = file.upload.total;
                                        myDropzone.emit("success", file);

                                        myDropzone.processQueue();

                                        myDropzone.emit("complete", file);
                                    }
                                    catch (e) {
                                        console.log(e);
                                        myDropzone.emit("reset");
                                    }
                                });
                            });
                            reader.readAsDataURL(file);
                        }
                        catch (e) {
                            console.log(e);
                        }
                    });
                    this.on("success", function (file, responseText) {
                        $("#txtDropzoneFile").val(responseText);
                    });
                    this.on("error", function (file, responseText) {
                        console.error(responseText);
                        alert(responseText);
                        myDropzone.emit("reset");
                        myDropzone.removeAllFiles();
                        console.log(myDropzone.files);
                    })
                    this.on("removedFile", function (file) {
                        console.log(file);
                        console.log(response);
                        console.log($("#txtDropzoneFile").val());
                    });
                }
            }
        );
    }
    catch (result) {
        alert(result.responseText);
        console.error(result.responseText);
        console.log(result);
    }
}

function dataURItoBlob(dataURI) {
    // convert base64/URLEncoded data component to raw binary data held in a string
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ia], {type: mimeString});
}

function cargarNestable() {
    $("ol:empty").remove()
    $(".nestable").nestable();
    $(".dd-nodrag").on("mousedown", function (event) { // mousedown prevent nestable click
        event.preventDefault();
        return false;
    });

    $(".dd-nodrag").on("click", function (event) { // click event
        event.preventDefault();
        return false;
    });
}

function cargarSwitchery() {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function (html) {
        var switchery = new Switchery(html, {size: 'small'});
    });
}

function cargarDatePicker(elemento, eStartDate, eEndDate, idioma, fn) {
    var date = new Date();
    var startDate = moment([date.getFullYear(), date.getMonth()]);
    var endDate = moment(moment([date.getFullYear(), date.getMonth()])).endOf('month');
    eStartDate.val(startDate.format('YYYY-MM-DD'));
    eEndDate.val(endDate.format('YYYY-MM-DD'));
    elemento.daterangepicker({
        "startDate": startDate,
        "endDate": endDate,
        "ranges": {
            "Hoy": [
                moment(),
                moment()
            ],
            "Ayer": [
                moment().subtract(1, 'days'),
                moment().subtract(1, 'days')
            ],
            "Ultimos 7 dias": [
                moment().subtract(6, 'days'),
                moment()
            ],
            "Ultimos 30 dias": [
                moment().subtract(29, 'days'),
                moment()
            ],
            "Este mes": [
                moment([date.getFullYear(), date.getMonth()]),
                moment(moment([date.getFullYear(), date.getMonth()])).endOf('month')
            ],
            "Mes pasado": [
                moment([date.getFullYear(), date.getMonth() - 1]),
                moment(moment([date.getFullYear(), date.getMonth() - 1])).endOf('month')
            ],
            "Este año": [
                moment([date.getFullYear()]),
                moment(moment([date.getFullYear()])).endOf('year')
            ],
            "Año pasado": [
                moment([date.getFullYear() - 1]),
                moment(moment([date.getFullYear() - 1])).endOf('year')
            ],
            "Todas": [
                moment().subtract(7, 'years').startOf('year'),
                moment().endOf('year')
            ]
        },
        linkedCalendars: false,
        autoApply: true,
        locale: {
            format: idioma.format,
            customRangeLabel: idioma.customRangeLabel
        }
    }, function (start, end, label) {
        eStartDate.val(start.format('YYYY-MM-DD'));
        eEndDate.val(end.format('YYYY-MM-DD'));
        if (elemento.prop("tagName") != "input") {
            elemento.html(label);
        }
        if (fn !== undefined)
            window[fn]();
    });
}

function cargarDoughnut(id, data, names, color) {
    var myChart = echarts.init(document.getElementById(id));
    myChart.setOption({
        tooltip: {
            transitionDuration: 0,
            showDelay: 0,
            hideDelay: 0,
            position: [0, 0],
            trigger: 'item',
            formatter: '{a} <br/>{b} : {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            x: 'left',
            textStyle: {
                color: 'auto'
            },
            data: names
        },
        calculable: true,
        series: [
            {
                name: id,
                itemStyle: {
                    normal: {
                        label: {
                            show: false,
                            textStyle: {
                                color: 'rgba(165,165,165,1)'
                            }
                        },
                        labelLine: {
                            show: false,
                            lineStyle: {
                                color: 'rgba(165,165,165,1)'
                            }
                        },
                        color: function (params) {
                            var red = color.red + (params.dataIndex * 33);
                            var green = color.green + (params.dataIndex * 33);
                            var blue = color.blue + (params.dataIndex * 33);
                            return 'rgba(' + red + ',' + green + ',' + blue + ',1)';
                            ;
                        }
                    }
                },
                type: 'pie',
                radius: ['50%', '70%'],
                data: data
            }
        ]
    });
    return myChart;
}

function cargarDoughnut2(id, data, color) {
    var myChart = echarts.init(document.getElementById(id));
    myChart.setOption({
        title: {
            x: 'center',
            y: 'center',
            itemGap: 20,
            textStyle: {
                color: 'rgba(30,144,255,0.8)',
                fontSize: 20,
                fontWeight: 'bolder'
            }
        },
        tooltip: {
            transitionDuration: 0,
            showDelay: 0,
            hideDelay: 0,
            position: [0, 0],
            show: true,
            formatter: '{a} <br/>{b} : {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            x: $('#pie').width() / 2 + 10,
            y: 20,
            itemGap: 12,
            textStyle: {
                color: 'auto'
            },
            data: [data.name1, data.name2]
        },
        series: [
            {
                name: data.name1,
                type: 'pie',
                clockWise: false,
                radius: [50, 70],
                itemStyle: {
                    normal: {
                        label: {show: false},
                        labelLine: {show: false},
                        color: 'rgba(' + color[0].red + ',' + color[0].green + ',' + color[0].blue + ',1)'
                    }
                },
                data: [
                    {
                        value: data.value1,
                        name: data.name1
                    },
                    {
                        value: data.value2,
                        name: 'invisible',
                        itemStyle: {
                            normal: {
                                color: 'rgba(0,0,0,0)',
                                label: {show: false},
                                labelLine: {show: false}
                            },
                            emphasis: {
                                color: 'rgba(0,0,0,0)'
                            }
                        }
                    }
                ]
            },
            {
                name: data.name2,
                type: 'pie',
                clockWise: false,
                radius: [30, 50],
                itemStyle: {
                    normal: {
                        label: {show: false},
                        labelLine: {show: false},
                        color: 'rgba(' + color[1].red + ',' + color[1].green + ',' + color[1].blue + ',1)'
                    }
                },
                data: [
                    {
                        value: data.value2,
                        name: data.name2
                    },
                    {
                        value: data.value1,
                        name: 'invisible',
                        itemStyle: {
                            normal: {
                                color: 'rgba(0,0,0,0)',
                                label: {show: false},
                                labelLine: {show: false}
                            },
                            emphasis: {
                                color: 'rgba(0,0,0,0)'
                            }
                        }
                    }
                ]
            }
        ]
    });
    return myChart;
}

function cerrarSesion() {
    navegar('login');
}

function btnMiPerfil() {
    aside("miperfil", "miperfil");
}

function btnCuenta() {
    aside("miperfil", "cuenta");
}

function buildListaCiudades(result) {
    $("#selectCiudad").html(result.listaCiudades).attr("disabled", false);
}

function asidetransaccionesnuevo() {
    var tipo = $("#selectTipo").val();
    if (tipo !== "" && tipo !== 3 && $("input[name=idTransaccion]").val() == "") {
        ajax('buildListaCategorias', {tipo: $("#selectTipo").val()}, 'transacciones');
    }
}

function validarFormulario(form) {
    var $myForm = $(form),
        valid = $myForm[0].checkValidity();
    if (!valid) {
        $('<input type="submit">').hide().appendTo($myForm).click().remove();
    }
    return valid;
}

function compress(source_img_obj, quality, maxWidth, output_format) {
    var mime_type = "image/jpeg";
    if (typeof output_format !== "undefined" && output_format == "png") {
        mime_type = "image/png";
    }

    maxWidth = maxWidth || 1000;
    var natW = source_img_obj.naturalWidth;
    var natH = source_img_obj.naturalHeight;
    var ratio = natH / natW;
    if (natW > maxWidth) {
        natW = maxWidth;
        natH = ratio * maxWidth;
    }

    var cvs = document.createElement('canvas');
    cvs.width = natW;
    cvs.height = natH;

    var ctx = cvs.getContext("2d").drawImage(source_img_obj, 0, 0, natW, natH);
    var newImageData = cvs.toDataURL(mime_type, quality / 100);
    var result_image_obj = new Image();
    result_image_obj.src = newImageData;
    return result_image_obj;
}