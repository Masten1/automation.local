{foreach from=$rooms item=room}
    <option value="{$room->getPk()}">{$room}</option>
{/foreach}