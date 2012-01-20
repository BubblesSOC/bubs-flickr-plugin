<div class="wrap">
	<h2>Bubs' Flickr Plugin Settings</h2>
	
	<form method="post" action="options.php">
<?php settings_fields( 'bfp_options-group' ); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">Flickr Username</th>
				<td><input type="text" name="bfp_username" value="<?php echo get_option('bfp_username'); ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Flickr NSID (<a href="http://idgettr.com/" title="Find your Flickr NSID">find yours</a>)</th>
				<td><input type="text" name="bfp_nsid" value="<?php echo get_option('bfp_nsid'); ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Number of Photos to Display</th>
				<td><input type="text" name="bfp_photo_count" value="<?php echo get_option('bfp_photo_count'); ?>" class="small-text" /></td>
			</tr>
		</table>
		
		<h3>Display Settings</h3>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Before List of Photos</th>
				<td>
					<input type="text" name="bfp_before_list" value="<?php echo htmlspecialchars(get_option('bfp_before_list')); ?>" />
					<span class="description">i.e. <code>&lt;ul&gt;</code> or <code>&lt;div&gt;</code></span>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Style of Each Photo</th>
				<td>
					<textarea name="bfp_photo_style" rows="10" cols="50"><?php echo htmlspecialchars(get_option('bfp_photo_style')); ?></textarea><br />
					Available Tags:<br />
					<code>%photo_url%</code> - URL of individual photo (Example: <code>http://www.flickr.com/photos/username/12345/</code>)<br />
					<code>%photo_title%</code> - Title of photo<br />
					<code>%img_sq%</code> - URL of 75x75 square photo (Example: <code>http://farm1.static.flickr.com/2/1418878_1e92283336_s.jpg</code>)<br />
					<code>%img_t%</code> - URL of thumbnail (100px on longest side) (Example: <code>http://farm1.static.flickr.com/2/1418878_1e92283336_t.jpg</code>)<br />
					<code>%width_t%</code> - Width of thumbnail<br />
					<code>%height_t%</code> - Height of thumbnail<br />
					<code>%img_s%</code> - URL of small photo (240px on longest side) (Example: <code>http://farm1.static.flickr.com/2/1418878_1e92283336_m.jpg</code>)<br />
					<code>%width_s%</code> - Width of small photo<br />
					<code>%height_s%</code> - Height of small photo<br />
					<code>%img_m%</code> - URL of medium photo (500px on longest side) (Example: <code>http://farm1.static.flickr.com/2/1418878_1e92283336.jpg</code>)<br />
					<code>%width_m%</code> - Width of medium photo<br />
					<code>%height_m%</code> - Height of medium photo<br />
					<code>%img_o%</code> - URL of original image (Example: <code>http://farm1.static.flickr.com/2/1418878_1e92283336_o.jpg</code>)<br />
					<code>%width_o%</code> - Width of original image<br />
					<code>%height_o%</code> - Height of original image<br />
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">After List of Photos</th>
				<td>
					<input type="text" name="bfp_after_list" value="<?php echo htmlspecialchars(get_option('bfp_after_list')); ?>" />
					<span class="description">i.e. <code>&lt;/ul&gt;</code> or <code>&lt;/div&gt;</code></span>
				</td>
			</tr>
		</table>
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Settings') ?>" />
		</p>

	</form>
</div>
