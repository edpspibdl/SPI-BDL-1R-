<style type="text/css">
	.order-card {
		    color: #fff;
		}

		.bg-c-blue {
		    background: linear-gradient(45deg,#4099ff,#2ed8b6);
		}

		.bg-c-green {
		    background: linear-gradient(45deg,#2ed8b6,#FFB64D);
		}

		.bg-c-yellow {
		    background: linear-gradient(45deg,#FFB64D,#ff53b2);
		}

		.bg-c-pink {
		    background: linear-gradient(45deg,#a653ff,#FFB64D);
		}

		.bg-c-ungu {
		    background: linear-gradient(45deg,#FFB64D,#53a9ff);
		}

		.bg-c-coklat {
		    background: linear-gradient(45deg,#53a9ff,#e786ff);
		}

		.card {
		    border-radius: 10px;
		    -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
		    box-shadow: 0 1px 2.94px 0.06px rgba(4,26,55,0.16);
		    border: none;
		    margin-bottom: 5px;
		    -webkit-transition: all 0.3s ease-in-out;
		    transition: all 0.3s ease-in-out;
			max-width: 100%;
		}

		.card .card-block {
		    padding: 10px;
			max-width: 100%;
		}

		.order-card i {
		    font-size: 26px;
		}
		.f-left {
		    float: left;
		}

		.f-right {
		    float: right;
		}
		hr{
			margin-top: 1rem;
			margin-bottom: 1rem
		}
		.text-bawah {
			padding-left: 10rem; 
			padding-top: 0px; 
			margin: 0px
		}

		.text-bawah:hover {
			cursor: pointer;
			color: #535353;
		}
		.m-b-20 {
			margin-top: 0.2rem;
		}
		:root {
		  --animate-delay: 0.5s;
		}
		
		.loader,
		.loader:before,
		.loader:after {
		  background: cornflowerblue;
		  -webkit-animation: load1 1s infinite ease-in-out;
		  animation: load1 1s infinite ease-in-out;
		  width: 1em;
		  height: 4em;
		}
		.loader {
		  color: cornflowerblue;
		  text-indent: -9999em;
		  margin: 120px auto;
		  position: relative;
		  font-size: 11px;
		  -webkit-transform: translateZ(0);
		  -ms-transform: translateZ(0);
		  transform: translateZ(0);
		  -webkit-animation-delay: -0.16s;
		  animation-delay: -0.16s;
		}
		.loader:before,
		.loader:after {
		  position: absolute;
		  top: 0;
		  content: '';
		}
		.loader:before {
		  left: -1.5em;
		  -webkit-animation-delay: -0.32s;
		  animation-delay: -0.32s;
		}
		.loader:after {
		  left: 1.5em;
		}
		@-webkit-keyframes load1 {
		  0%,
		  80%,
		  100% {
		    box-shadow: 0 0;
		    height: 4em;
		  }
		  40% {
		    box-shadow: 0 -2em;
		    height: 5em;
		  }
		}
		@keyframes load1 {
		  0%,
		  80%,
		  100% {
		    box-shadow: 0 0;
		    height: 4em;
		  }
		  40% {
		    box-shadow: 0 -2em;
		    height: 5em;
		  }
		}


		.noscroll {
			overflow-x: hidden;
			overflow-y: hidden;
		}

		table {
			font-size: 12px;
		}		
</style>

<?php 
function get_rekap() {
    $sql = "SELECT count(DISTINCT(mstd_kodesupplier)) AS jml_sup, 
                   count(DISTINCT(mstd_prdcd)) AS jml_prod, 
                   count(DISTINCT(mstd_nodoc)) AS jml_dok,
                   count(*) AS jml_all
            FROM tbtr_mstran_d
            WHERE mstd_recordid IS NULL
            AND mstd_typetrn = 'B'
            AND mstd_tgldoc::date = CURRENT_DATE";  

    include "../helper/connection.php";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'SUP'  => $row['jml_sup'] ?? 0,
            'PROD' => $row['jml_prod'] ?? 0,
            'DOK'  => $row['jml_dok'] ?? 0,
            'ALL'  => $row['jml_all'] ?? 0
        ];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return ['SUP' => 0, 'PROD' => 0, 'DOK' => 0, 'ALL' => 0];
    }
}

$BPBDAY = get_rekap();
?>


<div class="row d-flex flex-wrap">
  <div class="col-md-3 noscroll">
    <div class="card bg-c-blue order-card animate__animated animate__flipInX animate__delay-0.5s">
      <div class="card-block">
        <h4 class="m-b-20">PRODUK BPB</h4>
        <h2 class="text-right"><i class="fas fa-cart-arrow-down f-left"></i><span><b><?= $BPBDAY['PROD']; ?></b></span></h2>
        <hr>
        <p class="text-bawah bt_prod">Lihat Data</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3 noscroll">
    <div class="card bg-c-green order-card animate__animated animate__flipInX animate__delay-1s">
      <div class="card-block">
        <h4 class="m-b-20">DOKUMEN BPB</h4>
        <h2 class="text-right"><i class="fas fa-file-import f-left"></i><span><b><?= $BPBDAY['DOK']; ?></b></span></h2>
        <hr>
        <p class="text-bawah bt_dok">Lihat Data</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-3 noscroll">
    <div class="card bg-c-ungu order-card animate__animated animate__flipInX animate__delay-2s">
      <div class="card-block">
        <h4 class="m-b-20">SUPPLIER BPB</h4>
        <h2 class="text-right"><i class="fas fa-bus-alt f-left"></i><span><b><?= $BPBDAY['SUP']; ?></b></span></h2>
        <hr>
        <p class="text-bawah bt_supp">Lihat Data</p>
      </div>
    </div>
  </div>

  <div class="col-md-3 noscroll">
    <div class="card bg-c-pink order-card animate__animated animate__flipInX animate__delay-3s">
      <div class="card-block">
        <h4 class="m-b-20">DETAIL BPB</h4>
        <h2 class="text-right"><i class="fas fa-receipt f-left"></i><span><b><?= $BPBDAY['ALL']; ?></b></span></h2>
        <hr>
        <p class="text-bawah bt_det">Lihat Data</p>
      </div>
    </div>
  </div>
</div>
    

