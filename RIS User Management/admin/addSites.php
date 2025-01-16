            <!--Add Company User-->
            <form method="post"  id="addSiteForm">
                <table class="form-table">
                    <tbody>

                        <!--Username-->
                        <tr class="form-field form-required">
                            <th scope="row"><label for="siteName"><?php _e('Add Site', 'ld_refresher') ?> <span class="description">(<?php _e('required', 'ld_refresher') ?>)</span></label></th>
                            <td><input name="SiteName" type="text" id="siteName" required="true" autocapitalize="none" autocorrect="off" maxlength="60"></td>
                        </tr>
                        <!--End Username-->
                       
                    </tbody>
                </table>


                <p class="submit"><input type="submit" id="addSiteSubmit" class="button button-primary" value="<?php _e('Add Site', 'ld_refresher') ?>"></p>

                <div id="result"></div>
            </form>

         
<script type="text/javascript">
jQuery(document).ready(function($) {
jQuery("#addSiteSubmit").click(function() {
 jQuery('#addSiteForm').submit(function(e) {
  e.preventDefault();
  var siteName = jQuery('#siteName').val();
    var ajaxURL = "<?= admin_url('admin-ajax.php');?>";  
     jQuery.ajax({
      url: ajaxURL,
      type: 'post',
      dataType:'json',
      data: {
         action: 'addSideAction',
         siteName:siteName
               },            
      success: function(result) {
      // alert(result.responce);
       console.log(result);
       if(result.responce == '1'){
          $('#result').html(result.message);
          $( "#result" ).addClass( "alert alert-success" );
       }if(result.responce =='2'){
          $('#result').html(result.message);
          $( "#result" ).addClass( "alert alert-danger" );
           // var delay = 3000;
           //  var url = '<?//=home_url('/dashboard');?>';
           //  setTimeout(function(){ window.location = url; }, delay);
        }if(result.responce =='3'){
         $('#result').html(result.message);
         $( "#result" ).addClass( "alert alert-danger" );
      }
   },
   error: function() {
    alert("There was an issue");
 },
});
  });

});
});
</script>

