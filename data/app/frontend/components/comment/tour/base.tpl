<h3 class="col-lg-12" style="border-bottom: 1px solid #e0e0e0">Комментарии</h3>
{assign var=component value=$this}
<div class="col-lg-7" id="comments">
    {foreach from=$this->field item=comment}
        {$comment->render('tour')}
    {/foreach}
</div>
{assign var=this value=$component}
<div class="col-lg-5" id="addComment">
    <form id="commentForm" action="{$this->getAction()}">
        <div class="form-group">
            <textarea class="form-control" name=comment[text] id="newComment"></textarea>
            <input type="hidden" name=comment[managerId] value="1"  />
            <input type="hidden" name=comment[{$this->getForeignKey()}] value="{$this->entity->getPk()}"  />
        </div>
        <button data-container="#comments" class="btn btn-primary savecomment">Добавить комментарий</button>
    </form>
</div>