<table class="w-300px">
  <tr>
    <th>識別名</th>
    <th class="w-25">割合</th>
  </tr>

<?php foreach ($rates as $no => $rate): ?>
<tr>
    <td class="bg-light">
        <?= $this->Form->input("artist_rates.{$no}.id", ['type' => 'hidden', 'value' => $rate['id']]); ?>
        <?= $this->Form->input("artist_rates.{$no}.position", ['type' => 'hidden', 'value' => $rate['position']]); ?>
        <?= $this->Form->input("artist_rates.{$no}.name", ['type' => 'text', 'value' => $rate['name'], 'class' => 'w-100']); ?>
    </td>

    <td>
        <?= $this->Form->input("artist_rates.{$no}.rate", ['type' => 'text', 'value' => $rate['rate'], 'class' => 'w-75 text-right rate', 'onchange' => 'getRateAmount();']); ?>%
    </td>
</tr>
<?php endforeach; ?>

<tr>
    <td>合計</td>
    <td>
        <?= $this->Form->input("rate_amount", ['type' => 'text', 'readonly' => true, 'class' => 'w-75 text-right', 'id' => 'idRateAmount']); ?>%
    </td>
</tr>
</table>
