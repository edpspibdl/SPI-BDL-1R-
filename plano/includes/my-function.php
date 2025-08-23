<?php

	function sentence_case($string) {
	    $sentences = preg_split('/([.? !]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
	    $new_string = '';
	    foreach ($sentences as $key => $sentence) {
	        $new_string .= ($key & 1) == 0?
	            ucfirst(strtolower(trim($sentence))) :
	            $sentence.' ';
	    }
	    return trim($new_string);
	} 

	function getClientIP() {

    if (isset($_SERVER)) {

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
	}


	// konversi tanggal yyyymmdd -> dd-mm-yyyy
	function tanggalIND($tanggal) {
  		return 	substr($tanggal,6,2) . '-' .
				substr($tanggal,4,2) . '-' .
				substr($tanggal,0,4);
	}

	// hitung harga jual bersih
	function hargaNetto($harga, $bkp) {
		if ($bkp == 'Y') {
			return $harga / 1.1;
		} else {
			return $harga;
		}
	}

	function marginRupiah($harga, $cost, $bkp) {
		if ($bkp == 'Y') {
			$harga = $harga / 11 * 10;
		} 
		$marginRupiah = $harga - $cost;
		return $marginRupiah;
	}

	function marginPersen($harga, $cost, $bkp) {
		if ($bkp == 'Y') {
			$harga = $harga / 11 * 10;
		} 
		$marginRupiah = $harga - $cost;
		return $marginRupiah / $harga * 100;
	}


?>