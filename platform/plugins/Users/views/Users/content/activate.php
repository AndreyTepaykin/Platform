<div id="content">
<?php if ($user) : ?>
	<?php if (empty($_REQUEST['p']) and !empty($user->passphraseHash)): ?>
		<div class="Q_extra_pane">

		</div>
		<div class="Q_admin_pane">
			<?php echo Q::tool('Users/avatar', array(
				'icon' => true
			)) ?>
			<?php echo Q_Html::form(Q_Dispatcher::uri(), 'post', array('id' => 'Q_activation_form')) ?>
				<?php echo Q_Html::formInfo(null) ?>
				<div class='Q_big_prompt'>
					<input type="hidden" name="code" value="<?php echo Q_Html::text($code) ?>">
					<input type="hidden" id="activate_identifier" name="<?php echo $t ?>"
						value="<?php echo Q_Html::text($identifier) ?>">
					<div class='Q_buttons'>
						<button id="Users_activate_set_emailAddress" type="submit">
							Set
							<?php echo Q_Html::text($identifier) ?>
							as my primary <?php echo $type ?>
						</button>
					</div>
				</div>
			</form>
		</div>
	<?php else: ?>
		<div class="Q_extra_pane">
			<h2>Suggestions</h2>
			<ul id='suggestions'>
				<?php foreach ($suggestions as $s): ?>
					<li class="fromServer"><?php echo Q_Html::text($s) ?></li>
				<?php endforeach ?>
			</ul>
		</div>
		<div class="Q_admin_pane">
				<?php echo Q::tool('Users/avatar', array(
					'icon' => true,
					'user' => $user
				)) ?>
				<div class='Q_big_prompt'>
					<p>
						In order to protect your account, you should set up a pass <strong>phrase</strong>.<br>
						In case you're wondering, passphrases are harder to guess, and<br>
						easier to type than passwords with weird characters.<br>
						See the suggestions on the right for some ideas.
					</p>
					<?php echo Q_Html::form(Q_Dispatcher::uri(), 'post', array('id' => 'Q_activation_form')) ?>
						<?php echo Q_Html::formInfo(null) ?>
						<label for='activate_passphrase'>Please type a pass phrase for your account:</label><br>
						<input type="password" id='activate_passphrase' name="passphrase" class='password' /><br>
						<button type="submit">Activate My Account</button>
						<input type="hidden" id="activate_identifier" name="<?php echo $t ?>"
							value="<?php echo Q_Html::text($identifier) ?>">
						<input type="hidden" name="code" value="<?php echo Q_Html::text($code) ?>">
					</form>
				</div>
		</div>
	<?php endif; ?>
<?php elseif (Users::loggedInUser()): ?>
	<h1 class='Q_big_message'>If you feel something went wrong, <button id='activate_setEmail'>try again</button></h1>
<?php else: ?>
	<h1 class='Q_big_message'>Please <a href='#' id='activate_login'>log in</a> and get another email sent to you.</h1>
<?php endif; ?>

<?php Q_Response::addScript('plugins/Q/js/Q.js'); ?>
<?php Q_Response::addScript('plugins/Q/js/JSON.js'); ?>

<?php Q_Response::addScriptLine("jQuery(function() {
	$('#activate_setEmail').click(function() { Q.plugins.Users.setIdentifier(); });
	$('#activate_login').click(function() { Q.plugins.Users.login(); });
	$('#activate_passphrase').val('').focus();
	
	// Get the suggestions from YAHOO, if possible
	
	// this used to work:
	var url = 'http://query.yahooapis.com/v1/public/yql?format=json&diagnostics=false&q=select%20abstract%20from%20search.news%20where%20query%3D%22$noun_ue%22';
	// but YAHOO now deprecated the search.news table.
	// later we can pay for BOSS to do this. But for now, here is what we do:
	var url = 'http://query.yahooapis.com/v1/public/yql?format=json&diagnostics=false&q=select%20Rating.LastReviewIntro%20from%20local.search%20where%20zip%3D%2294085%22%20and%20query%3D%22$noun_ue%22';
	
	Q.jsonRequest(url, null, function(err, data) {
		if (data.query && data.query.results && data.query.results.Result) {
			// var r = data.query.results.result;
			var r = data.query.results.Result;
			var ul = $('#suggestions');
			var rand, text;
			var source = '';
			for (var i=0; i<r.length; ++i) {
				text = r[i].Rating.LastReviewIntro;
				if (text) {
					source += ' ' + text;
				}
			}
			var source_words = source.toLowerCase().replace(/[^A-Za-z0-9-_\'. ]/g, '').split(' ');
			for (var i=0; i<7; ++i) { // add seven quotes from here
				rand = Math.floor(Math.random() * source_words.length);
				$('li', ul).eq(0).remove();
				ul.append($('<li />').addClass('fromYahoo').html(
					// this used to work for YAHOO news search:
					// text = r[i].abstract;
					// now we do this:
					source_words.slice(rand, rand+3).join(' ')
				));

			}
		}

	}, {'callbackName': 'callback'});
}); "); ?>
</div>