
<div style="float: left; width: 20%;">

<table cellspacing="0" style="width: 100%;">
    <thead>
        <tr>
            <th>Add Task</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border-bottom: 0;">
                <form action="#" method="post">
                    <textarea name="item_description"></textarea>
                    <input type="reset" value="Clear">
                    <input type="button" id="preview-button" value="Preview">
                    <input type="submit" style="float: right;" value="Add Task">
                </form>
            </td>
        </tr>
        <tr>
            <td id="preview-area" style="border-bottom: 0;">

            </td>
        </tr>
    </tbody>
</table>

</div>
<div style="float: left; width: 80%;">

<table cellspacing="0" class="tablesorter">
    <thead>
        <tr>
            <th>Incomplete Tasks</th>
            <th>Added</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
<?php if (empty($store['incomplete'])): echo '<tr><td colspan="3">no incomplete items</td></tr>'; endif; ?>
<?php foreach ($store['incomplete'] as $key => $item): ?>
        <tr>
            <td><?=$pd->parse($item['description']);?></td>
            <td class="date" style="width: 150px;"><abbr class="timeago" title="<?=$item['added'];?>"><?=$item['added'];?></abbr></td>
            <td style="width: 150px; text-align: center;">
                <form action="#" method="post">
                    <input type="hidden" name="done" value="<?=$key;?>"></textarea>
                    <input type="submit" value="Mark as Complete">
                </form>
            </td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>

<table cellspacing="0" class="tablesorter">
    <thead>
        <tr>
            <th>Complete Tasks</th>
            <th>Completed</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
    <tbody>
<?php if (empty($store['complete'])): echo '<tr><td colspan="3">no complete items</td></tr>'; endif; ?>
<?php foreach ($store['complete'] as $key => $item): ?>
        <tr>
            <td class="complete"><?=$pd->parse($item['description']);?></td>
            <td class="date" style="width: 150px;"><abbr class="timeago" title="<?=$item['complete'];?>"><?=$item['complete'];?></abbr></td>
            <td style="width: 150px; text-align: center;">
                <form action="#" method="post">
                    <input type="hidden" name="undo" value="<?=$key;?>"></textarea>
                    <input type="submit" value="Mark as Incomplete">
                </form>
            </td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>

</div>