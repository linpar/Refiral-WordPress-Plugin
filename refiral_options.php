<style type="text/css">
.refiral_button {
	background: #F9745F;
	padding: 13px 15px;
	border-radius: 4px;
	margin-top: -13px;
	font-weight: bold;
	border: 0;
	cursor: pointer;
	-webkit-transition: all 0.2s ease-in;
	-moz-transition: all 0.2s ease-in;
	-o-transition: all 0.2s ease-in;
	transition: all 0.2s ease-in;
	color: #FFF;
}
.refiral_button:hover {
	background: #6DBDDC;
}
.refiral_col {
	width: 50%;
	float: left;
}
.refiral_desc {
	float: right;
	width: 40%;
	margin-top: 20px;
	background: #FFF;
	padding: 10px;
	border-radius: 10px;
}
.refiral_logo {
	width: 96px;
	margin: 0 auto;
}
</style>
<div class="wrap">
	<div class="refiral_col">
	    <h2>Refiral Campaign Settings</h2>
	    <hr/>
	    <?php
	    $this->get_refiral_options();
	    
	    if(isset($_POST['submit']))
	    {
	    	$this->options['refiral_key'] = $_POST['refiral_key'];
	    	$this->options['refiral_enable'] = $_POST['refiral_enable'];
	   		$this->update_refiral_options();
	   		echo '<div class="updated"><p><strong>Options saved.</strong></p></div>';
	    }

	    // Check if WooCommerce is active
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	    ?>

	    <form name="<?php echo $this->plugin_id;?>_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	        <table cellpadding="5" cellspacing="5">
	        	<tr>
	        		<td>
	        			<label><strong>Refiral Key:</strong></label>
	        		</td>
	        		<td>
	        			<input style="width:100%; height: 35px; border-radius:5px;" type="text" name="refiral_key" value="<?php echo $this->options['refiral_key']; ?>" placeholder="Refiral API Key">
	        		</td>
	        	</tr>
	        	<tr>
	        		<td></td>
	        		<td>
	        			<small>(You first need to create an account on Refiral.com and set-up your campaign. <a href="http://my.refiral.com/signup" target="_blank">Click here</a> to sign up.)</small>
	        			<hr/>
	        		</td>
	        	</tr>
	        	<tr>
	        		<td style="padding-bottom: 20px">
	        			<label><strong>Enable Campaign:</strong></label>
	        		</td>
	        		<td>
	        			<input type="checkbox" name="refiral_enable" <?php if($this->options['refiral_enable'] == 'on') echo 'checked'; ?> >
	        			<hr/>
	        		</td>
	        	</tr>
	        	<tr>
	        		<td></td>
	        		<td>
	        			<p class="submit"><input class="refiral_button" type="submit" name="submit" value="Update Options" /></p>
	        		</td>
	        	</tr>
	        </table>        
	    </form>
		<?php
		}
	    else
	        echo '<div class="wrap"><h3 style="color:#F36969">Either WooCommerce is not installed or it is not active.</h3></div>';
	   	?>
	</div>
	<div class="refiral_desc">
		<div class="refiral_logo"><a target="_blank" href="http://www.refiral.com"><img src="http://my.refiral.com/images/logo.png" width="100%" /></a></div>
		<hr/>
		<h3 style="text-align: center;">Launch your referral campaign virally.</h3> 
		<h4 style="text-align: justify;">Boost your sales upto 3X with our new hybrid marketing channel. Run your personalized, easy to integrate fully automated referral program.</h4>
	</div>
</div>