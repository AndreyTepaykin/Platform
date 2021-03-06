<div id="content" class="Streams_participating">
    <h2><?php echo $participating["Identifiers"]?></h2>
    <div class="Streams_participating_item" data-type="email" data-defined="<?php echo $user->emailAddress ? 'true' : 'false' ?>" data-subscribed="<?php echo $emailSubscribed ? 'true' : 'false' ?>">
        <span class="Streams_participating_id"><?php echo $user->emailAddress ?></span>
        <i class="Streams_participant_plus_icon"></i>
        <i class="Streams_participant_subscribed_icon"></i>
        <!--<i class="Streams_participant_delete_icon"></i>//-->
    </div>
    <div class="Streams_participating_item" data-type="mobile" data-defined="<?php echo $user->mobileNumber ? 'true' : 'false' ?>" data-subscribed="<?php echo $mobileSubscribed ? 'true' : 'false' ?>">
        <span class="Streams_participating_id"><?php echo $user->mobileNumber ?></span>
        <i class="Streams_participant_plus_icon"></i>
        <i class="Streams_participant_subscribed_icon"></i>
        <!--<i class="Streams_participant_delete_icon"></i>//-->
    </div>

    <?php if (count($devicesGrouped)) { ?>
        <h2><?php echo $participating["Devices"]?></h2>
		<?php
            foreach($devicesGrouped as $deviceName => $devices) {
				if (empty($devices)) {
					continue;
				}

				$content = '';
				foreach ($devices as $device) {
					$content .= '<div class="Streams_participating_item" data-type="device">';
					$content .= '   <span class="Streams_participating_id">'.$deviceName.'</span>';
					$content .= '   <input type="hidden" name="deviceId" value="'.$device->deviceId.'" />';
					$content .= '   <i class="Streams_participant_delete_icon"></i>';
					$content .= '</div>';
                }

				echo Q::Tool("Q/expandable", array(
					'title' => $deviceName.' <span>('.count($devices).')</span>',
					'content' => $content
				), $deviceName);

		    } ?>
    <?php } ?>

    <h2><?php echo $participating["Streams"]?></h2>
    <?php foreach($participantsGrouped as $streamType => $participants) {
        if (empty($participants)) {
            continue;
        }

        $content = '<div class="Streams_participating_stream">';
		//$content .= '<tr><th data-type="title">'.$participating['Title'].'</th>';
		//$content .= '<th data-type="checkmark">'.$participating['Subscribed'].'</th></tr>';
		$content .= join($participants);
		$content .= '</div>';

		echo Q::Tool("Q/expandable", array(
			'title' => $streamType.' <span>('.count($participants).')</span>',
            'content' => $content
		), $streamType);
    }?>
</div>