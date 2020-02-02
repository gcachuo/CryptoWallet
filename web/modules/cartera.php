<?php
?>
<div class="card">
    <div class="padding table-responsive">
        <table class="table"></table>
    </div>
</div>
<script>
    $(function () {
        const table = $("table").DataTable({
            ajax: {
                type: 'POST',
                url: 'api/users/fetchAmounts',
                dataSrc: ({status, code, response: {message, data: {amounts}}, error}) => {
                    return amounts;
                },
                data: {
                    user: JSON.parse(localStorage.getItem('user'))
                }
            },
            order: [[6, 'desc']],
            columnDefs: (() => {
                const columns = [
                    {title: 'Moneda', data: 'moneda'},
                    {
                        title: 'Cantidad', data: 'cantidad',
                        render: (data, type) => {
                            if (type === 'display') {
                                return numeral(data).format('0.00000000');
                            }
                            return data;
                        }
                    },
                    {
                        title: 'Precio', data: 'precio',
                        render: (data, type) => {
                            if (type === 'display') {
                                return numeral(data).format('$0,0.00');
                            }
                            return data;
                        }
                    },
                    {
                        title: 'Promedio', data: 'promedio',
                        render: (data, type) => {
                            if (type === 'display') {
                                return numeral(data).format('$0,0.00');
                            }
                            return data;
                        }
                    },
                    {
                        title: 'Costo', data: 'costo',
                        render: (data, type) => {
                            if (type === 'display') {
                                return numeral(data).format('$0,0.00');
                            }
                            return data;
                        }
                    },
                    {
                        title: 'Actual', data: 'total',
                        render: (data, type) => {
                            if (type === 'display') {
                                return numeral(data).format('$0,0.00');
                            }
                            return data;
                        }
                    },
                    {
                        title: '%', data: 'porcentaje',
                        render: (data, type) => {
                            if (type === 'display') {
                                return numeral(data).format('0,0.00%');
                            }
                            return data;
                        }
                    },
                ];
                columns.map((column, index) => {
                    column['targets'] = index;
                    return column;
                });
                return columns;
            })(),
            language: {
                search: "Buscar:",
                emptyTable: "No hay registros que consultar",
                lengthMenu: "Mostrar _MENU_ registros por pagina",
                info: "Mostrando pagina _PAGE_ de _PAGES_",
                loadingRecords: "Cargando...",
                paginate: {
                    first: "Primero",
                    last: "Ultimo",
                    next: "Siguiente",
                    previous: "Anterior"
                },
            },
            pageLength: 25,
            scrollX: true,
            processing: true,
            serverSide: false,
            responsive: true,
            paginate: false,
            searching: false,
            initComplete: () => {

            }
        });
    })
</script>
