<!--<div>
    <select class="span3" name="fk_user_id">
        <option value="0" <?php echo (!$select_user) ? "selected" : '' ?>>Papar semua pengguna</option>
<?php foreach ($users as $user): ?>
                            <option value="<?php echo $user->fk_user_id ?>" <?php echo ($select_user == $user->fk_user_id) ? "selected" : '' ?>><?php echo $user->full_name ?></option>
<?php endforeach; ?>
    </select>
</div>-->

<div class="control-group">
    <input type="text" name="user_id" id="user_id" class="ac_field span3 margin-bottom0" />
    <input type="hidden" name="ac_user_id" id="ac_user_id"/>
    <input type="button" class="btn" name="submit" id="submit" value="Papar"/>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th width="25%">Pengguna</strong></th>
            <th width="55%">Aktiviti</th>
            <th width="20%">Tarikh</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><i class="icon-user"></i> <?php echo $log->full_name; ?></td>
                    <td><?php echo $log->activity; ?></td>
                    <td><?php echo $log->created; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tiada senarai log dijumpai.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php echo $pagination; ?>

<?php if (!empty($bookings)): ?>
    <div class="custom-legend"><?php echo $this->pagination->create_pagination_text(); ?></div>
<?php endif; ?>
<br/><br/>

<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Pengesahan</h3>
    </div>
    <div class="modal-body">
        <p>Hapus rekod dari sistem?</p>
    </div>
    <div class="modal-footer">
        <?php echo form_open('rooms/delete', 'id="form-delete-list"'); ?>
        <input type="hidden" name="ids"/>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <input type="submit" class="btn btn-primary" value="Hapus Rekod"/>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#submit').click(function(){
            var url = "<?php echo site_url('logs/index/'); ?>" + "/" +$('#ac_user_id').val();
            $(location).attr('href', url);
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){    
        // Load users then initialize plugin:
        $.ajax({
            url: '<?php echo site_url('ajaxify/getUserList') ?>',
            //url: 'assets/js/countries.txt',
            dataType: 'json'
        }).done(function (source) {

            var userArray = $.map(source, function (value, key) {
                return {
                    value: value, 
                    data: key
                };            
            }),
            users = $.map(source, function (value) {
                return value;
            });
        
            // Setup jQuery ajax mock:
            $.mockjax({
                url: '*',
                responseTime:  200,
                response: function (settings) {
                    var query = settings.data.query,
                    queryLowerCase = query.toLowerCase(),
                    suggestions = $.grep(users, function(user) {
                        return user.toLowerCase().indexOf(queryLowerCase) !== -1;
                    }),
                    response = {
                        query: query,
                        suggestions: suggestions
                    };

                    this.responseText = JSON.stringify(response);
                }
            });

            // Initialize autocomplete with local lookup:
            $('.ac_field').autocomplete({
                lookup: userArray,
                onSelect: function (suggestion) {
                    //$('#selection-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
                    var hiddenfield = "ac_" + $(this).attr('id');
                    $("#"+hiddenfield).val(suggestion.data);
                }
            });       
        
        });    
    });
</script>