let AutoUpdater = {
	previous_status: undefined,
	current_version: {},
	releases: undefined,
	release_count: undefined,
	get_releases: function(callback) {
		var settings = {
			async: true,
			crossDomain: true,
			url: 'https://api.github.com/repos/bludit/bludit/releases',
			method: 'GET',
			headers: {}
		};

		$.ajax(settings).done(function(response) {
			callback(response);
		});
	}
};
AutoUpdater.get_releases(function(releases) {
	AutoUpdater.releases = releases;
	AutoUpdater.release_count = releases.length;
	counter = 0;
	releases.forEach((release) => {
		if (BLUDIT_VERSION == release.tag_name) {
			AutoUpdater.current_version.tag_name = release.tag_name;
			AutoUpdater.current_version.name = release.name;
			AutoUpdater.current_version.zipball_url = release.zipball_url;
			AutoUpdater.current_version.tarball_url = release.tarball_url;
			AutoUpdater.current_version.published_at = release.published_at;
		} else {
			counter++;
		}
	});
	if (counter < AutoUpdater.release_count) {
		$('#autoupdater_dynamic_content').html(`
        <div class="alert alert-info">
		installed version: <code>${BLUDIT_VERSION} (BUILD ${BLUDIT_BUILD}) (name: "${AutoUpdater.current_version
			.name}")</code>
        </div>
        <div class="alert alert-info">
        UPDATE AVAILABLE: <code>${AutoUpdater.releases[0].tag_name}</code> (<code>${AutoUpdater.releases[0]
			.name}</code>)
        <br>
        published at: <code>${AutoUpdater.releases[0].published_at}</code>
        <br>
        zipball: <code>${AutoUpdater.releases[0].zipball_url}</code>
        </div>
        <div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input" id="autoupdater_keep_zip">keep ZIP file</label></div>
        <br>
        <button type="button" id="autoupdate_start_update" class="btn btn-primary">Update now! 🚀</button>
        <div id="update_progress"></div>
        `);
		$('#autoupdate_start_update').click(function(e) {
			$('#autoupdate_start_update').unbind('click');
			$('#autoupdate_start_update').html('Updating... 🔨');
			$('#autoupdate_start_update').removeClass('btn-primary');
			$('#autoupdate_start_update').addClass('btn-warning');
			// $('#autoupdate_start_update').attr('disabled', '');
			let zip_path = `https://github.com/bludit/bludit/archive/${AutoUpdater.releases[0].tag_name}.zip`;
			$.post(HTML_PATH_ROOT + 'bl-plugins/bludit-auto-update/perform-update.php', {
				url: zip_path,
				tag: AutoUpdater.releases[0].tag_name,
				action_init: true
			}).done(function(data) {
				if (data == 'init-done') {
					$.post(HTML_PATH_ROOT + 'bl-plugins/bludit-auto-update/perform-update.php', {
						url: zip_path,
						tag: AutoUpdater.releases[0].tag_name,
						action_download: true
					}).done(function(data) {
						if (data == 'download-done') {
							$.post(HTML_PATH_ROOT + 'bl-plugins/bludit-auto-update/perform-update.php', {
								url: zip_path,
								tag: AutoUpdater.releases[0].tag_name,
								action_unzip: true
							}).done(function(data) {
								if (data == 'unzip-done') {
									$.post(HTML_PATH_ROOT + 'bl-plugins/bludit-auto-update/perform-update.php', {
										url: zip_path,
										tag: AutoUpdater.releases[0].tag_name,
										action_update_language: true
									}).done(function(data) {
										if (data == 'action_update_language-done') {
											$.post(
												HTML_PATH_ROOT + 'bl-plugins/bludit-auto-update/perform-update.php',
												{
													url: zip_path,
													tag: AutoUpdater.releases[0].tag_name,
													action_update_kernel: true
												}
											).done(function(data) {
												if (data == 'action_update_kernel-done') {
													$.post(
														HTML_PATH_ROOT +
															'bl-plugins/bludit-auto-update/perform-update.php',
														{
															url: zip_path,
															tag: AutoUpdater.releases[0].tag_name,
															cleanup: true
														}
													).done(function(data) {
														if (data == 'cleanup-done') {
															console.log('cleanup-done');
															$('#autoupdate_start_update').html('Update done 😎');
															$('#autoupdate_start_update').removeClass('btn-warning');
															$('#autoupdate_start_update').addClass('btn-success');
															// $('#autoupdate_start_update').attr('disabled', null);
															//
															// $('#autoupdater_dynamic_content').html(`
															// <div class="alert alert-info">
															// installed version: <code>${AutoUpdater.releases[0]
															// 	.name}</code>
															// </div>
															// <div class="alert alert-info">
															// This is the newest version available on GitHub.
															// </div>
															// `);
														}
													});
												}
											});
										}
									});
								}
							});
						}
					});
				}
			});
			setInterval(function() {
				$.get(HTML_PATH_ROOT + 'bl-plugins/bludit-auto-update/get-status.php').done(function(data) {
					if (AutoUpdater.previous_status != data) {
						$('#update_progress').html(data);
						AutoUpdater.previous_status = data;
					}
				});
			}, 50);
		});
	} else {
		$('#autoupdater_dynamic_content').html(`
        <div class="alert alert-info">
		installed version: <code>${BLUDIT_VERSION} (BUILD ${BLUDIT_BUILD}) (name: "${AutoUpdater.current_version
			.name}")</code>
        </div>
        <div class="alert alert-info">
		This is the newest version available on GitHub.
        </div>
        `);
	}
});
