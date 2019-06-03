<div class="delivery-dates">
  <?php if ($title) { ?>
  <h4><?php echo $title; ?></h4>
  <?php } ?>
  <table class="table table-condensed">
    <tbody>
      <?php foreach ($methods as $method) { ?>
      <tr>
        <td><?php echo $method['name']; ?></td>
        <td><strong><?php echo $method['date']; ?></strong></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
