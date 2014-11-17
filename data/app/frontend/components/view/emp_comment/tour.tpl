<div class="comment-body" id="comment-{$this->entity->getPk()}">
    <div class="comment-header">
        <div class="pull-left">{$this->entity->ctime->asAdorned()}</div>
        <div class="pull-right">{$this->entity->manager->name}</div>
    </div>
    <div class="comment-text">
        {$this->entity->text->get()}
    </div>
    <a data-record="#comment-{$this->entity->getPk()}" class="msglink removecomment" href="/delete/{$this->entity->getEntity()}/{$this->entity->getPk()}">удалить</a>
</div>