  <?php
		

		$time_start = microtime(true); 


		if(!isset($_GET['divisi'])) {
			
			$condition= "";
		} else {
			switch ($_GET['divisi']) {
				case 1: $condition="where plu in (select prd_prdcd from tbmaster_prodmast where prd_prdcd like '%0' and prd_kodedivisi='1')";
				break;
				case 2: $condition="where plu in (select prd_prdcd from tbmaster_prodmast where prd_prdcd like '%0' and prd_kodedivisi='2')";
				break;
				case 3: $condition="where plu in (select prd_prdcd from tbmaster_prodmast where prd_prdcd like '%0' and prd_kodedivisi='3')";
				break;
			}
			
			
		}


		$ora= oci_parse($conn,"
				select * from PHP_REKOMENDASI_SECOND ".$condition."
		 
		");

		oci_execute($ora) or die (oci_error());



		$temp=array();
		$counter=0;
		while (($row = oci_fetch_array($ora, OCI_NUM)) != false) {
		   
			
			
			
			$ora2= oci_parse($conn,"
				select * from PHP_REKOMENDASI_FIRST WHERE PLU='".$row[0]."' and substr(exp_date,4,6)='".$row[1]."'  order by maxpallet_min_plano desc");


			oci_execute($ora2) or die (oci_error());
			
				while (($row2 = oci_fetch_array($ora2, OCI_NUM)) != false) {
					
					$temp[$counter][]=$row2;
					
				}
			
			
			$counter++;
				

		}

			/* echo '<pre>';
			print_r($temp);
			
			echo '</pre>'; */    	
		  
		 
		 function rekomendasi($input) {
			 //cek all lokasi
			 //cari lokasi dengan space kosong terkecil (maxpallet- (plano+spb))
			 //LOOP
			 //cari lokasi dengan space kosong terbesar-> jika hitung sisa plano 1 + plano tujuan masih kurang dari maxpallet, next lokasi 2, 
			 //
			 //
			 //
			 
				
		 
			 
			 //setelah ketemu isi box1 dan box 2
			 $back=array();
			 $all_lokasi=$input;
			 $count_all_lokasi=count($all_lokasi);

				
				for ($i=0; $i<count($input); $i++) {
					if (!isset($box1)){
						
						$box1[0]=$all_lokasi[$i];
						
						
						
					} else {
					
					
					

					if(!empty($box1[0])) {	
							
							
							
							switch ($all_lokasi[$i]) {
								
							case ($all_lokasi[$i][9] < $box1[0][9] ) :    $box2[]=$box1[0];  unset($box1[0]); $box1[0]=$all_lokasi[$i];
								break;
							case  ($all_lokasi[$i][9] > $box1[0][9] ) :   $box2[]=$all_lokasi[$i];
								break;
							case  ($all_lokasi[$i][9] == $box1[0][9] ) :  $box2[]=$all_lokasi[$i];
								break;
							}
						
					}	
					
					}
				}
				
				
				//echo count($box2).'<br/>';
				//qty space kosong tersedia 
				
				//box 5 berisi box2 yang akan didelete jika sudah termasuk kandidat
				$box5=$box2;
				/* echo'box2';
				echo '<pre>';
				print_r($box2);
				echo '</pre>';  */
				
				if (!empty($box1[0])) {
					//qty karton space
					$space=$box1[0][9];
					
					//echo $space.'<=SPACE<br/>';
					
					
					
						if (count($box2) > 0) {
							for ($k=0; $k<count($box2); $k++) {
									if (!isset($box3)) {
										
										$box3[0]=$box2[$k];
										
										
									} else {
										
										if (!empty($box3[0])) {
											switch ($box2[$k]) {
												
												
												case ($box2[$k][9] > $box3[0][9] ) :  unset($box3[0]); $box3[0]=$box2[$k];												
													break;
												case ($box2[$k][9] == $box3[0][9]) : if ($box2[$k][7] >  $box3[0][7] ) { unset($box3[0]); $box3[0]=$box2[$k]; }	
													break;									
												
											}
											
										}
										
										
										
									}
										
									
								
								
							}
							
										
										
								$canditat[0]=$box3[0];
								$candidat_qty[0]=$box3[0][8];
								
										//echo 'KANDIDAT1';
										//print_r($canditat[0]);
										
								
							
								
								
								
								
								//echo 'kandidat';
								//print_r($canditat);
					
								
								
								
								$z=count($box2);
								$x=0;
								do  {
									//echo $x.'--X';			echo '<br/>';				
										//cek apakah box5 sudah ada di kandidat, jika ada delete key 
										
										for ($m=0; $m<count($canditat); $m++){
											//echo count($box5).'<=jumlah box<br/>';
											/* 
											echo 'cek isi box <br/>';
											echo '<pre>';
											print_r($box5);
											echo '</pre>';
											echo 'end of cek isi box <br/>';; */
											
											for ($n=0; $n<count($box5); $n++){
												
												//cek agiiiiiii
											if (isset($box5[$n])) {
												if ($candidat[$m][3]=$box5[$n][3]){
													
													
													unset($box5[$n]);
													$n=count($box5);
													
												}  
												
											}
												
											}
											
											//print_r($box5);
																		
										}
										
										array_filter($box5);		
										sort($box5);
										
										
										
										
										
										
										
										//-------------------------------
										if (isset($box5)) {
											if(count($box5)>0) {
												
													for ($k=0; $k<count($box5); $k++) {
														//echo count($box5).'--cekjumlah box5 <br/>';
														//echo $k.'--cekloop <br/>';
															if (!isset($box6)) {
																
																$box6[0]=$box5[$k];
																	//echo 'cek0';
																
															} else {
																
																//echo 'cek--'; echo $k;
																
																if (!empty($box6[0])) {
																	
																	
																	switch ($box5[$k]) {
																		
																		
																		
																		case ($box5[$k][9] > $box6[0][9] ) :  unset($box6[0]); $box6[0]=$box5[$k];												
																			break;
																		case ($box5[$k][9] == $box6[0][9]) : if ($box5[$k][7] > 0 && $box6[0][7]==0 ) { unset($box6[0]); $box6[0]=$box5[$k]; }	
																			break;

																				
																		
																	}
																	
																}
																
																
																
															}
																
															
														
														
													}
													/* echo 'kanditat <br/>';
													print_r($box6);
													echo 'end kanditat <br/>'; */
													
													
													$canditat[]=$box6[0];
													$candidat_qty[]=$box6[0][8];
													unset($box6); 		
											}
																		
											
										}			//echo 'BOX3';
													//print_r($box3[0]);
											
										
										
										//--------------------------------
										
										
										
										
										
										
										 
										 $total=array_sum($candidat_qty);
								
									//evaluate box5
										
										
									//
								
									$x++;
										
								
								 } while  ($x<$z);
								 
								// echo count($candidat).'--kandidat';
								// echo count($box2).'--box2';
							
							//echo $space-$canditat[0][7];
							
						} 
						//end loop
				
				}
				//
				
					/* 	$keys = array();
						foreach ($box2 as $box2) $keys[] = intval($box2[9]);
						array_multisort($keys, $box2);	 */
						
						
					
						
						
				
				$back[]=$box1;
				$back[]=$canditat;
				//print_r($back);
			 return $back;
			 //return var_dump($all_lokasi);;
				
				
			 
			 
			 
			 
		 }
			
			 for ($k=0; $k<count($temp); $k++) {
				 
					
						if (count($temp[$k]) >1) {	
								
							  
							/* 	echo '<pre>';
								print_r(rekomendasi($temp[$k]));
								echo '</pre>'; */
								
								$data[]=rekomendasi($temp[$k]);
						
									
						}
								
						
				 
				}   
			 
						
			 
		$time_end = microtime(true);

		//dividing with 60 will give the execution time in minutes other wise seconds
		$execution_time = ($time_end - $time_start)/60;

		//execution time of the script
		//echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';

		oci_close($conn);
		
		
		//secho count($data);
		
		/* echo '<pre>';
		print_r($data);
		echo '</pre>';   */ 
		
		//sort($data);
		?>
	
	

  
 
  

  
  


  

  <div class="panel bayang">
  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
      <thead>
        <tr  class="info">
          <th>#</th>
          <th>Asal</th>
          <th>Tujuan</th>
          <th>Nama Barang</th>
        </tr>
      </thead>
      <tbody>

          <?php
			$x=1;
			for ($a=0; $a<count($data); $a++){
					
				
				$qty_plano_client=$data[$a][0][0][9];
				$qty_kirim=0;
				for ($b=0; $b<count($data[$a][1]); $b++) {
					
					if ($qty_kirim==0) {
						//echo 'a';
					
						switch ($data[$a][1][$b][8]) {
						
							
							case ($data[$a][1][$b][8] >= $qty_plano_client) : 
										//echo 'B';
									$give_qty=$qty_plano_client;
									echo '<tr>';	
										
									echo '<td><h4>'.$x.'</h4></td><td><h4>'.$data[$a][1][$b][3].' </h4>Give: '.$give_qty.'CTN</td><td><h4>'.$data[$a][0][0][3].'</h4>Space: '.$data[$a][0][0][9].'CTN</td><td><h4>'.$data[$a][0][0][1].'</h4>'.$data[$a][0][0][0].'/'.$data[$a][0][0][2].'</td>';	
									echo '</tr>';
									$qty_kirim=$give_qty;
									$b=count($data[$a][1]);		
							
							break;
							
							case ($data[$a][1][$b][8] < $qty_plano_client) :
									//echo 'C';
									$give_qty=$data[$a][1][$b][8];
									echo '<tr>';	
										
									//echo '<td>'.$x.'</td><td>'.$data[$a][1][$b][3].'&nbsp; GIVE: '.$give_qty.'CTN</td><td>'.$data[$a][0][0][3].'&nbsp FREE SPACE:'.$data[$a][0][0][9].' CTN</td><td>'.$data[$a][0][0][0].'-'.$data[$a][0][0][1].'</td>';	
									echo '<td><h4>'.$x.'</h4></td><td><h4>'.$data[$a][1][$b][3].' </h4>Give: '.$give_qty.'CTN</td><td><h4>'.$data[$a][0][0][3].'</h4>Space: '.$data[$a][0][0][9].'CTN</td><td><h4>'.$data[$a][0][0][1].'</h4>'.$data[$a][0][0][0].'/'.$data[$a][0][0][2].'</td>';	
									
									echo '</tr>';
									$qty_kirim=$give_qty;		
							
							
							break;
							
							
							
							
						}
				
					} else {
						
						//echo 'b';
						//echo 'D';
						if ($qty_kirim <$qty_plano_client ) {
							$sisa=$qty_plano_client-$qty_kirim;
							switch ($data[$a][1][$b][8]) {
								
									
								
									case ($data[$a][1][$b][8] >= $sisa) : 
									//echo 'E';
											
											$before=$sisa;
											
											//echo 'before'.$before;
											
											//$give_qty=$data[$a][1][$b][8]-$sisa;
											$give_qty=$sisa;
											//echo  'give qty'.$give_qty; 
											echo '<tr>';	
											
											//echo $data[$a][1][$b][3];
											
											echo '<tr>';
												echo '<td><h4>'.$x.'</h4></td><td><h4>'.$data[$a][1][$b][3].'</h4>GIVE: '.$give_qty.'CTN</td><td><h4>'.$data[$a][0][0][3].'</h4>SPACE: '.$sisa.'CTN</td><td><h4>'.$data[$a][0][0][1].'</h4>'.$data[$a][0][0][0].'/'.$data[$a][0][0][2].'</td>';
												
											echo '</tr>';
											$qty_kirim +=$give_qty;
											
											$b=count($data[$a][1]);
										
										
									
									break;
									
									case ($data[$a][1][$b][8] < $sisa) :
									
									//echo 'F';
											
											$before=$sisa;
											//echo 'before'.$before;											
											$give_qty=$data[$a][1][$b][8];
											//echo  'give qty'.$give_qty; 
											echo '<tr>';	
											
											//echo $data[$a][1][$b][3];
											
											echo '<tr>';
												echo '<td><h4>'.$x.'</h4></td><td><h4>'.$data[$a][1][$b][3].'</h4>GIVE: '.$give_qty.'CTN</td><td><h4>'.$data[$a][0][0][3].'</h4>SPACE: '.$sisa.'CTN</td><td><h4>'.$data[$a][0][0][1].'</h4>'.$data[$a][0][0][0].'/'.$data[$a][0][0][2].'</td>';
												
											echo '</tr>';
											$qty_kirim +=$give_qty;
											
											
									
									break;
								}
					}
					
					
					/* echo '<tr>';	
						
					echo '<td>'.$x.'</td><td>'.$data[$a][1][$b][3].'&nbsp;: '.$data[$a][1][$b][8].'CTN</td><td>'.$data[$a][0][0][3].'</td>';	
					echo '</tr>';		
						 */
					}
				
				}
			
			$x++;
			}

          ?>
      	
        
        
        
      </tbody>
    </table>
  </div>
  </div>
