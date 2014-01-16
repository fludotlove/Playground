
<p><?=isset($_GET['tag']) ? 'Filtered by: <strong>' . $_GET['tag'] . '</strong> [<a href="' . $url . '">Clear</a>]' : '<strong>&nbsp;</strong>'; ?></p>

<div class="add-div">

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
                        <select name="item_priority">
                            <option>Very High</option>
                            <option>High</option>
                            <option selected="selected">Medium</option>
                            <option>Low</option>
                            <option>Very Low</option>
                        </select><br /><br />
                        <input type="reset" value="Clear">
                        <input class="tbl-info" type="button" id="preview-button" value="Preview">
                        <input type="submit" class="add-task" value="Add Task">
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
<div class="list-div">

    <table cellspacing="0" class="tablesorter">
        <thead>
            <tr>
                <th>Incomplete Tasks</th>
                <th class="tbl-info">Added</th>
                <th class="tbl-info">Priority</th>
                <th class="tbl-info">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
    <?php if (empty($store['incomplete'])): echo '<tr><td>No incomplete items!</td><td class="tbl-info">N/A</td><td class="tbl-info"></td><td class="tbl-info"></td></tr>'; endif; ?>
    <?php foreach ($store['incomplete'] as $key => $item): ?>
            <tr>
                <td>
                    <?=$pd->parse($item['description']);?>
                    <div class="list-info">
                        <p style="float: left;" class="date"><span class="prio-<?=str_replace(' ', '-', strtolower($item['priority']));?>"><?=$item['priority'];?></span> <abbr class="timeago" title="<?=$item['added'];?>"><?=$item['added'];?></abbr></p>
                        <form action="#" method="post">
                            <input type="hidden" name="done" value="<?=$key;?>"></textarea>
                            <input style="float: right;" type="submit" value="&#10004;">
                        </form>
                    </div>
                </td>
                <td class="tbl-info date" style="width: 150px;"><abbr class="timeago" title="<?=$item['added'];?>"><?=$item['added'];?></abbr></td>
                <td class="tbl-info" style="width: 100px;"><span class="prio-<?=str_replace(' ', '-', strtolower($item['priority']));?>"><?=$item['priority'];?></span></td>
                <td class="tbl-info" style="width: 150px; text-align: center;">
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
                <th class="tbl-info">Completed</th>
                <th class="tbl-info">Priority</th>
                <th class="tbl-info">&nbsp;</th>
            </tr>
            </thead>
        <tbody>
    <?php if (empty($store['complete'])): echo '<tr><td>No complete items! Get cracking!</td><td class="tbl-info">N/A</td><td class="tbl-info"></td><td class="tbl-info"></td></tr>'; endif; ?>
    <?php foreach ($store['complete'] as $key => $item): ?>
            <tr>
                <td class="complete">
                    <?=$pd->parse($item['description']);?>
                    <div class="list-info">
                        <p style="float: left;" class="date"><span class="prio-<?=str_replace(' ', '-', strtolower($item['priority']));?>"><?=$item['priority'];?></span> <abbr class="timeago" title="<?=$item['complete'];?>"><?=$item['complete'];?></abbr></p>
                        <form action="#" method="post">
                            <input type="hidden" name="undo" value="<?=$key;?>"></textarea>
                            <input style="float: right;" type="submit" value="&#10008;">
                        </form>
                    </div>
                </td>
                <td class="tbl-info date" style="width: 150px;"><abbr class="timeago" title="<?=$item['complete'];?>"><?=$item['complete'];?></abbr></td>
                <td class="tbl-info" style="width: 100px;"><span class="prio-<?=str_replace(' ', '-', strtolower($item['priority']));?>"><?=$item['priority'];?></span></td>
                <td class="tbl-info" style="width: 150px; text-align: center;">
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