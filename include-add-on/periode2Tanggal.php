<div class="form-group"> 
    <form action="index.php" method="post"> <!-- Set the action to your PHP script -->
        <!-- Tanggal Ke 1 -->
        <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="tanggalMulai" data-link-format="yyyy-mm-dd">
            <input class="form-control" type="text" id="dateStart" name="tanggalMulai" value="" readonly>
            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
        <input type="hidden" id="tanggalMulai" name="tanggalMulai" value="" />

        <p></p>
        <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="tanggalSelesai" data-link-format="yyyy-mm-dd">
            <input class="form-control" type="text" id="dateEnd" name="tanggalSelesai" value="" readonly>
            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </div>
        <input type="hidden" id="tanggalSelesai" name="tanggalSelesai" value="" /><br/>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
