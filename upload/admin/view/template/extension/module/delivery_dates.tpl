<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-delivery-dates" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-delivery-dates" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-rules" data-toggle="tab"><?php echo $tab_rules; ?></a></li>
            <li><a href="#tab-calendar" data-toggle="tab"><?php echo $tab_calendar; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="delivery_dates_status" id="input-status" class="form-control">
                    <option value="1"<?php echo $delivery_dates_status ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
                    <option value="0"<?php echo $delivery_dates_status ? '' : ' selected="selected"'; ?>><?php echo $text_disabled; ?></option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-title"><span data-toggle="tooltip" title="<?php echo $help_title; ?>"><?php echo $entry_title; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="delivery_dates_title" value="<?php echo $delivery_dates_title; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title" class="form-control" />
                  <?php if ($error_title) { ?>
                  <div class="text-danger"><?php echo $error_title; ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-rules">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <td class="text-left" style="width: 20%;"><?php echo $column_stock_status; ?></td>
                      <td class="text-left"><?php echo $column_methods; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rules as $key => $rule) { ?>
                    <tr>
                      <td class="text-left"><?php echo $rule['name']; ?></td>
                      <td class="text-left">
                        <div class="methods" data-key="<?php echo $key; ?>">
                          <?php foreach ($rule['methods'] as $index => $method) { ?>
                          <?php $name = 'delivery_dates_rules[' . $key . '][methods][' . $index . ']'; ?>
                          <div class="method">
                            <div class="row">
                              <div class="col-sm-8"><input type="text" name="<?php echo $name; ?>[name]" value="<?php echo $method['name']; ?>" placeholder="<?php echo $entry_method; ?>" class="form-control" /></div>
                              <div class="col-sm-3"><input type="text" name="<?php echo $name; ?>[days]" value="<?php echo $method['days']; ?>" placeholder="<?php echo $entry_days; ?>" title="<?php echo $help_days; ?>" class="form-control" /></div>
                              <div class="col-sm-1"><button type="button" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-remove"><i class="fa fa-minus-circle"></i></button></div>
                            </div>
                            <?php if (isset($error_methods[$key][$index])) { ?>
                            <div class="text-danger"><?php echo $error_methods[$key][$index]; ?></div>
                            <?php } ?>
                          </div>
                          <?php } ?>
                        </div>
                        <?php if (!$rule['methods']) { ?>
                        <p class="text-muted empty"><?php echo $text_no_methods; ?></p>
                        <?php } ?>
                        <button type="button" class="btn btn-default btn-sm btn-add"><i class="fa fa-plus-circle"></i> <?php echo $button_add; ?></button>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-calendar">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-cutoff"><span data-toggle="tooltip" title="<?php echo $help_cutoff; ?>"><?php echo $entry_cutoff; ?></span></label>
                <div class="col-sm-10">
                  <div class="input-group time" style="max-width: 200px;">
                    <input type="text" name="delivery_dates_cutoff" value="<?php echo $delivery_dates_cutoff; ?>" placeholder="14:00" data-date-format="HH:mm" id="input-cutoff" class="form-control" />
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default"><i class="fa fa-clock-o"></i></button>
                    </span>
                  </div>
                  <p class="help-block"><?php echo $text_server_time; ?></p>
                  <?php if ($error_cutoff) { ?>
                  <div class="text-danger"><?php echo $error_cutoff; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_weekends; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($weekdays as $day => $text) { ?>
                  <label class="checkbox-inline"><input type="checkbox" name="delivery_dates_weekends[]" value="<?php echo $day; ?>"<?php echo in_array($day, $delivery_dates_weekends) ? ' checked="checked"' : ''; ?> /> <?php echo $text; ?></label>
                  <?php } ?>
                  <?php if ($error_weekends) { ?>
                  <div class="text-danger"><?php echo $error_weekends; ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/html" id="method-template">
<div class="method">
  <div class="row">
    <div class="col-sm-8"><input type="text" name="delivery_dates_rules[{key}][methods][{index}][name]" value="" placeholder="<?php echo $entry_method; ?>" class="form-control" /></div>
    <div class="col-sm-3"><input type="text" name="delivery_dates_rules[{key}][methods][{index}][days]" value="" placeholder="<?php echo $entry_days; ?>" title="<?php echo $help_days; ?>" class="form-control" /></div>
    <div class="col-sm-1"><button type="button" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-remove"><i class="fa fa-minus-circle"></i></button></div>
  </div>
</div>
</script>
<script type="text/javascript"><!--
var methodIndex = <?php echo (int)$method_index; ?>;

$('#tab-rules').on('click', '.btn-add', function() {
  var cell = $(this).closest('td');
  var html = $('#method-template').html().replace(/{key}/g, cell.find('.methods').data('key')).replace(/{index}/g, methodIndex++);

  cell.find('.methods').append(html);
  cell.find('.empty').remove();
  cell.find('.method:last input:first').focus();
});

$('#tab-rules').on('click', '.btn-remove', function() {
  $(this).tooltip('destroy');
  $(this).closest('.method').remove();
});

$('.time').datetimepicker({
  pickDate: false
});
//--></script>
<style type="text/css">
#tab-rules .method {
  margin-bottom: 8px;
}
</style>
<?php echo $footer; ?>
