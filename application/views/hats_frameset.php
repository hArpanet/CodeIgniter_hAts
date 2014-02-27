<?php
 // An example layout using framesets (a simple two frame horizontal split)
 // Note the additional template variables needed in order to
 // specify the sizes and content of the framesets. ?>
<frameset cols="100%" rows="<?= tplGet('top_rows'); ?>, <?= tplGet('bottom_rows'); ?>">
  <frame scrolling="<?= tplGet('top_scrolling'); ?>" name="top_frame" src="<?= tplGet('top_frame'); ?>" />
  <frame scrolling="<?= tplGet('bottom_scrolling'); ?>" name="bottom_frame" src="<?= tplGet('bottom_frame'); ?>" />
</frameset>
