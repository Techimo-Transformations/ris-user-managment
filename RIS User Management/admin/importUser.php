 <div class="wrap imform">
 <form method="post" id="importUserForm" enctype="multipart/form-data">
       
        <div class="form-group">
            <label for="InputName" style="display:block">Upload users csv file </label>
            <input type="file" name="InputImport" class="form-control custom-file-input" accept=".csv, text/csv" id="InputImport" placeholder="Name" required>


        </div>

        <button type="submit" name="importUserSubmit" id="importUserSubmit" class="btn btn-success">Import Now</button>

    </form> 
	<div class="alert alert-warning" role="alert">
	<a class="sampleFile" href="<?php echo home_url();?>/wp-content/uploads/csv/sample.csv" target="_blank">Sample File</a>
	<p>Please Use this file</p>
	</div>
</div>