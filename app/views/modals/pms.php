<?php $maxlength = Config::get('pms.maxlength'); ?>

<link href="<?php echo asset_url('css/pms.css') ?>" rel="stylesheet">

<div class="modal fade" id="pmModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md-2">
		<div class="modal-content">
			
			<div class="pm-conversation-list">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<div class="pull-right" style="margin-right: 10px;">
						<button type="button" class="btn btn-default btn-sm pm-refresh" data-toggle="tooltip" data-placement="top" title="<?php _e('main.refresh_messages') ?>">
							<span class="glyphicon glyphicon-refresh"></span>
						</button>
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
								<span class="glyphicon glyphicon-ok"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#" class="pm-mark-all"><?php _e('main.mark_messages') ?></a></li>
								<li><a href="#" class="pm-delete-all"><?php _e('main.delete_all_messages') ?></a></li>
							</ul>
						</div>
						<button type="button" class="btn btn-primary btn-sm new-message"><?php _e('main.new_message') ?></button>
					</div>
					<h4 class="modal-title"><?php _e('main.pms') ?></h4>
				</div>

				<div class="modal-body">
					<ul class="list-group"></ul>
				</div>
				<div class="modal-footer"><div class="ajax-loader"></div></div>
			</div>

			<div class="pm-conversation hidden">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">
						<a href="#" class="return"><?php _e('main.pms') ?></a>
						<?php _e('main.message_with') ?>
						<span class="pm-with"></span>
					</h4>
				</div>
				<div class="modal-body">
					<ul class="pm-list"></ul>
					<div class="ajax-loader"></div>
				</div>
				<div class="modal-footer">
					<form action="sendMessage" class="ajax-form">
						<input type="hidden" name="to">

						<div class="form-group">
			                <textarea name="message" class="form-control pm-textarea focus-me" rows="3" <?php if ($maxlength) echo 'maxlength="'.$maxlength.'"'; ?>></textarea>
			            </div>
			            <div class="pull-right">
							<?php if ($maxlength): ?>
								<span class="counter"><?php echo $maxlength; ?></span>
			            	<?php endif ?>
			            	<button type="submit" class="btn btn-primary"><?php _e('main.send_message') ?></button>
			            </div>
					</form>
				</div>
			</div>

			<div class="pm-conversation-new hidden">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">
						<a href="#" class="return"><?php _e('main.pms') ?></a> &rsaquo; <?php _e('main.message_new') ?>
					</h4>
				</div>
				<div class="modal-body">
					<div class="form-group" style="margin-bottom: 10px;">
		                <input type="text" name="search" class="form-control pm-search-contact" autocomplete="off" placeholder="<?php _e('main.message_to') ?>">
		            </div>
					<div class="list-group"></div>
				</div>
				<div class="modal-footer">
					<div class="form-group">
		                <textarea name="message" class="form-control pm-textarea" rows="3" <?php if ($maxlength) echo 'maxlength="'.$maxlength.'"'; ?>></textarea>
		            </div>
		            <div class="pull-right">
						<?php if ($maxlength): ?>
							<span class="counter"><?php echo $maxlength; ?></span>
		            	<?php endif ?>
		            	<button type="button" class="btn btn-primary"><?php _e('main.send_message') ?></button>
		            </div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>EasyLogin.options.pms = <?php echo json_encode(Config::get('pms')) ?>;</script>
<script src="<?php echo asset_url('js/vendor/jquery.timeago.js') ?>"></script>
<script src="<?php echo asset_url('js/vendor/tmpl.js') ?>"></script>
<script src="<?php echo asset_url('js/pms.js') ?>"></script>

<script type="text/html" id="conversationItemTemplate">
	<li class="list-group-item <% if (!replied && !read) { %>unread<% } %>" data-conversation-id="<%= user.id %>">
		<div class="row">
			<div class="col-md-1">
				<a href="<?php echo App::url('profile.php?u=<%= user.id %>') ?>" target="_blank" class="profile-url"><img src="<%= user.avatar %>" class="pm-avatar"></a>
				<span class="label label-primary pm-unread"><?php _e('main.unread_new') ?></span>
			</div>
			<div class="col-md-11">
				<a href="<?php echo App::url('profile.php?u=<%= user.id %>') ?>" target="_blank" class="profile-url pm-user-name"><%= user.name %></a>
				<time class="pm-time timeago" datetime="<%= timestamp %>" title="<%= timestamp %>"></time>
				<div class="pm-read-more"><span class="glyphicon glyphicon-chevron-right"></span></div>
				<div class="pm-message"><% if (replied) { %> <span class="pm-replied"></span> <% } %> <%= message %></div>
			</div>
		</div>
	</li>
</script>

<script type="text/html" id="conversationMessageTemplate">
	<li class="pm <% if (sent) { %>sent<% } else { %>received<% } %>" data-message-id="<%= id %>">
		<img src="<%= user.avatar %>" class="pm-avatar">
		<div class="pm-content clearfix">
			<time class="pm-time timeago" datetime="<%= timestamp %>" title="<%= timestamp %>"></time>
			<div class="pm-message">
				<div class="pm-text"><%= message %></div>
				<div class="pm-caret">
		    		<div class="pm-caret-outer"></div>
		        	<div class="pm-caret-inner"></div>
		      	</div>
			</div>
			<span class="pm-delete" data-toggle="tooltip" data-placement="top" title="<?php _e('main.delete_message') ?>">
				<span class="glyphicon glyphicon-trash"></span>
			</span>
		</div>
	</li>
</script>

<script type="text/html" id="contactSearchTemplate">
	<a href="#" class="list-group-item" data-conversation-id="<%= id %>">
		<img src="<%= avatar %>" class="pm-avatar">
		<span class="v-middle">
			<span class="fullname"><%= name %></span>
			<% if (username) { %>(<span class="username pm-user-name"><%= username %></span>)<% } %>
		</span>
	</a>
</script>