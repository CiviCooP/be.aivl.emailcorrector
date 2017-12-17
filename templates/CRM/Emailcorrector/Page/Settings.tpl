<div class="crm-content-block crm-block">
  <div id="help">
    {ts}Instellingen voor email correcties. Let op, alleen het deel achter de @ kan gecorrigeerd worden!{/ts}
  </div>
  <div id="email_corrector_page_wrapper" class="dataTables_wrapper">
    <table id="email_corrector_-table" class="display">
      <thead>
        <tr>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Instelling{/ts}</th>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Waardes{/ts}</th>
          <th class="sorting_disabled" rowspan="1" colspan="1"></th>
        </tr>
      </thead>
      <tbody>
      {foreach from=$rows item=row}
          <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
            <td>{$row.label}</td>
            <td>{$row.values}</td>
            <td><span>{$row.edit}</span></td>
          </tr>
      {/foreach}
      </tbody>
    </table>
    <div class="action-link">{$done_url}</div>
  </div>
</div>
