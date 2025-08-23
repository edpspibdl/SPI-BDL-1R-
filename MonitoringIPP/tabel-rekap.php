
<div class="table-responsive">
    <table align="center" class="table table-bordered table-striped table-hover table-nonfluid bayang">
        <thead>
            
        </thead>
        <tbody>
            <?php
            //$noUrut = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //$noUrut++;
                //echo '<tr>';
              	    echo '<th align="left"><h5><strong>' . $row['sti_drivername'] . '</th>';
							echo '<th align="center" style="color: #EE4B2B"><h5><strong>' . $row['total_sertim'] . '</th>';
                //echo '</tr>';
            }

            ?>
        </tbody>
    </table>
</div>






	
