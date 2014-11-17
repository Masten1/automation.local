{foreach from=$this->entity->tiers->get() item=tier}

    <h5>{$tier->buildArray()}</h5>

    <table>
        {section name=row loop=$tier->vehicle->width->get()}
            <tr>
                {section name=col loop=$tier->rows->get() }
                    <td class="seat-td">
                        <div data-val="{$tier->getSeatNumber($smarty.section.row.iteration, $smarty.section.col.iteration)}"
                             data-comment="{$tier->getSeatComment($smarty.section.row.iteration, $smarty.section.col.iteration)}"
                             data-tier="{$tier->getPk()}"
                             data-row="{$smarty.section.row.iteration}"
                             data-column="{$smarty.section.col.iteration}" class="seat">
                            {$tier->getSeatNumber($smarty.section.row.iteration, $smarty.section.col.iteration)}
                        </div>
                    </td>
                {/section}
            </tr>
        {/section}
    </table>
{/foreach}