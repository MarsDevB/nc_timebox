<?php
/**
 * Nextcloud - TimeBox personal settings
 *
 * @var array $_
 */
script('timebox', 'timebox-settings');
?>
<div id="timebox-personal-settings" class="section">
<h2>TimeBox</h2>
<p class="settings-hint"><?php p($l->t('Limit how many items and calendar events are shown in the TimeBox app.')); ?></p>
<div class="timebox-settings__field">
<label for="timebox-max-items"><?php p($l->t('Maximum number of items displayed')); ?></label>
<input type="number" id="timebox-max-items" min="1" max="500" value="<?php p($_['maxItems']); ?>" />
</div>
<div class="timebox-settings__field">
<label for="timebox-max-events"><?php p($l->t('Maximum number of calendar events displayed')); ?></label>
<input type="number" id="timebox-max-events" min="1" max="500" value="<?php p($_['maxEvents']); ?>" />
</div>
<button class="primary" id="timebox-settings-save"><?php p($l->t('Save')); ?></button>
<span id="timebox-settings-status" style="margin-left: 10px;"></span>
</div>
