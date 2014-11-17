<h3 class="col-lg-12" style="clear: both; border-bottom: 1px solid #e0e0e0">Комментарии</h3>
{assign var=component value=$this}
<div class="col-lg-7" id="comments">
    {foreach from=$this->field item=comment}
        {$comment->asAdorned()}
    {/foreach}
</div>
{assign var=this value=$component}
<div class="col-lg-5" id="addComment">
    <form id="commentForm" action="{$this->getAction()}">
        <div class="form-group">
            <label for="status">Статус заявки</label>
            <select class="form-control noclear" name="comment[statusId]" id="status">
                {foreach from=$this->statuses item=status}
                    <option value="{$status->getPk()}" {if $this->entity->statusId == $status->getPk()}selected{/if} >{$status->name->get()}</option>
                {/foreach}
            </select>
        </div>

        <div class="form-group">
            <label for="commentBody">Комментарий</label>
            <textarea id="commentBody" class="form-control" name=comment[text] id="newComment"></textarea>
            <input type="hidden" name=comment[{$this->getForeignKey()}] value="{$this->entity->getPk()}"  />
        </div>
        <button data-container="#comments" class="btn btn-primary savecomment">Добавить комментарий</button>
    </form>
</div>