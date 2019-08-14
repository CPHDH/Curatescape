<style>
.child{padding:1em;background: #eaeaea;display: inline-block;}
.child .columns.omega{margin-left: 0;}
</style>

<h2><?php echo __('Configurations'); ?></h2>

<fieldset>
  <h3><?php echo __('General Settings'); ?></h3>

  <div class="field">
      <div class="two columns alpha">
          <label for="srss_replace_default_rss"><?php echo __('Replace Default RSS Feed?'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <?php echo get_view()->formCheckbox('srss_replace_default_rss', true,
                  array('checked'=>(boolean)get_option('srss_replace_default_rss'))); ?>

          <p class="explanation"><?php echo __(
                    'If checked, the default RSS feed at <a target="_blank" href="'.WEB_ROOT.'/items/browse?output=rss2">/items/browse?output=rss2</a> will be replaced by the Super RSS feed. If unchecked, the Super RSS feed will be available at <a target="_blank" href="'.WEB_ROOT.'/items/browse?output=srss">/items/browse?output=srss</a> (adjust theme header as needed).'
                  ); ?></p>
      </div>
  </div>

  <div class="field linkback master">
      <div class="two columns alpha">
          <label for="srss_include_read_more_link"><?php echo __('Include link back to item?'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <?php echo get_view()->formCheckbox('srss_include_read_more_link', true,
                  array('checked'=>(boolean)get_option('srss_include_read_more_link'))); ?>

          <p class="explanation"><?php echo __(
                    'If checked, the text of each feed item will include a link back to the original, reading "For more, view the original article."'
                  ); ?></p>
      </div>
  </div>

  <div class="field linkback child">
      <div class="two columns alpha">
          <label for="srss_include_mediastats_footer"><?php echo __('Include media stats in link back to item?'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <?php echo get_view()->formCheckbox('srss_include_mediastats_footer', true,
                  array('checked'=>(boolean)get_option('srss_include_mediastats_footer'))); ?>

          <p class="explanation"><?php echo __(
                    'If checked, the included "read more" link for each RSS item will contain details about media files for the item, e.g. "For more (including 8 images, 4 sound clips, and 1 video), view the original article."'
                  ); ?></p>
      </div>
  </div>

</fieldset>

<fieldset>
  <h3><?php echo __('Social Media Accounts'); ?></h3>


  <div class="field social master">
      <div class="two columns alpha">
          <label for="srss_include_social_footer"><?php echo __('Include social media links?'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <?php echo get_view()->formCheckbox('srss_include_social_footer', true,
                  array('checked'=>(boolean)get_option('srss_include_social_footer'))); ?>

          <p class="explanation"><?php echo __(
                    'If checked, the text of each RSS item will include links to configured social media profiles, e.g. "Find us on Facebook, Twitter and Youtube."'
                  ); ?></p>
      </div>
  </div>

  <div class="field social child">
      <div class="two columns alpha">
          <label for="srss_facebook_link"><?php echo __('Facebook Link'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __("URL of related Facebook profile"); ?></p>

          <div class="input-block">
              <input type="text" class="textinput" name="srss_facebook_link" value="<?php echo get_option('srss_facebook_link'); ?>">
              <small><?php echo __("Example: http://www.facebook.com/pages/your_page/12345"); ?></small>
          </div>
      </div>
  </div>

  <div class="field social child">
      <div class="two columns alpha">
          <label for="srss_twitter_user"><?php echo __('Twitter Username'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __("Twitter username"); ?></p>

          <div class="input-block">
              <input type="text" class="textinput" name="srss_twitter_user" value="<?php echo get_option('srss_twitter_user'); ?>">
              <small><?php echo __('Note: Please <em>do not</em> include the @ symbol');?></small>
          </div>
      </div>
  </div>

  <div class="field social child">
      <div class="two columns alpha">
          <label for="srss_youtube_user"><?php echo __('Youtube Username'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __("Youtube username"); ?></p>

          <div class="input-block">
              <input type="text" class="textinput" name="srss_youtube_user" value="<?php echo get_option('srss_youtube_user'); ?>">
              <small><?php echo __("Example: username123"); ?></small>
          </div>
      </div>
  </div>

</fieldset>

<fieldset>
  <h3><?php echo __('App Store Details'); ?></h3>

  <div class="field appstore master">
      <div class="two columns alpha">
          <label for="srss_include_applink_footer"><?php echo __('Include app store links?'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <?php echo get_view()->formCheckbox('srss_include_applink_footer', true,
                  array('checked'=>(boolean)get_option('srss_include_applink_footer'))); ?>

          <p class="explanation"><?php echo __(
                    'If checked, the text of each RSS item will include links to configured app store downloads, e.g. "Download the [Site Title] app for iOS and Android."'
                  ); ?></p>
      </div>
  </div>

  <div class="field appstore child">
      <div class="two columns alpha">
          <label for="srss_ios_id"><?php echo __('iOS App Store ID'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __("ID for related app in iOS App Store"); ?></p>

          <div class="input-block">
              <input type="text" class="textinput" name="srss_ios_id" value="<?php echo get_option('srss_ios_id'); ?>">
              <small><?php echo __('Example: id123456789');?></small>
          </div>
      </div>
  </div>

  <div class="field appstore child">
      <div class="two columns alpha">
          <label for="srss_android_id"><?php echo __('Android App ID'); ?></label>
      </div>

      <div class="inputs five columns omega">
          <p class="explanation"><?php echo __("ID for related app in Google Play app market"); ?></p>

          <div class="input-block">
              <input type="text" class="textinput" name="srss_android_id" value="<?php echo get_option('srss_android_id'); ?>">
              <small><?php echo __('Example: com.developer.your.app');?></small>
          </div>
      </div>
  </div>

</fieldset>

<script>
	jQuery('fieldset .child').hide();
	jQuery('.field.master input:checked').parentsUntil('fieldset','.master').addClass('checked').siblings('.child').show();
	jQuery('.field.master input').change(function(){
		jQuery(this).parentsUntil('fieldset','.master').toggleClass('checked').siblings('.child').slideToggle();
	});
</script>
