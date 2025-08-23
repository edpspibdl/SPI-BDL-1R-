<style>
    table {
        border-collapse: collapse;
        width: 100%;
        text-align: center;
    }

    th,
    td {
        border: 1px solid #fff;
        padding: 8px;
        color: white;
    }

    /* Header utama */
    .header-main {
        background-color: #f5f5f5;
        color: #666;
        font-weight: bold;
        font-size: 18px;
        text-align: center;
        padding: 10px;
    }

    /* Sub-header */
    .sub-header-hj {
        background-color: #007bff;
    }

    /* Biru muda */
    .sub-header-promo {
        background-color: #0000ff;
    }

    /* Biru tua */
    .sub-header-mm {
        background-color: red;
    }

    .sub-header-mb {
        background-color: blue;
    }

    .sub-header-mp {
        background-color: black;
    }

    /* Teks gelap untuk MP */
    .sub-header-mp th,
    .sub-header-mp td {
        color: white;
    }

    .white-text {
        color: white;
    }
</style>


<div class="row">
    <div class="col-md-10">
        <div class="table-responsive">
            <table id="headertbl" class="table table-striped table-bordered table-hover cell-border compact" style="width:100%; font-size:14px; margin-bottom: 15px;" align="center">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">LPP vs PLANO REKAP</th>
                    </tr>
                    <tr>
                        <th>KETERANGAN</th>
                        <th>PLU</th>
                        <th>RUPIAH</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PLUS</td>
                        <td id="r-plu-plus" class="text-right"></td>
                        <td id="r-rph-plus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>MINUS</td>
                        <td id="r-plu-minus" class="text-right"></td>
                        <td id="r-rph-minus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>TIDAK SELISIH</td>
                        <td id="r-plu-ts" class="text-right"></td>
                        <td id="r-rph-ts" class="text-right"></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>SALDO AKHIR</th>
                        <th id="r-plu-sa" class="text-right"></th>
                        <th id="r-rph-sa" class="text-right"></th>
                    </tr>
                    <tr>
                        <th>PERSEN</th>
                        <th id="persenplu" class="text-right"></th>
                        <th id="persenrph" class="text-right"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover cell-border compact" style="width:100%; font-size:14px;" align="center">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">KETERANGAN</th>
                        <th>ITEM</th>
                        <th>RUPIAH</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- FOOD -->
                    <tr>
                        <td rowspan="3" class="text-center align-middle">FOOD</td>
                        <td>PLUS</td>
                        <td id="divf-plu-plus" class="text-right"></td>
                        <td id="divf-rph-plus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>MINUS</td>
                        <td id="divf-plu-minus" class="text-right"></td>
                        <td id="divf-rph-minus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>TIDAK SELISIH</td>
                        <td id="divf-plu-ts" class="text-right"></td>
                        <td id="divf-rph-ts" class="text-right"></td>
                    </tr>

                    <!-- NON FOOD -->
                    <tr>
                        <td rowspan="3" class="text-center align-middle">NON FOOD</td>
                        <td>PLUS</td>
                        <td id="divnf-plu-plus" class="text-right"></td>
                        <td id="divnf-rph-plus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>MINUS</td>
                        <td id="divnf-plu-minus" class="text-right"></td>
                        <td id="divnf-rph-minus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>TIDAK SELISIH</td>
                        <td id="divnf-plu-ts" class="text-right"></td>
                        <td id="divnf-rph-ts" class="text-right"></td>
                    </tr>

                    <!-- GENERAL MERCHANDISING -->
                    <tr>
                        <td rowspan="3" class="text-center align-middle">GENERAL MERCHANDISING</td>
                        <td>PLUS</td>
                        <td id="divgms-plu-plus" class="text-right"></td>
                        <td id="divgms-rph-plus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>MINUS</td>
                        <td id="divgms-plu-minus" class="text-right"></td>
                        <td id="divgms-rph-minus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>TIDAK SELISIH</td>
                        <td id="divgms-plu-ts" class="text-right"></td>
                        <td id="divgms-rph-ts" class="text-right"></td>
                    </tr>

                    <!-- PERISHABLE -->
                    <tr>
                        <td rowspan="3" class="text-center align-middle">PERISHABLE</td>
                        <td>PLUS</td>
                        <td id="divprsh-plu-plus" class="text-right"></td>
                        <td id="divprsh-rph-plus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>MINUS</td>
                        <td id="divprsh-plu-minus" class="text-right"></td>
                        <td id="divprsh-rph-minus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>TIDAK SELISIH</td>
                        <td id="divprsh-plu-ts" class="text-right"></td>
                        <td id="divprsh-rph-ts" class="text-right"></td>
                    </tr>

                    <!-- COUNTER & PROMOTION -->
                    <tr>
                        <td rowspan="3" class="text-center align-middle">COUNTER & PROMOTION</td>
                        <td>PLUS</td>
                        <td id="divcntr-plu-plus" class="text-right"></td>
                        <td id="divcntr-rph-plus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>MINUS</td>
                        <td id="divcntr-plu-minus" class="text-right"></td>
                        <td id="divcntr-rph-minus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>TIDAK SELISIH</td>
                        <td id="divcntr-plu-ts" class="text-right"></td>
                        <td id="divcntr-rph-ts" class="text-right"></td>
                    </tr>

                    <!-- FAST FOOD -->
                    <tr>
                        <td rowspan="3" class="text-center align-middle">FAST FOOD</td>
                        <td>PLUS</td>
                        <td id="divfsf-plu-plus" class="text-right"></td>
                        <td id="divfsf-rph-plus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>MINUS</td>
                        <td id="divfsf-plu-minus" class="text-right"></td>
                        <td id="divfsf-rph-minus" class="text-right"></td>
                    </tr>
                    <tr>
                        <td>TIDAK SELISIH</td>
                        <td id="divfsf-plu-ts" class="text-right"></td>
                        <td id="divfsf-rph-ts" class="text-right"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.ajax({
            type: "GET", // mengirim data dengan method POST 
            url: "query.php", // proses update data
            dataType: 'JSON',
            beforeSend: function() {
                Swal.fire({
                    title: 'Menunggu',
                    html: 'Memproses data',
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading()
                    }
                })
            },
            success: function(res) {
                Swal.close()
                if (res.STATUS === 'OK') {

                    $('#r-plu-plus').text(formatRupiah(res.R_PLU_PLUS))
                    $('#r-plu-minus').text(formatRupiah(res.R_PLU_MINUS))
                    $('#r-plu-ts').text(formatRupiah(res.R_PLU_TS))

                    $('#r-rph-plus').text(formatRupiah(res.R_RPH_PLUS))
                    $('#r-rph-minus').text(formatRupiah(res.R_RPH_MINUS))
                    $('#r-rph-ts').text(formatRupiah(res.R_RPH_TS))

                    $('#r-plu-sa').text(formatRupiah(res.R_PLU_ALL))
                    $('#r-rph-sa').text(formatRupiah(res.R_RPH_ALL))

                    let persenplu = parseInt(res.R_PLU_PLUS) + parseInt(res.R_PLU_MINUS);
                    persenplu = persenplu / parseInt(res.R_PLU_ALL) * 100;

                    $('#persenplu').text(persenplu.toFixed(2) + '%')

                    //FOOD
                    $('#divf-plu-plus').text(formatRupiah(res.R_DIVF_PLU_PLUS))
                    $('#divf-rph-plus').text(formatRupiah(res.R_DIVF_RPH_PLUS))
                    $('#divf-plu-minus').text(formatRupiah(res.R_DIVF_PLU_MINUS))
                    $('#divf-rph-minus').text(formatRupiah(res.R_DIVF_RPH_MINUS))
                    $('#divf-plu-ts').text(formatRupiah(res.R_DIVF_PLU_TS))
                    $('#divf-rph-ts').text(formatRupiah(res.R_DIVF_RPH_TS))

                    //NON FOOD
                    $('#divnf-plu-plus').text(formatRupiah(res.R_DIVNF_PLU_PLUS))
                    $('#divnf-rph-plus').text(formatRupiah(res.R_DIVNF_RPH_PLUS))
                    $('#divnf-plu-minus').text(formatRupiah(res.R_DIVNF_PLU_MINUS))
                    $('#divnf-rph-minus').text(formatRupiah(res.R_DIVNF_RPH_MINUS))
                    $('#divnf-plu-ts').text(formatRupiah(res.R_DIVNF_PLU_TS))
                    $('#divnf-rph-ts').text(formatRupiah(res.R_DIVNF_RPH_TS))

                    //GENERAL MERCHANDISING
                    $('#divgms-plu-plus').text(formatRupiah(res.R_DIVGMS_PLU_PLUS))
                    $('#divgms-rph-plus').text(formatRupiah(res.R_DIVGMS_RPH_PLUS))
                    $('#divgms-plu-minus').text(formatRupiah(res.R_DIVGMS_PLU_MINUS))
                    $('#divgms-rph-minus').text(formatRupiah(res.R_DIVGMS_RPH_MINUS))
                    $('#divgms-plu-ts').text(formatRupiah(res.R_DIVGMS_PLU_TS))
                    $('#divgms-rph-ts').text(formatRupiah(res.R_DIVGMS_RPH_TS))

                    //PERISHABLE
                    $('#divprsh-plu-plus').text(formatRupiah(res.R_DIVPRSH_PLU_PLUS))
                    $('#divprsh-rph-plus').text(formatRupiah(res.R_DIVPRSH_RPH_PLUS))
                    $('#divprsh-plu-minus').text(formatRupiah(res.R_DIVPRSH_PLU_MINUS))
                    $('#divprsh-rph-minus').text(formatRupiah(res.R_DIVPRSH_RPH_MINUS))
                    $('#divprsh-plu-ts').text(formatRupiah(res.R_DIVPRSH_PLU_TS))
                    $('#divprsh-rph-ts').text(formatRupiah(res.R_DIVPRSH_RPH_TS))

                    //COUNTER & PROMOTION
                    $('#divcntr-plu-plus').text(formatRupiah(res.R_DIVCNTR_PLU_PLUS))
                    $('#divcntr-rph-plus').text(formatRupiah(res.R_DIVCNTR_RPH_PLUS))
                    $('#divcntr-plu-minus').text(formatRupiah(res.R_DIVCNTR_PLU_MINUS))
                    $('#divcntr-rph-minus').text(formatRupiah(res.R_DIVCNTR_RPH_MINUS))
                    $('#divcntr-plu-ts').text(formatRupiah(res.R_DIVCNTR_PLU_TS))
                    $('#divcntr-rph-ts').text(formatRupiah(res.R_DIVCNTR_RPH_TS))

                    //FAST FOOD
                    $('#divfsf-plu-plus').text(formatRupiah(res.R_DIVFSF_PLU_PLUS))
                    $('#divfsf-rph-plus').text(formatRupiah(res.R_DIVFSF_RPH_PLUS))
                    $('#divfsf-plu-minus').text(formatRupiah(res.R_DIVFSF_PLU_MINUS))
                    $('#divfsf-rph-minus').text(formatRupiah(res.R_DIVFSF_RPH_MINUS))
                    $('#divfsf-plu-ts').text(formatRupiah(res.R_DIVFSF_PLU_TS))
                    $('#divfsf-rph-ts').text(formatRupiah(res.R_DIVFSF_RPH_TS))

                }

            },

            error: function(res) {
                console.log(res.text);
            }
        });

        const formatRupiah = (money) => {
            return new Intl.NumberFormat().format(money);
        }
    });
</script>